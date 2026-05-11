@extends('layouts.mailbox')

@section('mailbox.content')
    <section id="trash-inbox">

        {{-- ── Toolbar ── --}}
        <div class="flex items-center justify-between px-6 h-12 border-b border-gray-200 shrink-0">
            <div class="flex items-center gap-1 h-full">
                {{-- Select all checkbox + dropdown --}}
                <div tabindex="1" class="flex items-center relative h-full py-2">
                    <div class="group focus-within:bg-gray-300 flex h-full rounded-lg">
                        <div
                            class="flex items-center justify-center h-full w-5 hover:bg-gray-400 rounded-l-lg cursor-pointer">
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
                        <li onclick="selectByFilter('important')"
                            class="text-sm hover:bg-gray-100 px-8 py-1 cursor-pointer">
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
                    <button onclick="bulkAction('untrash')" type="button" aria-label="Untrash" title="Untrash"
                        class="size-9 rounded-xl hover:bg-gray-100 transition-all duration-150 flex items-center justify-center cursor-pointer">
                        <x-heroicon-o-arrow-uturn-left class="size-4" aria-hidden="true" />
                    </button>
                    <button onclick="bulkAction('delete_forever')" type="button" aria-label="Delete forever"
                        title="Delete forever"
                        class="size-9 rounded-xl hover:bg-red-50 hover:text-red-500 w-fit transition-all duration-150 flex items-center justify-center cursor-pointer">
                        <x-heroicon-o-trash class="size-4" aria-hidden="true" />
                        <span class="text-xs ml-1">Forever</span>
                    </button>
                    <button onclick="bulkAction(getMarkAction())" type="button" id="mark-btn" aria-label="Mark as unread"
                        title="Mark unread"
                        class="relative size-9 rounded-xl hover:bg-blue-50 hover:text-blue-500 transition-all duration-150 flex items-center justify-center cursor-pointer">
                        <div id="mark-dot" class="absolute top-2.5 right-2 size-1.5 bg-blue-500 rounded-full"></div>
                        <x-heroicon-o-envelope id="mark-icon-unread" class="size-4" aria-hidden="true" />
                        <x-heroicon-o-envelope-open id="mark-icon-read" class="size-4 hidden" aria-hidden="true" />
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

        {{-- Email List --}}
        <ul id="inbox-list" role="list">
            @forelse ($inbox as $message)
                <x-mail-list :message="$message" :section="$section" />
            @empty
                <div class="flex flex-col items-center justify-center py-24 px-8 text-center">
                    <div class="relative mb-6">
                        <div class="size-24 rounded-full bg-gray-100 flex items-center justify-center">
                            <x-heroicon-o-archive-box class="size-12 text-gray-300" />
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 size-8 rounded-full bg-amber-50 flex items-center justify-center">
                            <x-heroicon-o-inbox class="size-4 text-amber-400" />
                        </div>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700 mb-1">No archived messages</h3>
                    <p class="text-sm text-gray-400 max-w-xs">Messages you archive will appear here. Archive a message to keep
                        your inbox clean.</p>
                </div>
            @endforelse
        </ul>

    </section>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const selectAll = document.getElementById('select-all');

        function rowCheckboxes() {
            return document.querySelectorAll('.row-checkbox');
        }

        function updateSelectAllState() {
            const all = rowCheckboxes();
            const checked = document.querySelectorAll('.row-checkbox:checked');

            if (checked.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (checked.length === all.length) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            }

            updateToolbar();
        }

        selectAll.addEventListener('change', function () {
            rowCheckboxes().forEach(cb => cb.checked = this.checked);
            updateSelectAllState();
        });

        document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('row-checkbox')) return;
            updateSelectAllState();
        });

        window.selectByFilter = function (filter) {
            rowCheckboxes().forEach(cb => {
                switch (filter) {
                    case 'all': cb.checked = true; break;
                    case 'read': cb.checked = cb.dataset.read === 'true'; break;
                    case 'unread': cb.checked = cb.dataset.read === 'false'; break;
                    case 'important': cb.checked = cb.dataset.important === 'true'; break;
                }
            });
            updateSelectAllState();
            document.getElementById('select-dropdown').classList.add('hidden');
        }

        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('select-dropdown');
            const trigger = document.querySelector('[onclick="toggleSelectDropdown()"]');
            if (!dropdown.contains(e.target) && !trigger.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        window.toggleSelectDropdown = function () {
            document.getElementById('select-dropdown').classList.toggle('hidden');
        }

        function updateMarkButton() {
            const checked = [...document.querySelectorAll('.row-checkbox:checked')];
            if (checked.length === 0) return;

            const allRead = checked.every(cb => cb.dataset.read === 'true');

            const dot = document.getElementById('mark-dot');
            const iconUnread = document.getElementById('mark-icon-unread');
            const iconRead = document.getElementById('mark-icon-read');
            const btn = document.getElementById('mark-btn');

            if (allRead) {
                // all selected are read → action is mark_unread
                dot.classList.remove('hidden');
                iconUnread.classList.remove('hidden');
                iconRead.classList.add('hidden');
                btn.title = 'Mark unread';
                btn.setAttribute('aria-label', 'Mark as unread');
            } else {
                // at least one unread → action is mark_read
                dot.classList.add('hidden');
                iconUnread.classList.add('hidden');
                iconRead.classList.remove('hidden');
                btn.title = 'Mark read';
                btn.setAttribute('aria-label', 'Mark as read');
            }
        }

        window.getMarkAction = function () {
            const checked = [...document.querySelectorAll('.row-checkbox:checked')];
            const allRead = checked.every(cb => cb.dataset.read === 'true');
            return allRead ? 'mark_unread' : 'mark_read';
        }

        const toolbarOptions = document.getElementById('toolbar-options');
        const selectionActions = document.getElementById('selection-actions');

        function updateToolbar() {
            const checked = document.querySelectorAll('.row-checkbox:checked');

            if (checked.length > 0) {
                toolbarOptions.classList.add('hidden');
                selectionActions.classList.remove('hidden');
                selectionActions.classList.add('flex');
                updateMarkButton();
            } else {
                toolbarOptions.classList.remove('hidden');
                selectionActions.classList.add('hidden');
                selectionActions.classList.remove('flex');
            }
        }

        function getSelectedIds() {
            return [...document.querySelectorAll('.row-checkbox:checked')]
                .map(cb => cb.value);
        }

        window.bulkAction = function (action) {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("mail.bulk-update") }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);

            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'message_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    });
</script>