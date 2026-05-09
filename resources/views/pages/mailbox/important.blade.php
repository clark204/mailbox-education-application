@extends('pages.mailbox.inbox')

@section('mailbox.tab')
    {{-- Email List --}}
    <ul id="inbox-list" role="list">
        @forelse ($inbox as $message)
            <x-mail-list :message="$message" :section="$section" />
        @empty
            <div class="flex flex-col items-center justify-center py-24 px-8 text-center">
                <div class="relative mb-6">
                    <div class="size-24 rounded-full bg-gray-100 flex items-center justify-center">
                        <x-heroicon-o-star class="size-12 text-gray-300" />
                    </div>
                    <div class="absolute -bottom-1 -right-1 size-8 rounded-full bg-amber-50 flex items-center justify-center">
                        <x-heroicon-o-plus class="size-4 text-amber-400" />
                    </div>
                </div>
                <h3 class="text-base font-semibold text-gray-700 mb-1">No important messages</h3>
                <p class="text-sm text-gray-400 max-w-xs">Messages you mark as important will appear here.</p>
            </div>
        @endforelse
    </ul>
@endsection