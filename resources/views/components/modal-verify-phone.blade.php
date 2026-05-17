@if (Session::has('otp_target'))
    @php
        $target = Session::get('otp_target');
    @endphp
    <div id="modal-verify-phone" class="fixed inset-0 z-20 pointer-events-none flex items-center justify-center">
        <div
            class="pointer-events-auto bg-white rounded-xl md:w-lg lg:w-xl h-96 shadow-[0px_0px_10px_0.5px] shadow-gray-300">
            <div class="flex flex-col p-4 h-full">
                <div class="flex justify-end">
                    <button onclick="closeVerifyPhoneModal()"
                        class="size-8 flex items-center justify-center hover:bg-gray-100 rounded-full cursor-pointer transition-all ease-in">
                        <x-heroicon-s-x-mark class="size-6" />
                    </button>
                </div>
                <h3 class="font-medium text-xl lg:text-2xl">
                    Check your phone messages
                </h3>
                <p class="text-sm text-gray-800 mb-2">
                    Enter the code we sent to your phone number at
                    {{ substr($target, 0, 3) . str_repeat('*', strlen($target) - 5) . substr($target, -2) }}.
                </p>
                <form method="POST" action="{{ route('contact.verify') }}" class="flex flex-col justify-between flex-1">
                    @csrf
                    <div class="flex flex-col">
                        <label for="otp" class="">Code</label>
                        <input type="tel" name="otp" id="otp"
                            class="{{ $errors->has('otp') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg py-2"
                            pattern="[0-9]{6}" oninput="this.value = this.value.replace(/[^0-9+\-\s]/g, '').slice(0, 6)">
                        @error('otp')
                            <p class="text-red-500 text-xs pl-4">{{ $message }}</p>
                        @enderror
                    </div>
                    <button
                        class="bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition-all ease-in cursor-pointer">
                        Verify Phone
                    </button>
                </form>
            </div>
        </div>
        <form id="form-forget-otp" method="POST" action="{{ route('contact.forget') }}" class="hidden">
            @csrf
        </form>
    </div>
    <script>
        function closeVerifyPhoneModal() {
            document.getElementById('modal-verify-phone').classList.add('hidden');
            document.getElementById('form-forget-otp').submit();
        }
    </script>
@endif