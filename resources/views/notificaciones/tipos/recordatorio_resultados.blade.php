@php
    $fase = $mensaje['fase'] ?? 'apertura';
    $ensayo = $mensaje['ensayo'] ?? null;
    $ciclo = $mensaje['ciclo'] ?? null;
    $fechaLimite = $mensaje['fecha_limite'] ?? null;
    $esFinal = $fase === 'final';
@endphp

<div class="space-y-3">
    <p class="text-sm text-gray-700">
        @if ($esFinal)
            El plazo para enviar sus resultados está por cerrar. Por favor registre sus resultados antes de la fecha límite.
        @else
            Ya está habilitado el envío de resultados. Le recordamos registrar sus resultados dentro del plazo establecido.
        @endif
    </p>

    <ul class="text-sm text-gray-700 space-y-1">
        @if ($ensayo)
            <li><strong class="text-gray-800">Ensayo de aptitud:</strong> {{ $ensayo }}</li>
        @endif
        @if ($ciclo)
            <li><strong class="text-gray-800">Ciclo:</strong> {{ $ciclo }}</li>
        @endif
        @if ($fechaLimite)
            <li><strong class="text-gray-800">Fecha límite de envío:</strong> {{ formatDate($fechaLimite, false) }}</li>
        @endif
    </ul>

    @if ($esFinal)
        <div class="mt-3 p-3 bg-red-50 border-l-4 border-red-400 text-sm text-gray-700">
            <strong>Atención:</strong> el periodo de envío de resultados está próximo a finalizar.
        </div>
    @endif
</div>
