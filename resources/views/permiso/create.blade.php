<x-app-layout>
    <div class="px-4 py-6 max-w-xl mx-auto">
        {{-- Card --}}
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">

            {{-- Botón volver --}}
            <div>
                <x-shared.btn-volver :url="route('permiso.index')" />
            </div>

            {{-- Título --}}
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-800">Nuevo Permiso</h1>
            </div>

            {{-- Formulario --}}
            <form action="{{ route('permiso.store') }}" method="POST" class="space-y-4">
                @include('permiso.form')

                {{-- Botón guardar --}}
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-500 transition text-sm flex gap-2 items-center">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
