@props(['message', 'section'])

<li id="{{ $message->id }}" class="flex group {{ $message->is_read ? 'font-extralight' : '' }}">

    <a @if ($message->is_draft) 
        onclick="openCompose({
        to:       '{{ $message->compose->receiver->email ?? null }}',
        subject:  '{{ addslashes($message->compose->subject) }}',
        message:  `{!! addslashes($message->compose->message) !!}`,
        draft_id: '{{ $message->inbox_id }}'
    })"@else href="{{ route('view.mail', ['section' => $section, 'inboxID' => $message->inbox_id]) }}" @endif 
        class="{{ $message->is_read ? 'bg-blue-100 group-hover:bg-blue-100' : 'group-hover:bg-gray-50' }} flex-1 px-4 h-12 border-b border-gray-300 flex gap-2 items-center  cursor-pointer">

        <input type="checkbox" name="selected[]" value="{{ $message->inbox_id }}"
            data-read="{{ $message->is_read ? 'true' : 'false' }}"
            data-important="{{ $message->is_important ? 'true' : 'false' }}" onclick="event.stopPropagation()"
            class="row-checkbox w-4 h-4 rounded border-gray-300 accent-blue-600 cursor-pointer shrink-0" />

        <form action="{{ route('mail.update') }}" method="POST">
            @csrf
            <input type="hidden" name="message_id" value="{{ $message->inbox_id }}">
            <input type="hidden" name="action" id="important-action"
                value="{{ $message->is_important ? 'unmark_important' : 'mark_important' }}">
            <button aria-label="Star email" onclick="event.stopPropagation()"
                title="{{ $message->is_important ? 'Unmark as important' : 'Mark as important' }}"
                class="flex z-10 items-center shrink-0 cursor-pointer rounded-full hover:bg-yellow-50 hover:text-yellow-600 p-2">
                @if ($message->is_important)
                    <x-heroicon-s-star class="size-4 text-yellow-400" aria-hidden="true" />
                @else
                    <x-heroicon-o-star class="size-4" aria-hidden="true" />
                @endif
            </button>
        </form>

        <div class="flex min-w-0 {{ $message->is_draft ? 'gap-2' : '' }}">
            @if ($message->is_draft)
                <p class="text-red-500">(Draft)</p>
            @endif
            <div class="flex-1 min-w-0 flex items-center">
                <address class="not-italic truncate text-sm font-medium">{{ $message->compose->sender->email }}
                </address>
            </div>
        </div>

        <div class="ml-2 flex-1 min-w-0 max-lg:flex">
            <p class="text-sm font-medium truncate">{{ $message->compose->subject ?? '(No Subject)' }}</p>
            <div class="mx-2 lg:hidden text-gray-300">-</div>
            <p class="text-sm text-gray-500 truncate">{!! $message->compose->message !!}</p>
        </div>

        <time class="hidden lg:flex items-center text-xs text-gray-400 shrink-0">
            {{ $message->created_at->format('h:i A') }}
        </time>
    </a>

    {{-- Hover Quick Actions--}}
    <form method="POST" action="{{ route('mail.update') }}"
        class="hidden lg:group-hover:flex {{ $message->is_read ? 'group-hover:bg-blue-100' : 'group-hover:bg-gray-50' }}  items-center gap-0.5 border-b border-gray-300 pr-4">
        @csrf
        <input type="hidden" name="message_id" value="{{ $message->inbox_id }}">
        <input type="hidden" name="action" id="action-{{ $message->inbox_id }}">
        @if ($section === 'archived' && $message->is_archived)
            <button type="submit" aria-label="Unarchive email" title="Unarchive"
                onclick="document.getElementById('action-{{ $message->inbox_id }}').value = 'unarchive'"
                class="size-9 rounded-xl hover:bg-gray-100 transition-all duration-150 flex items-center justify-center cursor-pointer">
                <x-heroicon-o-archive-box-x-mark class="size-4" aria-hidden="true" />
            </button>
        @else
            <button type="submit" aria-label="Archive email" title="Archive"
                onclick="document.getElementById('action-{{ $message->inbox_id }}').value = 'archive'"
                class="{{ $message->is_archived || $message->is_trash ? 'text-gray-600 pointer-events-none' : '' }} size-9 rounded-xl hover:bg-gray-100 transition-all duration-150 flex items-center justify-center cursor-pointer">
                <x-heroicon-o-archive-box-arrow-down class="size-4" aria-hidden="true" />
            </button>
        @endif
        <button aria-label="Delete email" title="Delete"
            onclick="document.getElementById('action-{{ $message->inbox_id }}').value = 'delete'"
            class="{{ $message->is_trash ? 'pointer-events-none text-gray-600' : '' }} size-9 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all duration-150 flex items-center justify-center cursor-pointer">
            <x-heroicon-o-trash class="size-4" aria-hidden="true" />
        </button>
        <button type="submit" aria-label="{{ $message->is_read ? 'Mark as unread' : 'Mark as Read' }}"
            title="{{ $message->is_read ? 'Mark as unread' : 'Mark as read' }}"
            onclick="document.getElementById('action-{{ $message->inbox_id }}').value = '{{ $message->is_read ? 'mark_unread' : 'mark_read' }}'"
            class="relative size-9 rounded-xl hover:bg-blue-50 hover:text-blue-500 transition-all duration-150 flex items-center justify-center cursor-pointer">
            <div class="absolute top-2.5 right-2 size-1.5 bg-blue-500 rounded-full"></div>
            @if ($message->is_read)
                <x-heroicon-o-envelope class="size-4" aria-hidden="true" />
            @else
                <x-heroicon-o-envelope-open class="size-4" aria-hidden="true" />
            @endif
        </button>
    </form>
</li>