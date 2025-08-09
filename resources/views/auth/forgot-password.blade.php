<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white border border-gray-200 shadow-md rounded-xl p-6 space-y-6">
        <h1 class="text-2xl font-bold text-indigo-700">¿Olvidaste tu Contraseña?</h1>

        <p class="text-sm text-gray-700 leading-relaxed">
            No hay problema. Ingresa tu dirección de correo electrónico y te enviaremos un enlace para que puedas
            restablecer tu contraseña.
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Correo electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-envelope mr-1"></i> Enviar enlace
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
