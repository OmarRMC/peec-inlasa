@if ($topValores->isEmpty())
    <div class="bg-white border rounded p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-2xl mb-2"></i>
        <p>Sin respuestas de texto en este ciclo.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Valores distintos</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $distintos }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Top valor</div>
            <div class="text-sm font-bold text-gray-800 mt-1 truncate" title="{{ $topValores->first()->valor }}">
                {{ $topValores->first()->valor }}
            </div>
            <div class="text-xs text-gray-500">{{ $topValores->first()->count }} veces ({{ $topValores->first()->pct }}%)</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Longitud promedio (top)</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">{{ $topValores->first()->longitud_promedio }}</div>
            <div class="text-xs text-gray-500">caracteres</div>
        </div>
    </div>

    <div class="bg-white border rounded p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Top valores más frecuentes</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase">
                    <tr>
                        <th class="px-2 py-1 text-left">Valor</th>
                        <th class="px-2 py-1 text-right">Cantidad</th>
                        <th class="px-2 py-1 text-right">%</th>
                        <th class="px-2 py-1 text-right">Long. prom.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topValores as $t)
                        <tr class="border-t">
                            <td class="px-2 py-1 break-words">{{ $t->valor }}</td>
                            <td class="px-2 py-1 text-right font-mono">{{ $t->count }}</td>
                            <td class="px-2 py-1 text-right font-mono">{{ $t->pct }}%</td>
                            <td class="px-2 py-1 text-right font-mono">{{ $t->longitud_promedio }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
