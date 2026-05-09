@extends('layouts.mailbox')

@section('mailbox.content')
    <section id="search-inbox">

        <x-nav-inbox :inbox="$inbox" />

        {{-- Search heading --}}
        <div class="px-6 py-3 text-sm text-gray-500 border-b border-gray-100">
            @if ($inbox->total() > 0)
                Found <span class="font-medium text-gray-700">{{ $inbox->total() }}</span>
                result{{ $inbox->total() > 1 ? 's' : '' }} for
                <span class="font-medium text-gray-700">"{{ $query }}"</span>
            @else
                No results for <span class="font-medium text-gray-700">"{{ $query }}"</span>
            @endif
        </div>

        {{-- Email List --}}
        <ul id="inbox-list" role="list">
            @forelse ($inbox as $message)
                <x-mail-list :message="$message" section="search" />
            @empty
                <div class="flex flex-col items-center justify-center py-24 px-8 text-center">
                    <div class="size-24 rounded-full bg-gray-100 flex items-center justify-center mb-6">
                        <x-heroicon-o-magnifying-glass class="size-12 text-gray-300" />
                    </div>
                    <h3 class="text-base font-semibold text-gray-700 mb-1">No results found</h3>
                    <p class="text-sm text-gray-400 max-w-xs">Try different keywords or check your spelling.</p>
                </div>
            @endforelse
        </ul>

    </section>
@endsection