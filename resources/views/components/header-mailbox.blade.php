@php
    $user = Auth::user();
@endphp

<header class="h-16 bg-primary flex items-center px-4 max-lg:gap-4">
    <div class="flex-1 flex items-center gap-4">
        <button id="sidemenu-btn"
            class="text-text-white lg:hidden hover:bg-third transition-colors ease-in size-8 cursor-pointer flex items-center justify-center">
            <x-heroicon-s-bars-3 class="size-6" />
        </button>
        <form action="{{ route('mail.search') }}" method="get" class="flex-1">
            <input type="text" name="q" placeholder="Search mail" value="{{ request('q') }}"
                class="w-full max-w-96 h-10 bg-text-white rounded-xl px-4">
        </form>
    </div>
    <div class="flex gap-2 relative">
        <button id="chatbot-btn"
            class="group relative bg-third border border-surface rounded-full hover:bg-gray-100 transition-all ease-in cursor-pointer">
            <img src="{{ asset('chatbot.png') }}" alt="ChatBot AI" class="size-10" />
            <div
                class="transition-all ease-in group-hover:delay-500 group-hover:opacity-100 absolute -bottom-6 left-1/2 -translate-x-1/2 bg-gray-400 rounded-lg text-sm px-2 whitespace-nowrap opacity-0 pointer-events-none">
                Chatbot
            </div>
        </button>
        @if ($user->avatar)
            <button id="avatar-btn"
                class="border border-surface bg-gray-100 size-10 rounded-full flex items-center justify-center cursor-pointer">
                <img src="{{ asset('storage/' . $user->avatar) }}" class="object-cover rounded-full" />
            </button>
        @else
            <button id="avatar-btn"
                class="border border-surface bg-gray-100 size-10 rounded-full flex items-center justify-center cursor-pointer">
                <x-heroicon-o-user class="size-8" />
            </button>
        @endif
        <x-menu-profile />
    </div>
    <x-chatbot />
</header>

<script>
    const sideMenuBtn = document.getElementById('sidemenu-btn');
    const profileMenu = document.getElementById('profile-menu');
    const avatarBtn = document.getElementById('avatar-btn');

    sideMenuBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (navSideMenu.classList.contains('max-lg:hidden')) {
            navSideMenu.classList.remove('max-lg:hidden');
            navSideMenu.classList.add('max-lg:absolute');
        }
    });

    avatarBtn.addEventListener('click', function () {
        toggleProfileMenu();
    });

    function toggleProfileMenu() {
        profileMenu.classList.toggle('hidden');
    }

    document.getElementById('chatbot-btn').addEventListener('click', function (e) {
        e.stopPropagation();
        toggleChatbot();
        document.getElementById('compose-window').classList.add('hidden');
        document.getElementById('nav-sidemenu').classList.add('max-lg:hidden');
    });
</script>