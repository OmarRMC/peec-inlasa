@php
use App\Models\Inscripcion;
@endphp

<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 gap-2">
            <!-- Título -->
            <h1 class="text-xl font-bold text-gray-800">Reporte de Pagos</h1>

            <!-- Botones -->
            <div class="flex sm:flex-row flex-wrap gap-2 sm:justify-end  sm:w-auto">
                <a href="{{ route('reportes.pagos.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>

                <a href="{{ route('reportes.pagos.export', array_merge(request()->query(), ['format' => 'csv'])) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </a>

                <a href="{{ route('reportes.pagos.export', array_merge(request()->query(), ['format' => 'json'])) }}"
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

    <div class="flex items-center gap-2 justify-end !w-full sm:!w-auto ml-auto">
        <div class="relative w-full sm:w-64">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                <i class="fas fa-search"></i>
            </span>
            <input type="search" name="search" id="custom-search" value="{{ request('search') }}"
                class="w-full pl-10 pr-4 py-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs"
                placeholder="Buscar por código/nombre del lab">
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
                    <th>Laboratorio</th>
                    <th>Paquetes Inscritos</th>
                    <th>Gestión</th>
                    <th>Fecha Inscripción</th>
                    <th># de Pagos</th>
                    <th>Total Pagado</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Detalle de Pagos</th>
                </tr>
            </thead>

            <tbody>
                @forelse($inscripciones as $row)
                @php
                $ins = $row['model'];
                $lab = optional($ins->laboratorio);
                @endphp

                <tr class="border-t align-top">
                    <!-- Laboratorio -->
                    <td class="px-4 py-2">
                        <div class="font-medium">{{ $lab->cod_lab ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $lab->nombre_lab ?? '-' }}</div>
                    </td>

                    <td class="px-4 py-2 text-sm text-gray-600">
                        {{ $ins->detalleInscripciones->pluck('descripcion_paquete')->implode(', ') ?? '-' }}
                    </td>

                    <!-- Gestión -->
                    <td class="px-4 py-2">{{ $ins->gestion }}</td>

                    <!-- Fecha inscripción -->
                    <td class="px-4 py-2 text-sm">
                        {{ $ins->fecha_inscripcion }}
                    </td>

                    <!-- Pagos -->
                    <td class="px-4 py-2">
                        <div class="text-sm">
                            {{-- <span class="font-medium">Pagos:</span>  --}}
                            {{ $row['cantidad_pagos'] }}
                        </div>
                    </td>

                    <!-- Total pagado -->
                    <td class="px-4 py-2 text-green-700 font-medium">
                        Bs {{ number_format($row['total_pagado'], 2) }}
                    </td>

                    <!-- Saldo -->
                    <td class="px-4 py-2 {{ $row['saldo'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Bs {{ number_format($row['saldo'], 2) }}
                    </td>

                    <!-- Estado cuenta -->
                    <td class="px-4 py-2">
                        <span
                            class="px-2 py-1 rounded text-xs
                {{ $row['saldo'] > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $row['saldo'] > 0 ? 'Pendiente' : 'Pagado' }}
                        </span>
                    </td>

                    <!-- Detalle pagos -->

                    <td class="px-4 py-2 text-xs text-gray-700">
                        @foreach ($ins->pagos as $pago)
                        <div class="mb-1">
                            <span class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}
                            </span>
                            /
                            <span class="text-xs px-1 rounded font-semibold bg-blue-100 text-blue-700">
                                {{ $pago->tipo_transaccion }}
                            </span>

                            <span class="font-bold text-green-700">
                                / Bs {{ number_format($pago->monto_pagado, 2) }}
                            </span>

                            @if($pago->nro_tranferencia)
                            / N°Transf.: {{ $pago->nro_tranferencia }}
                            @endif
                            @if($pago->nro_factura)
                            / Factura: {{ $pago->nro_factura }}
                            @endif

                            @if($pago->razon_social)
                            / Razon Social: {{ $pago->razon_social }}
                            @endif

                            @if($pago->obs_pago)
                            / Obs: <span class="text-gray-500 italic">{{ $pago->obs_pago }}</span>
                            @endif
                        </div>
                        @endforeach
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