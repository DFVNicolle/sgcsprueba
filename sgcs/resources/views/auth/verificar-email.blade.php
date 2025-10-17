<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Verificación de Correo Electrónico
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Sistema de Gestión de Configuración de Software (SGCS)
                </p>
            </div>

            <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-10">
                <div class="mb-4 text-sm text-gray-700 leading-relaxed">
                    Gracias por registrarte en SGCS. Antes de comenzar, verifica tu dirección de correo electrónico haciendo clic en el enlace que te hemos enviado. Si no recibiste el correo, podemos enviarte otro.
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    Nuevo enlace enviado
                                </p>
                                <p class="text-sm text-green-700">
                                    Se ha enviado un nuevo enlace de verificación a la dirección de correo proporcionada durante el registro.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex items-center justify-between space-x-4">
                    <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
                        @csrf
                        <x-primary-button class="w-full">
                            Reenviar Correo de Verificación
                        </x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
