<x-app-layout>
    <div class="px-4 py-6 max-w-6xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">

            <x-shared.btn-volver :url="route('laboratorio.index')" />

            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-primary">Editar Laboratorio</h1>
            </div>

            <form action="{{ route('laboratorio.update', $laboratorio) }}" method="POST" class="space-y-6">
                @include('laboratorio.form', ['method' => 'PUT'])
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
