@php
    $url = $mensaje['url_descarga'] ?? null;
    $ensayo = $mensaje['ensayo'] ?? null;
@endphp

<div class="space-y-3 text-sm">
    <p class="text-gray-700">
        Su certificado @if ($ensayo) del ensayo <strong>{{ $ensayo }}</strong> @endif ya se encuentra disponible para descarga.
    </p>

    @if ($url)
        <a href="{{ $url }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            <i class="fas fa-download"></i> Descargar certificado
        </a>
    @endif
</div>
