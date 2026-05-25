@if ($c['data']->isEmpty())
    <p class="text-xs text-gray-500 italic">No hay laboratorios con ambos campos completos para graficar.</p>
@else
    <div style="position:relative; height: 380px;">
        <canvas id="chartScatter"></canvas>
    </div>
    <div class="mt-3 text-xs text-gray-500">
        <i class="fas fa-info-circle"></i>
        {{ $c['data']->count() }} laboratorio(s) reportaron ambos valores.
    </div>
@endif
