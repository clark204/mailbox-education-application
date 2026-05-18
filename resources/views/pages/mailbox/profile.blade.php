@extends('pages.mailbox.manage-account')

@php
@endphp

@section('manage-account.content')
    <x-modal-contact />
    <x-modal-verify-phone />

    <form class="flex-1 flex flex-col py-4 px-8 gap-4 bg-white" action="{{ route('user.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="flex gap-4">
            <div class="flex-1 flex flex-col group">
                <label id="fn-label" for="first_name"
                    class=" {{ $errors->has('first_name') ? 'text-red-500' : 'group-focus-within:text-blue-500 text-gray-800' }}">First
                    Name</label>
                <input
                    class="{{ $errors->has('first_name') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg"
                    type="text" name="first_name" id="first_name" value="{{ $user->first_name }}">
                @error('first_name')
                    <p id="fn-error" class="text-red-500 text-xs pl-4">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex-1 flex flex-col group">
                <label id="ln-label" for="last_name"
                    class=" {{ $errors->has('last_name') ? 'text-red-500' : 'group-focus-within:text-blue-500 text-gray-800' }}">Last
                    Name</label>
                <input
                    class="{{ $errors->has('last_name') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg"
                    type="text" name="last_name" id="last_name" value="{{ $user->last_name }}">
                @error('last_name')
                    <p id="ln-error" class="text-red-500 text-xs pl-4">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="flex flex-col">
            <label for="email" class="text-gray-800">
                Email
                <span class="ml-2 text-green-500 bg-green-50 px-2 rounded-full">verified <x-heroicon-o-check-badge
                        class="size-4 inline-block ml-1" /></span>
            </label>
            <input class="h-10 px-4 outline-none border border-gray-300 rounded-lg bg-gray-200 pointer-events-none"
                type="email" name="email" id="email" value="{{ $user->email }}">
        </div>
        <div class="flex-1">
            <div onclick="document.getElementById('modal-contact').classList.toggle('hidden')"
                class="flex justify-between px-4 py-2 cursor-pointer border border-gray-300 hover:bg-gray-100 transition-colors ease-in rounded-lg">
                <div class="truncate">
                    <h2 class="font-medium">
                        Contact Info
                    </h2>
                    <div class="flex items-center gap-2">
                        @forelse ($user->phones as $phone)
                            <p class="text-xs shrink-0">{{ $phone->phone_number }}</p>

                        @empty
                            <p class="text-xs shrink-0">No contact number</p>
                        @endforelse
                    </div>
                </div>
                <span class="flex items-center justify-center">
                    <x-heroicon-s-chevron-right class="size-5" />
                </span>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="submit" onclick="loader(this)"
                class="flex items-center justify-center min-w-36 gap-1.5 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition-all ease-in cursor-pointer">
                <span id="btn-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                        <path
                            d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z" />
                    </svg>
                </span>
                <span id="btn-text">Save changes</span>
                <span id="btn-loader" class="loader hidden"></span>
            </button>
        </div>
    </form>
    <script>
        const fnError = document.getElementById('fn-error');
        const fnInput = document.getElementById('first_name');
        const fnLabel = document.getElementById('fn-label');

        const lnError = document.getElementById('ln-error');
        const lnInput = document.getElementById('last_name');
        const lnLabel = document.getElementById('ln-label');

        fnInput.addEventListener('input', function () {
            fnError.classList.add('hidden');
            fnInput.classList.remove('ring');
            fnInput.classList.add('focus:ring')
            fnInput.classList.remove('ring-red-500');
            fnInput.classList.add('ring-blue-500');
            fnLabel.classList.remove('text-red-500');
            fnLabel.classList.add('group-focus-within:text-blue-500')
            fnLabel.classList.add('text-gray-800')
        });

        lnInput.addEventListener('input', function () {
            lnError.classList.add('hidden');
            lnInput.classList.remove('ring');
            lnInput.classList.add('focus:ring')
            lnInput.classList.remove('ring-red-500');
            lnInput.classList.add('ring-blue-500');
            lnLabel.classList.remove('text-red-500');
            lnLabel.classList.add('group-focus-within:text-blue-500')
            lnLabel.classList.add('text-gray-800')
        });

        function loader(btn) {
            document.getElementById('btn-icon').classList.add('hidden');
            document.getElementById('btn-text').classList.add('hidden');
            document.getElementById('btn-loader').classList.remove('hidden');
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            setTimeout(() => btn.disabled = true, 100);
        }
    </script>
@endsection