<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('auth.2fa.verify') }}" class="space-y-6 max-w-md mx-auto bg-white p-8 rounded shadow">
        @csrf
        <h2 class="font-semibold text-xl text-gray-800 text-center mb-2">Verificación en dos pasos (2FA)</h2>
        <p class="text-gray-600 text-center mb-4">Ingresa el código generado por Google Authenticator para continuar.</p>
        <div>
            <x-input-label for="codigo" :value="__('Código 2FA')" />
            <x-text-input id="codigo" class="block mt-1 w-full" type="text" name="codigo" required autofocus />
            <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
        </div>
        <div class="flex items-center justify-end">
            <x-primary-button>
                {{ __('Verificar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
