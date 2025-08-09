<x-app-layout>
    <div class="px-4 py-6 max-w-xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">
            <x-shared.btn-volver :url="route('programa.index')" />

            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-primary">Nuevo Programa</h1>
            </div>

            <form action="{{ route('programa.store') }}" method="POST" class="space-y-4">
                @include('programa.form')
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
