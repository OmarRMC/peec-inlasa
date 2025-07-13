<x-app-layout>
    <div class="px-4 py-6 max-w-xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">
            <x-shared.btn-volver :url="route('ensayo_aptitud.index')" />
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-primary">Nuevo Ensayo</h1>
            </div>
            <form action="{{ route('ensayo_aptitud.store') }}" method="POST" class="space-y-4">
                @include('ensayo_aptitud.form')
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
