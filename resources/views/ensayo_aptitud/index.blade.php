@php
    use App\Models\Permiso;
@endphp
<x-app-layout>
    <div class="container py-6 max-w-4xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Ensayos de Aptitud</h1>
            <a href="{{ route('ensayo_aptitud.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Ensayo
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Area</th>
                        <th>Paquete</th>
                        <th>Ensayo A.</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ensayos as $ensayo)
                        <tr>
                            <td>{{ $ensayo->id }}</td>
                            <td>{{ $ensayo->paquete->area->descripcion ?? 'N/D' }}</td>
                            <td>{{ $ensayo->paquete->descripcion ?? 'N/D' }}</td>
                            <td>{{ $ensayo->descripcion }}</td>
                            <td>
                                @if ($ensayo->status)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('ensayo_aptitud.edit', $ensayo->id) }}"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if (Gate::any(Permiso::DELETE_GESTION_PROGRAMAS))
                                        <form method="POST" action="{{ route('ensayo_aptitud.destroy', $ensayo->id) }}"
                                            class="delete-form inline" data-nombre="{{ $ensayo->descripcion }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
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
                            <td colspan="5" class="px-4 py-4 text-center text-muted">No hay ensayos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $ensayos->links() }}
        </div>
    </div>
</x-app-layout>
