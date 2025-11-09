<x-app-layout>
    <div class="container py-6 max-w-5xl">
        <x-shared.btn-volver :url="route('lab.inscritos-ensayos.index')" />
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Formularios del Ensayo: {{ $ensayo->descripcion }}</h1>
        </div>
        @php
            dump($ensayo->id);
        @endphp
        <div class="mb-6">
            @if ($estado === 'activo' && $cicloActivo)
                <div class="bg-green-100 border border-green-300 text-green-800 px-5 py-4 rounded-xl shadow-sm">
                    <p class="font-semibold text-lg">🟢 Ciclo activo</p>
                    <p class="mt-1 text-sm text-green-700">
                        <span class="font-medium">Número:</span> {{ $cicloActivo->numero }}
                    </p>

                    @if ($cicloActivo->fecha_inicio_envio_reportes && $cicloActivo->fecha_fin_envio_reportes)
                        <p class="mt-1 text-sm">
                            <span class="font-medium">Envío de Reportes:</span>
                            {{ $cicloActivo->fecha_inicio_envio_reportes }}
                            –
                            {{ $cicloActivo->fecha_fin_envio_reportes }}
                        </p>
                    @endif

                    {{-- Rango de Envío de Resultados --}}
                    @if ($cicloActivo->fecha_inicio_envio_resultados && $cicloActivo->fecha_fin_envio_resultados)
                        <p class="mt-1 text-sm">
                            <span class="font-medium">Envío de Resultados:</span>
                            {{ $cicloActivo->fecha_inicio_envio_resultados }}
                            –
                            {{ $cicloActivo->fecha_fin_envio_resultados }}
                        </p>
                    @endif
                </div>

                {{-- 🔹 PRÓXIMO CICLO --}}
            @elseif ($estado === 'pendiente' && $cicloSiguiente)
                <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-5 py-4 rounded-xl shadow-sm">
                    <p class="font-semibold text-lg">🟡 Próximo ciclo</p>
                    <p class="mt-1 text-sm text-yellow-700">
                        <span class="font-medium">Número:</span> {{ $cicloSiguiente->numero }}
                    </p>
                    @if ($cicloSiguiente->fecha_inicio_envio_muestras && $cicloSiguiente->fecha_fin_envio_muestras)
                        <p class="mt-1 text-sm">
                            <span class="font-medium">Envío de Muestras:</span>
                            {{ $cicloSiguiente->fecha_inicio_envio_muestras }}
                            –
                            {{ $cicloSiguiente->fecha_fin_envio_muestras }}
                        </p>
                    @endif
                    @if ($cicloSiguiente->fecha_inicio_envio_resultados && $cicloSiguiente->fecha_fin_envio_resultados)
                        <p class="mt-1 text-sm">
                            <span class="font-medium">Envío de Resultados:</span>
                            {{ $cicloSiguiente->fecha_inicio_envio_resultados }}
                            –
                            {{ $cicloSiguiente->fecha_fin_envio_resultados }}
                        </p>
                    @endif
                </div>
            @else
                <div class="bg-gray-100 border border-gray-300 text-gray-700 p-1 rounded-xl shadow-sm">
                    <p class="font-semibold text-lg">
                        ⚪ Todos los ciclos han finalizado o no hay ciclos habilitados.
                    </p>
                </div>
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
