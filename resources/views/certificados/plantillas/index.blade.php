<x-app-layout>
    <div class="container py-6 max-w-6xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Plantillas de Certificados</h1>

            <a href="{{ route('plantillas-certificados.create') }}" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nueva Plantilla
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nro</th>
                        <th>Plantilla</th>
                        <th>Dimensiones</th>
                        <th>Seleccionado</th>
                        <th class="w-40">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($plantillas as $tpl)
                    <tr>
                        <td>{{ $tpl->id }}</td>

                        <td class="min-w-[220px]">
                            <div class="flex items-center gap-3">
                                {{-- Miniatura del fondo (si es URL / path público) --}}
                                <div class="w-16 h-10 rounded bg-gray-100 overflow-hidden flex items-center justify-center border">
                                    @if (!empty($tpl->imagen_fondo))
                                    <img src="{{ $tpl->imagen_fondo }}" alt="Fondo"
                                        class="w-full h-full object-cover">
                                    @else
                                    <i class="fas fa-image text-gray-400"></i>
                                    @endif
                                </div>

                                <div class="leading-tight">
                                    <div class="font-semibold text-gray-900">{{ $tpl->nombre }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- <td class="max-w-sm">
                            <div class="text-sm text-gray-700 line-clamp-2">
                                
                            </div>
                        </td> -->

                        <td class="whitespace-nowrap">
                            {{ number_format($tpl->ancho_mm, 2) }} × {{ number_format($tpl->alto_mm, 2) }} mm
                        </td>

                        <td>
                            @if ($tpl->activo)
                            <span class="badge badge-success">Seleccionado</span>
                            @else
                            <span class="badge badge-danger">No Seleccionado</span>
                            @endif
                        </td>

                        <td>
                            <div class="flex space-x-1">
                                {{-- Previsualizar --}}
                                <a href="{{ route('plantillas-certificados.preview', $tpl->id) }}"
                                    data-tippy-content="Previsualizar"
                                    target="_blank"
                                    class="bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-2 py-1 rounded shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Editar --}}
                                <a href="{{ route('plantillas-certificados.edit', $tpl->id) }}"
                                    data-tippy-content="Editar"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Eliminar --}}
                                <form method="POST" action="{{ route('plantillas-certificados.destroy', $tpl->id) }}"
                                    class="delete-form inline" data-nombre="{{ $tpl->nombre }}">
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
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay plantillas registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación (si aplica) --}}
        @if (method_exists($plantillas, 'links'))
        <div class="mt-4">
            {{ $plantillas->links() }}
        </div>
        @endif
    </div>
</x-app-layout>