@php
    use App\Models\Inscripcion;
@endphp

<x-app-layout>
    <div class="container py-6 max-w-6xl">
        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Ensayos de Aptitud</h1>
            <!-- Buscador -->
            <form method="GET" action="{{ route('certificado-desempeno.index') }}"
                class="flex items-center gap-2 justify-end !w-full sm:!w-auto ml-auto">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="search" name="search" id="custom-search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs"
                        placeholder="Buscar Ensayo...">
                </div>
                <button type="submit"
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Área</th>
                        <th class="px-3 py-2 text-left">Paquete</th>
                        <th class="px-3 py-2 text-left">Ensayo A.</th>
                        <th class="px-3 py-2 text-left">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @php
                        // AGRUPAR POR ÁREA
                        $areas = $ensayos->groupBy(fn($e) => $e->paquete->area->descripcion ?? 'N/D');
                    @endphp

                    @forelse ($areas as $areaNombre => $ensayosPorArea)

                        @php
                            $paquetes = $ensayosPorArea->groupBy(fn($e) => $e->paquete->descripcion ?? 'N/D');
                            $rowspanArea = $ensayosPorArea->count();
                        @endphp

                        @foreach ($paquetes as $paqueteNombre => $ensayosPorPaquete)
                            @php
                                $rowspanPaquete = $ensayosPorPaquete->count();
                            @endphp

                            @foreach ($ensayosPorPaquete as $index => $ensayo)
                                <tr class="border-b">

                                    @if ($loop->parent->first && $loop->first)
                                        <td rowspan="{{ $rowspanArea }}" class="px-3 py-2 align-middle font-semibold">
                                            {{ $areaNombre }}
                                        </td>
                                    @endif

                                    @if ($loop->first)
                                        <td rowspan="{{ $rowspanPaquete }}" class="px-3 py-2 align-middle">
                                            {{ $paqueteNombre }}
                                        </td>
                                    @endif

                                    <td class="px-3 py-2 align-middle">
                                        {{ $ensayo->descripcion }}
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <div class="flex gap-2">
                                            <a href="{{ route('ea.lab.inscritos', $ensayo->id) }}"
                                                class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm ml-1"
                                                data-tippy-content="Lista de inscripciones aprobadas" traget="_blank">
                                                <i class="fas fa-list-check"></i>
                                            </a>
                                            <a href="{{ route('certificados.desempeno.labs.show', $ensayo->id) }}"
                                                target="_blank"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                                data-tippy-content="Actualizar Desempeño Registrado">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('ea.lab.certificados', $ensayo->id) }}"
                                                class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm ml-1"
                                                data-tippy-content="Cargar Resultados / Desempeño">
                                                <i class="fas fa-upload"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-muted">
                                No hay ensayos registrados.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $ensayos->appends(request()->query())->links() }}
        </div>

    </div>
</x-app-layout>
