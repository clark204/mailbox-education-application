@extends('pages.mailbox.inbox')

@section('mailbox.tab')
    {{-- Email List --}}
    <ul id="inbox-list" role="list" class="">
        @forelse ($inbox as $message)
            <x-mail-list :message="$message" :section="$section" />
        @empty
            <li class="flex flex-col items-center justify-center py-24 px-8 text-center">
                <div class="relative mb-6">
                    <div class="size-24 rounded-full bg-gray-100 flex items-center justify-center">
                        <x-heroicon-o-inbox class="size-12 text-gray-300" />
                    </div>
                    <div class="absolute -bottom-1 -right-1 size-8 rounded-full bg-blue-50 flex items-center justify-center">
                        <x-heroicon-o-check class="size-4 text-blue-400" />
                    </div>
                </div>
                <h3 class="text-base font-semibold text-gray-700 mb-1">You're all caught up</h3>
                <p class="text-sm text-gray-400 max-w-xs">No messages here yet. When someone sends you a message, it'll show
                    up here.</p>
            </li>
        @endforelse
    </ul>
@endsection
