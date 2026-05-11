@extends('layouts.verification')

@section('verification')
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl border border-gray-100">

        <div class="flex flex-col items-center mb-6">
            <div class="bg-blue-50 p-3 rounded-full mb-3">
                <x-heroicon-o-lock-closed class="size-7 text-blue-500" />
            </div>
            <h1 class="text-2xl font-semibold text-gray-800">Change Password</h1>
            <p class="text-sm text-gray-500 text-center mt-1">
                Enter your new password below.
            </p>
        </div>

        <form method="POST" action="{{ route('forgot.password') }}">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    <label id="new-label" for="new_password"
                        class="{{ $errors->has('new_password') ? 'text-red-500' : 'text-gray-800' }} text-sm font-medium">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        class="{{ $errors->has('new_password') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg text-sm"
                        type="password" name="new_password" id="new_password"
                        placeholder="Enter new password">
                    @error('new_password')
                        <p id="new-error" class="text-red-500 text-xs pl-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label id="confirm-label" for="new_password_confirmation"
                        class="{{ $errors->has('new_password') ? 'text-red-500' : 'text-gray-800' }} text-sm font-medium">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        class="{{ $errors->has('new_password') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg text-sm"
                        type="password" name="new_password_confirmation" id="new_password_confirmation"
                        placeholder="Re-enter new password">
                </div>
            </div>

            <button type="submit"
                class="w-full mt-6 flex items-center justify-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2.5 rounded-lg transition-all ease-in cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#fff">
                    <path d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z" />
                </svg>
                Change password
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('view.sign-in') }}"
                class="text-sm text-blue-500 hover:underline flex items-center justify-center gap-1">
                <x-heroicon-o-arrow-left class="size-4" />
                Back to sign in
            </a>
        </div>

    </div>

    <script>
        const newError = document.getElementById('new-error');
        const newInput = document.getElementById('new_password');
        const newLabel = document.getElementById('new-label');

        const confirmInput = document.getElementById('new_password_confirmation');
        const confirmLabel = document.getElementById('confirm-label');

        if (newInput) {
            newInput.addEventListener('input', function () {
                if (newError) newError.classList.add('hidden');
                newInput.classList.remove('ring', 'ring-red-500');
                newInput.classList.add('focus:ring', 'ring-blue-500');
                if (newLabel) {
                    newLabel.classList.remove('text-red-500');
                    newLabel.classList.add('text-gray-800');
                }
            });
        }

        if (confirmInput) {
            confirmInput.addEventListener('input', function () {
                confirmInput.classList.remove('ring', 'ring-red-500');
                confirmInput.classList.add('focus:ring', 'ring-blue-500');
                if (confirmLabel) {
                    confirmLabel.classList.remove('text-red-500');
                    confirmLabel.classList.add('text-gray-800');
                }
            });
        }
    </script>
@endsection