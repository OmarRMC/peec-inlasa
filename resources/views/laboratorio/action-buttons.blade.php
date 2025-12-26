@php
    use App\Models\Permiso;
@endphp
@props(['showUrl', 'editUrl', 'deleteUrl', 'inscribirUrl', 'nombre', 'id', 'activo' => false, 'tiene_descuento'])

<div class="flex space-x-1">
    {{-- Ver --}}
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO]))
        <a href="{{ $showUrl }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded shadow-sm"
            data-tippy-content="Ver Detalles">
            <i class="fas fa-eye"></i>
        </a>
    @endif

    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO]))
        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded shadow-sm toggle-descuento-btn"
            data-id="{{ $id }}"
            data-tippy-content="{{ $tiene_descuento ? 'Tiene descuento' : 'No tiene descuento' }}">
            @if ($tiene_descuento)
                <i class="fas fa-tags text-green-600"></i>
            @else
                <i class="fas fa-ban text-red-600"></i>
            @endif
        </button>
    @endif


    {{-- Editar --}}
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO]))
        <a href="{{ $editUrl }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
            data-tippy-content="Editar">
            <i class="fas fa-edit"></i>
        </a>
    @endif

    {{-- Inscribir a programas (solo si está activo) --}}
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
        @if ($activo)
            <a href="{{ $inscribirUrl }}"
                class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm"
                data-tippy-content="Inscribir a programas">
                <i class="fas fa-clipboard-check"></i>
            </a>
        @endif
    @endif

    {{-- Eliminar --}}
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO]))
        <button class="delete-button bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
            data-id="{{ $id }}" data-nombre="{{ $nombre }}" data-url="{{ $deleteUrl }}"
            data-tippy-content="Eliminar">
            <i class="fas fa-trash-alt"></i>
        </button>
    @endif
</div>
