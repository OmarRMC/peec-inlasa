{{-- Stats numéricos --}}
@if ($descriptivo['n'] === 0)
    <div class="bg-white border rounded p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-2xl mb-2"></i>
        <p>Sin datos numéricos en este ciclo.</p>
    </div>
@else
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">N</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $descriptivo['n'] }}</div>
            <div class="text-[10px] text-gray-400">de {{ $descriptivo['n_total_envios'] }} envíos</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Completitud</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $descriptivo['pct_completitud'] }}%</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Media</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $descriptivo['media'] }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Mediana</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $descriptivo['mediana'] }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">σ</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $descriptivo['desv'] }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">CV%</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $descriptivo['cv_pct'] ?? '-' }}</div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4 text-xs">
        <div class="bg-white border rounded p-2">
            <div class="text-gray-500 uppercase text-[10px]">Mín</div>
            <div class="font-semibold text-gray-800">{{ $descriptivo['minimo'] }}</div>
        </div>
        <div class="bg-white border rounded p-2">
            <div class="text-gray-500 uppercase text-[10px]">Q1</div>
            <div class="font-semibold text-gray-800">{{ $descriptivo['q1'] }}</div>
        </div>
        <div class="bg-white border rounded p-2">
            <div class="text-gray-500 uppercase text-[10px]">Q3</div>
            <div class="font-semibold text-gray-800">{{ $descriptivo['q3'] }}</div>
        </div>
        <div class="bg-white border rounded p-2">
            <div class="text-gray-500 uppercase text-[10px]">Máx</div>
            <div class="font-semibold text-gray-800">{{ $descriptivo['maximo'] }}</div>
        </div>
        <div class="bg-white border rounded p-2">
            <div class="text-gray-500 uppercase text-[10px]">IQR</div>
            <div class="font-semibold text-gray-800">{{ $descriptivo['iqr'] }}</div>
        </div>
    </div>

    {{-- Histograma --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        <div class="bg-white border rounded p-4 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Distribución (histograma)</h3>
            <div style="position:relative; height: 300px;">
                <canvas id="chartHistograma"></canvas>
            </div>
        </div>

        {{-- Box-plot textual --}}
        <div class="bg-white border rounded p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Box-plot (resumen IQR)</h3>
            @php
                $bp = $boxplot;
                $rango = ($bp['max'] - $bp['min']) ?: 1;
            @endphp
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span>Mín:</span><strong>{{ $bp['min'] }}</strong></div>
                <div class="flex justify-between"><span>Whisker bajo:</span><strong>{{ $bp['whisker_min'] }}</strong></div>
                <div class="flex justify-between"><span>Q1:</span><strong>{{ $bp['q1'] }}</strong></div>
                <div class="flex justify-between"><span>Mediana:</span><strong class="text-indigo-600">{{ $bp['mediana'] }}</strong></div>
                <div class="flex justify-between"><span>Q3:</span><strong>{{ $bp['q3'] }}</strong></div>
                <div class="flex justify-between"><span>Whisker alto:</span><strong>{{ $bp['whisker_max'] }}</strong></div>
                <div class="flex justify-between"><span>Máx:</span><strong>{{ $bp['max'] }}</strong></div>
                <div class="flex justify-between pt-2 border-t">
                    <span>Outliers:</span>
                    <strong class="text-red-600">{{ count($bp['outliers']) }}</strong>
                </div>
                @if (count($bp['outliers']) > 0)
                    <div class="text-[10px] text-gray-500 break-words">
                        {{ implode(', ', array_slice($bp['outliers'], 0, 20)) }}
                        @if (count($bp['outliers']) > 20)…@endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabla de valores por lab --}}
    <div class="bg-white border rounded p-4 mb-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Valores enviados por laboratorio ({{ $valoresLab->count() }})</h3>
        <div class="overflow-auto border rounded" style="max-height: 480px;">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-2 py-2 text-left bg-gray-50">Código</th>
                        <th class="px-2 py-2 text-left bg-gray-50">Laboratorio</th>
                        <th class="px-2 py-2 text-right bg-gray-50">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($valoresLab as $v)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-2 py-1 font-mono whitespace-nowrap">{{ $v->cod_lab ?? '-' }}</td>
                            <td class="px-2 py-1 whitespace-nowrap">{{ $v->nombre_lab }}</td>
                            <td class="px-2 py-1 text-right font-mono">{{ $v->valor }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
