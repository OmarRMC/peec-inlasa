@php $d = $c['data']; @endphp
@if (empty($d['filas']) || empty($d['columnas']))
    <p class="text-xs text-gray-500 italic">No hay datos para construir la tabla cruzada.</p>
@else
    <div class="overflow-x-auto">
        <table class="text-xs border-collapse">
            <thead>
                <tr>
                    <th class="px-2 py-1 bg-gray-50 text-left sticky left-0 z-10">
                        {{ $c['x_label'] }} \ {{ $c['y_label'] }}
                    </th>
                    @foreach ($d['columnas'] as $col)
                        <th class="px-3 py-1 bg-gray-50 text-center" title="{{ $col }}">
                            {{ \Illuminate\Support\Str::limit($col, 20) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $max = 0; foreach ($d['celdas'] as $r) foreach ($r as $v) if ($v > $max) $max = $v; @endphp
                @foreach ($d['filas'] as $fila)
                    <tr>
                        <td class="px-2 py-1 bg-white sticky left-0 z-10 font-medium border-r">{{ $fila }}</td>
                        @foreach ($d['columnas'] as $col)
                            @php
                                $v = $d['celdas'][$fila][$col] ?? 0;
                                $intensidad = $max > 0 ? $v / $max : 0;
                                $alpha = $v > 0 ? (0.15 + 0.7 * $intensidad) : 0;
                            @endphp
                            <td class="text-center font-semibold border"
                                style="background: rgba(99,102,241,{{ $alpha }}); padding: 6px;">
                                {{ $v > 0 ? $v : '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
