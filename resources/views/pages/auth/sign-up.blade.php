@extends('layouts.auth')

@section('auth.content')
    <div class="flex h-screen flex-col lg:flex-row">

        {{-- Left Side --}}
        <aside class="h-[35%] sm:h-[30%] lg:h-full w-full lg:w-[50%]">
            <x-header-auth />
        </aside>

        {{-- Right Side / Form --}}
        <main class="content-center h-[65%] sm:h-[70%] lg:h-full w-full lg:w-[50%] overflow-y-auto">
            <section aria-labelledby="register-heading" class="px-10 md:px-14 xl:px-16 2xl:px-18 py-10">
                <div class="space-y-6">

                    {{-- Heading --}}
                    <header>
                        <h2 id="register-heading" class="text-2xl lg:text-3xl text-black/90">Welcome!</h2>
                        <p class="text-lg">Please register your account.</p>
                    </header>

                    {{-- Form --}}
                    <form action="{{ route('auth.sign-up') }}" method="POST" id="register-form" class="flex flex-col space-y-2">
                        @csrf

                        {{-- First Name --}}
                        <div class="flex flex-col gap-1">
                            <label for="first_name">First Name</label>
                            <input
                                class="w-full border h-10 outline-none px-4 rounded ring-offset-1 focus:ring-1 focus:ring-cyan-500 @error('first_name') border-rose-500 @else border-gray-300 @enderror"
                                type="text" id="first_name" name="first_name" autocomplete="given-name"
                                value="{{ old('first_name') }}" required aria-required="true" />
                            @error('first_name')
                                <p id="first_name-error" class="text-red-500 text-xs" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="flex flex-col gap-1">
                            <label for="last_name">Last Name</label>
                            <input
                                class="w-full border h-10 outline-none px-4 rounded ring-offset-1 focus:ring-1 focus:ring-cyan-500 @error('last_name') border-rose-500 @else border-gray-300 @enderror"
                                type="text" id="last_name" name="last_name" autocomplete="family-name"
                                value="{{ old('last_name') }}" required aria-required="true" />
                            @error('last_name')
                                <p id="last_name-error" class="text-red-500 text-xs" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="flex flex-col gap-1">
                            <label for="email">Email</label>
                            <input
                                class="w-full border h-10 outline-none px-4 rounded ring-offset-1 focus:ring-1 focus:ring-cyan-500 @error('email') border-rose-500 @else border-gray-300 @enderror"
                                type="email" id="email" name="email" autocomplete="email"
                                value="{{ old('email') }}" required aria-required="true" />
                            @error('email')
                                <p id="email-error" class="text-red-500 text-xs" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="flex flex-col gap-1">
                            <label for="password">Password</label>
                            <div class="relative">
                                <input
                                    class="w-full border h-10 outline-none pl-4 pr-12 rounded ring-offset-1 focus:ring-1 focus:ring-cyan-500 @error('password') border-rose-500 @else border-gray-300 @enderror"
                                    type="password" id="password" name="password" autocomplete="new-password" required
                                    aria-required="true" oninput="clearError(this, 'password-error')" />
                                <button type="button" onclick="togglePassword('password', 'eye-1', 'eye-slash-1')"
                                    tabindex="-1"
                                    class="absolute top-px right-px bg-neutral-200 h-9.5 flex items-center justify-center px-2 rounded-r border-l border-neutral-300 cursor-pointer">
                                    <x-heroicon-o-eye id="eye-1" class="size-5" />
                                    <x-heroicon-o-eye-slash id="eye-slash-1" class="size-5 hidden" />
                                </button>
                            </div>
                            @error('password')
                                <p id="password-error" class="text-red-500 text-xs" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="flex flex-col gap-1">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="relative flex items-center">
                                <input
                                    class="w-full border h-10 outline-none pl-4 pr-12 rounded ring-offset-1 focus:ring-1 focus:ring-cyan-500 @error('password_confirmation') border-rose-500 @else border-gray-300 @enderror"
                                    type="password" id="password_confirmation" name="password_confirmation"
                                    autocomplete="new-password" required aria-required="true" oninput="clearError(this, 'password_confirmation-error')" />
                                <button type="button" tabindex="-1"
                                    onclick="togglePassword('password_confirmation', 'eye-2', 'eye-slash-2')"
                                    class="absolute top-px right-px bg-neutral-200 h-9.5 flex items-center justify-center px-2 rounded-r border-l border-neutral-300 cursor-pointer">
                                    <x-heroicon-o-eye id="eye-2" class="size-5" />
                                    <x-heroicon-o-eye-slash id="eye-slash-2" class="size-5 hidden" />
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p id="password_confirmation-error" class="text-red-500 text-xs" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <button
                            onclick="loader(this)"
                            class="mt-2 focus:ring-1 ring-offset-2 ring-blue-500 outline-none h-10 text-base sm:w-35 lg:w-50 lg:text-xl font-bold p-2 bg-sky-700 text-white/90 rounded-4xl cursor-pointer transition-colors duration-300 tracking-wide hover:bg-sky-900 flex items-center justify-center"
                            type="submit">
                            <span id="btn-text">Register</span>
                            <div id="btn-loader" class="loader hidden"></div>
                        </button>

                    </form>

                    {{-- Divider --}}
                    <div class="flex items-center gap-4" role="separator" aria-hidden="true">
                        <span class="w-fit text-nowrap">Or continue with</span>
                        <div class="border h-0 w-full text-gray-300"></div>
                    </div>

                    {{-- Google Login --}}
                    <section aria-label="Social login options">
                        <a class="focus:ring-1 ring-offset-2 ring-slate-600 rounded-4xl outline-2 outline-gray-300 font-medium flex items-center justify-center gap-2 sm:w-35 lg:w-50 p-2"
                            href="{{ route('auth.google-redirect') }}" role="button">
                            <svg class="w-4 h-4" viewBox="-3 0 262 262" xmlns="http://www.w3.org/2000/svg"
                                preserveAspectRatio="xMidYMid">
                                <path
                                    d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027"
                                    fill="#4285F4"></path>
                                <path
                                    d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1"
                                    fill="#34A853"></path>
                                <path
                                    d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782"
                                    fill="#FBBC05"></path>
                                <path
                                    d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251"
                                    fill="#EB4335"></path>
                            </svg>
                            Google
                        </a>
                    </section>

                </div>

                {{-- Footer --}}
                <footer class="sm:text-sm bg-gray-200 h-18 flex items-center px-12 mt-12">
                    <p>Already have an account?
                        <a class="text-blue-500 hover:underline font-medium" href="{{ route('view.sign-in') }}">Sign
                            in</a>
                    </p>
                </footer>

            </section>
        </main>

    </div>

    {{-- Password Toggle Script --}}
    <script>
        function togglePassword(inputId, eyeId, eyeSlashId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const eyeSlash = document.getElementById(eyeSlashId);

            input.type = input.type === 'password' ? 'text' : 'password';
            eye.classList.toggle('hidden');
            eyeSlash.classList.toggle('hidden');
        }

        function clearError(input, errorId) {
            // remove red border
            input.classList.remove('border-rose-500');
            input.classList.add('border-gray-300');

            // hide error message
            const error = document.getElementById(errorId);
            if (error) error.classList.add('hidden');
        }

        document.querySelector('#register-form').addEventListener('submit', function() {
            const btn = document.getElementById('register-btn');
            document.getElementById('btn-text').classList.add('hidden');
            document.getElementById('btn-loader').classList.remove('hidden');
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            setTimeout(() => btn.disabled = true, 100);
        });
    </script>
@endsection