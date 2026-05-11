@extends('layouts.mailbox')

@php
    $user = Auth::user();
@endphp

@section('mailbox.content')
    <div class="p-6 shadow-md h-full bg-gray-100 rounded-tl-4xl">
        <h2 class="text-2xl font-semibold">Manage Account</h2>
        <p class="text-gray-800">Here you can manage your account settings.</p>
        <div class="bg-white rounded-xl flex max-lg:flex-col mt-6 min-h-96">
            <div class="flex flex-col items-center p-4">
                <form class="relative group" action="{{ route('user.change-avatar') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <label for="avatar" class="cursor-pointer">
                        @if ($user->avatar)
                            <span
                                class="absolute bottom-0 right-0 z-10 bg-white group-hover:text-blue-500 p-1 rounded-full transition-all ease-in">
                                <x-heroicon-o-camera class="size-4" />
                            </span>
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                alt="Profile picture of {{ $user->first_name }} {{ $user->last_name }}"
                                class="relative w-20 h-20 ring-blue-500 ring-offset-1 group-hover:ring rounded-full object-cover border-2 border-white shadow-sm transition-all ease-in" />
                        @else
                            <span
                                class="absolute bottom-0 right-0 z-10 bg-white group-hover:text-blue-500 p-1 rounded-full transition-all ease-in">
                                <x-heroicon-o-camera class="size-4" />
                            </span>
                            <x-heroicon-s-user
                                class="relative w-20 h-20 ring-blue-500 ring-offset-1 group-hover:ring rounded-full object-cover border-2 border-white shadow-sm text-slate-600"
                                aria-hidden="true" />
                        @endif
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden"
                        onchange="this.form.submit()" />
                </form>
                {{-- User Info --}}
                <h2 class="mt-3 font-semibold text-slate-800 text-lg">
                    {{ $user->first_name }} {{ $user->last_name }}
                </h2>
                <div class="flex items-center gap-1.5 text-slate-500 mt-0.5">
                    <x-heroicon-o-envelope class="size-3.5" aria-hidden="true" />
                    <span class="text-xs font-medium">{{ $user->email }}</span>
                </div>
                <div class="flex flex-col gap-2 items-center mt-4 w-full">
                    <a href="{{ route('view.profile') }}"
                        class="{{ Request::routeIs('view.profile') ? 'bg-blue-500 text-white hover:bg-blue-600' : 'border border-gray-200 bg-gray-100 text-gray-800 hover:bg-gray-200' }} py-2 px-4 rounded-md w-full text-center transition-all ease-in cursor-pointer">
                        User Profile
                    </a>
                    <a href="{{ route('view.account-settings') }}"
                        class="{{ Request::routeIs('view.account-settings') ? 'bg-blue-500 text-white hover:bg-blue-600' : 'border border-gray-200 bg-gray-100 text-gray-800 hover:bg-gray-200' }} py-2 px-4 rounded-md w-full text-center transition-all ease-in cursor-pointer">
                        Account Settings
                    </a>
                </div>
            </div>

            @yield('manage-account.content')

        </div>
    </div>
    <script>
        document.getElementById('avatar').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = e => {
                let img = document.querySelector('img[alt^="Profile picture"]');

                if (!img) {
                    // No avatar yet — replace the SVG with an img element
                    const svg = document.querySelector('label svg');
                    img = document.createElement('img');
                    img.alt = 'Profile picture preview';
                    img.className = 'relative w-20 h-20 ring-blue-500 ring-offset-1 group-hover:ring rounded-full object-cover border-2 border-white shadow-sm transition-all ease-in';
                    svg.replaceWith(img);
                }

                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
@endsection