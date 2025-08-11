<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Nombre -->
        <div class="mt-4">
            <x-input-label for="nombre" :value="__('Nombre')" />
            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')"
                required autocomplete="given-name" />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <!-- Apellido Paterno -->
        <div class="mt-4">
            <x-input-label for="ap_paterno" :value="__('Apellido Paterno')" />
            <x-text-input id="ap_paterno" class="block mt-1 w-full" type="text" name="ap_paterno"
                :value="old('ap_paterno')" required />
            <x-input-error :messages="$errors->get('ap_paterno')" class="mt-2" />
        </div>

        <!-- Apellido Materno -->
        <div class="mt-4">
            <x-input-label for="ap_materno" :value="__('Apellido Materno')" />
            <x-text-input id="ap_materno" class="block mt-1 w-full" type="text" name="ap_materno"
                :value="old('ap_materno')" />
            <x-input-error :messages="$errors->get('ap_materno')" class="mt-2" />
        </div>

        <!-- CI -->
        <div class="mt-4">
            <x-input-label for="ci" :value="__('Cédula de Identidad')" />
            <x-text-input id="ci" class="block mt-1 w-full" type="text" name="ci" :value="old('ci')" required />
            <x-input-error :messages="$errors->get('ci')" class="mt-2" />
        </div>

        <!-- Teléfono -->
        <div class="mt-4">
            <x-input-label for="telefono" :value="__('Teléfono')" />
            <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono"
                :value="old('telefono')" />
            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Cargo -->
        <div class="mt-4">
            <x-input-label for="id_cargo" :value="__('Cargo')" />
            <select id="id_cargo" name="id_cargo"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- Seleccione un cargo --</option>
                @foreach($cargos as $cargo)
                    <option value="{{ $cargo->id }}" {{ old('id_cargo') == $cargo->id ? 'selected' : '' }}>
                        {{ $cargo->nombre }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('id_cargo')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="off" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="off" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Botón -->
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                {{ __('¿Ya estás registrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>