@if ($c['data']->isEmpty())
    <p class="text-xs text-gray-500 italic">No hay datos para graficar la combinación.</p>
@else
    <div style="position:relative; height: 320px;">
        <canvas id="chartBoxAgrupado"></canvas>
    </div>
    <div class="overflow-x-auto mt-4">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-600 uppercase">
                <tr>
                    <th class="px-2 py-1 text-left">Categoría</th>
                    <th class="px-2 py-1 text-right">N</th>
                    <th class="px-2 py-1 text-right">Mín</th>
                    <th class="px-2 py-1 text-right">Q1</th>
                    <th class="px-2 py-1 text-right">Mediana</th>
                    <th class="px-2 py-1 text-right">Q3</th>
                    <th class="px-2 py-1 text-right">Máx</th>
                    <th class="px-2 py-1 text-right">Outliers</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($c['data'] as $g)
                    <tr class="border-t">
                        <td class="px-2 py-1 font-medium">{{ $g->categoria }}</td>
                        <td class="px-2 py-1 text-right">{{ $g->n }}</td>
                        <td class="px-2 py-1 text-right font-mono">{{ $g->min }}</td>
                        <td class="px-2 py-1 text-right font-mono">{{ $g->q1 }}</td>
                        <td class="px-2 py-1 text-right font-mono text-indigo-600">{{ $g->mediana }}</td>
                        <td class="px-2 py-1 text-right font-mono">{{ $g->q3 }}</td>
                        <td class="px-2 py-1 text-right font-mono">{{ $g->max }}</td>
                        <td class="px-2 py-1 text-right text-red-600">{{ count($g->outliers) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
