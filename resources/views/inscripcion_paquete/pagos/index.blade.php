<x-app-layout>
    <div class="container">
        <h2 class="text-lg font-semibold mb-4">Comprobantes de Pagos</h2>

        <div class="mb-4 text-sm text-gray-700">
            <p><strong>Gestión:</strong> {{ $gestion }}</p>
            <p><strong>Código del laboratorio:</strong> {{ $codigo }}</p>
        </div>
        <div class="flex flex-wrap gap-4">
            @forelse ($documentos as $doc)
                <div class="border rounded p-3 bg-gray-50 text-gray-700 flex flex-col items-center w-40">

                    <div class="text-center mb-2">
                        <strong class="text-sm">Documento:</strong>
                        <p class="text-sm truncate" title="{{ $doc->nombre_doc }}">{{ $doc->nombre_doc }}</p>
                    </div>

                    <div class="mt-2 flex items-center justify-center w-40 max-h-[100px] border rounded bg-gray-50 text-gray-500 text-xs overflow-hidden"
                        id="preview-db-{{ $doc->id }}">
                    </div>
                    <a href="{{ asset($doc->ruta_doc) }}" target="_blank"
                        class="text-blue-600 text-xs px-2 py-1 border rounded hover:bg-blue-50">
                        Ver en nueva pestaña
                    </a>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Sin documentos disponibles.</p>
            @endforelse
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @foreach ($documentos as $doc)
                (function() {
                    const container = document.getElementById('preview-db-{{ $doc->id }}');
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
</x-app-layout>
