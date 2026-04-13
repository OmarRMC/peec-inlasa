<x-app-layout>
    <div class="container py-6 max-w-5xl">
        <x-shared.btn-volver :url="route('lab.inscritos-ensayos.index')" />
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Formularios del Ensayo: {{ $ensayo->descripcion }}</h1>
        </div>
        <!-- @php
           //TODO: OMAR
            dump($ensayo->id);
        @endphp -->
        <div class="mb-6">
            @if ($estado === 'activo' && $cicloActivo)
                <div class="bg-green-100 border border-green-300 text-green-800 px-5 py-2 rounded-xl shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold uppercase tracking-wide text-green-600">Ciclo activo</span>
                        <span class="font-bold text-base">{{ $cicloActivo->nombre }}</span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-x-6 gap-y-1 text-sm text-green-700">
                        @if ($cicloActivo->fecha_inicio_envio_resultados && $cicloActivo->fecha_fin_envio_resultados)
                            <span>
                                <span class="font-medium">Resultados:</span>
                                {{ $cicloActivo->fecha_inicio_envio_resultados_show }} - {{ $cicloActivo->fecha_fin_envio_resultados_show }}
                            </span>
                        @endif
                        @if ($cicloActivo->fecha_inicio_envio_reportes && $cicloActivo->fecha_fin_envio_reportes)
                            <span>
                                <span class="font-medium">Reportes:</span>
                                {{ $cicloActivo->fecha_inicio_envio_reportes_show }} - {{ $cicloActivo->fecha_fin_envio_reportes_show }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- 🔹 PRÓXIMO CICLO --}}
            @elseif ($estado === 'pendiente' && $cicloSiguiente)
                <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-5 py-2 rounded-xl shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold uppercase tracking-wide text-yellow-600">Próximo ciclo</span>
                        <span class="font-bold text-base">{{ $cicloSiguiente->nombre }}</span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-x-6 gap-y-1 text-sm text-yellow-700">
                        @if ($cicloSiguiente->fecha_inicio_envio_muestras && $cicloSiguiente->fecha_fin_envio_muestras)
                            <span>
                                <span class="font-medium">Muestras:</span>
                                {{ $cicloSiguiente->fecha_inicio_envio_muestras_show }} - {{ $cicloSiguiente->fecha_fin_envio_muestras_show }}
                            </span>
                        @endif
                        @if ($cicloSiguiente->fecha_inicio_envio_resultados && $cicloSiguiente->fecha_fin_envio_resultados)
                            <span>
                                <span class="font-medium">Resultados:</span>
                                {{ $cicloSiguiente->fecha_inicio_envio_resultados_show }} - {{ $cicloSiguiente->fecha_fin_envio_resultados_show }}
                            </span>
                        @endif
                    </div>
                </div>
            @else
                <!-- <div class="bg-gray-100 border border-gray-300 text-gray-700 p-1 rounded-xl shadow-sm">
                    <p class="font-semibold text-lg">
                        ⚪ Todos los ciclos han finalizado o no hay ciclos habilitados.
                    </p>
                </div> -->
            @endif
        </div>


        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre del Formulario</th>
                        @if ($cicloActivo)
                            <th>Completar formulario</th>
                        @endif
                        <th>Guía</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($formularios as $formulario)
                        <tr>
                            <td>{{ $formulario->codigo ?? '-' }}</td>
                            <td>{{ $formulario->nombre ?? '-' }}</td>
                            @if ($cicloActivo)
                                <td>
                                    <a href="{{ route('lab.inscritos-ensayos.formularios.llenar', ['id' => $formulario->id, 'idEA' => $ensayo->id]) }}"
                                        class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Llenar formulario">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </td>
                            @endif
                            <td>
                                <a href="#"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                    data-tippy-content="Ver guía">
                                    <i class="fas fa-book"></i>
                                </a>
                            </td>

                            <td>
                                <a href="#"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded shadow-sm"
                                    data-tippy-content="Ver formulario">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-muted">
                                No hay formularios disponibles para este ensayo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
