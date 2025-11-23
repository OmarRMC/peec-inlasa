@php
    use App\Models\Configuracion;
@endphp
<x-guest-layout>
    <div class="w-full max-w-md bg-white bg-opacity-95 rounded-md shadow-lg px-6 pt-4 pb-8 card-lon relative">
        <div class="mx-auto h-16 mb-2 left-1/2">
            <img src="{{ asset('img/logoinlasa.png') }}" width="70px" class="mx-auto" alt="INLASA Logo">
        </div>
        <h2 class="text-center text-lg font-semibold text-gray-800 mb-4">
            Por favor, ingrese sus credenciales de acceso
        </h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold"></strong>
                <span class="block sm:inline">
                    Estas credenciales no coinciden con nuestros registros.
                </span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Usuario -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">CÓDIGO / USUARIO</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input id="username" name="username" type="text" :value="old('username')" required autofocus
                        class="pl-10 w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-200"
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
                        class="pl-10 w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-200"
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

            <!-- Botón ingresar -->
            <div class="mb-4">
                <button type="submit" class="btn-primary w-full justify-center font-semibold py-2 px-4 rounded shadow">
                    INGRESAR
                </button>
            </div>

            @if (Configuracion::esPeriodoRegistro())
                <!-- Enlace a registro -->
                <div class="text-center text-sm text-gray-700">
                    ¿No tienes una cuenta?
                    <a href="{{ route('form.registro.tem.lab') }}" class="text-blue-600 hover:underline font-semibold">
                        Regístrate aquí
                    </a>
                </div>
            @endif
        </form>

        <!-- Pie institucional -->
        <div class="text-xs text-center text-gray-500 mt-3">
            © 2025 | Instituto Nacional de Laboratorios de Salud<br>
            Programa de Evaluación Externa de la Calidad
        </div>
    </div>
</x-guest-layout>
