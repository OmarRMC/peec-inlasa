<x-app-layout>
    <div class="container py-1 max-w-4xl">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">Certificados Disponibles</h1>
        </div>

        <!-- Tabla formal -->
        <div class="overflow-x-auto bg-white rounded-xl shadow border">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">Gestión</th>
                        <th class="px-4 py-3 text-left">Código</th>
                        <th class="px-4 py-3 text-center">Participación</th>
                        <th class="px-4 py-3 text-center">Desempeño</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($certificadosDisponibles as $gestion => $data)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Gestión -->
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $gestion }}
                            </td>

                            <!-- Código -->
                            <td class="px-4 py-3 text-gray-600">
                                {{ $data->codigo ?? 'N/D' }}
                            </td>

                            <!-- Participación -->
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('lab.certificados.participacion.pdf', ['gestion' => $gestion]) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-blue-500 text-blue-600 hover:bg-blue-50 transition text-xs font-medium"
                                    data-tippy-content="Descargar certificado de participación">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </td>

                            <!-- Desempeño -->
                            <td class="px-4 py-3 text-center">
                                @if ($data->tiene_certificado_desempeno)
                                    <a href="{{ route('certificados.desemp.pdf', ['gestion' => $gestion]) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-green-600 text-green-700 hover:bg-green-50 transition text-xs font-medium"
                                        data-tippy-content="Descargar certificado de desempeño">
                                        <i class="fas fa-file-signature"></i> PDF
                                    </a>
                                @else
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-gray-300 text-gray-400 text-xs font-medium cursor-not-allowed">
                                        <i class="fas fa-ban"></i> N/A
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                No hay certificados disponibles.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $certificadosDisponibles->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                tippy("[data-tippy-content]", {
                    animation: "scale",
                    theme: "light-border",
                });
            });
        </script>
    @endpush
</x-app-layout>
