@props(['editUrl', 'deleteUrl', 'nombre', 'id'])

<div class="flex space-x-1">
    <a href="{{ $editUrl }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
        data-tippy-content="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button class="delete-button bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
        data-id="{{ $id }}" data-nombre="{{ $nombre }}" data-url="{{ $deleteUrl }}"
        data-tippy-content="Eliminar">
        <i class="fas fa-trash-alt"></i>
    </button>
</div>
