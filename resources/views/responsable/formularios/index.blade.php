<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-xl font-bold text-gray-800">Formularios de Ensayos de Aptitud</h1>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 w-5">Programa</th>
                        <th class="px-3 py-2">Área</th>
                        <th class="px-3 py-2">Paquete</th>
                        <th class="px-3 py-2">Ensayo</th>
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programas as $programa)
                        @php
                            $rowPrograma = $programa->areas->sum(
                                fn($a) => $a->paquetes->sum(fn($p) => $p->ensayosAptitud->count()),
                            );
                        @endphp
                        @foreach ($programa->areas as $iArea => $area)
                            @php
                                $rowArea = $area->paquetes->sum(fn($p) => $p->ensayosAptitud->count());
                            @endphp
                            @foreach ($area->paquetes as $iPaquete => $paquete)
                                @foreach ($paquete->ensayosAptitud as $iEnsayo => $ensayo)
                                    <tr class="border-b">
                                        {{-- Programa --}}
                                        @if ($iArea === 0 && $iPaquete === 0 && $iEnsayo === 0)
                                            <td class="px-3 py-2 font-semibold text-gray-700 align-top"
                                                rowspan="{{ $rowPrograma }}">
                                                {{ $programa->descripcion }}
                                            </td>
                                        @endif

                                        {{-- Área --}}
                                        @if ($iPaquete === 0 && $iEnsayo === 0)
                                            <td class="px-3 py-2 text-gray-600 align-top" rowspan="{{ $rowArea }}">
                                                {{ $area->descripcion }}
                                            </td>
                                        @endif

                                        {{-- Paquete --}}
                                        @if ($iEnsayo === 0)
                                            <td class="px-3 py-2 text-gray-500 align-top"
                                                rowspan="{{ $paquete->ensayosAptitud->count() }}">
                                                {{ $paquete->descripcion }}
                                            </td>
                                        @endif

                                        {{-- Ensayo --}}
                                        <td class="px-3 py-2">{{ $ensayo->descripcion }}</td>

                                        {{-- Acciones --}}
                                        <td class="px-3 py-2 text-center gap-2 flex justify-center">
                                            <a href="{{ route('admin.formularios.show', $ensayo->id) }}"
                                                data-tippy-content="Ver Formularios"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                            <a href="{{ route('ea.formulario.lab.inscritos', $ensayo->id) }}"
                                                data-tippy-content="Configurar la cantidad de formularios"
                                                class="relative bg-purple-100 hover:bg-purple-200 text-purple-700 px-2 py-1 rounded shadow-sm">
                                                <i class="fas fa-clipboard-list"></i>
                                                <span
                                                    class="absolute -top-1 -right-1 bg-purple-600 text-white text-xs rounded-full px-1">3</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                No hay ensayos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        {{-- <div class="mt-4">
            {{ $programas->appends(['search' => $search])->links() }}
        </div> --}}
    </div>
</x-app-layout>
