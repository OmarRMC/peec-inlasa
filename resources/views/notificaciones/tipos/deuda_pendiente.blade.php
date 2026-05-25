@php
    $monto = $mensaje['monto'] ?? 0;
    $vencimiento = $mensaje['vencimiento'] ?? null;
@endphp

<div class="space-y-3 text-sm">
    <p class="text-gray-700">
        Tiene un saldo pendiente de pago asociado a su inscripción.
    </p>

    <div class="p-3 bg-red-50 border-l-4 border-red-400">
        <p><strong>Monto adeudado:</strong> Bs. {{ number_format($monto, 2) }}</p>
        @if ($vencimiento)
            <p><strong>Fecha límite:</strong> {{ $vencimiento }}</p>
        @endif
    </div>

    <p class="text-xs text-gray-500">
        Por favor regularice su situación a la brevedad para evitar el bloqueo de servicios.
    </p>
</div>
