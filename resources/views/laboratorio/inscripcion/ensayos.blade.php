<x-app-layout>
    <div class="container py-6 max-w-5xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Ensayos Inscritos</h1>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>Paquete</th>
                        <th>Ensayo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ultimoPaquete = null;
                    @endphp

                    @forelse ($ensayos as $ensayo)
                        <tr>
                            <td>
                                @if ($ultimoPaquete !== optional($ensayo->ensayoAptitud->paquete)->descripcion)
                                    <strong>{{ optional($ensayo->ensayoAptitud->paquete)->descripcion }}</strong>
                                    @php
                                        $ultimoPaquete = optional($ensayo->ensayoAptitud->paquete)->descripcion;
                                    @endphp
                                @endif
                            </td>

                            <td>{{ $ensayo->descripcion_ea }}</td>

                            <td>
                                <div class="flex space-x-1">
                                    <a href="{{ route('lab.inscritos-ensayos.formularios', $ensayo->ensayoAptitud->id) }}"
                                        class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Llenar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
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
