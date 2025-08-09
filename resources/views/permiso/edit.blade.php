<x-app-layout>
    <div class="px-4 py-6 max-w-xl mx-auto">
        {{-- Card --}}
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">
            <div>
                <x-shared.btn-volver :url="route('permiso.index')" />
            </div>

            <!-- Encabezado + botón Volver -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-gray-800">Editar Permiso</h1>
            </div>

            <!-- Formulario de edición -->
            <form action="{{ route('permiso.update', $permiso) }}" method="POST">
                @csrf
                @method('PUT')
                @include('permiso.form')
                <div class="text-right">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-500 transition text-sm">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
