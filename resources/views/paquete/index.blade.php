@php
    use App\Models\Permiso;
@endphp
<x-app-layout>
    <div class="container py-6 max-w-6xl">
        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4 mb-4">
            <h1 class="text-xl font-bold text-primary">Lista de Paquetes</h1>
            <div class="flex items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('paquete.index') }}" class="flex items-center gap-2">
                    <div class="relative w-56">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Buscar paquete..."
                            class="w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs">
                    </div>
                    <button type="submit"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                        <i class="fas fa-search"></i>
                    </button>
                    @if ($search)
                        <a href="{{ route('paquete.index') }}"
                            class="px-3 py-1.5 border border-gray-300 text-gray-600 rounded hover:bg-gray-50 transition shadow text-xs">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('paquete.create') }}"
                    class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i> Nuevo Paquete
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>Programa</th>
                        <th>Área</th>
                        <th>Paquete</th>
                        <th>Costo</th>
                        <th>Max. Participantes</th>
                        <th>Tipo Laboratorios</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $paquetesAgrupados = $paquetes->groupBy(function ($item) {
                            return $item->area->programa->descripcion ?? 'N/D';
                        });
                    @endphp

                    @forelse ($paquetesAgrupados as $programa => $paquetesPorPrograma)
                        @php
                            $areasAgrupadas = $paquetesPorPrograma->groupBy(function ($item) {
                                return $item->area->descripcion ?? 'N/D';
                            });
                        @endphp

                        @foreach ($areasAgrupadas as $area => $paquetesPorArea)
                            @foreach ($paquetesPorArea as $index => $paquete)
                                <tr>
                                    @if ($index === 0 && $loop->parent->first)
                                        <td rowspan="{{ $paquetesPorPrograma->count() }}">{{ $programa }}</td>
                                    @endif

                                    @if ($index === 0)
                                        <td rowspan="{{ $paquetesPorArea->count() }}">{{ $area }}</td>
                                    @endif
                                    <td>{{ $paquete->descripcion }}</td>
                                    <td>{{ $paquete->costo_paquete }} Bs</td>
                                    <td>{{ $paquete->max_participantes }}</td>
                                    <td>{{ $paquete->tiposLaboratorios->pluck('descripcion')->implode(', ') }}</td>
                                    <td>
                                        @if ($paquete->status)
                                            <span class="badge badge-success">Activo</span>
                                        @else
                                            <span class="badge badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex space-x-1">
                                            <a href="{{ route('paquete.edit', $paquete->id) }}"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                                data-tippy-content="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if (Gate::any(Permiso::DELETE_GESTION_PROGRAMAS))
                                                <form method="POST"
                                                    action="{{ route('paquete.destroy', $paquete->id) }}"
                                                    class="delete-form inline"
                                                    data-nombre="{{ $paquete->descripcion }}">
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
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                           <td colspan="6" class="px-4 py-4 text-center text-muted">No hay paquetes registrados.
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
