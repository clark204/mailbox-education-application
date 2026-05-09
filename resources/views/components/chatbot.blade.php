<div id="chatbot" style="height: 70vh;"
    class="hidden fixed bottom-0 right-0 w-full lg:w-[480px] flex-col rounded-t-2xl overflow-hidden shadow-[0_-4px_24px_rgba(0,0,0,0.08)] border border-gray-200 bg-white">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 bg-primary rounded-t-2xl">
        <div class="flex items-center gap-3">
            @if (asset('chatbot.png'))
                <img src="{{ asset('chatbot.png') }}" alt="Clarky AI"
                    class="size-9 rounded-full object-cover border border-surface bg-third">
            @else
                <div class="size-9 rounded-full bg-white/15 flex items-center justify-center">
                    <x-heroicon-o-cpu-chip class="size-5 text-white" />
                </div>
            @endif
            <div>
                <p class="text-sm font-medium text-white">CLARKY AI</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <div class="size-1.5 rounded-full bg-green-400"></div>
                    <span class="text-[11px] text-white/60">Online</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <button id="chatbot-clear-btn" title="Clear chat"
                class="size-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center">
                <x-heroicon-o-arrow-path class="size-4 text-white" />
            </button>
            <button id="chatbot-minimize-btn"
                class="size-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center">
                <x-heroicon-o-minus class="size-4 text-white" />
            </button>
            <button id="chatbot-close-btn"
                class="size-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center">
                <x-heroicon-o-x-mark class="size-4 text-white" />
            </button>
        </div>
    </div>

    {{-- Messages --}}
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 flex flex-col gap-3 bg-gray-50">
        {{-- Initial greeting --}}
        <div class="flex justify-start items-end gap-2">
            <img src="{{ asset('chatbot.png') }}" alt="Clarky"
                class="size-7 rounded-full object-cover border border-gray-200 shrink-0 mb-1">
            <div class="max-w-[75%] px-4 py-2.5 rounded-2xl rounded-bl-sm bg-white border border-gray-200 text-gray-800 text-sm leading-relaxed shadow-sm">
                Hi! I'm Clarky AI 👋 How can I help you today?
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div id="inp_message" class="px-3 py-3 border-t border-gray-200 bg-white flex items-center gap-2">
        <input id="chat-input" type="text" placeholder="Ask Clarky AI anything..."
            class="flex-1 h-10 rounded-full border border-gray-200 bg-gray-50 px-4 text-sm outline-none focus:border-primary" />
        <button id="chat-send-btn"
            class="size-10 rounded-full bg-primary flex items-center justify-center shrink-0 transition-opacity disabled:opacity-50">
            <x-heroicon-s-paper-airplane class="size-4 text-white -rotate-45" />
        </button>
    </div>
</div>

<script>
    const chatMessages = document.getElementById('chat-messages');
    const chatInput    = document.getElementById('chat-input');
    const sendBtn      = document.getElementById('chat-send-btn');
    const CHAT_URL     = '{{ route("chatbot.message") }}';
    const CLEAR_URL    = '{{ route("chatbot.clear") }}';
    const CSRF_TOKEN   = '{{ csrf_token() }}';
    const AVATAR_URL   = '{{ asset("chatbot.png") }}';

    // ── UI Helpers ──────────────────────────────────────────────

    function appendMessage(role, text) {
        const isUser = role === 'user';
        const wrapper = document.createElement('div');
        wrapper.className = `flex ${isUser ? 'justify-end' : 'justify-start'} items-end gap-2`;

        if (!isUser) {
            const avatar = document.createElement('img');
            avatar.src = AVATAR_URL;
            avatar.alt = 'Clarky';
            avatar.className = 'size-7 rounded-full object-cover border border-gray-200 shrink-0 mb-1';
            wrapper.appendChild(avatar);
        }

        const bubble = document.createElement('div');
        bubble.className = isUser
            ? 'max-w-[75%] px-4 py-2.5 rounded-2xl rounded-br-sm bg-primary text-white text-sm leading-relaxed'
            : 'max-w-[75%] px-4 py-2.5 rounded-2xl rounded-bl-sm bg-white border border-gray-200 text-gray-800 text-sm leading-relaxed shadow-sm';
        bubble.innerText = text;
        wrapper.appendChild(bubble);

        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function appendTypingIndicator() {
        const wrapper = document.createElement('div');
        wrapper.id = 'typing-indicator';
        wrapper.className = 'flex justify-start items-end gap-2';
        wrapper.innerHTML = `
            <img src="${AVATAR_URL}" alt="Clarky"
                class="size-7 rounded-full object-cover border border-gray-200 shrink-0 mb-1">
            <div class="px-4 py-3 rounded-2xl rounded-bl-sm bg-white border border-gray-200 shadow-sm flex gap-1 items-center">
                <span class="size-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:0ms"></span>
                <span class="size-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:150ms"></span>
                <span class="size-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:300ms"></span>
            </div>`;
        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeTypingIndicator() {
        document.getElementById('typing-indicator')?.remove();
    }

    function setLoading(loading) {
        sendBtn.disabled = loading;
        chatInput.disabled = loading;
    }

    // ── Send Message ────────────────────────────────────────────

    async function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        appendMessage('user', text);
        chatInput.value = '';
        setLoading(true);
        appendTypingIndicator();

        try {
            const res = await fetch(CHAT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({ message: text }),
            });

            const data = await res.json();
            removeTypingIndicator();

            if (data.error) {
                appendMessage('assistant', '⚠️ ' + data.error);
            } else {
                appendMessage('assistant', data.message);
            }
        } catch (err) {
            removeTypingIndicator();
            appendMessage('assistant', '⚠️ Network error. Please try again.');
        } finally {
            setLoading(false);
            chatInput.focus();
        }
    }

    // ── Event Listeners ─────────────────────────────────────────

    sendBtn.addEventListener('click', sendMessage);

    chatInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    document.getElementById('chatbot-close-btn').addEventListener('click', function () {
        const chatbot = document.getElementById('chatbot');
        chatbot.classList.add('hidden');
        chatbot.classList.remove('flex');
    });

    document.getElementById('chatbot-minimize-btn').addEventListener('click', function () {
        const chatbot = document.getElementById('chatbot');
        const messages = document.getElementById('chat-messages');
        const input = document.getElementById('inp_message');

        messages.classList.toggle('hidden');
        input.classList.toggle('hidden');
        chatbot.style.height = chatbot.style.height === 'auto' ? '70vh' : 'auto';
    });

    document.getElementById('chatbot-clear-btn').addEventListener('click', async function () {
        await fetch(CLEAR_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        });

        // Reset UI — keep only the greeting
        chatMessages.innerHTML = `
            <div class="flex justify-start items-end gap-2">
                <img src="${AVATAR_URL}" alt="Clarky"
                    class="size-7 rounded-full object-cover border border-gray-200 shrink-0 mb-1">
                <div class="max-w-[75%] px-4 py-2.5 rounded-2xl rounded-bl-sm bg-white border border-gray-200 text-gray-800 text-sm leading-relaxed shadow-sm">
                    Hi! I'm Clarky AI 👋 How can I help you today?
                </div>
            </div>`;
    });

    function toggleChatbot() {
        const chatbot = document.getElementById('chatbot');
        chatbot.classList.toggle('hidden');
        chatbot.classList.toggle('flex');
    }
</script>