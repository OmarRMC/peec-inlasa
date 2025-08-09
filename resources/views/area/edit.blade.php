<x-app-layout>
    <div class="px-4 py-6 max-w-xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">
            <x-shared.btn-volver :url="route('area.index')" />

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-primary">Editar Área</h1>
            </div>

            <form action="{{ route('area.update', $area) }}" method="POST">
                @include('area.form', ['method' => 'PUT', 'programas' => $programas])
                <div class="text-right">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
