@props(['draft'])

<div id="compose-window"
    class="compose-window hidden fixed bottom-0 right-0 z-40 w-full lg:w-120 rounded-t-2xl shadow-2xl overflow-hidden"
    style="background: #1e1b4b; border: 1px solid rgba(255,255,255,0.1); border-bottom: none;">

    {{-- Header --}}
    <div id="compose-header" class="flex items-center justify-between px-5 h-12 cursor-pointer select-none"
        style="background: #2d2a6e;">
        <div class="flex items-center gap-2">
            <div class="size-2 rounded-full bg-violet-400 animate-pulse"></div>
            <span id="compose-title" class="text-white text-sm font-semibold tracking-wide">New Message</span>
        </div>
        <div class="flex items-center gap-1">
            <button id="minimize-btn" title="Minimize"
                class="p-1.5 rounded-lg text-violet-300 hover:text-white hover:bg-white/10 transition-all duration-150">
                <x-heroicon-s-minus class="size-3.5" />
            </button>
            <button id="expand-btn" title="Expand"
                class="hidden p-1.5 rounded-lg text-violet-300 hover:text-white hover:bg-white/10 transition-all duration-150">
                <x-heroicon-s-chevron-up class="size-3.5" />
            </button>
            <button id="close-btn" title="Close"
                class="p-1.5 rounded-lg text-violet-300 hover:text-red-400 hover:bg-red-500/10 transition-all duration-150">
                <x-heroicon-s-x-mark class="size-3.5" />
            </button>
        </div>
    </div>

    {{-- compose body --}}
    <div id="compose-body" class="flex flex-col" style="height: calc(70vh - 48px);">
        <form id="compose-form" action="{{ route('mail.sent') }}" method="POST" class="flex flex-col h-full">
            @csrf

            <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
            {{-- Recipient --}}
            <div class="flex flex-col border-b" style="border-color: rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 px-5 py-2.5">
                    <span
                        class="text-xs font-semibold text-violet-400 uppercase tracking-widest w-12 shrink-0">To</span>
                    <input type="text" name="inp_to" id="inp_to"
                        class="flex-1 bg-transparent text-sm outline-none placeholder:text-white/30 h-8 {{ $errors->has('inp_to') ? 'text-red-400' : 'text-white' }}"
                        placeholder="recipient@email.com" value="{{ old('inp_to') }}" />
                </div>
                @error('inp_to')
                    <span id="inp_to-error"
                        class="px-5 pb-2 text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            {{-- Subject --}}
            <div class="flex items-center gap-3 px-5 py-2.5 border-b" style="border-color: rgba(255,255,255,0.08);">
                <input type="text" name="inp_subject"
                    class="flex-1 bg-transparent text-white text-sm outline-none placeholder:text-white/30 h-8"
                    placeholder="Subject" />
            </div>

            <div id="message-editor" contenteditable="true"
                class="flex-1 px-5 py-3 w-full h-full text-white/90 text-sm outline-none leading-relaxed overflow-y-auto"
                data-placeholder="Write your message here..."></div>

            <input type="hidden" name="inp_message" id="inp_message_hidden" />
            <input type="hidden" name="draft_id" id="draft_id_hidden" />
            {{-- Footer --}}
            <div class="flex items-center justify-between px-5 py-3 border-t"
                style="border-color: rgba(255,255,255,0.08);">
                <div class="bg-gray-100 flex items-center gap-1 rounded-xl px-2">
                    <div class="relative">
                        <button id="size-list-btn" type="button" onclick="toggleSizeList()"
                            class="flex items-center justify-center cursor-pointer p-1.5">
                            <span class="text-xs font-bold">a</span>
                            <span class="text-base font-bold leading-none">A</span>
                            <x-heroicon-o-chevron-down class="size-3 text-black stroke-3" />
                        </button>
                        <ul class="hidden absolute bg-white bottom-full w-24 select-none py-1 rounded" id="size-list">
                            <li onclick="selectSize('text-sm')"
                                class="text-xs  hover:bg-gray-200 px-2 py-1 cursor-pointer">Small</li>
                            <li onclick="selectSize('text-base')"
                                class="text-sm  hover:bg-gray-200 px-2 py-1 cursor-pointer">Normal</li>
                            <li onclick="selectSize('text-lg')"
                                class="text-xl  hover:bg-gray-200 px-2 py-1 cursor-pointer">Large</li>
                            <li onclick="selectSize('text-xl')"
                                class="text-2xl hover:bg-gray-200 px-2 py-1 cursor-pointer">Huge</li>
                        </ul>
                    </div>
                    <button type="button" id="btn-bold" title="Bold" onclick="toggleBold()"
                        class="cursor-pointer p-1.5 rounded-lg transition-all duration-150">
                        <x-heroicon-o-bold class="size-4 stroke-3" />
                    </button>
                    <button type="button" id="btn-italic" title="Italic" onclick="toggleItalic()"
                        class="cursor-pointer p-1.5 rounded-lg transition-all duration-150">
                        <x-heroicon-o-italic class="size-4 stroke-3" />
                    </button>
                    <button type="button" id="btn-underline" title="Underline" onclick="toggleUnderline()"
                        class="cursor-pointer p-1.5 rounded-lg transition-all duration-150">
                        <x-heroicon-o-underline class="size-4 stroke-3" />
                    </button>
                </div>
                <button type="submit"
                    class="bg-blue-700 cursor-pointer flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white transition-all duration-200 hover:scale-105 active:scale-95">
                    <x-heroicon-s-paper-airplane class="size-4 -rotate-45" />
                    Send
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const composeWindow = document.getElementById('compose-window');
    const composeBody = document.getElementById('compose-body');
    const composeBtn = document.getElementById('compose-btn');
    const closeBtn = document.getElementById('close-btn');
    const minimizeBtn = document.getElementById('minimize-btn');
    const expandBtn = document.getElementById('expand-btn');

    let isMinimized = false;

    composeBtn.addEventListener('click', () => {
        composeWindow.classList.remove('hidden');
        document.getElementById('chatbot').classList.add('hidden');
        document.getElementById('nav-sidemenu').classList.add('max-lg:hidden');
        if (isMinimized) expand();


        document.querySelector('input[name="inp_to"]').value = '';
        document.querySelector('input[name="inp_subject"]').value = '';
        document.getElementById('message-editor').innerHTML = '';
        document.getElementById('compose-title').textContent = 'New Message';
        delete document.getElementById('compose-form').dataset.draftId;
    });

    minimizeBtn.addEventListener('click', () => {
        isMinimized ? expand() : minimize();
    });

    expandBtn.addEventListener('click', () => {
        isMinimized ? expand() : minimize();
    });

    function minimize() {
        isMinimized = true;
        composeBody.style.height = '0';
        composeBody.style.overflow = 'hidden';
        minimizeBtn.classList.add('hidden');
        expandBtn.classList.remove('hidden');
    }

    function expand() {
        isMinimized = false;
        composeBody.style.height = 'calc(70vh - 48px)';
        composeBody.style.overflow = '';
        minimizeBtn.classList.remove('hidden');
        expandBtn.classList.add('hidden');
    }

    function toggleSizeList() {
        const sizeList = document.getElementById('size-list');
        sizeList.classList.toggle('hidden');
    }

    document.addEventListener('click', function (e) {
        const sizeList = document.getElementById('size-list');
        const sizeListBtn = document.getElementById('size-list-btn');

        const isOutside = !sizeList.contains(e.target) && !sizeListBtn.contains(e.target);
        const isVisible = sizeList.classList.contains('hidden') === false;

        if (isOutside && isVisible) {
            sizeList.classList.add('hidden');
        }
    });

    window.selectSize = function (cls) {
        const sizeMap = {
            'text-sm': '1',
            'text-base': '2',
            'text-lg': '4',
            'text-xl': '6',
        };
        document.getElementById('message-editor').focus();
        document.execCommand('fontSize', false, sizeMap[cls]);
        document.getElementById('size-list').classList.add('hidden');
    }

    function updateToolbarState() {
        const btnBold = document.getElementById('btn-bold');
        const btnItalic = document.getElementById('btn-italic');
        const btnUnderline = document.getElementById('btn-underline');

        if (!btnBold) return;

        btnBold.classList.toggle('bg-gray-300', document.queryCommandState('bold'));
        btnItalic.classList.toggle('bg-gray-300', document.queryCommandState('italic'));
        btnUnderline.classList.toggle('bg-gray-300', document.queryCommandState('underline'));
    }

    window.toggleBold = () => { document.execCommand('bold'); updateToolbarState(); }
    window.toggleItalic = () => { document.execCommand('italic'); updateToolbarState(); }
    window.toggleUnderline = () => { document.execCommand('underline'); updateToolbarState(); }

    document.getElementById('message-editor').addEventListener('keyup', updateToolbarState);
    document.getElementById('message-editor').addEventListener('mouseup', updateToolbarState);

    function hasDraftContent() {
        const to = document.querySelector('input[name="inp_to"]').value.trim();
        const subject = document.querySelector('input[name="inp_subject"]').value.trim();
        const message = document.getElementById('message-editor').innerHTML.trim();
        const textOnly = document.getElementById('message-editor').innerText.trim();

        return to !== '' || subject !== '' || textOnly !== '';
    }

    let isSavingDraft = false;

    function saveDraft() {
        const form = document.getElementById('compose-form');

        isSavingDraft = true; // ← set flag before submit
        document.getElementById('inp_message_hidden').value = document.getElementById('message-editor').innerHTML;
        document.getElementById('draft_id_hidden').value = form.dataset.draftId || '';

        form.action = '{{ route("mail.draft") }}';
        form.submit();
    }

    // Subject syncs to header title
    document.querySelector('input[name="inp_subject"]').addEventListener('input', function () {
        const title = document.getElementById('compose-title');
        title.textContent = this.value.trim() || 'New Message';
    });

    closeBtn.addEventListener('click', () => {
        if (hasDraftContent()) {
            saveDraft();
        } else {
            composeWindow.classList.add('hidden');
            expand();
        }
    });

    document.querySelector('#compose-form').addEventListener('submit', function (e) {
        if (isSavingDraft) return;

        const content = document.getElementById('message-editor').innerHTML.trim();
        const textOnly = document.getElementById('message-editor').innerText.trim();

        document.getElementById('inp_message_hidden').value = content;
        document.getElementById('draft_id_hidden').value = this.dataset.draftId || '';
    });

    window.openCompose = function (data = {}) {
        document.querySelector('input[name="inp_to"]').value = data.to || '';
        document.querySelector('input[name="inp_subject"]').value = data.subject || '';
        document.getElementById('message-editor').innerHTML = data.message || '';
        document.getElementById('compose-title').textContent = data.subject || 'New Message';

        // Store draft ID to update the existing draft
        if (data.draft_id) {
            document.getElementById('compose-form').dataset.draftId = data.draft_id;
        }

        composeWindow.classList.remove('hidden');
        if (isMinimized) expand();
    }

    document.getElementById('inp_to').addEventListener('input', function () {
        if (this.value.trim() !== '') {
            this.classList.remove('text-red-400');
            this.classList.add('text-white');

            const error = document.getElementById('inp_to-error');
            if (error) error.style.display = 'none';
        }
    });
</script>
@if ($errors->has('inp_to'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('compose-window').classList.remove('hidden');
        });
    </script>
@endif