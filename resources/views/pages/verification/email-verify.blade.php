@extends('layouts.verification')

@section('verification')
    <div class="w-full max-w-md rounded-2xl bg-text-white p-8 shadow-xl border border-gray-100 text-center">
        {{-- Icon Header --}}
        <div class="mb-6 flex justify-center">
            <div class="relative">
                <div class="h-20 w-20 rounded-full bg-blue-50 flex items-center justify-center">
                    <x-heroicon-o-envelope class="w-10 h-10 text-blue-500" />
                </div>
                <div class="absolute -bottom-1 -right-1 bg-white p-1 rounded-full">
                    <div class="bg-green-400 rounded-full p-1">
                        <x-heroicon-s-check class="w-3 h-3 text-white" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Heading --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Verify Your Email</h2>
        <p class="text-gray-500 mb-6 leading-relaxed text-sm">
            A 6-digit one-time password has been sent to
            <span class="font-medium text-gray-700">your email provided</span>.
            Please enter it below to confirm your account.
        </p>

        {{-- Error --}}
        @if ($errors->any())
            <div class="mb-4 text-red-500 text-sm bg-red-50 border border-red-200 rounded-xl px-4 py-3" role="alert"
                aria-live="polite">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Success --}}
        @if (session('success'))
            <div class="mb-4 text-green-600 text-sm bg-green-50 border border-green-200 rounded-xl px-4 py-3" role="status"
                aria-live="polite">
                {{ session('success') }}
            </div>
        @endif

        {{-- OTP Form --}}
        <form action="{{ route('verify.otp') }}" method="POST">
            @csrf
            <input type="hidden" name="otp" id="otp-value" />

            {{-- OTP Boxes --}}
            <div class="flex justify-between gap-2 mb-6" id="otp-inputs" role="group" aria-label="Enter 6-digit OTP">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="{{ $i }}"
                        class="otp-input w-12 h-14 border-2 border-gray-200 rounded-xl text-center text-xl font-bold
                                                                                focus:border-blue-500 focus:outline-none transition-all" autocomplete="off" aria-label="Digit {{ $i + 1 }} of 6" />
                @endfor
            </div>

            {{-- Resend --}}
            <div class="text-sm text-gray-500 mb-8">
                Didn't receive code?
                <span id="resend-timer" class="text-gray-400">
                    Resend in <span id="countdown">{{ $ttl }}</span>s
                </span>
                <button type="button" id="resend-btn" onclick="document.getElementById('resend-form').submit()" class="cursor-pointer hidden text-blue-600 font-semibold hover:underline
                                focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">
                    Resend Code
                </button>
            </div>

            {{-- Submit --}}
            <button type="submit" class="cursor-pointer w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold
                            rounded-xl shadow-lg transition-colors mb-4
                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                Verify
            </button>

            {{-- Go Back --}}
            <a href="{{ route('verify.forget') }}"
                class="text-gray-400 font-bold tracking-wide hover:text-gray-600 transition-colors text-sm">
                Go Back
            </a>

        </form>

        {{-- RESEND --}}
        <form id="resend-form" action="{{ route('verify.resend') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const otpValue = document.getElementById('otp-value');
        const countdown = document.getElementById('countdown');
        const resendTimer = document.getElementById('resend-timer');
        const resendBtn = document.getElementById('resend-btn');

        // Focus first input on load
        inputs[0]?.focus();

        inputs.forEach((input, index) => {
            input.addEventListener('keypress', function (e) {
                if (isNaN(e.key) || e.key === ' ') e.preventDefault();
            });

            input.addEventListener('input', function () {
                // Only keep last character
                this.value = this.value.slice(-1);

                if (this.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateOtpValue();
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('focus', function () {
                this.select();
            });

            input.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                paste.split('').forEach((char, i) => {
                    if (inputs[i]) inputs[i].value = char;
                });
                updateOtpValue();
                inputs[Math.min(paste.length, 5)].focus();
            });
        });

        function updateOtpValue() {
            otpValue.value = Array.from(inputs).map(i => i.value).join('');
        }

        // Countdown
        let seconds = {{ $ttl }};

        countdown.textContent = seconds;

        const timer = setInterval(() => {
            seconds--;
            countdown.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(timer);
                resendTimer.classList.add('hidden');
                resendBtn.classList.remove('hidden');
            }
        }, 1000);
    </script>
@endsection