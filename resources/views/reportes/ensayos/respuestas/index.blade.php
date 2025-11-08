<x-app-layout>
    <div class="container py-6 max-w-5xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Reporte de Ensayos por Paquete</h1>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            @php
                $agrupados = $ensayos->groupBy(fn($e) => $e->paquete->descripcion ?? 'Sin Paquete');
            @endphp
            <table class="table w-full">
                <thead>
                    <tr>
                        <th class="w-1/3">Paquete</th>
                        <th class="w-1/3">Ensayo</th>
                        <th class="w-1/3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agrupados as $paquete => $items)
                        <!-- Fila del paquete -->
                        <tr class="bg-gray-100">
                            <td colspan="3" class="px-4 py-2 font-semibold text-primary">
                                <i class="fas fa-box mr-1"></i> {{ $paquete }}
                            </td>
                        </tr>
                        <!-- Ensayos de ese paquete -->
                        @foreach ($items as $ensayo)
                            <tr>
                                <td></td>
                                <td>{{ $ensayo->descripcion }}</td>
                                <td>
                                    <div class="flex space-x-1">
                                        <a href="{{ route('reportes.resultados.registrados', $ensayo->id) }}"
                                            class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                            data-tippy-content="Ver detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-muted">
                                No hay ensayos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
