<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">

        <x-shared.btn-volver :url="route('reportes.resultados.registrados', ['idEA' => $ensayo->id])" />
        <!-- Información del laboratorio -->
        <div class="mb-4 space-y-1">
            <h1 class="text-lg font-semibold text-gray-800">Laboratorio: {{ $lab->nombre_lab }}</h1>
            <p class="text-sm text-gray-600 font-semibold">Código: {{ $lab->cod_lab ?? '-' }}</p>
            <p class="text-sm text-gray-600 font-semibold">Nombre del ciclo / Numero:
                {{ $ciclo->nombre ? $ciclo->nombre : ' ' }}
                {{ $ciclo->numero ? ' / Ciclo ' . $ciclo->numero : '' }}
            </p>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Resultados enviados</h2>

        <!-- Tabla de resultados -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Formulario</th>
                        <th class="px-3 py-2 text-center">Fecha envío</th>
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($resultados as $resultado)
                        {{-- @dump($resultado) --}}
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2 align-top">
                                {{ $resultado->formulario->nombre ?? 'Formulario #' . $resultado->id }}</td>
                            <td class="px-3 py-2 align-top text-center">
                                {{ formatDate($resultado->fecha_envio) }}</td>
                            <td class="px-3 py-2 text-center gap-2 flex justify-center">
                                <!-- Botón previsualizar -->

                                <a href="{{ route('responsable.resultados.preview', ['id' => $resultado->id]) }}"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm text-xs"
                                    data-tippy-content="Previsualizar resultados del formulario"
                                    title="Previsualizar formulario">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">No se encontraron resultados
                                enviados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación / nota -->
        <div class="mt-4 flex items-center justify-between">
            <div class="text-xs text-gray-500">Mostrando <span class="font-medium">{{ $resultados->count() }}</span>
                resultados</div>
            <div>
                @if (method_exists($resultados, 'links'))
                    {{ $resultados->appends(request()->query())->links() }}
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
