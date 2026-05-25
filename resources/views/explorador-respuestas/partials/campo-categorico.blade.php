@if ($frecuencias->isEmpty())
    <div class="bg-white border rounded p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-2xl mb-2"></i>
        <p>Sin respuestas en este ciclo.</p>
    </div>
@else
    @php
        $total = $frecuencias->sum('count');
        $moda = $frecuencias->first();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Total respuestas</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $total }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Opciones distintas</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $frecuencias->count() }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Moda</div>
            <div class="text-sm font-bold text-gray-800 mt-1 truncate" title="{{ $moda->etiqueta }}">
                {{ $moda->etiqueta }}
            </div>
            <div class="text-xs text-gray-500">{{ $moda->count }} ({{ $moda->pct }}%)</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border rounded p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Frecuencia por opción</h3>
            <div style="position:relative; height:{{ max(200, $frecuencias->count() * 26) }}px;">
                <canvas id="chartCategorico"></canvas>
            </div>
        </div>

        <div class="bg-white border rounded p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Tabla de frecuencias</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase">
                        <tr>
                            <th class="px-2 py-1 text-left">Opción</th>
                            <th class="px-2 py-1 text-right">Cantidad</th>
                            <th class="px-2 py-1 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($frecuencias as $f)
                            <tr class="border-t">
                                <td class="px-2 py-1">{{ $f->etiqueta }}</td>
                                <td class="px-2 py-1 text-right font-mono">{{ $f->count }}</td>
                                <td class="px-2 py-1 text-right font-mono">{{ $f->pct }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
