<x-app-layout>
    <div class="container py-6 max-w-4xl">
        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Programas</h1>
            <a href="{{ route('programa.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Programa
            </a>
        </div>

        <!-- Tabla -->
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
                    @forelse ($programas as $programa)
                        <tr>
                            <td>{{ $programa->id }}</td>
                            <td>{{ $programa->descripcion }}</td>
                            <td>
                                @if ($programa->status)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('programa.edit', $programa->id) }}"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('programa.destroy', $programa->id) }}"
                                        class="delete-form inline" data-nombre="{{ $programa->descripcion }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" data-tippy-content="Eliminar"
                                            class="delete-button bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-muted">No hay programas registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
