<x-guest-layout>
    <h2 class="text-center text-lg font-semibold text-gray-800 mb-6">Por favor, ingrese sus credenciales de acceso</h2>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Usuario -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">CÓDIGO / USUARIO</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-user"></i>
                </span>
                <input id="email" name="email" type="text" :value="old('email')" required autofocus
                    class="pl-10 w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-200"
                    placeholder="Ingrese su código o usuario" />
            </div>
        </div>

        <!-- Contraseña -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">CONTRASEÑA</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input id="password" name="password" type="password" required
                    class="pl-10 w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-200"
                    placeholder="Ingrese su contraseña" />
            </div>
        </div>

        <!-- Mostrar contraseña -->
        <div class="flex items-center mb-4 text-sm text-gray-600">
            <input type="checkbox" id="show_password" class="mr-2"
                onclick="document.getElementById('password').type = this.checked ? 'text' : 'password'">
            <label for="show_password">Mostrar Contraseña</label>
        </div>

        <!-- Enlace recuperar -->
        <div class="flex justify-end text-sm mb-4">
            <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">¿Olvidaste tu
                contraseña?</a>
        </div>

        <!-- Botón -->
        <div>
            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded shadow">
                INGRESAR
            </button>
        </div>
    </form>

    <!-- Pie institucional -->
    <div class="text-xs text-center text-gray-500 mt-6">
        © 2025 | Instituto Nacional de Laboratorios de Salud<br>
        Programa de Evaluación Externa de la Calidad
    </div>
</x-guest-layout>