@php
    use App\Models\User;
    $user = User::with('phones')->findOrFail(Auth::id());
@endphp
<div id="modal-contact"
    class="hidden fixed inset-0 m-auto bg-white rounded-xl md:w-lg lg:w-xl h-96 shadow-[0px_0px_10px_0.5px] shadow-gray-300 z-20">
    <div class="flex flex-col p-4">
        <div class="flex justify-end">
            <button onclick="document.getElementById('modal-contact').classList.toggle('hidden')"
                class="size-8 flex items-center justify-center hover:bg-gray-100 rounded-full cursor-pointer transition-all ease-in">
                <x-heroicon-s-x-mark class="size-6" />
            </button>
        </div>
        <h3 class="font-medium text-xl lg:text-2xl">
            Contact Information
        </h3>
        <p class="text-sm text-gray-800 mb-2">
            Manage your mobile numbers. Use any of them to access in this Account.
        </p>
        <ul class="flex flex-col border rounded-xl overflow-hidden">
            <li class="flex items-center px-4 py-2 border-b hover:bg-gray-200 cursor-pointer">
                <span class="flex items-center justify-center size-6 ">
                    <x-heroicon-o-envelope class="size-4" />
                </span>
                <p class="">
                    {{ $user->email }}
                </p>
            </li>
            @forelse ($user->phones as $contact)
                <li class="flex items-center px-4 py-2 border-b hover:bg-gray-200 cursor-pointer">
                    <span class="flex items-center justify-center size-6 ">
                        <x-heroicon-s-phone class="size-4" />
                    </span>
                    <p class="">
                        {{ $contact->phone_number }}
                    </p>
                </li>
            @empty
                <li class="flex items-center px-4 py-2 border-b hover:bg-gray-200 cursor-pointer">
                    <span class="flex items-center justify-center size-6 ">
                        <x-heroicon-s-phone class="size-4" />
                    </span>
                    <p class="">
                        No phone number
                    </p>
                </li>
            @endforelse

            <button onclick="addModalContact()"
                class="text-start px-4 py-2 text-blue-600 font-medium hover:bg-gray-200 cursor-pointer">
                Add new contact
            </button>
        </ul>
    </div>
</div>
<x-modal-add-phone />
<script>
    function addModalContact() {
        const modalContact = document.getElementById('modal-contact');
        const modalAddContact = document.getElementById('modal-add-contact');

        modalContact.classList.add('hidden');
        modalAddContact.classList.toggle('hidden');
    }
</script>