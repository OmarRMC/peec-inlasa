@php
    use App\Models\DocumentoInscripcion;
    use App\Models\Permiso;
@endphp
<x-app-layout>
    <div class="container">
        <h2 class="text-lg font-semibold mb-4">Comprobantes de Pagos</h2>

        <div class="mb-4 text-sm text-gray-700">
            <p><strong>Gestión:</strong> {{ $gestion }}</p>
            <p><strong>Código del laboratorio:</strong> {{ $codigo }}</p>
        </div>
        <div class="flex flex-wrap gap-4">
            @forelse ($documentos as $doc)
                <div
                    class="border rounded-lg shadow-md p-4 bg-white w-48 flex flex-col items-center space-y-3 hover:shadow-lg transition-shadow justify-between">

                    <!-- Encabezado del documento -->
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-semibold text-gray-800 truncate" title="{{ $doc->nombre_doc }}">
                            {{ $doc->nombre_doc }}
                        </h3>
                    </div>
                    @if (Gate::any([Permiso::GESTION_PAGOS, Permiso::ADMIN]))
                        @if ($doc->status == DocumentoInscripcion::STATUS_DOCUMENTO_PENDIENTE)
                            <button onclick="toggleDocumento({{ $doc->id }})" id="btn-doc-{{ $doc->id }}"
                                class="w-full text-center bg-green-600 hover:bg-green-700 text-white text-xs py-1 rounded transition-colors">
                                Registrar
                            </button>
                        @else
                            <button onclick="toggleDocumento({{ $doc->id }})" id="btn-doc-{{ $doc->id }}"
                                class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 text-xs py-1 rounded transition-colors flex items-center justify-center">
                                ✔ Registrado (desmarcar)
                            </button>
                        @endif
                    @endif
                    <!-- Información de pago -->
                    <div class="bg-gray-50 rounded p-2 w-full text-xs text-gray-700 flex flex-col space-y-1">
                        <div class="flex justify-between">
                            <span class="font-semibold">NIT:</span>
                            <span class="truncate" title="{{ $doc->detallePago->nit ?? 'N/A' }}">
                                {{ $doc->detallePago->nit ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Razón Social:</span>
                            <span class="break-words whitespace-normal  w-full"
                                title="{{ $doc->detallePago->razon_social ?? 'N/A' }}">
                                {{ $doc->detallePago->razon_social ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <!-- Preview del documento -->
                    <div class="w-full h-24 border rounded bg-gray-50 flex items-center justify-center overflow-hidden">
                        @php
                            $extension = pathinfo($doc->ruta_doc, PATHINFO_EXTENSION);
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        @endphp

                        @if (in_array(strtolower($extension), $imageExtensions))
                            <!-- Mostrar imagen -->
                            <img src="{{ asset($doc->ruta_doc) }}" alt="{{ $doc->nombre_doc }}"
                                class="object-contain w-full h-full">
                        @else
                            <!-- Mostrar ícono PDF u otro tipo -->
                            <i class="fas fa-file-pdf text-red-400 text-2xl" title="Archivo PDF"></i>
                            {{-- <div class="mt-2 flex items-center justify-center w-40 max-h-[100px] border rounded bg-gray-50 text-gray-500 text-xs overflow-hidden hidden"
                                id="preview-db-{{ $doc->id }}"> --}}
                        @endif
                    </div>

                    <!-- Botón de acción -->
                    <a href="{{ asset($doc->ruta_doc) }}" target="_blank"
                        class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white text-xs py-1 rounded transition-colors">
                        Ver Documento
                    </a>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Sin documentos disponibles.</p>
            @endforelse
        </div>
    </div>
    @push('scripts')
        <script>
            const urlDocumento = "{{ route('documentos.pago.toggle', ['id' => 'DOC_ID']) }}";
            function toggleDocumento(id) {
                const btn = document.getElementById('btn-doc-' + id);
                const originalText = btn.innerText;
                btn.disabled = true;
                btn.innerText = "Procesando...";
                const url = urlDocumento.replace('DOC_ID', id);
                fetch(`${url}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {

                        if (data.new_status == 0) {
                            btn.className =
                                "w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 text-xs py-1 rounded transition-colors flex items-center justify-center";
                            btn.innerHTML = "✔ Registrado (desmarcar)";
                        } else {
                            btn.className =
                                "w-full text-center bg-green-600 hover:bg-green-700 text-white text-xs py-1 rounded transition-colors";
                            btn.innerText = "Registrar";
                        }

                        btn.disabled = false;
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.innerText = originalText;
                        alert("Error al actualizar el documento.");
                    });
            }

            document.addEventListener('DOMContentLoaded', () => {
                @foreach ($documentos as $doc)
                    (function() {
                        // Se quito por que por momento no se va user el previewizador 
                        const container = document.getElementById('preview-db-{{ $doc->id }}') ?? null;
                        if (!container) return null;
                        const url = "{{ asset($doc->ruta_doc) }}";
                        const ext = url.split('.').pop().toLowerCase();

                        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) {
                            const img = document.createElement('img');
                            img.src = url;
                            img.classList.add('max-h-40', 'rounded', 'shadow');
                            container.appendChild(img);
                        } else if (ext === 'pdf') {
                            fetch(url).then(res => res.arrayBuffer()).then(async function(data) {
                                const pdf = await window.pdfjsLib.getDocument(new Uint8Array(data))
                                    .promise;
                                const page = await pdf.getPage(1);
                                const scale = 0.6;
                                const viewport = page.getViewport({
                                    scale
                                });

                                const canvas = document.createElement('canvas');
                                const context = canvas.getContext('2d');
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                await page.render({
                                    canvasContext: context,
                                    viewport
                                }).promise;
                                container.appendChild(canvas);
                            });
                        } else {
                            container.textContent = "Formato no soportado";
                        }
                    })();
                @endforeach
            })
        </script>
    @endpush
</x-app-layout>
