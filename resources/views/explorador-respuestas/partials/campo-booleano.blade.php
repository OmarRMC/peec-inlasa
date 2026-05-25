@if ($booleano['n'] === 0)
    <div class="bg-white border rounded p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-2xl mb-2"></i>
        <p>Sin datos en este ciclo.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Total respuestas</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $booleano['n'] }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Marcado</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $booleano['marcado'] }}</div>
            <div class="text-xs text-gray-500">{{ $booleano['pct_marcado'] }}%</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">No marcado</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ $booleano['no_marcado'] }}</div>
            <div class="text-xs text-gray-500">{{ 100 - $booleano['pct_marcado'] }}%</div>
        </div>
    </div>

    <div class="bg-white border rounded p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Distribución marcado / no marcado</h3>
        <div style="position:relative; height: 300px;">
            <canvas id="chartBooleano"></canvas>
        </div>
    </div>
@endif
