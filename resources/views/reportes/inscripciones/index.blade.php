@php
    use App\Models\Inscripcion;
@endphp

<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 gap-2">
            <!-- Título -->
            <h1 class="text-xl font-bold text-gray-800">Reporte de Inscripciones</h1>

            <!-- Botones -->
            <div class="flex sm:flex-row flex-wrap gap-2 sm:justify-end  sm:w-auto">
                <a href="{{ route('reportes.inscripciones.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>

                <a href="{{ route('reportes.inscripciones.export', array_merge(request()->query(), ['format' => 'csv'])) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </a>

                <a href="{{ route('reportes.inscripciones.export', array_merge(request()->query(), ['format' => 'json'])) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-code"></i> Exportar JSON
                </a>
            </div>
        </div>

        <!-- Filtros (diseño igual al primero) -->
        <form method="GET" id="filtrosForm" class="mb-4 bg-white rounded-lg p-4 shadow">
            <div class="flex flex-wrap gap-3 items-end text-sm">
                <!-- Mostrar registros -->
                {{-- <div class="flex items-center gap-2 flex-wrap">
                    <label for="per_page" class="whitespace-nowrap">Mostrar</label>
                    <select id="per_page" name="per_page"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[80px]">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                    </select>
                    <span class="whitespace-nowrap">registros</span>
                </div> --}}
                <div>
                    <label class="block text-sm whitespace-nowrap">Gestión</label>
                    <select name="gestion" id="filter-gestion"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px]">
                        @foreach ($gestiones ?? [now()->year] as $g)
                            <option value="{{ $g }}"
                                {{ (request('gestion') ?? now()->year) == $g ? 'selected' : '' }}>{{ $g }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha desde / hasta -->
                {{-- <div class="flex items-center gap-2">
                    <label for="fecha_desde" class="whitespace-nowrap text-sm">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px]">
                </div>

                <div class="flex items-center gap-2">
                    <label for="fecha_hasta" class="whitespace-nowrap text-sm">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px]">
                </div> --}}

                <!-- Buscador -->
                <div class="flex items-center gap-2 justify-end !w-full sm:!w-auto ml-auto">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" name="search" id="custom-search" value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs"
                            placeholder="Buscar por código, laboratorio o correo...">
                    </div>

                    <button type="submit"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabla (tarjeta blanca, shadow, overflow-x igual que el original) -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Código</th>
                        <th class="px-4 py-2 text-left">Laboratorio</th>
                        <th class="px-4 py-2 text-left">Correo</th>
                        <th class="px-4 py-2 text-left">Paquetes</th>
                        <th class="px-4 py-2 text-left">Saldo</th>
                        <th class="px-4 py-2 text-left">Costo</th>
                        <th class="px-4 py-2 text-left">Cantidad Ins.</th>
                        <th class="px-4 py-2 text-left">Estado de Cuenta</th>
                        <th class="px-4 py-2 text-left">Fecha de ultima Ins.</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($inscripciones as $ins)
                        @php
                            $lab = optional($ins['laboratorio']);
                            $paquetes = $ins['paquetes'];

                        @endphp
                        <tr class="border-t">
                            <td class="px-4 py-2 font-medium">
                                {{ optional($lab)->cod_lab ?? $ins->id_lab }}</td>
                            <td class="px-4 py-2">{{ $lab->nombre_lab ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">
                                {{ optional($lab)->mail_lab ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                @if (!empty($paquetes))
                                    <ul class="list-disc pl-5">
                                        @foreach ($paquetes as $d)
                                            <li class="text-sm">
                                                <span class="font-medium">{{ $d['descripcion_paquete'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-sm text-gray-500">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-2">{{ $ins['saldo_total'] ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $ins['costo_total'] ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $ins['inscripciones_count'] ?? '-' }}</td>
                            <td class="px-4 py-2">
                                {{ $ins['deuda_pendiente'] ? Inscripcion::STATUS_CUENTA[Inscripcion::STATUS_DEUDOR] : Inscripcion::STATUS_CUENTA[Inscripcion::STATUS_PAGADO] }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600">
                                {{ $ins['ultima_fecha_inscripcion'] ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron inscripciones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex justify-between items-center flex-wrap mt-6 gap-2">
            <div class="flex justify-center">
                {{ $inscripciones->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('filtrosForm');

                // Seleccionamos los inputs que dispararán la actualización
                const inputsAutoSubmit = [
                    document.getElementById('fecha_desde'),
                    document.getElementById('fecha_hasta'),
                    document.getElementById('filter-gestion'),
                    document.getElementById('per_page')
                ];
                inputsAutoSubmit.forEach(input => {
                    if (input) {
                        input.addEventListener('change', () => {
                            form.submit();
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
