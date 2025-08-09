<x-app-layout>
    <div class="px-4 py-6 max-w-xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">
            <x-shared.btn-volver :url="route('nivel_laboratorio.index')" />
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-primary">Nuevo Nivel de Laboratorio</h1>
            </div>
            <form action="{{ route('nivel_laboratorio.store') }}" method="POST">
                @include('nivel_laboratorio.form')
                @csrf

                <div class="text-right">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
