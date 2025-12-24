<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white border border-gray-200 shadow-md rounded-xl p-6 space-y-6 z-10 relative">
        <h1 class="text-2xl font-bold text-indigo-700">Restablecer Contraseña</h1>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Correo electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)"
                    required autofocus autocomplete="username" readonly />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Contraseña -->
            <div class="relative">
                <x-input-label for="password" :value="__('Nueva Contraseña')" />
                <x-text-input id="password" class="block mt-1 w-full pr-12" type="password" name="password" required
                    autocomplete="new-password" />
                <!-- Botón ojito (centrado verticalmente) -->
                <button type="button" id="toggle-password"
                    class="absolute right-3 top-1/2 -translate-y-1/2 mt-[18px] text-gray-500 hover:text-gray-700 focus:outline-none"
                    aria-label="Mostrar u ocultar contraseña" title="Mostrar/ocultar contraseña">
                    <i class="fas fa-eye"></i>
                </button>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="relative">
                <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-12" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <button type="button" id="toggle-password-confirmation"
                    class="absolute right-3 top-1/2 -translate-y-1/2 mt-[18px] text-gray-500 hover:text-gray-700 focus:outline-none"
                    aria-label="Mostrar u ocultar confirmación de contraseña" title="Mostrar/ocultar confirmación">
                    <i class="fas fa-eye"></i>
                </button>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-key mr-1"></i> Restablecer contraseña
                </button>
            </div>
        </form>
    </div>

    <script>
        function setupToggle(inputId, btnId) {
            const input = document.getElementById(inputId);
            const btn = document.getElementById(btnId);
            if (!input || !btn) return;

            const icon = btn.querySelector('i');

            btn.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !show);
                icon.classList.toggle('fa-eye-slash', show);
                btn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
                btn.setAttribute('title', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        }

        setupToggle('password', 'toggle-password');
        setupToggle('password_confirmation', 'toggle-password-confirmation');
    </script>
</x-guest-layout>
