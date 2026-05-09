@props(['inbox'])

{{-- ── Toolbar ── --}}
<div class="flex items-center justify-between px-6 h-12 border-b border-gray-200 shrink-0">
    <div class="flex items-center gap-1 h-full">
        {{-- Select all checkbox + dropdown --}}
        <div tabindex="1" class="flex items-center relative h-full py-2">
            <div class="group focus-within:bg-gray-300 flex h-full rounded-lg">
                <div class="flex items-center justify-center h-full w-5 hover:bg-gray-400 rounded-l-lg cursor-pointer">
                    <input id="select-all" type="checkbox"
                        class=" rounded border-gray-300 accent-blue-600 cursor-pointer" />
                </div>
                <button onclick="toggleSelectDropdown()"
                    class="h-full w-5 flex items-center justify-center cursor-pointer rounded-r-lg hover:bg-gray-300">
                    <x-heroicon-s-chevron-down class="size-3" />
                </button>
            </div>
            <ul id="select-dropdown"
                class="hidden absolute z-10 bg-white top-[calc(100%-10px)] left-0 w-40 rounded shadow-xl border border-gray-200 py-1">
                <li onclick="selectByFilter('all')" class="text-sm hover:bg-gray-100 px-8 py-1 cursor-pointer">All
                </li>
                <li onclick="selectByFilter('read')" class="text-sm hover:bg-gray-100 px-8 py-1 cursor-pointer">Read
                </li>
                <li onclick="selectByFilter('unread')" class="text-sm hover:bg-gray-100 px-8 py-1 cursor-pointer">
                    Unread</li>
                <li onclick="selectByFilter('important')" class="text-sm hover:bg-gray-100 px-8 py-1 cursor-pointer">
                    Important</li>
            </ul>
        </div>
        <div id="toolbar-options" class="flex">
            {{-- Refresh --}}
            <button onclick="window.location.reload()" title="Refresh"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition cursor-pointer">
                <x-heroicon-o-arrow-path class="size-4" />
            </button>
        </div>
        <div id="selection-actions" class="hidden items-center gap-2 ml-4 text-sm">
            <button onclick="bulkAction('archive')" type="button" aria-label="Archive email" title="Archive"
                class="size-9 rounded-xl hover:bg-gray-100 transition-all duration-150 flex items-center justify-center cursor-pointer">
                <x-heroicon-o-archive-box-arrow-down class="size-4" aria-hidden="true" />
            </button>
            <button onclick="bulkAction('delete')" type="button" aria-label="Delete email" title="Delete"
                class="size-9 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all duration-150 flex items-center justify-center cursor-pointer">
                <x-heroicon-o-trash class="size-4" aria-hidden="true" />
            </button>
            <button onclick="bulkAction('mark_unread')" type="button" aria-label="Mark as unread" title="Mark unread"
                class="relative size-9 rounded-xl hover:bg-blue-50 hover:text-blue-500 transition-all duration-150 flex items-center justify-center cursor-pointer">
                <div class="absolute top-2.5 right-2 size-1.5 bg-blue-500 rounded-full"></div>
                <x-heroicon-o-envelope class="size-4" aria-hidden="true" />
            </button>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center gap-1 text-xs text-gray-500">
        <span>
            @if($inbox->total() > 0)
                {{ $inbox->firstItem() }}–{{ $inbox->lastItem() }} of {{ $inbox->total() }}
            @else
                0 of 0
            @endif
        </span>
        <a href="{{ $inbox->previousPageUrl() }}"
            class="w-7 h-7 flex items-center justify-center rounded-full {{ $inbox->onFirstPage() ? 'text-gray-300 pointer-events-none' : 'text-gray-500 hover:bg-gray-100 cursor-pointer' }} transition">
            <x-heroicon-s-chevron-left class="size-4" />
        </a>
        <a href="{{ $inbox->nextPageUrl() }}"
            class="w-7 h-7 flex items-center justify-center rounded-full {{ !$inbox->hasMorePages() ? 'text-gray-300 pointer-events-none' : 'text-gray-500 hover:bg-gray-100 cursor-pointer' }} transition">
            <x-heroicon-s-chevron-right class="size-4" />
        </a>
    </div>
</div>