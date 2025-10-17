<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activar verificación en dos pasos (2FA)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">
                        {{ __('Activar verificación en dos pasos (2FA)') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 mb-4">
                        {{ __('Escanea el código QR con Google Authenticator o ingresa el código secreto manualmente.') }}
                    </p>
                    <div class="mb-4 flex justify-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrUrl) }}" alt="QR 2FA">
                    </div>
                    <p class="mb-2 text-sm text-gray-600">{{ __('Si no puedes escanear el QR, usa este código secreto:') }}</p>
                    <div class="bg-gray-100 p-2 rounded mb-4 text-center font-mono">{{ $secret }}</div>
                    <form method="POST" action="{{ route('perfil.confirmar2fa') }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="codigo" :value="__('Ingresa el código generado por la app')" />
                            <x-text-input id="codigo" name="codigo" type="text" class="mt-1 block w-full text-gray-900 placeholder-gray-400" required autofocus />
                            <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                        </div>
                        <div class="flex justify-end">
                            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                                {{ __('Confirmar y activar 2FA') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
