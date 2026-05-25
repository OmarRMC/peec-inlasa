<x-app-layout>
    <div class="px-4 max-w-5xl mx-auto">
        {{-- Breadcrumb --}}
        <div class="mb-4">
            <nav class="text-xs text-gray-500 mb-1">
                <a href="{{ route('reportes.resultados.ensayos') }}" class="hover:underline">Reporte de ensayos</a>
                <span class="mx-1">/</span>
                <span>{{ $ensayo->paquete->descripcion ?? 'Sin paquete' }}</span>
            </nav>
            <h1 class="text-xl font-bold text-gray-800">
                <i class="fas fa-flask text-indigo-500 mr-1"></i>
                {{ $ensayo->descripcion }}
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Selecciona un formulario para ver las estadísticas descriptivas de las respuestas.
            </p>
        </div>

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Selectores: gestion + ciclo (separados) --}}
        <div class="flex flex-wrap items-center gap-2 mb-4 text-sm">
            <label class="whitespace-nowrap text-gray-700 font-medium">Gestión:</label>
            <select onchange="window.location.href = '{{ route('explorador-respuestas.ensayo', $ensayo->id) }}?gestion=' + this.value"
                class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[100px]">
                @foreach ($gestiones as $g)
                    <option value="{{ $g }}" @if ($g == $gestion) selected @endif>{{ $g }}</option>
                @endforeach
            </select>

            <label class="whitespace-nowrap text-gray-700 font-medium ml-2">Ciclo:</label>
            <select onchange="window.location.href = '{{ route('explorador-respuestas.ensayo', $ensayo->id) }}?gestion={{ $gestion }}&ciclo=' + this.value"
                class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[200px]">
                @foreach ($ciclos as $c)
                    @php $esActivo = $ensayo->getCicloActivo()?->id === $c->id; @endphp
                    <option value="{{ $c->id }}" @if ($c->id == $ciclo->id) selected @endif>
                        {{ $c->nombre }}{{ $esActivo ? ' - Activo' : '' }}
                    </option>
                @endforeach
            </select>

            <a href="{{ $backUrl }}"
                class="ml-auto flex items-center gap-2 px-4 py-2 border rounded-md text-sm bg-white hover:bg-gray-50 shadow-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        {{-- Formularios del ensayo --}}
        @if ($formularios->isEmpty())
            <div class="bg-white border rounded p-8 text-center text-gray-500">
                <i class="fas fa-file-circle-xmark text-3xl mb-2"></i>
                <p>Este ensayo no tiene formularios registrados.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($formularios as $form)
                    <a href="{{ route('explorador-respuestas.formulario', [$ensayo->id, $ciclo->id, $form->id]) }}"
                        class="block bg-white border rounded p-4 hover:shadow hover:border-indigo-300 transition">
                        <div class="flex items-start gap-3">
                            <div class="text-indigo-500 text-2xl">
                                <i class="fas fa-file-lines"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-bold text-gray-800 leading-tight">
                                    {{ $form->nombre }}
                                </div>
                                @if ($form->descripcion)
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ $form->descripcion }}
                                    </div>
                                @endif
                                <div class="text-[11px] text-gray-400 mt-2">
                                    <i class="fas fa-chart-mixed"></i> Ver estadísticas descriptivas →
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
