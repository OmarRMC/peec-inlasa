<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        {{-- Breadcrumb / header --}}
        <div class="mb-4">
            <nav class="text-xs text-gray-500 mb-1">
                <a href="{{ route('reportes.resultados.ensayos') }}" class="hover:underline">Reporte de ensayos</a>
                <span class="mx-1">/</span>
                <a href="{{ route('explorador-respuestas.ensayo', $ensayo->id) }}" class="hover:underline">{{ $ensayo->descripcion }}</a>
                <span class="mx-1">/</span>
                <span>{{ $ensayo->paquete->descripcion ?? 'Sin paquete' }}</span>
                <span class="mx-1">/</span>
                <span class="text-gray-700">{{ $ensayo->descripcion }}</span>
            </nav>
            <h1 class="text-xl font-bold text-gray-800">
                <i class="fas fa-file-lines text-indigo-500 mr-1"></i>
                {{ $formulario->nombre }}
            </h1>
        </div>

        {{-- Selectores --}}
        <div class="flex flex-wrap items-center gap-2 mb-4 text-sm">
            <label class="whitespace-nowrap text-gray-700 font-medium">Ciclo:</label>
            <select onchange="window.location.href = '{{ route('explorador-respuestas.formulario', [$ensayo->id, '__CICLO__', $formulario->id]) }}'.replace('__CICLO__', this.value)"
                class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[200px]">
                @foreach ($ciclos as $c)
                    <option value="{{ $c->id }}" @if ($c->id == $ciclo->id) selected @endif>
                        {{ $c->nombre }} ({{ $c->gestion }})
                    </option>
                @endforeach
            </select>

            @if ($formularios->count() > 1)
                <label class="whitespace-nowrap text-gray-700 font-medium ml-2">Formulario:</label>
                <select onchange="window.location.href = '{{ route('explorador-respuestas.formulario', [$ensayo->id, $ciclo->id, '__FORM__']) }}'.replace('__FORM__', this.value)"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[200px]">
                    @foreach ($formularios as $f)
                        <option value="{{ $f->id }}" @if ($f->id == $formulario->id) selected @endif>
                            {{ $f->nombre }}
                        </option>
                    @endforeach
                </select>
            @endif

            <div class="ml-auto flex gap-2">
                <a href="{{ route('explorador-respuestas.formulario.export', [$ensayo->id, $ciclo->id, $formulario->id]) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>
                <a href="{{ $backUrl }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
            <div class="bg-white border rounded p-3">
                <div class="text-[10px] text-gray-500 uppercase">Envíos del lab</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $nEnvios }}</div>
            </div>
            <div class="bg-white border rounded p-3">
                <div class="text-[10px] text-gray-500 uppercase">Parámetros</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $nParametros }}</div>
            </div>
            <div class="bg-white border rounded p-3">
                <div class="text-[10px] text-gray-500 uppercase">Campos totales</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $nCampos }}</div>
            </div>
            <div class="bg-white border rounded p-3">
                <div class="text-[10px] text-gray-500 uppercase">Completitud promedio</div>
                <div class="text-2xl font-bold text-green-600 mt-1">{{ $pctPromedio }}%</div>
            </div>
            <div class="bg-white border rounded p-3">
                <div class="text-[10px] text-gray-500 uppercase">Ciclo</div>
                <div class="text-sm font-semibold text-gray-700 mt-1">{{ $ciclo->nombre }}</div>
                <div class="text-xs text-gray-400">Gestión {{ $ciclo->gestion }}</div>
            </div>
        </div>

        {{-- Bar chart de completitud por parámetro --}}
        @if ($completitudParametros->isNotEmpty() && $nEnvios > 0)
            <div class="bg-white border rounded p-4 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Completitud por parámetro</h3>
                <p class="text-xs text-gray-500 mb-3">
                    Porcentaje promedio de campos del parámetro respondidos a través de todos los envíos
                    (respuestas reales / respuestas esperadas).
                </p>
                <div style="position:relative; height:{{ max(220, $completitudParametros->count() * 24) }}px;">
                    <canvas id="chartCompletitud"></canvas>
                </div>
            </div>
        @endif

        {{-- Grilla de parámetros (entrada principal del drill-down) --}}
        @if ($parametros->isNotEmpty())
            @php
                $porSeccion = $parametros->groupBy('seccion');
            @endphp
            @foreach ($porSeccion as $seccionNombre => $items)
                <div class="bg-white border rounded p-4 mb-4">
                    <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase border-b pb-1">
                        {{ $seccionNombre }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach ($items as $param)
                            <a href="{{ route('explorador-respuestas.parametro', [$ensayo->id, $ciclo->id, $param->id_parametro]) }}"
                                class="block border rounded p-3 hover:bg-indigo-50 hover:border-indigo-300 transition">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-bold text-gray-800 leading-tight">
                                            <i class="fas fa-cubes text-indigo-400 mr-1"></i>
                                            {{ $param->nombre }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">
                                            {{ $param->n_campos }} campo(s)
                                        </div>
                                    </div>
                                </div>
                                <ul class="space-y-1">
                                    @foreach ($param->campos as $c)
                                        @php
                                            $badge = match ($c->tipo_descriptivo) {
                                                'numerico'   => ['bg' => 'bg-blue-100', 'tx' => 'text-blue-700', 'ic' => 'fa-hashtag'],
                                                'categorico' => ['bg' => 'bg-purple-100', 'tx' => 'text-purple-700', 'ic' => 'fa-list'],
                                                'booleano'   => ['bg' => 'bg-amber-100', 'tx' => 'text-amber-700', 'ic' => 'fa-check-square'],
                                                'fecha'      => ['bg' => 'bg-teal-100', 'tx' => 'text-teal-700', 'ic' => 'fa-calendar'],
                                                default      => ['bg' => 'bg-gray-100', 'tx' => 'text-gray-700', 'ic' => 'fa-font'],
                                            };
                                        @endphp
                                        <li class="flex items-start justify-between gap-2 text-xs border-l-2 border-gray-200 pl-2 py-1">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-gray-800 font-medium truncate">{{ $c->label }}</div>
                                                <div class="text-gray-500 mt-0.5">{{ $c->snippet }}</div>
                                            </div>
                                            <span class="text-[9px] {{ $badge['bg'] }} {{ $badge['tx'] }} px-1.5 py-0.5 rounded uppercase whitespace-nowrap shrink-0">
                                                <i class="fas {{ $badge['ic'] }}"></i> {{ $c->tipo_descriptivo }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-white border rounded p-6 text-center text-gray-500">
                <p>El formulario no tiene parámetros.</p>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($completitudParametros->isNotEmpty() && $nEnvios > 0)
        (function () {
            const items = @json($completitudParametros->all());
            const labels = items.map(i => i.nombre.length > 50 ? i.nombre.slice(0, 50) + '…' : i.nombre);
            new Chart(document.getElementById('chartCompletitud'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '% Completitud',
                        data: items.map(i => i.pct_completitud),
                        backgroundColor: items.map(i => {
                            if (i.pct_completitud >= 80) return 'rgba(34,197,94,0.85)';
                            if (i.pct_completitud >= 50) return 'rgba(234,179,8,0.85)';
                            return 'rgba(239,68,68,0.85)';
                        }),
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                afterLabel: ctx => {
                                    const i = items[ctx.dataIndex];
                                    return [
                                        'Campos del parámetro: ' + i.n_campos,
                                        'Respuestas: ' + i.n_respuestas + ' / ' + i.n_respuestas_esperadas,
                                        'Envíos: ' + i.n_envios
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, max: 100, title: { display: true, text: '%' }},
                        y: { ticks: { font: { size: 9 }, autoSkip: false }}
                    }
                }
            });
        })();
        @endif
    });
    </script>
    @endpush
</x-app-layout>
