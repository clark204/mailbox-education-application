@if (!Session::has('otp_target'))
    <div id="modal-add-contact"
        class="{{ $errors->has('phone') ? '' : 'hidden' }} fixed inset-0 m-auto bg-white rounded-xl md:w-lg lg:w-xl h-96 shadow-[0px_0px_10px_0.5px] shadow-gray-300 z-20">
        <div class="flex flex-col p-4 h-full">
            <div class="flex justify-between">
                <button onclick="backToContactModal()"
                    class="size-8 flex items-center justify-center hover:bg-gray-100 rounded-full cursor-pointer transition-all ease-in">
                    <x-heroicon-s-chevron-left class="size-6" />
                </button>
                <button onclick="document.getElementById('modal-add-contact').classList.toggle('hidden')"
                    class="size-8 flex items-center justify-center hover:bg-gray-100 rounded-full cursor-pointer transition-all ease-in">
                    <x-heroicon-s-x-mark class="size-6" />
                </button>
            </div>
            <h3 class="font-medium text-xl lg:text-2xl">
                Add new phone number
            </h3>
            <p class="text-sm text-gray-800 mb-2">
                Add a new phone number to your account.
            </p>
            <form method="POST" action="{{ route('contact.sendSms') }}" class="flex flex-col justify-between flex-1">
                @csrf
                <div class="flex flex-col">
                    <label for="phone" class="">Phone Number</label>
                    <input type="tel" name="phone" id="phone"
                        class="{{ $errors->has('phone') ? 'ring ring-red-500' : 'focus:ring ring-blue-500' }} ring-offset-1 h-10 px-4 outline-none border border-gray-300 rounded-lg"
                        py-2" pattern="[0-9]{11}" placeholder="+63 900 000 0000"
                        oninput="this.value = this.value.replace(/[^0-9+\-\s]/g, '')">
                    @error('phone')
                        <p id="fn-error" class="text-red-500 text-xs pl-4">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    class="bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition-all ease-in cursor-pointer">
                    Send verification
                </button>
            </form>
        </div>
    </div>
    <script>
        function backToContactModal() {
            const modalContact = document.getElementById('modal-contact');
            const modalAddContact = document.getElementById('modal-add-contact');

            modalAddContact.classList.add('hidden');
            modalContact.classList.toggle('hidden');
        }
    </script>
@endif