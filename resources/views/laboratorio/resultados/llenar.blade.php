@php
    $primaryColor = $formulario->color_primario;
    $secondaryColor = $formulario->color_secundario;
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 rounded shadow bg-white" style="border-top: 6px solid {{ $primaryColor }}">

        {{-- Paso inicial --}}
        <div x-data="{ cantidad: 0, cantidadTemp: 1, actual: 0 }">
            <template x-if="cantidad === 0">
                <div class="text-center">
                    <h2 class="font-bold text-lg mb-4" style="color: {{ $primaryColor }}">
                        ¿Cuántos formularios desea generar?
                    </h2>

                    <div class="flex items-center justify-center gap-3">
                        <input type="number" min="1" x-model.number="cantidadTemp" max="10"
                            class="border rounded px-2 py-1 w-24 text-center"
                            style="border-color: {{ $primaryColor }}" />

                        <button
                            @click="if (cantidadTemp >= 1) { cantidad = cantidadTemp; actual = 0 } else alert('Ingrese un valor >= 1')"
                            class="px-4 py-2 text-white rounded" style="background-color: {{ $primaryColor }}">
                            Generar
                        </button>
                    </div>
                </div>
            </template>

            {{-- Cuando ya hay formularios --}}
            <template x-if="cantidad > 0">
                <div>
                    <div class="space-y-4">
                        {{-- Botón volver --}}
                        <div>
                            <button @click="cantidad = 0; actual = 0"
                                class="inline-flex items-center px-4 py-2 text-sm rounded-md transition"
                                style="background-color: {{ $primaryColor }}; color: #fff;">
                                <i class="fas fa-arrow-left mr-2"></i> Volver
                            </button>
                        </div>

                        {{-- Encabezado --}}
                        <div class="grid grid-cols-2 gap-6 text-center">
                            <div class="p-3 rounded-lg shadow-sm" style="background-color: {{ $primaryColor }}20;">
                                <span class="block text-xs text-gray-500">Nombre del formulario</span>
                                <span class="text-sm font-semibold" style="color: {{ $primaryColor }}">
                                    {{ $formulario->nombre }}
                                </span>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg shadow-sm">
                                <span class="block text-xs text-gray-500">Código del formulario</span>
                                <span class="text-sm font-semibold text-gray-800">
                                    {{ $formulario->codigo }}
                                </span>
                            </div>
                        </div>

                        {{-- Nota --}}
                        @if (!empty($formulario->nota))
                            <div class="p-2 rounded-lg mb-2 bg-yellow-100 border border-yellow-300">
                                <span class="text-sm text-yellow-800">{{ $formulario->nota }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Slides --}}
                    <div class="relative">
                        <template x-for="i in cantidad" :key="i">
                            <div x-show="actual === (i-1)" x-transition class="space-y-4">
                                <form method="POST"
                                    action="{{ route('laboratorio.formularios.guardar', $formulario->id) }}">
                                    @csrf

                                    <h2 class="font-bold text-lg mb-2" style="color: {{ $primaryColor }}">
                                        Formulario <span x-text="i"></span> / <span x-text="cantidad"></span>
                                    </h2>

                                    {{-- Secciones --}}
                                    @foreach ($formulario->secciones as $seccion)
                                        <div class="border p-4 rounded mb-4">
                                            <h3 class="font-bold px-2 py-1 text-sm uppercase mb-3 text-white rounded"
                                                style="background-color: {{ $primaryColor }}">
                                                {{ $seccion->nombre }}
                                            </h3>

                                            @if ($seccion->descripcion)
                                                <p class="text-xs mb-3" style="color: {{ $secondaryColor }}">
                                                    {{ $seccion->descripcion }}
                                                </p>
                                            @endif

                                            <table class="w-full text-sm border">
                                                <thead style="background-color: {{ $primaryColor }}15;">
                                                    <tr>
                                                        <th class="border px-2 py-1">Parámetro</th>
                                                        <th class="border px-2 py-1">Resultado</th>
                                                        @if ($seccion->parametros->whereNotNull('unidad')->count() > 0)
                                                            <th class="border px-2 py-1">Unidad</th>
                                                        @endif
                                                        @if ($seccion->parametros->where('tipo', 'select')->count() > 0)
                                                            <th class="border px-2 py-1">Método</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Aquí se mantiene tu foreach de parámetros --}}
                                                    @foreach ($seccion->parametros as $parametro)
                                                        <tr>
                                                            <td class="border px-2 py-1">{{ $parametro->nombre }}</td>
                                                            <td class="border px-2 py-1">
                                                                @if ($parametro->tipo === 'text')
                                                                    <input
                                                                        :name="'respuestas[' + (i - 1) +
                                                                        '][{{ $parametro->id }}]'"
                                                                        type="text"
                                                                        class="w-full border rounded px-2 py-1 text-xs"
                                                                        @if ($parametro->requerido) required @endif
                                                                        @if ($parametro->validacion) pattern="^{{ $parametro->validacion }}$" title="Formato esperado" @endif>
                                                                @elseif ($parametro->tipo === 'number')
                                                                    <input
                                                                        :name="'respuestas[' + (i - 1) +
                                                                        '][{{ $parametro->id }}]'"
                                                                        type="number" step="any"
                                                                        @if ($parametro->requerido) required @endif
                                                                        class="w-full border rounded px-2 py-1 text-xs"
                                                                        @if ($parametro->validacion) pattern="^{{ $parametro->validacion }}$" title="Formato esperado" @endif>
                                                                @elseif ($parametro->tipo === 'date')
                                                                    <input
                                                                        :name="'respuestas[' + (i - 1) +
                                                                        '][{{ $parametro->id }}]'"
                                                                        type="date"
                                                                        class="w-full border rounded px-2 py-1 text-xs">
                                                                @elseif ($parametro->tipo === 'select' && $parametro->grupoSelector)
                                                                    <select
                                                                        :name="'respuestas[' + (i - 1) +
                                                                        '][{{ $parametro->id }}]'"
                                                                        class="w-full border rounded px-2 py-1 text-xs">
                                                                        <option value="">-- Seleccione --</option>
                                                                        @foreach ($parametro->grupoSelector->opciones as $op)
                                                                            <option value="{{ $op->valor }}">
                                                                                {{ $op->etiqueta }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                @elseif ($parametro->tipo === 'checkbox')
                                                                    <input
                                                                        :name="'respuestas[' + (i - 1) +
                                                                        '][{{ $parametro->id }}]'"
                                                                        type="checkbox" value="1">
                                                                @elseif ($parametro->tipo === 'textarea')
                                                                    <textarea :name="'respuestas[' + (i - 1) + '][{{ $parametro->id }}]'" class="w-full border rounded px-2 py-1 text-xs"
                                                                        @if ($parametro->validacion) pattern="{{ $parametro->validacion }}" title="Formato esperado" @endif></textarea>
                                                                @endif
                                                            </td>

                                                            <!-- Unidad -->
                                                            @if ($seccion->parametros->whereNotNull('unidad')->count() > 0)
                                                                <td class="border px-2 py-1 text-center">
                                                                    {{ $parametro->unidad ?? '--' }}</td>
                                                            @endif

                                                            <!-- Método (si aplica) -->
                                                            @if (
                                                                $parametro->grupoSelector &&
                                                                    $parametro->grupoSelector->opciones &&
                                                                    $parametro->grupoSelector->opciones->count() > 0)
                                                                <td class="border px-2 py-1 text-center">
                                                                    <select
                                                                        :name="'metodos[' + (i - 1) +
                                                                        '][{{ $parametro->id }}]'"
                                                                        class="w-full border rounded px-2 py-1 text-xs">
                                                                        <option value="">Seleccione</option>
                                                                        @foreach ($parametro->grupoSelector->opciones as $op)
                                                                            <option value="{{ $op->valor }}">
                                                                                {{ $op->etiqueta }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                            @endif

                                                        </tr>
                                                        </td>
                                                        {{-- Unidad y método siguen igual --}}
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach

                                    {{-- Observación --}}
                                    <div class="border p-4 rounded mb-4">
                                        <h3 class="font-bold px-2 py-1 text-sm uppercase mb-3 text-white rounded"
                                            style="background-color: {{ $primaryColor }}">
                                            Observación
                                        </h3>
                                        <textarea :name="'observacion[' + (i - 1) + ']'" rows="3" placeholder="Escriba alguna observación..."
                                            class="w-full border rounded px-2 py-1 text-sm" style="border-color: {{ $primaryColor }}"></textarea>
                                    </div>

                                    {{-- Navegación --}}
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <button type="button" @click="if (actual > 0) actual--"
                                                class="px-3 py-1 rounded"
                                                style="background-color: {{ $secondaryColor }}20">
                                                Anterior
                                            </button>
                                            <button type="button" @click="if (actual < cantidad-1) actual++"
                                                class="px-3 py-1 rounded"
                                                style="background-color: {{ $secondaryColor }}20">
                                                Siguiente
                                            </button>
                                        </div>
                                        <a href="{{ route('lab.inscritos-ensayos.index') }}"
                                            class="px-3 py-1 text-white rounded" style="background-color: red;">
                                            Cancelar
                                        </a>
                                        <div>
                                            <button type="submit" class="px-2 py-1 text-white rounded"
                                                style="background-color: {{ $primaryColor }}">
                                                Guardar este formulario
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-app-layout>
