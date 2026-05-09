<nav id="nav-sidemenu" class="z-50 space-y-4 pr-4 w-64 bg-primary max-lg:hidden top-0 left-0 h-full text-text-white">
    <a href="{{ route('view.primary') }}"
        class="h-16 flex items-center text-text-white gap-2 px-4 max-lg:border-b-2 border-t-text-white">
        <img src="{{ asset('clarky.png') }}" class="size-16" />
        <h1 class="font-bungee">Clarky Hub</h1>
        <button id="close-sidemenu-btn"
            class="lg:hidden size-8 bg-third flex justify-center items-center cursor-pointer">
            <x-heroicon-s-arrow-left-start-on-rectangle class="size-6" />
        </button>
    </a>
    <div class="w-full px-4">
        <button id="compose-btn" class="cursor-pointer w-full flex items-center gap-3 h-11 px-5 rounded-full
           bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/30
           text-white text-sm font-medium tracking-wide
           transition-all duration-200 hover:shadow-lg active:scale-95">
            <x-heroicon-o-pencil-square class="size-4 shrink-0" />
            Compose
        </button>
    </div>
    <ul class="flex flex-col">
        <a href="{{ route('view.primary') }}"
            class="{{ request()->routeIs('view.primary') || (request()->routeIs('view.mail') && request()->route('section') === 'primary') ? 'bg-secondary' : 'hover:bg-secondary' }} rounded-r-full flex items-center px-4 gap-3 h-12 transition-colors ease-in cursor-pointer">
            <x-heroicon-s-inbox class="size-4 shrink-0" />
            <span class="flex-1 text-sm">Inbox</span>
            @if($receiveInbox->count() > 0)
                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">{{ $receiveInbox->count() }}</span>
            @endif
        </a>

        <a href="{{ route('view.sent') }}"
            class="{{ request()->routeIs('view.sent') || (request()->routeIs('view.mail') && request()->route('section') === 'sent') ? 'bg-secondary' : 'hover:bg-secondary' }} rounded-r-full flex items-center px-4 gap-3 h-12 transition-colors ease-in cursor-pointer">
            <x-heroicon-s-paper-airplane class="size-4 shrink-0" />
            <span class="flex-1 text-sm">Sent</span>
            @if($sentInbox->count() > 0)
                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">{{ $sentInbox->count() }}</span>
            @endif
        </a>

        <a href="{{ route('view.drafts') }}"
            class="{{ request()->routeIs('view.drafts') ||  (request()->routeIs('view.mail') && request()->route('section') === 'drafts') ? 'bg-secondary' : 'hover:bg-secondary' }} rounded-r-full flex items-center px-4 gap-3 h-12 transition-colors ease-in cursor-pointer">
            <x-heroicon-s-document class="size-4 shrink-0" />
            <span class="flex-1 text-sm">Drafts</span>
            @if($drafts->count() > 0)
                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">{{ $drafts->count() }}</span>
            @endif
        </a>

        <a href="{{ route('view.archived') }}"
            class="{{ request()->routeIs('view.archived') || (request()->routeIs('view.mail') && request()->route('section') === 'archived') ? 'bg-secondary' : 'hover:bg-secondary' }} rounded-r-full flex items-center px-4 gap-3 h-12 transition-colors ease-in cursor-pointer">
            <x-heroicon-s-archive-box class="size-4 shrink-0" />
            <span class="flex-1 text-sm">Archived</span>
            @if($archived->count() > 0)
                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">{{ $archived->count() }}</span>
            @endif
        </a>
    </ul>
    <a href="{{ route('view.trash') }}"
        class="{{ request()->routeIs('view.trash') || (request()->routeIs('view.mail') && request()->route('section') === 'trash') ? 'bg-secondary' : 'hover:bg-secondary' }} rounded-r-full flex items-center px-4 gap-3 h-12 transition-colors ease-in cursor-pointer">
        <x-heroicon-s-trash class="size-4 shrink-0" />
        <span class="flex-1 text-sm">
            Trash
        </span>
        @if($trashed->count() > 0)
            <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">{{ $trashed->count() }}</span>
        @endif
    </a>
</nav>

<script>
    const navSideMenu = document.getElementById('nav-sidemenu');
    const closeNav = document.getElementById('close-sidemenu-btn');

    closeNav.addEventListener('click', function (e) {
        e.stopPropagation();

        navSideMenu.classList.remove('max-lg:absolute');
        navSideMenu.classList.add('max-lg:hidden');
    });

    document.addEventListener('click', function (e) {
        const isOutside = !navSideMenu.contains(e.target);
        const isVisible = navSideMenu.classList.contains('max-lg:absolute');

        if (isOutside && isVisible) {
            navSideMenu.classList.remove('max-lg:absolute');
            navSideMenu.classList.add('max-lg:hidden');
        }
    });
</script>