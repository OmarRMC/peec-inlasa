<x-app-layout>
    <div class="container py-6 max-w-6xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Laboratorios</h1>
            <a href="{{ route('laboratorio.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Laboratorio
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Responsable</th>
                        <th>País</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laboratorios as $laboratorio)
                        <tr>
                            <td>{{ $laboratorio->id }}</td>
                            <td>{{ $laboratorio->nombre_lab }}</td>
                            <td>{{ $laboratorio->cod_lab }}</td>
                            <td>{{ $laboratorio->respo_lab }}</td>
                            <td>{{ $laboratorio->pais->nombre ?? '-' }}</td>
                            <td>
                                @if ($laboratorio->status)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('laboratorio.edit', $laboratorio->id) }}"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('laboratorio.destroy', $laboratorio->id) }}"
                                        class="delete-form inline" data-nombre="{{ $laboratorio->nombre_lab }}">
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
                            <td colspan="7" class="px-4 py-4 text-center text-muted">No hay laboratorios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{-- {{ $laboratorios->links() }} --}}
        </div>
    </div>
</x-app-layout>
