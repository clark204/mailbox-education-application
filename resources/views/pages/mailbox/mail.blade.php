@extends('layouts.mailbox')

@section('mailbox.content')
    <section id="mail" aria-label="Email view" class="flex flex-col h-full">

        <header class="flex items-center px-4 h-14 gap-1 border-b border-gray-100 shrink-0">
            <a href="{{ request()->route()->getName() === 'view.mail' ? route('view.' . request()->route()->parameter('section')) : route('view.inbox') }}"
                aria-label="Back to inbox"
                class="size-9 rounded-xl hover:bg-gray-100 transition-all duration-150 flex items-center justify-center hover:text-gray-800">
                <x-heroicon-s-arrow-left class="size-4" aria-hidden="true" />
            </a>

            <div class="w-px h-5 bg-gray-200 mx-1"></div>

            <form action="{{ route('mail.update') }}" method="POST" class="flex gap-1.5">
                @csrf
                <input type="hidden" name="message_id" value="{{ $inbox->inbox_id }}">
                <input type="hidden" name="action" id="inp_action">
                <input type="hidden" name="section" value="{{ $section }}">
                <button type="submit" aria-label="{{ $inbox->is_trash ? 'Untrash inbox' : 'Archive inbox' }}"
                    title="{{ $inbox->is_trash ? 'Untrash' : 'Archive' }}"
                    onclick="document.getElementById('inp_action').value = '{{ $inbox->is_trash ? 'untrash' : 'archive' }}'"
                    class="size-9 rounded-xl hover:bg-gray-100 transition-all duration-150 flex items-center justify-center cursor-pointer">
                    @if ($inbox->is_trash)
                        <x-heroicon-o-arrow-uturn-left class="size-4" aria-hidden="true" />
                    @else
                        <x-heroicon-o-archive-box-arrow-down class="size-4" aria-hidden="true" />
                    @endif
                </button>
                <button aria-label="Delete email" title="{{ $inbox->is_trash ? 'Delete forever' : 'Delete' }}"
                    onclick="document.getElementById('inp_action').value = '{{ $inbox->is_trash ? 'delete_forever' : 'delete' }}'"
                    class="size-9 rounded-xl hover:bg-red-50 {{ $inbox->is_trash ? 'w-fit' : '' }} hover:text-red-500 transition-all duration-150 flex items-center justify-center cursor-pointer">
                    @if ($inbox->is_trash)
                        <x-heroicon-o-trash class="size-4" aria-hidden="true" />
                        <span class="text-xs ml-1">Forever</span>
                    @else
                        <x-heroicon-o-trash class="size-4" aria-hidden="true" />
                    @endif
                </button>

                <button type="submit" aria-label="Mark as unread" title="Mark unread"
                    onclick="document.getElementById('inp_action').value = 'mark_unread'"
                    class="relative size-9 rounded-xl hover:bg-blue-50 hover:text-blue-500 transition-all duration-150 flex items-center justify-center cursor-pointer">
                    <div class="absolute top-2.5 right-2 size-1.5 bg-blue-500 rounded-full"></div>
                    <x-heroicon-o-envelope class="size-4" aria-hidden="true" />
                </button>

                <div class="flex-1"></div>

                @if (!$inbox->is_trash)
                    <button type="submit" aria-label="Star email" title="Important" id="star-btn"
                        data-id="{{ $inbox->inbox_id }}"
                        onclick="document.getElementById('inp_action').value = '{{ $inbox->is_important ? 'unmark_important' : 'mark_important' }}'"
                        class="size-9 rounded-xl hover:bg-amber-50 hover:text-amber-400 transition-all duration-150 flex items-center justify-center cursor-pointer">
                        @if ($inbox->is_important)
                            <x-heroicon-s-star class="size-4 text-amber-400" aria-hidden="true" />
                        @else
                            <x-heroicon-o-star class="size-4" aria-hidden="true" />
                        @endif
                    </button>
                @endif
            </form>
        </header>

        {{-- Email Content --}}
        <article class="flex-1 overflow-y-auto px-6 py-6 lg:px-10 lg:py-8 w-full" aria-label="Email content">

            {{-- Subject --}}
            <h1 class="text-2xl font-semibold text-gray-900 leading-snug mb-6">
                {{ $inbox->compose->subject ?? 'No subject' }}
            </h1>

            {{-- Sender Card --}}
            <div class="flex items-start gap-4 mb-6">
                {{-- Avatar --}}
                <div class="shrink-0 size-10 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    @if ($inbox->compose->sender->avatar)
                        <img src="{{ asset('storage/' . $inbox->compose->sender->avatar) }}" alt=""
                            class="size-10 rounded-full">
                    @else
                        <x-heroicon-s-user
                            class="relative size-10 rounded-full object-cover border-2 border-white shadow-sm text-slate-600"
                            aria-hidden="true" />
                    @endif
                </div>

                {{-- Meta --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-gray-900 text-sm">
                            {{ $inbox->compose->sender->first_name }} {{ $inbox->compose->sender->last_name }}
                        </span>
                        <span class="text-xs text-gray-400">&lt;{{ $inbox->compose->sender->email }}&gt;</span>
                    </div>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="text-xs text-gray-400">to</span>
                        <span
                            class="text-xs text-gray-500 font-medium">{{ $inbox->compose->receiver->email ?? 'me' }}</span>
                    </div>
                </div>

                {{-- Timestamp --}}
                <time datetime="{{ $inbox->compose->created_at->toDateTimeLocalString() }}"
                    class="shrink-0 text-xs text-text-black whitespace-nowrap mt-0.5"
                    title="{{ $inbox->compose->created_at->format('M j, Y, g:i A') }}">
                    {{ $inbox->compose->created_at->format('M j, Y') }} &middot;
                    {{ $inbox->compose->created_at->format('g:i A') }}
                </time>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100 mb-6"></div>

            {{-- Body --}}
            <div class="text-sm bg-gray-100 whitespace-pre-wrap min-h-96">
                {!! $inbox->compose->message !!}
            </div>

        </article>
    </section>
@endsection