<x-app-layout>
    <div class="max-w-2xl mx-auto">

        
        <div class="bg-indigo-600 text-white rounded-lg px-6 py-5 mb-6">
            <p class="text-xs uppercase tracking-widest text-indigo-200 mb-0.5">Seguridad de la cuenta</p>
            <h2 class="text-lg font-bold leading-tight flex items-center gap-2">
                <i class="fas fa-key text-indigo-200"></i> Cambiar contraseña
            </h2>
            <p class="text-indigo-200 text-sm mt-0.5">
                Usa una contraseña larga y segura para proteger tu cuenta.
            </p>
        </div>

        
        <div class="bg-white rounded-lg shadow-sm border p-5 sm:p-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-5 flex items-center gap-2">
                <i class="fas fa-lock text-indigo-500"></i> Datos de acceso
            </h3>

            @if (session('status') === 'password-updated')
                <div class="mb-4 flex items-center gap-2 rounded-md bg-green-50 border border-green-200 text-green-700 px-3 py-2 text-sm">
                    <i class="fas fa-check-circle"></i> Tu contraseña fue actualizada correctamente.
                </div>
            @endif

            <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('put')

        
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-unlock-alt text-gray-400"></i> Contraseña actual
                    </label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                            class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 pr-10 @error('current_password', 'updatePassword') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('current_password', this)"
                            class="absolute inset-y-0 right-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-key text-gray-400"></i> Nueva contraseña
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" autocomplete="new-password"
                            class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 pr-10 @error('password', 'updatePassword') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('password', this)"
                            class="absolute inset-y-0 right-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password', 'updatePassword')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-key text-gray-400"></i> Confirmar nueva contraseña
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                            class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 pr-10">
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                            class="absolute inset-y-0 right-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</x-app-layout>
