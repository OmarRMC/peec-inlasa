@php $d = $c['data']; $tipo = $c['tipo_x'] ?? null; @endphp

@if (!empty($d['vacio_por_filtro']))
    <p class="text-xs text-amber-700 bg-amber-50 px-3 py-2 rounded">
        El filtro seleccionado no devuelve respuestas para este campo.
    </p>
@elseif (in_array($tipo, ['numerico', 'categorico']))
    <div style="position:relative; height: 300px;">
        <canvas id="chartUnivariado"></canvas>
    </div>
    @if ($tipo === 'numerico' && !empty($d['stats']))
        <div class="mt-3 grid grid-cols-3 md:grid-cols-6 gap-2 text-xs">
            <div class="bg-gray-50 p-2 rounded"><div class="text-gray-500">N</div><div class="font-bold">{{ $d['stats']['n'] }}</div></div>
            <div class="bg-gray-50 p-2 rounded"><div class="text-gray-500">Media</div><div class="font-bold">{{ $d['stats']['media'] }}</div></div>
            <div class="bg-gray-50 p-2 rounded"><div class="text-gray-500">Mediana</div><div class="font-bold">{{ $d['stats']['mediana'] }}</div></div>
            <div class="bg-gray-50 p-2 rounded"><div class="text-gray-500">σ</div><div class="font-bold">{{ $d['stats']['desv'] }}</div></div>
            <div class="bg-gray-50 p-2 rounded"><div class="text-gray-500">Mín</div><div class="font-bold">{{ $d['stats']['min'] }}</div></div>
            <div class="bg-gray-50 p-2 rounded"><div class="text-gray-500">Máx</div><div class="font-bold">{{ $d['stats']['max'] }}</div></div>
        </div>
    @endif
@elseif ($tipo === 'booleano' && !empty($d['booleano']))
    <table class="w-full text-xs">
        <tr><td class="py-1">Marcado</td><td class="text-right font-bold text-green-600">{{ $d['booleano']['marcado'] }} ({{ $d['booleano']['pct_marcado'] }}%)</td></tr>
        <tr><td class="py-1">No marcado</td><td class="text-right font-bold text-red-600">{{ $d['booleano']['no_marcado'] }}</td></tr>
        <tr class="border-t"><td class="py-1">Total</td><td class="text-right">{{ $d['booleano']['n'] }}</td></tr>
    </table>
@elseif ($tipo === 'fecha' && !empty($d['fechas']))
    <table class="w-full text-xs">
        <thead class="bg-gray-50"><tr><th class="px-2 py-1 text-left">Período</th><th class="px-2 py-1 text-right">Cantidad</th></tr></thead>
        <tbody>
            @foreach ($d['fechas'] as $f)
                <tr class="border-t"><td class="px-2 py-1">{{ $f->periodo }}</td><td class="px-2 py-1 text-right">{{ $f->count }}</td></tr>
            @endforeach
        </tbody>
    </table>
@elseif (!empty($d['top']))
    <table class="w-full text-xs">
        <thead class="bg-gray-50"><tr><th class="px-2 py-1 text-left">Valor</th><th class="px-2 py-1 text-right">Cantidad</th><th class="px-2 py-1 text-right">%</th></tr></thead>
        <tbody>
            @foreach ($d['top'] as $t)
                <tr class="border-t"><td class="px-2 py-1">{{ $t->valor }}</td><td class="px-2 py-1 text-right">{{ $t->count }}</td><td class="px-2 py-1 text-right">{{ $t->pct }}%</td></tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-xs text-gray-500 italic">Sin datos para esta combinación.</p>
@endif
