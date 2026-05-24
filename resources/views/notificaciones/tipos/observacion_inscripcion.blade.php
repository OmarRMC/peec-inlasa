@php
    $documentos = $mensaje['documentos'] ?? [];
    $comentario = $mensaje['comentario'] ?? null;
@endphp

<div class="space-y-3">
    <p class="text-sm text-gray-600">
        Se han registrado observaciones en los siguientes documentos de su inscripción.
        Por favor revíselos y suba las correcciones para continuar con el proceso.
    </p>

    @if (count($documentos))
        <ol class="list-decimal list-inside space-y-2 text-sm">
            @foreach ($documentos as $documento => $observacion)
                <li>
                    <strong class="text-gray-800">{{ $documento }}:</strong>
                    <span class="text-gray-700">{{ $observacion }}</span>
                </li>
            @endforeach
        </ol>
    @endif

    @if ($comentario)
        <div class="mt-3 p-3 bg-yellow-50 border-l-4 border-yellow-400 text-sm text-gray-700">
            <strong>Comentario:</strong> {{ $comentario }}
        </div>
    @endif
</div>
