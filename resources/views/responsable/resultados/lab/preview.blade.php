@php
    $primaryColor = $formulario->color_primario;
    $secondaryColor = $formulario->color_secundario;
    $cantidad = $cantidad ?? 1;
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 rounded shadow bg-white" style="border-top: 6px solid {{ $primaryColor }}">
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 text-sm rounded-md transition"
                style="background-color: {{ $primaryColor }}; color: #fff;">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <div class="max-w-5xl mx-auto p-4 rounded shadow bg-white border-t-4 mb-2"
            style="border-color: {{ $primaryColor }}">
            <!-- Título opcional -->
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del envío</h2>

            <!-- Grid simple de información -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-700">
                <div>
                    <span class="font-medium text-gray-500">Laboratorio:</span>
                    <span class="ml-1 font-semibold text-gray-800">{{ $laboratorio->nombre_lab ?? '-' }}</span>
                </div>

                <div>
                    <span class="font-medium text-gray-500">Código:</span>
                    <span class="ml-1 font-semibold text-gray-800">{{ $laboratorio->cod_lab ?? '-' }}</span>
                </div>

                <div>
                    <span class="font-medium text-gray-500">Ensayo:</span>
                    <span class="ml-1 font-semibold text-gray-800">{{ $ensayo->descripcion ?? '-' }}</span>
                </div>

                <div>
                    <span class="font-medium text-gray-500">Fecha del primer envío:</span>
                    <span class="ml-1 font-semibold text-gray-800">{{ $fechaRegistro ?? '-' }}</span>
                </div>

                <div>
                    <span class="font-medium text-gray-500">Fecha del último envío:</span>
                    <span class="ml-1 font-semibold text-gray-800">{{ $fechaActualizacion ?? '-' }}</span>
                </div>
            </div>
        </div>
        {{-- Información del formulario --}}
        <div class="grid grid-cols-2 gap-6 mb-4 text-center">
            <div class="p-3 rounded-lg shadow-sm" style="background-color: {{ $primaryColor }}20;">
                <span class="block text-xs text-gray-500">Nombre del formulario</span>
                <span class="text-sm font-semibold" style="color: {{ $primaryColor }}">
                    {{ $formulario->nombre }}
                </span>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg shadow-sm">
                <span class="block text-xs text-gray-500">Código del formulario</span>
                <span class="text-sm font-semibold text-gray-800">
                    {{ $formulario->codigo }}
                </span>
            </div>
        </div>

        {{-- Nota --}}
        @if (!empty($formulario->nota))
            <div class="p-2 rounded-lg mb-4 bg-yellow-100 border border-yellow-300">
                <span class="text-sm text-yellow-800">{{ $formulario->nota }}</span>
            </div>
        @endif

        @for ($i = 1; $i <= $cantidad; $i++)
            <div class="space-y-4 mb-8 border-b pb-6">
                <form method="POST">
                    <input type="hidden" value="{{ $formulario->id }}" name="id_formulario">
                    @csrf
                    @include('admin.formularios.partials.preview')
                </form>
            </div>
        @endfor
    </div>
</x-app-layout>
