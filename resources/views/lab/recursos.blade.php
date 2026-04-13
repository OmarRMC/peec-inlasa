<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
            <i class="fas fa-folder-open text-indigo-500"></i> Recursos disponibles
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto">

        @if ($recursos->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border p-12 flex flex-col items-center text-center text-gray-400">
                <i class="fas fa-folder-open text-5xl mb-4 text-gray-300"></i>
                <p class="text-base font-medium text-gray-500">No hay recursos disponibles por el momento.</p>
                <p class="text-sm mt-1">Vuelve más tarde, el equipo PEEC publicará los documentos aquí.</p>
            </div>
        @else
            <div class="flex flex-wrap gap-5">
                @foreach ($recursos as $recurso)
                    @php
                        $urlFinal = $recurso->url_final;
                        if ($recurso->archivo) {
                            $ext = strtolower(pathinfo($recurso->archivo, PATHINFO_EXTENSION));
                            [$cardIcon, $iconColor, $bgColor, $badgeClass, $badgeText] = match($ext) {
                                'pdf'        => ['fas fa-file-pdf',   'text-red-500',    'bg-red-50',    'bg-red-100 text-red-700',      'PDF'],
                                'doc','docx' => ['fas fa-file-word',  'text-blue-500',   'bg-blue-50',   'bg-blue-100 text-blue-700',    'Word'],
                                'jpg','jpeg',
                                'png'        => ['fas fa-file-image', 'text-purple-500', 'bg-purple-50', 'bg-purple-100 text-purple-700','Imagen'],
                                default      => ['fas fa-file-alt',   'text-gray-500',   'bg-gray-50',   'bg-gray-100 text-gray-600',    'Archivo'],
                            };
                            $previewType = match(true) {
                                $ext === 'pdf'                       => 'pdf',
                                in_array($ext, ['jpg','jpeg','png']) => 'image',
                                default                              => null,
                            };
                            $subtitulo = null;
                        } else {
                            $ext = null;
                            $host = parse_url($recurso->url ?? '', PHP_URL_HOST) ?? $recurso->url;
                            [$cardIcon, $iconColor, $bgColor, $badgeClass, $badgeText] = [
                                'fas fa-link', 'text-indigo-500', 'bg-indigo-50',
                                'bg-indigo-100 text-indigo-700', 'Enlace',
                            ];
                            $previewType = null;
                            $subtitulo   = $host;
                        }
                    @endphp

                    <a href="{{ $urlFinal }}" target="_blank" rel="noopener noreferrer"
                       class="group flex flex-col bg-white rounded-2xl border border-gray-100 overflow-hidden
                              hover:border-indigo-300 hover:shadow-lg transition-all duration-150"
                       style="width:220px; height:300px; flex-shrink:0;">

                        {{-- Área de previsualización --}}
                        @if ($previewType === 'image')
                            <div class="flex-1 bg-gray-100 overflow-hidden min-h-0">
                                <img src="{{ $urlFinal }}" alt="{{ $recurso->titulo }}"
                                     class="w-full h-full object-cover pointer-events-none">
                            </div>
                        @elseif ($previewType === 'pdf')
                            <div class="flex-1 relative min-h-0"
                                 style="overflow:hidden; background:#fff;">
                                <iframe src="{{ $urlFinal }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                                        scrolling="no"
                                        style="border:0; outline:none; pointer-events:none; background:#fff;
                                               width:calc(100% + 40px); height:calc(100% + 40px);
                                               position:absolute; top:-2px; left:-2px; overflow:hidden;">
                                </iframe>
                                <div style="position:absolute; inset:0;"></div>
                            </div>
                        @else
                            <div class="flex-1 {{ $bgColor }} flex items-center justify-center min-h-0">
                                <i class="{{ $cardIcon }} {{ $iconColor }} text-5xl opacity-40"></i>
                            </div>
                        @endif

                        {{-- Info del recurso --}}
                        <div class="flex flex-col gap-3 p-4 shrink-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-bold text-gray-800 leading-snug line-clamp-2 flex-1">
                                    {{ $recurso->titulo }}
                                </p>
                                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full shrink-0 {{ $badgeClass }}">
                                    {{ $badgeText }}
                                </span>
                            </div>

                            @if ($subtitulo)
                                <p class="text-xs text-gray-400 truncate -mt-1">{{ $subtitulo }}</p>
                            @endif

                            <div class="flex items-center gap-1.5 text-xs text-indigo-500 font-semibold
                                        group-hover:text-indigo-700 transition-colors">
                                Abrir
                                <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
