<?php

namespace App\Http\Controllers;

use App\Models\ComposeModel;
use App\Models\InboxModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class InboxController extends Controller
{
    public function inbox(string $section = 'primary')
    {
        $userId = Auth::id();

        $data = match ($section) {
            'primary' => InboxModel::with('compose.sender')
                ->where('user_id', $userId)
                ->where('type', 'receiver')
                ->where('is_draft', false)
                ->where('is_trash', false)
                ->where('is_archived', false)
                ->latest()
                ->paginate(25),

            'sent' => InboxModel::with('compose.sender')
                ->where('user_id', $userId)
                ->where('type', 'sender')
                ->where('is_draft', false)
                ->where('is_archived', false)
                ->where('is_trash', false)
                ->latest()
                ->paginate(25),

            'drafts' => InboxModel::with('compose')
                ->where('user_id', $userId)
                ->where('is_draft', true)
                ->where('is_trash', false)
                ->latest()
                ->paginate(25),

            'archived' => InboxModel::with('compose.sender')
                ->where('user_id', $userId)
                ->where('is_archived', true)
                ->where('is_trash', false)
                ->latest()
                ->paginate(25),

            'trash' => InboxModel::with('compose')
                ->where('user_id', $userId)
                ->where('is_trash', true)
                ->latest()
                ->paginate(25),

            'important' => InboxModel::with('compose.sender')
                ->where('user_id', $userId)
                ->where('is_important', true)
                ->where('is_trash', false)
                ->latest()
                ->paginate(25),

            default => abort(404),
        };

        return view("pages.mailbox.{$section}", ['inbox' => $data, 'section' => $section]);
    }

    public function store(Request $request)
    {
        $key = 'compose:'.$request->input('idempotency_id');

        if (Cache::has($key)){
            return back()->with('error', 'Too many request.');
        }

        Cache::put($key, true, 60);

        $request->validate([
            'inp_to' => 'required|exists:users,email',
            'inp_subject' => 'nullable|string|max:255',
            'inp_message' => 'nullable|string',
            'draft_id' => 'nullable|exists:tbl_inbox,inbox_id',
        ], [
            'inp_to.required' => 'The recipient email is required.',
            'inp_to.exists' => 'The specified recipient does not exist.',
            'draft_id.exists' => 'The specified draft does not exist.',
        ]);

        $receiver = User::where('email', $request->inp_to)->firstOrFail();

        // Sending from a draft
        if ($request->filled('draft_id')) {
            $draft = InboxModel::where('inbox_id', $request->draft_id)
                ->where('user_id', Auth::id())
                ->where('is_draft', true)
                ->firstOrFail();

            $draft->compose->update([
                'receiver_id' => $receiver->id,
                'subject' => $request->inp_subject,
                'message' => $request->inp_message,
            ]);

            $draft->update(['is_draft' => false]);

            InboxModel::create([
                'compose_id' => $draft->compose_id,
                'user_id' => $receiver->id,
                'type' => 'receiver',
            ]);

            return back()->with('success', 'Draft sent successfully!');
        }

        $compose = ComposeModel::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'subject' => $request->inp_subject,
            'message' => $request->inp_message,
        ]);

        InboxModel::create([
            'compose_id' => $compose->id,
            'user_id' => Auth::id(),
            'type' => 'sender',
        ]);

        InboxModel::create([
            'compose_id' => $compose->id,
            'user_id' => $receiver->id,
            'type' => 'receiver',
        ]);

        return back()->with('success', 'Message sent successfully!');

    }

    public function show($section, $inboxID)
    {
        $validSections = ['primary', 'sent', 'drafts', 'archived', 'trash', 'important'];
        if (! in_array($section, $validSections)) {
            abort(404);
        }

        $inbox = InboxModel::with('compose.sender', 'compose.receiver')
            ->where('inbox_id', $inboxID)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Mark as read if receiver
        if (! $inbox->is_read) {
            $inbox->update(['is_read' => true]);
        }

        return view('pages.mailbox.mail', compact('inbox', 'section'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:tbl_inbox,inbox_id',
            'action' => 'required|in:mark_read,mark_unread,mark_important,unmark_important,archive,unarchive,delete,untrash,delete_forever',
            'section' => 'nullable|in:primary,sent,drafts,archived,trash,important',
        ]);

        $inbox = InboxModel::where('inbox_id', $request->message_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        match ($request->action) {
            'mark_read' => $inbox->update(['is_read' => true]),
            'mark_unread' => $inbox->update(['is_read' => false]),
            'mark_important' => $inbox->update(['is_important' => true]),
            'unmark_important' => $inbox->update(['is_important' => false]),
            'archive' => $inbox->update(['is_archived' => true]),
            'unarchive' => $inbox->update(['is_archived' => false]),
            'delete' => $inbox->update(['is_trash' => true, 'trashed_at' => now()]),
            'untrash' => $inbox->update(['is_trash' => false, 'trashed_at' => null]),
            'delete_forever' => $inbox->delete(),
        };

        $section = $request->section ?? 'primary';
        $routeName = 'view.'.$section;

        if (! Route::has($routeName)) {
            $routeName = 'view.primary';
        }

        return redirect()->route($routeName)->with('success', 'Message updated successfully!');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array|min:1',
            'message_ids.*' => 'exists:tbl_inbox,inbox_id',
            'action' => 'required|in:mark_read,mark_unread,mark_important,unmark_important,archive,delete,delete_forever,untrash,unarchive',
        ]);

        $messages = InboxModel::whereIn('inbox_id', $request->message_ids)
            ->where('user_id', Auth::id());

        match ($request->action) {
            'mark_read' => $messages->update(['is_read' => true]),
            'mark_unread' => $messages->update(['is_read' => false]),
            'mark_important' => $messages->update(['is_important' => true]),
            'unmark_important' => $messages->update(['is_important' => false]),
            'archive' => $messages->update(['is_archived' => true]),
            'delete' => $messages->update(['is_trash' => true, 'trashed_at' => now()]),
            'delete_forever' => $messages->delete(),
            'untrash' => $messages->update(['is_trash' => false, 'trashed_at' => null]),
            'unarchive' => $messages->update(['is_archived' => false]),
        };

        return back()->with('success', 'Messages updated successfully!');
    }

    public function saveDraft(Request $request)
    {
        $request->validate([
            'draft_id' => 'nullable|exists:tbl_inbox,inbox_id',
            'inp_to' => 'nullable|exists:users,email',
            'inp_subject' => 'nullable|string|max:255',
            'inp_message' => 'nullable|string',
        ], [
            'draft_id.exists' => 'The specified draft does not exist.',
            'inp_to.exists' => 'The specified recipient does not exist.',
        ]);

        $receiver = $request->inp_to
            ? User::where('email', $request->inp_to)->first()
            : null;

        if ($request->draft_id) {
            // Update existing draft
            $draft = InboxModel::where('inbox_id', $request->draft_id)
                ->where('user_id', Auth::id())
                ->where('is_draft', true)
                ->firstOrFail();

            $draft->compose->update([
                'receiver_id' => $receiver?->id,
                'subject' => $request->inp_subject,
                'message' => $request->inp_message,
            ]);
        } else {
            // Create new draft — only 1 inbox row, no receiver row yet
            $compose = ComposeModel::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $receiver?->id,
                'subject' => $request->inp_subject,
                'message' => $request->inp_message,
            ]);

            $draft = InboxModel::create([
                'compose_id' => $compose->id,
                'user_id' => Auth::id(),
                'type' => 'sender',
                'is_draft' => true,
            ]);
        }

        return back()->with('warning', 'Draft saved successfully!');
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $userId = Auth::id();

        $inbox = InboxModel::with('compose.sender', 'compose.receiver')
            ->where('user_id', $userId)
            ->where('is_trash', false)
            ->whereHas('compose', function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('subject', 'like', "%{$query}%")
                        ->orWhere('message', 'like', "%{$query}%")
                        ->orWhereHas('sender', function ($q) use ($query) {
                            $q->where('first_name', 'like', "%{$query}%")
                                ->orWhere('last_name', 'like', "%{$query}%")
                                ->orWhere('email', 'like', "%{$query}%");
                        });
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('pages.mailbox.search', ['inbox' => $inbox, 'query' => $query]);
    }
}
