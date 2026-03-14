@php
    use App\Models\Permiso;
    $backUrl = route('area.paquetes', $area->id);
@endphp
<x-app-layout>
    <div class="container py-6 max-w-6xl">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-4 flex-wrap">
            <a href="{{ route('programa.index') }}" class="hover:text-primary flex items-center gap-1">
                <i class="fas fa-th-list"></i> Programas
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('programa.areas', $area->programa->id) }}" class="hover:text-primary">
                {{ $area->programa->descripcion ?? 'N/D' }}
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800 font-medium">{{ $area->descripcion }}</span>
        </nav>

        {{-- Encabezado --}}
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">
                <i class="fas fa-box-open mr-1"></i> Paquetes de: {{ $area->descripcion }}
            </h1>
            <a href="{{ route('paquete.create', ['back_url' => $backUrl, 'id_area' => $area->id]) }}"
                class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Paquete
            </a>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>Paquete</th>
                        <th>Costo</th>
                        <th>Max. Participantes</th>
                        <th>Tipo Laboratorios</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paquetes as $paquete)
                        <tr>
                            <td>{{ $paquete->descripcion }}</td>
                            <td>{{ $paquete->costo_paquete }} Bs</td>
                            <td>{{ $paquete->max_participantes }}</td>
                            <td>{{ $paquete->tiposLaboratorios->pluck('descripcion')->implode(', ') ?: '-' }}</td>
                            <td>
                                @if ($paquete->status)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('paquete.ensayos', $paquete->id) }}"
                                        class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Ver ensayos">
                                        <i class="fas fa-flask"></i>
                                    </a>
                                    <a href="{{ route('paquete.edit', [$paquete->id, 'back_url' => $backUrl]) }}"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if (Gate::any(Permiso::DELETE_GESTION_PROGRAMAS))
                                        <form method="POST" action="{{ route('paquete.destroy', $paquete->id) }}"
                                            class="delete-form inline" data-nombre="{{ $paquete->descripcion }}">
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
                            <td colspan="6" class="px-4 py-4 text-center text-muted">
                                No hay paquetes registrados para esta área.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $paquetes->links() }}
        </div>
    </div>
</x-app-layout>
