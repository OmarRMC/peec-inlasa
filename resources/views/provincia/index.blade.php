<x-app-layout>
    <div class="container py-6 max-w-4xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Provincias</h1>
            <a href="{{ route('provincia.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nueva Provincia
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Departamento</th>
                        <th>Provincia</th>
                        <th>Código</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($provincias as $prov)
                        <tr>
                            <td>{{ $prov->id }}</td>
                            <td>{{ $prov->departamento->nombre_dep ?? 'N/D' }}</td>
                            <td>{{ $prov->nombre_prov }}</td>
                            <td>{{ $prov->cod_prov }}</td>
                            <td>
                                @if ($prov->status_prov)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('provincia.edit', $prov->id) }}" data-tippy-content="Editar"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('provincia.destroy', $prov->id) }}"
                                        class="delete-form inline" data-nombre="{{ $prov->nombre_prov }}">
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
                            <td colspan="6" class="text-center text-muted py-4">No hay provincias registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
