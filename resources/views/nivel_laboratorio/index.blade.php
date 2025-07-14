<x-app-layout>
    <div class="container py-6 max-w-4xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Niveles de Laboratorio</h1>
            <a href="{{ route('nivel_laboratorio.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Nivel
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($niveles as $nivel)
                        <tr>
                            <td>{{ $nivel->id }}</td>
                            <td>{{ $nivel->descripcion_nivel }}</td>
                            <td>
                                @if ($nivel->status_nivel)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('nivel_laboratorio.edit', $nivel->id) }}"
                                        data-tippy-content="Editar"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('nivel_laboratorio.destroy', $nivel->id) }}"
                                        class="inline delete-form" data-nombre="{{ $nivel->descripcion_nivel }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" data-tippy-content="Eliminar"
                                            class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm delete-button">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay niveles registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
