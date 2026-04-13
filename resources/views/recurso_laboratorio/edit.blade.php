<x-app-layout>
    <div class="px-4 py-6 max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">

            <x-shared.btn-volver :url="route('recursos_lab.index')" />

            <h1 class="text-xl font-bold text-primary">
                <i class="fas fa-edit text-indigo-500 mr-1"></i> Editar Recurso
            </h1>

            <form action="{{ route('recursos_lab.update', $recurso) }}" method="POST" enctype="multipart/form-data">
                @include('recurso_laboratorio.form', ['method' => 'PUT', 'recurso' => $recurso])
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
