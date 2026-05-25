@if ($fechas->isEmpty())
    <div class="bg-white border rounded p-6 text-center text-gray-500">
        <i class="fas fa-inbox text-2xl mb-2"></i>
        <p>Sin fechas registradas en este ciclo.</p>
    </div>
@else
    @php
        $total = $fechas->sum('count');
        $primero = $fechas->first();
        $ultimo = $fechas->last();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Total fechas</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $total }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Período mínimo</div>
            <div class="text-sm font-bold text-gray-800 mt-1">{{ $primero->periodo }}</div>
        </div>
        <div class="bg-white border rounded p-3">
            <div class="text-[10px] text-gray-500 uppercase">Período máximo</div>
            <div class="text-sm font-bold text-gray-800 mt-1">{{ $ultimo->periodo }}</div>
        </div>
    </div>

    <div class="bg-white border rounded p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Distribución temporal por mes</h3>
        <div style="position:relative; height: 300px;">
            <canvas id="chartFechas"></canvas>
        </div>
    </div>
@endif
