<x-app-layout>
    <div class="px-4 py-6 max-w-3xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-6 space-y-6 border border-gray-200">
            <x-shared.btn-volver :url="route('usuario.index')" />

            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-800">Editar Usuario</h1>
            </div>

            <form action="{{ route('usuario.update', $usuario->id) }}" method="POST" class="space-y-4">
                @include('usuario.form', ['method' => 'PUT'])
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-500 transition text-sm flex gap-2 items-center">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
