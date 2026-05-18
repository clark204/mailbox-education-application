@extends('pages.mailbox.manage-account')

@section('manage-account.content')
    <form class="flex-1 flex flex-col py-4 px-8 gap-4 bg-white" action="{{ route('user.change-password') }}" method="POST">
        @csrf
        @method('PUT')
        @if ($user->has_password)
            <div class="flex flex-col group">
                <label id="current-label" for="current_password"
                    class="{{ $errors->has('current_password') ? 'text-red-500' : 'group-focus-within:text-blue-500 text-gray-800' }}">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <input
                    class="{{ $errors->has('current_password') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg"
                    onkeypress="return /[a-zA-Z\s\'\-]/.test(event.key)" type="password" name="current_password"
                    id="current_password" placeholder="Enter your current password">
                @error('current_password')
                    <p id="current-error" class="text-red-500 text-xs pl-4">{{ $message }}</p>
                @enderror
            </div>

            <hr class="text-gray-300">
        @else
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                <x-heroicon-o-information-circle class="size-5 mt-0.5 shrink-0 text-blue-500" />
                <p class="text-sm text-blue-700">
                    Since you signed in with Google, you can set a password to also enable email login.
                </p>
            </div>
        @endif


        <div class="flex-1 flex gap-4">
            <div class="flex-1 flex flex-col group">
                <label id="new-label" for="new_password"
                    class="{{ $errors->has('new_password') ? 'text-red-500' : 'group-focus-within:text-blue-500 text-gray-800' }}">
                    New Password <span class="text-red-500">*</span>
                </label>
                <input
                    class="{{ $errors->has('new_password') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg"
                    type="password" name="new_password" id="new_password"
                    onkeypress="return /[a-zA-Z\s\'\-]/.test(event.key)" placeholder="Enter new password">
                @error('new_password')
                    <p id="new-error" class="text-red-500 text-xs pl-4">{{ $message }}</p> 
                @enderror
            </div>
            <div class="flex-1 flex flex-col group">
                <label id="confirm-label" for="new_password_confirmation"
                    class="{{ $errors->has('new_password') ? 'text-red-500' : 'group-focus-within:text-blue-500 text-gray-800' }}">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input
                    class="{{ $errors->has('new_password') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg"
                    type="password" name="new_password_confirmation" id="new_password_confirmation"
                    --}} placeholder="Re-enter new password">
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit"
                class="flex items-center gap-1.5 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition-all ease-in cursor-pointer">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                        <path
                            d="M840-680v480q0 33-23.5 56.5T760-120H200q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h480l160 160Zm-80 34L646-760H200v560h560v-446ZM565-275q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35ZM240-560h360v-160H240v160Zm-40-86v446-560 114Z" />
                    </svg>
                </span>
                Change password
            </button>
        </div>
    </form>
    <script>
        const currentError = document.getElementById('current-error');
        const currentInput = document.getElementById('current_password');
        const currentLabel = document.getElementById('current-label');

        const newError = document.getElementById('new-error');
        const newInput = document.getElementById('new_password');
        const newLabel = document.getElementById('new-label');

        const confirmInput = document.getElementById('new_password_confirmation');
        const confirmLabel = document.getElementById('confirm-label');

        currentInput.addEventListener('input', function () {
            currentError.classList.add('hidden');
            currentInput.classList.remove('ring');
            currentInput.classList.add('focus:ring')
            currentInput.classList.remove('ring-red-500');
            currentInput.classList.add('ring-blue-500');
            currentLabel.classList.remove('text-red-500');
            currentLabel.classList.add('group-focus-within:text-blue-500')
            currentLabel.classList.add('text-gray-800')
        });

        newInput.addEventListener('input', function () {
            newError.classList.add('hidden');
            newInput.classList.remove('ring');
            newInput.classList.add('focus:ring')
            newInput.classList.remove('ring-red-500');
            newInput.classList.add('ring-blue-500');
            newLabel.classList.remove('text-red-500');
            newLabel.classList.add('group-focus-within:text-blue-500')
            newLabel.classList.add('text-gray-800')
        });

        confirmInput.addEventListener('input', function () {
            confirmInput.classList.remove('ring');
            confirmInput.classList.add('focus:ring')
            confirmInput.classList.remove('ring-red-500');
            confirmInput.classList.add('ring-blue-500');
            confirmLabel.classList.remove('text-red-500');
            confirmLabel.classList.add('group-focus-within:text-blue-500')
            confirmLabel.classList.add('text-gray-800')
        });
    </script>
@endsection