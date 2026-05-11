@extends('layouts.verification')

@section('verification')
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl border border-gray-100">

        <div class="flex flex-col items-center mb-6">
            <div class="bg-blue-50 p-3 rounded-full mb-3">
                <x-heroicon-o-envelope class="size-7 text-blue-500" />
            </div>
            <h1 class="text-2xl font-semibold text-gray-800">Account Recovery</h1>
            <p class="text-sm text-gray-500 text-center mt-1">
                Enter your registered email address and we'll send you a password reset link.
            </p>
        </div>

        <form method="POST" action="{{ route('forgot.send-otp') }}">
            @csrf
            <div class="flex flex-col gap-1.5 mb-4">
                <label for="email" class="text-sm font-medium text-gray-700">Email address</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                    class="h-10 px-4 border border-gray-300 rounded-lg outline-none focus:ring ring-offset-1 ring-blue-500 text-sm">
                @error('email')
                    <p class="text-red-500 text-xs pl-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2.5 rounded-lg transition-all ease-in cursor-pointer">
                Send reset link
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('view.sign-in') }}" class="text-sm text-blue-500 hover:underline flex items-center justify-center gap-1">
                <x-heroicon-o-arrow-left class="size-4" />
                Back to sign in
            </a>
        </div>

    </div>
@endsection