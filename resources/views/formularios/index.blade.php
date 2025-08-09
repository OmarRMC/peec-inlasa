<x-app-layout>
    <div class="container py-6 max-w-4xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Formularios</h1>
            <a href="{{ route('formularios.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Formulario
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Version</th>
                        <th>Proceso</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($formularios as $form)
                        <tr>
                            <td>{{ $form->id }}</td>
                            <td>{{ $form->codigo }}</td>
                            <td>{{ $form->version }}</td>
                            <td>{{ $form->proceso }}</td>
                            <td>{{ $form->titulo }}</td>
                            {{-- <td>{{ $form->fec_formulario ? $form->fec_formulario->format('d/m/Y H:i') : 'N/D' }}</td> --}}
                            <td>{{ $form->fec_formulario }}</td>
                            <td>
                                @if ($form->status)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('formularios.edit', $form->id) }}" data-tippy-content="Editar"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('formularios.destroy', $form->id) }}"
                                        class="delete-form inline" data-nombre="{{ $form->titulo }}">
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
                            <td colspan="7" class="text-center text-muted py-4">No hay formularios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
