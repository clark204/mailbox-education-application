<?php

namespace App\Http\Controllers;

use App\Models\InboxModel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ChatbotController extends Controller
{
    private function buildSystemPrompt(): string
    {
        $user = Auth::user();
        $userId = $user->id;

        // Get all inbox data for this user with full relationships
        $allInbox = InboxModel::with(['compose.sender', 'compose.receiver'])
            ->where('user_id', $userId)
            ->where('is_trash', false)
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->inbox_id,
                'type' => $m->type,
                'from' => $m->compose->sender->name ?? 'Unknown',
                'from_email' => $m->compose->sender->email ?? '',
                'to' => $m->compose->receiver->name ?? 'Unknown',
                'to_email' => $m->compose->receiver->email ?? '',
                'subject' => $m->compose->subject ?? '(No subject)',
                'message' => $m->compose->message ?? '',
                'is_read' => $m->is_read,
                'is_draft' => $m->is_draft,
                'is_important' => $m->is_important,
                'is_archived' => $m->is_archived,
                'is_trash' => $m->is_trash,
                'date' => $m->created_at->format('M j, Y g:i A'),
            ]);

        $inboxJson = $allInbox->toJson(JSON_PRETTY_PRINT);

        return config('services.groq.system_prompt')."\n\n".
            "Current user:\n".
            "- Name: {$user->name}\n".
            "- Email: {$user->email}\n\n".
            'Today: '.now()->format('F j, Y g:i A')."\n\n".
            "Here is the complete inbox data for this user in JSON format:\n".
            "```json\n{$inboxJson}\n```\n\n".
            'Use this data to answer any questions about their emails, drafts, sent messages, and inbox status.';
    }

    public function message(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $userMessage = $request->input('message');
        $history = Session::get('chat_history', []);
        $history[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.groq.key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model'),
                'max_tokens' => 1024,
                'temperature' => 0.7,
                'messages' => array_merge(
                    [['role' => 'system', 'content' => $this->buildSystemPrompt()]],
                    $history
                ),
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'AI service unavailable. Please try again.'], 503);
            }

            $aiMessage = $response->json('choices.0.message.content');

            $history[] = ['role' => 'assistant', 'content' => $aiMessage];

            if (count($history) > 20) {
                $history = array_slice($history, -20);
            }

            Session::put('chat_history', $history);

            return response()->json(['message' => $aiMessage]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function clearHistory()
    {
        Session::forget('chat_history');

        return response()->json(['success' => true]);
    }

    // public function repohiveAssistant(Request $request)
    // {
    //     $validated = $request->validate([
    //         'message' => ['required', 'string', 'max:2000'],
    //         'conversationId' => ['nullable', 'string'],
    //         'history' => ['nullable', 'array'],
    //     ]);

    //     try {
    //         $response = Http::withToken(config('services.repohive_assistant.token'))
    //             ->acceptJson()
    //             ->timeout(30)
    //             ->post(rtrim(config('services.repohive_assistant.base_url'), '/').'/assistant/chat', [
    //                 'message' => $validated['message'],
    //                 'conversation_id' => $validated['conversationId'] ?? null,
    //                 'history' => $validated['history'] ?? [],
    //                 'metadata' => [
    //                     'module' => 'support',
    //                     'user_id' => (string) $request->user()->id,
    //                 ],
    //             ]);

                
    //         \Log::info('Repohive response', [
    //             'status' => $response->status(),
    //             'body' => $response->body(),
    //         ]);

    //         if ($response->failed()) {
    //             return response()->json([
    //                 'error' => 'Assistant service unavailable. ('.$response->status().')',
    //             ], 502);
    //         }

    //         return response()->json($response->json());

    //     } catch (ConnectionException $e) {
    //         return response()->json(['error' => 'Could not reach assistant service.'], 504);
    //     }
    // }
}
