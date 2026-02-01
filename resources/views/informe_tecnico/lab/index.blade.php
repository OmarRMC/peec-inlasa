<x-app-layout>
    <div class="container py-6 max-w-5xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-1">
            <h1 class="text-xl font-bold text-primary">Lista de Ensayos Inscritos</h1>
        </div>

        <form method="GET" id="filtrosForm" class="bg-white rounded-lg p-2 shadow w-fit mb-1">
            <div class="flex">
                <div class="flex flex-wrap gap-3 items-end text-sm items-center flex-col">
                    <label class="block text-sm whitespace-nowrap">Gestión</label>
                    <select name="gestion" id="filter-gestion"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px]">
                        @foreach ($gestiones ?? [now()->year] as $g)
                            <option value="{{ $g }}"
                                {{ (request('gestion') ?? now()->year) == $g ? 'selected' : '' }}>
                                {{ $g }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th class="w-1/3">Ensayos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ensayos as $ensayo)
                        <tr class="bg-gray-100">
                            <td colspan="3" class="px-4 py-2 font-semibold text-primary">
                                <i class="fas fa-box mr-1"></i> {{ $ensayo->descripcion_ea }}
                                {{ $ensayo->ensayoAptitud->paquete ? ' - ' . $ensayo->ensayoAptitud->paquete->descripcion : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($ensayo->ensayoAptitud->ciclos as $ciclo)
                                        @php
                                            $informe = $ciclo->informes->first();
                                        @endphp
                                        @if ($informe)
                                            <a href="{{ asset('storage/' . $informe->reporte) }}" target="_blank"
                                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition cursor-pointer"
                                                title="Ver informe técnico">
                                                {{ $ciclo->nombre }}
                                            </a>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-400 border border-gray-300 cursor-default select-none"
                                                title="Sin informe técnico">
                                                {{ $ciclo->nombre }}
                                            </span>
                                        @endif
                                    @endforeach
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
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('filtrosForm');
                const inputsAutoSubmit = [
                    document.getElementById('filter-gestion'),
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
