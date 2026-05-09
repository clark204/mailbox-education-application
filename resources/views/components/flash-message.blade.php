@if (session('success'))
    <div id="flash-success"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl shadow-lg text-sm w-max max-w-sm animate-slide-down">
        <x-heroicon-o-check-circle class="size-5 shrink-0" />
        <p>{{ session('success') }}</p>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
            <x-heroicon-o-x-mark class="size-4" />
        </button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('compose-window').classList.add('hidden');
        });
    </script>
@endif

@if (session('error'))
    <div id="flash-error"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl shadow-lg text-sm w-max max-w-sm animate-slide-down">
        <x-heroicon-o-x-circle class="size-5 shrink-0" />
        <p>{{ session('error') }}</p>
        <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
            <x-heroicon-o-x-mark class="size-4" />
        </button>
    </div>
@endif

@if (session('warning'))
    <div id="flash-warning"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 bg-yellow-100 border border-yellow-300 text-yellow-700 px-4 py-3 rounded-xl shadow-lg text-sm w-max max-w-sm animate-slide-down">
        <x-heroicon-o-exclamation-triangle class="size-5 shrink-0" />
        <p>{{ session('warning') }}</p>
        <button onclick="this.parentElement.remove()" class="ml-auto text-yellow-500 hover:text-yellow-700">
            <x-heroicon-o-x-mark class="size-4" />
        </button>
    </div>
@endif

<script>
    setTimeout(() => {
        ['flash-success', 'flash-error', 'flash-warning'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateX(-50%) translateY(-20px)';
                setTimeout(() => el.remove(), 500);
            }
        });
    }, 3000);
</script>