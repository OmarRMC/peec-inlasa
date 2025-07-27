{{-- @props(['showUrl', 'editUrl', 'deleteUrl', 'inscribirUrl', 'nombre', 'id', 'activo' => false]) --}}

<div class="flex space-x-1">
    {{-- Ver --}}
    {{-- <a href="{{ $showUrl }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded shadow-sm"
        data-tippy-content="Ver Detalles">
        <i class="fas fa-eye"></i>
    </a> --}}

    {{-- Editar --}}
    {{-- <a href="{{ $editUrl }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
        data-tippy-content="Editar">
        <i class="fas fa-edit"></i>
    </a> --}}

    {{-- Inscribir a programas (solo si está activo) --}}
    {{-- @if ($activo)
        <a href="{{ $inscribirUrl }}" class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm"
            data-tippy-content="Inscribir a programas">
            <i class="fas fa-clipboard-check"></i>
        </a>
    @endif --}}

    {{-- Eliminar --}}
    {{-- <button class="delete-button bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
        data-id="{{ $id }}" data-nombre="{{ $nombre }}" data-url="{{ $deleteUrl }}"
        data-tippy-content="Eliminar">
        <i class="fas fa-trash-alt"></i>
    </button> --}}
</div>
