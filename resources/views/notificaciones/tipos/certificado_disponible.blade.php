@php
    $url = $mensaje['url_descarga'] ?? null;
    $ensayo = $mensaje['ensayo'] ?? null;
    $gestion = $mensaje['gestion'] ?? null;
@endphp

<div class="space-y-3 text-sm">
    <p class="text-gray-700">
        Su certificado
        @if ($ensayo)
            del ensayo <strong>{{ $ensayo }}</strong>
        @elseif ($gestion)
            de la gestión <strong>{{ $gestion }}</strong>
        @endif
        ya se encuentra disponible para descarga.
    </p>

    @if ($url)
        <a href="{{ $url }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            <i class="fas fa-download"></i> Ver mis certificados
        </a>
    @endif
</div>
