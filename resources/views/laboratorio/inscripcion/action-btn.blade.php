@props(['showUrl', 'boletaPdf', 'contratoPdf', 'inscripcion'])

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

    @if (!$inscripcion->estaAnulado() && $inscripcion->estaEnRevision())
        <form method="POST" class="anular-inscripcion" action="{{ route('inscripciones.anular', $inscripcion->id) }}">
            @csrf
            @method('PUT')
            <button type="submit"
                class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded shadow-sm"
                data-tippy-content="Anular inscripción">
                <i class="fas fa-ban"></i>
            </button>
        </form>
    @endif

</div>
