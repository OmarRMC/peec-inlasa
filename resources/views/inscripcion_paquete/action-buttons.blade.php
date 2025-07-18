@props(['showUrl'])

<div class="flex space-x-1">
    {{-- Ver --}}
    <a href="{{ $showUrl }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded shadow-sm"
        data-tippy-content="Ver Detalles">
        <i class="fas fa-eye"></i>
    </a>
    
</div>
