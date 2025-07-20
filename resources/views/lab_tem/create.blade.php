<x-guest-layout>
    <div class="bg-white border border-gray-200 mx-auto relative"
        style="overflow: hidden; display: flex; flex-direction: column; width: 100vw; max-height: 100vh;">

        <!-- NOTIFICACIÓN FIJA -->
        <div
            class="absolute top-4 right-4 z-50 bg-yellow-100 border border-yellow-400 text-yellow-800 text-sm px-4 py-2 rounded shadow-md pointer-events-none select-none">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Los campos con <span class="text-red-500 font-bold">(*)</span> son obligatorios.
        </div>

        <!-- Encabezado fijo -->
        <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-200 sticky top-0 z-40">
            <h1 class="text-xl font-bold text-primary text-center">Registra tu Información</h1>
        </div>

        <!-- Contenido scrollable -->
        <div class="overflow-y-auto px-6 py-4 space-y-6" style="flex-grow: 1;">
            <form action="{{ route('registro.tem.lab') }}" method="POST" class="space-y-6">
                @csrf
                @include('laboratorio.form')

                <!-- Botones -->
                <div class="flex justify-center gap-4 pt-4">
                    <!-- Botón volver a iniciar sesión -->
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-md shadow">
                        <i class="fas fa-arrow-left"></i>
                        Volver a Iniciar Sesión
                    </a>

                    <!-- Botón registrar -->
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Registrar Laboratorio
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
