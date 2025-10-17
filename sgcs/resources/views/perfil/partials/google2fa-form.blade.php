<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Verificación en dos pasos (2FA)') }}
        </h2>
    </header>
    @if(auth()->user()->google2fa_secret)
        <div class="mb-4 text-green-700">
            2FA está <strong>activado</strong> en tu cuenta.
        </div>
        <form method="POST" action="{{ route('perfil.desactivar2fa') }}" class="mt-2">
            @csrf
            <x-primary-button class="bg-red-600 hover:bg-red-700">
                Desactivar 2FA
            </x-primary-button>
        </form>
    @else
        <div class="mb-4 text-gray-700">
            2FA está <strong>desactivado</strong>. Puedes activarlo para mayor seguridad.
        </div>
        <form method="POST" action="{{ route('perfil.activar2fa') }}" class="mt-2">
            @csrf
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                Activar 2FA
            </x-primary-button>
        </form>
    @endif
</section>
