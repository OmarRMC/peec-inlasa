@php
    use App\Models\Permiso;
    $backUrl = route('paquete.ensayos', $paquete->id);
    $area = $paquete->area;
    $programa = $area->programa ?? null;
@endphp
<x-app-layout>
    <div class="container py-6 max-w-5xl">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-4 flex-wrap">
            <a href="{{ route('programa.index') }}" class="hover:text-primary flex items-center gap-1">
                <i class="fas fa-th-list"></i> Programas
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            @if ($programa)
                <a href="{{ route('programa.areas', $programa->id) }}" class="hover:text-primary">
                    {{ $programa->descripcion }}
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
            @endif
            <a href="{{ route('area.paquetes', $area->id) }}" class="hover:text-primary">
                {{ $area->descripcion }}
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">{{ $paquete->descripcion }}</span>
        </nav>

        {{-- Encabezado --}}
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">
                <i class="fas fa-flask mr-1"></i> Ensayos de: {{ $paquete->descripcion }}
            </h1>
            <a href="{{ route('ensayo_aptitud.create', ['back_url' => $backUrl, 'id_paquete' => $paquete->id]) }}"
                class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Ensayo
            </a>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ensayo de Aptitud</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ensayos as $ensayo)
                        <tr>
                            <td>{{ $ensayo->id }}</td>
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
                                    <a href="{{ route('ensayo_aptitud.edit', [$ensayo->id, 'back_url' => $backUrl]) }}"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if (Gate::any(Permiso::DELETE_GESTION_PROGRAMAS))
                                        <form method="POST"
                                            action="{{ route('ensayo_aptitud.destroy', $ensayo->id) }}"
                                            class="delete-form inline" data-nombre="{{ $ensayo->descripcion }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="_back_url" value="{{ $backUrl }}">
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
                            <td colspan="4" class="px-4 py-4 text-center text-muted">
                                No hay ensayos registrados para este paquete.
                            </td>
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
