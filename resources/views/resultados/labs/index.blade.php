<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-xl font-bold text-gray-800">Resultados registrados por laboratorio</h1>

            <form method="GET" action="{{ route('reportes.resultados.registrados', $idEA) }}"
                class="flex items-center gap-2">
                <input type="hidden" name="idEA" value="{{ request('idEA') ?? '' }}">

                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="search" name="search" value="{{ request('search') ?? '' }}"
                        class="w-full pl-10 pr-4 py-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs"
                        placeholder="Buscar por código o nombre de laboratorio...">
                </div>

                <div class="flex items-center gap-2">
                    <select name="idCiclo" id="idCiclo"
                        class="text-xs py-1 px-2 w-fit border rounded-md shadow-sm min-w-[75px]">
                        @foreach ($ciclos as $c)
                            <option value="{{ $c->id }}" {{ $c->id == ($cicloId ?? null) ? 'selected' : '' }}>
                                {{ $c->descripcion ?? 'Ciclo ' . $c->numero }}
                            </option>
                        @endforeach
                    </select>

                    <input type="number" name="gestion" value="{{ request('gestion') ?? now()->year }}"
                        class="w-20 text-xs py-1 px-2 border rounded-md shadow-sm" placeholder="Gestión">

                    <button type="submit"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Resumen / acciones rápidas -->
        <div class="flex items-center justify-between mb-3 gap-4">
            <div class="text-sm text-gray-600">Total de laboratorios: <span
                    class="font-semibold">{{ $labs->count() }}</span></div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reportes.resultados.ensayos.export', array_merge(request()->query(), ['idEA' => $idEA, 'ciclo' => $cicloId, 'format' => 'xlsx'])) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>

                {{-- <a href="{{ route('reportes.resultados.ensayos.export', array_merge(request()->query(), ['idEA' => $idEA, 'ciclo' => $cicloId,'format' => 'csv'])) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </a> --}}

                <a href="{{ route('reportes.resultados.ensayos.export', array_merge(request()->query(), ['idEA' => $idEA, 'ciclo' => $cicloId, 'format' => 'json'])) }}"
                    class="flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-file-code"></i> Exportar JSON
                </a>

            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2">Código</th>
                        <th class="px-3 py-2">Laboratorio</th>
                        {{-- <th class="px-3 py-2">Departamento / Ciudad</th> --}}
                        <th class="px-3 py-2 text-center"># Resultados</th>
                        {{-- <th class="px-3 py-2 text-center">Estado</th> --}}
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($labs as $lab)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2 align-top font-medium">{{ $lab->cod_lab ?? '-' }}</td>
                            <td class="px-3 py-2 align-top">{{ $lab->nombre_lab }}</td>
                            {{-- <td class="px-3 py-2 align-top text-gray-500 text-xs">
                                {{ $lab->departamento ?? ($lab->ciudad ?? '-') }}</td> --}}
                            <td class="px-3 py-2 text-center">{{ optional($lab->respuestas)->count() ?? '-' }}</td>
                            {{-- <td class="px-3 py-2 text-center">
                                @if (optional($lab->respuestas)->count() > 0)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Con resultados</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Sin resultados
                                    </span>
                                @endif
                            </td> --}}
                            <td class="px-3 py-2 text-center gap-2 flex justify-center">
                                <a href="#" data-lab-id="{{ $lab->id }}"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm text-xs"
                                    title="Ver resultados">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                                <a href="#" data-lab-id="{{ $lab->id }}"
                                    class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm text-xs"
                                    data-tippy-content="Ver respuestas">
                                    <i class="fas fa-list"></i>
                                </a>
                                <a href="#" data-lab-id="{{ $lab->id }}"
                                    class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm text-xs"
                                    title="Notificar">
                                    <i class="fas fa-bell"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No se encontraron
                                laboratorios con resultados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación / nota -->
        <div class="mt-4 flex items-center justify-between">
            <div class="text-xs text-gray-500">Mostrando <span class="font-medium">{{ $labs->count() }}</span>
                laboratorios</div>
            <div>
                {{-- Si $labs es paginación usa links(), si es colección simple puedes implementar paginación manual --}}
                @if (method_exists($labs, 'links'))
                    {{ $labs->appends(request()->query())->links() }}
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
