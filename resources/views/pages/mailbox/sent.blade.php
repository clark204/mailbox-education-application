@extends('layouts.mailbox')

@section('mailbox.content')
    <section id="sent-inbox">

        <x-nav-inbox :inbox="$inbox" />

        {{-- Email List --}}
        <ul id="inbox-list" role="list">
            @forelse ($inbox as $message)
                <x-mail-list :message="$message" :section="$section" />
            @empty
                <div class="flex flex-col items-center justify-center py-24 px-8 text-center">
                    <div class="relative mb-6">
                        <div class="size-24 rounded-full bg-gray-100 flex items-center justify-center">
                            <x-heroicon-o-paper-airplane class="size-12 text-gray-300" />
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 size-8 rounded-full bg-blue-50 flex items-center justify-center">
                            <x-heroicon-o-pencil class="size-4 text-blue-400" />
                        </div>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700 mb-1">Nothing sent yet</h3>
                    <p class="text-sm text-gray-400 max-w-xs">Messages you send will appear here. Compose a new message to get
                        started.</p>
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