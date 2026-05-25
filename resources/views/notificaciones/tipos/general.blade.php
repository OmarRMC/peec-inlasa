@php
    $html = $mensaje['html'] ?? null;
    $texto = $mensaje['texto'] ?? null;
@endphp

<div class="text-sm text-gray-700">
    @if ($html)
        {!! $html !!}
    @elseif ($texto)
        {{ $texto }}
    @else
        @foreach (($mensaje ?? []) as $clave => $valor)
            @if (is_scalar($valor) && $clave !== 'titulo')
                <p><strong>{{ ucfirst($clave) }}:</strong> {{ $valor }}</p>
            @endif
        @endforeach
    @endif
</div>
