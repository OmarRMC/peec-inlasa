@php
    use App\Models\Permiso;
@endphp

<x-app-layout>
    <div class="container py-6 max-w-4xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Categorías</h1>
            <a href="{{ route('categoria_laboratorio.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nueva Categoría
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr>
                            <td>{{ $categoria->id }}</td>
                            <td>{{ $categoria->descripcion }}</td>
                            <td>
                                @if ($categoria->status)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('categoria_laboratorio.edit', $categoria->id) }}"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if (Gate::any(Permiso::DELETE_GESTION_PROGRAMAS))
                                        <form method="POST"
                                            action="{{ route('categoria_laboratorio.destroy', $categoria->id) }}"
                                            class="delete-form inline" data-nombre="{{ $categoria->descripcion }}">
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
                            <td colspan="4" class="px-4 py-4 text-center text-muted">No hay categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $categorias->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
