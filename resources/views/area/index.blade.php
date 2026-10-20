@php
    use App\Models\Permiso;
@endphp
<x-app-layout>
    <div class="container py-6 max-w-5xl">
        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4 mb-4">
            <h1 class="text-xl font-bold text-primary">Lista de Áreas</h1>
            <div class="flex items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('area.index') }}" class="flex items-center gap-2">
                    <div class="relative w-56">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Buscar área..."
                            class="w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs">
                    </div>
                    <button type="submit"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                        <i class="fas fa-search"></i>
                    </button>
                    @if ($search)
                        <a href="{{ route('area.index') }}"
                            class="px-3 py-1.5 border border-gray-300 text-gray-600 rounded hover:bg-gray-50 transition shadow text-xs">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('area.create') }}"
                    class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i> Nueva Área
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        @if($verIds)<th>Nro</th>@endif
                        <th>Programa</th>
                        <th>Area</th>
                        {{-- <th>Máx. Paquetes</th> --}}
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($areas as $area)
                        <tr>
                            @if($verIds)<td>{{ $area->id }}</td>@endif
                            <td>{{ $area->programa->descripcion ?? 'N/D' }}</td>
                            <td>{{ $area->descripcion }}</td>
                            {{-- <td>{{ $area->max_paquetes_inscribir }}</td> --}}
                            <td>
                                @if ($area->status)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('area.edit', $area->id) }}"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Editar"
                                        >
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if (Gate::any(Permiso::DELETE_GESTION_PROGRAMAS))
                                        <form method="POST" action="{{ route('area.destroy', $area->id) }}"
                                            class="delete-form inline" data-nombre="{{ $area->descripcion }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" data-tippy-content="Eliminar"
                                                class="delete-button bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-muted">No hay áreas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $areas->links() }}
        </div>
    </div>
</x-app-layout>
