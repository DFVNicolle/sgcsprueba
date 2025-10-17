<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nombre -->
        <div>
            <x-input-label for="name" :value="__('Nombre completo')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Correo electrónico -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4 relative">
            <x-input-label for="password" :value="__('Contraseña')" />

            <x-text-input id="password" class="block mt-1 w-full pr-10"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <button type="button" id="toggle-password" class="absolute top-8 right-3 text-gray-500 focus:outline-none" tabindex="-1">
                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
            <div id="password-strength" class="h-2 mt-2 rounded bg-gray-200">
                <!-- Barra de fuerza JS -->
            </div>
            <div id="password-strength-text" class="text-xs mt-1 text-gray-600"></div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar contraseña -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200" href="{{ route('login') }}">
                {{ __('¿Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>

    </form>
    <script>
    // Barra de fuerza de contraseña
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('password-strength-text');
    passwordInput.addEventListener('input', function() {
        const val = passwordInput.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[a-z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        let colors = ['bg-red-500', 'bg-orange-400', 'bg-yellow-400', 'bg-green-400', 'bg-green-600'];
        strengthBar.className = 'h-2 mt-2 rounded ' + (colors[score-1] || 'bg-gray-200');
        let texts = ['Muy débil', 'Débil', 'Regular', 'Fuerte', 'Muy fuerte'];
        strengthText.textContent = val ? texts[score-1] || 'Muy débil' : '';
    });
    // Mostrar/ocultar contraseña
    const togglePassword = document.getElementById('toggle-password');
    const eyeIcon = document.getElementById('eye-icon');
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
    // Cambia el color del ícono al mostrar
    eyeIcon.classList.toggle('text-blue-500', type === 'text');
    });
    </script>
</x-guest-layout>
