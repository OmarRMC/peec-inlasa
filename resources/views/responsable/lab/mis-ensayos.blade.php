<x-app-layout>
    <div class="px-4 py-6 max-w-6xl mx-auto">

        <div class="flex justify-between items-center flex-wrap gap-4 mb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-vials text-indigo-500 mr-1"></i> Mis ensayos asignados
                </h1>
                <p class="text-xs text-gray-500 mt-1">
                    Listado de los ensayos de aptitud bajo tu responsabilidad y su nivel de participación.
                </p>
            </div>
            <div class="flex items-end gap-2">
                <div>
                    <label for="filter-gestion" class="block text-xs text-gray-600 mb-1">Gestión</label>
                    <select id="filter-gestion"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px] font-semibold"
                        onchange="window.location.href = '?gestion=' + this.value">
                        @foreach ($gestiones as $g)
                            <option value="{{ $g }}" @selected((int)$g === (int)$gestion)>{{ $g }}</option>
                        @endforeach
                        @if (!in_array((int)$gestion, array_map('intval', (array) $gestiones)))
                            <option value="{{ $gestion }}" selected>{{ $gestion }}</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500 uppercase tracking-wide">Ensayos asignados</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">{{ $ensayos->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500 uppercase tracking-wide">Total inscripciones</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalInscritos }}</div>
                <div class="text-xs text-gray-400 mt-1">Suma de inscritos en cada ensayo</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-xs text-gray-500 uppercase tracking-wide">Laboratorios únicos</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalLabsUnicos }}</div>
                <div class="text-xs text-gray-400 mt-1">Labs distintos inscritos en al menos un ensayo</div>
            </div>
        </div>

        @if ($ensayos->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                <p>No tienes ensayos de aptitud asignados.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($ensayos as $ea)
                    @php
                        $inscritos = (int) ($conteoPorEa[$ea->id] ?? 0);
                        $paquete = $ea->paquete;
                        $area = $paquete?->area;
                    @endphp
                    <a href="{{ route('ea.lab.inscritos', $ea->id) }}"
                        class="block bg-white rounded-lg shadow hover:shadow-md transition p-4 hover:bg-indigo-50">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                @if ($area)
                                    <div class="text-[10px] text-gray-400 uppercase tracking-wide truncate">
                                        {{ $area->descripcion }}
                                    </div>
                                @endif
                                @if ($paquete && mb_strtolower(trim($paquete->descripcion)) !== mb_strtolower(trim($ea->descripcion)))
                                    <div class="text-xs text-gray-500 truncate">{{ $paquete->descripcion }}</div>
                                @endif
                                <div class="text-sm font-semibold text-gray-800 mt-0.5">
                                    {{ $ea->descripcion }}
                                </div>
                            </div>
                            <i class="fas fa-flask text-indigo-400"></i>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-gray-500">Laboratorios inscritos</div>
                                <div class="text-lg font-bold text-indigo-700">{{ $inscritos }}</div>
                            </div>
                            <span class="text-xs text-indigo-600 font-medium">
                                Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
