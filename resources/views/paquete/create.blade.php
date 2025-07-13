<x-app-layout>
    <div class="px-4 py-6 max-w-xl mx-auto">
        <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">
            <x-shared.btn-volver :url="route('paquete.index')" />

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-primary">Registrar Paquete</h1>
            </div>

            <form action="{{ route('paquete.store') }}" method="POST" class="flex flex-col gap-3">
                @include('paquete.form')
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
