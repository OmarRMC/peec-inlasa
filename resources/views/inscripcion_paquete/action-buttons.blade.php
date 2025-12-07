@php
    use App\Models\Permiso;
@endphp
@props(['showUrl', 'boletaPdf', 'contratoPdf', 'docPagosUrl', 'tieneDocPagoPendiente'])

<div class="flex space-x-1">
    {{-- Ver --}}
    <a href="{{ $showUrl }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded shadow-sm"
        data-tippy-content="Ver Detalles">
        <i class="fas fa-eye"></i>
    </a>
    {{-- Boleta PDF --}}
    <a href="{{ $boletaPdf }}" target="_blank"
        class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
        data-tippy-content="Imprimir el formulario de Inscripción">
        <i class="fas fa-file-pdf"></i>
    </a>

    <a href="{{ $contratoPdf }}" target="_blank"
        class="bg-blue-100 hover:bg-blue-200 text-blue-700 pl-2 pr-2 py-1 rounded shadow-sm"
        data-tippy-content="Generar contrato de inscripción">
        <i class="fas fa-file-signature"></i>
    </a>
    @if (isset($tieneDocPagoPendiente) && $tieneDocPagoPendiente && Gate::any([Permiso::ADMIN, Permiso::GESTION_PAGOS]))
        @if ($tieneDocPagoPendiente)
            <a href="{{ $docPagosUrl }}" target="_blank"
                class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm"
                data-tippy-content="El documento de pago está pendiente de revisión">
                <i class="fas fa-file-invoice-dollar"></i>
            </a>
        @endif
    @endif
</div>
