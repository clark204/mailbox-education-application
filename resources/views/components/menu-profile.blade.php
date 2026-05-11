@php
    $user = Auth::user();
@endphp

<nav id="profile-menu" class="select-none hidden absolute top-[calc(100%+5px)] right-0 z-50 w-72 p-1 rounded-2xl border border-slate-200
        bg-white/90 backdrop-blur-md shadow-xl shadow-slate-200/50" role="menu" aria-label="Profile menu" aria-hidden="true">

    {{-- Header --}}
    <header class="relative p-4 flex flex-col items-center border-b border-slate-100">

        {{-- Close Button --}}
        <button onclick="toggleProfileMenu()" class="cursor-pointer absolute right-2 top-2 p-1 rounded-full text-slate-400
                hover:bg-slate-100 hover:text-slate-600 transition-colors
                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            aria-label="Close profile menu" role="menuitem">
            <x-heroicon-o-x-mark class="size-5" aria-hidden="true" />
        </button>

        {{-- Profile Image --}}
        <div class="relative group" aria-hidden="true">
            <div
                class="absolute -inset-0.5 bg-linear-to-r from-blue-500 to-indigo-500 rounded-full opacity-20 group-hover:opacity-40 transition">
            </div>
            @if ($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}"
                    alt="Profile picture of {{ $user->first_name }} {{ $user->last_name }}"
                    class="relative w-20 h-20 rounded-full object-cover border-2 border-white shadow-sm" />
            @else
                <x-heroicon-s-user
                    class="relative w-20 h-20 rounded-full object-cover border-2 border-white shadow-sm text-slate-600"
                    aria-hidden="true" />
            @endif
        </div>

        {{-- User Info --}}
        <h2 class="mt-3 font-semibold text-slate-800 text-lg text-center">
            {{ $user->first_name }} {{ $user->last_name }}
        </h2>
        <div class="flex items-center gap-1.5 text-slate-500 mt-0.5">
            <x-heroicon-o-envelope class="size-3.5" aria-hidden="true" />
            <span class="text-xs font-medium">{{ $user->email }}</span>
        </div>

    </header>

    {{-- Menu Actions --}}
    <div class="p-2 space-y-1" role="none">

        {{-- Manage Account --}}
        <a href="{{ route('view.profile') }}" role="menuitem" tabindex="-1" class="text-slate-700 w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                hover:bg-slate-100 hover:text-blue-600 transition-all
                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            <x-heroicon-o-cog-6-tooth class="size-5" aria-hidden="true" />
            Manage Account
        </a>

        <div class="h-px bg-slate-100 my-1 mx-2" role="separator" aria-hidden="true"></div>

        {{-- Sign Out --}}
        <form method="POST" action="{{ route('auth.logout') }}" role="none">
            @csrf
            <button type="submit" role="menuitem" tabindex="-1" class="cursor-pointer w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    text-rose-600 hover:bg-rose-50 transition-all
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500"
                aria-label="Sign out of your account">
                <x-heroicon-o-arrow-right-on-rectangle class="size-5" aria-hidden="true" />
                Sign out
            </button>
        </form>

    </div>

</nav>