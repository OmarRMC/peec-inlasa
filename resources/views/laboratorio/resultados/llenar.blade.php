<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 bg-white rounded shadow" x-data="{ cantidad: 0, cantidadTemp: 1, actual: 0, color: '#2563eb' }">
        <template x-if="cantidad === 0">
            <div class="text-center">
                <h2 class="font-bold text-lg mb-4">¿Cuántos formularios desea generar?</h2>

                <div class="flex items-center justify-center gap-3">
                    <input type="number" min="1" x-model.number="cantidadTemp" max="10"
                        class="border rounded px-2 py-1 w-24 text-center" />
                    <button
                        @click="if (cantidadTemp >= 1) { cantidad = cantidadTemp; actual = 0 } else alert('Ingrese un valor >= 1')"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                        Generar
                    </button>
                </div>
            </div>
        </template>

        <template x-if="cantidad > 0">
            <div>
                <div class="space-y-4">
                    <div>
                        <button @click="cantidad = 0; actual = 0"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 transition"><i
                                class="fas fa-arrow-left mr-2"></i> Volver</button>
                    </div>
                    <div class="grid grid-cols-2 gap-6 text-center">
                        <div class="p-3 bg-gray-50 rounded-lg shadow-sm">
                            <span class="block text-xs text-gray-500">Nombre del formulario</span>
                            <span class="text-sm font-semibold text-gray-800">
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

                    <!-- Nota (solo si existe) -->
                    @if (!empty($formulario->nota))
                        <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow text-center">
                            <span class="block text-xs text-blue-100 uppercase tracking-wide">Nota</span>
                            <span class="text-lg font-bold text-white">{{ $formulario->nota }}</span>
                        </div>
                    @endif
                </div>


                <!-- Slides -->
                <div class="relative">
                    <template x-for="i in cantidad" :key="i">
                        <div x-show="actual === (i-1)" x-transition class="space-y-4">
                            <form method="POST"
                                action="{{ route('laboratorio.formularios.guardar', $formulario->id) }}">
                                @csrf

                                <h2 class="font-bold text-lg mb-2">Formulario <span x-text="i"></span> / <span
                                        x-text="cantidad"></span></h2>

                                @foreach ($formulario->secciones as $seccion)
                                    <div class="border p-4 rounded mb-4">
                                        <h3 class="bg-gray-100 font-bold px-2 py-1 text-sm uppercase mb-3">
                                            {{ $seccion->nombre }}
                                        </h3>

                                        @if ($seccion->descripcion)
                                            <p class="text-xs text-gray-600 mb-3">{{ $seccion->descripcion }}</p>
                                        @endif

                                        <table class="w-full text-sm border">
                                            <thead class="bg-gray-50">
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
                                                @foreach ($seccion->parametros as $parametro)
                                                    {{-- {{ $seccion->cantidad_parametros }} --}}
                                                    <tr>
                                                        <td class="border px-2 py-1">{{ $parametro->nombre }}</td>

                                                        <!-- Resultado: name dinámico por índice (i-1) -->
                                                        <td class="border px-2 py-1">
                                                            @if ($parametro->tipo === 'text')
                                                                <input
                                                                    :name="'respuestas[' + (i - 1) + '][{{ $parametro->id }}]'"
                                                                    type="text"
                                                                    class="w-full border rounded px-2 py-1 text-xs"
                                                                    @if ($parametro->requerido) required @endif
                                                                    @if ($parametro->validacion) pattern="^{{ $parametro->validacion }}$" title="Formato esperado" @endif>
                                                            @elseif ($parametro->tipo === 'number')
                                                                <input
                                                                    :name="'respuestas[' + (i - 1) + '][{{ $parametro->id }}]'"
                                                                    type="number" step="any"
                                                                    @if ($parametro->requerido) required @endif
                                                                    class="w-full border rounded px-2 py-1 text-xs"
                                                                    @if ($parametro->validacion) pattern="^{{ $parametro->validacion }}$" title="Formato esperado" @endif>
                                                            @elseif ($parametro->tipo === 'date')
                                                                <input
                                                                    :name="'respuestas[' + (i - 1) + '][{{ $parametro->id }}]'"
                                                                    type="date"
                                                                    class="w-full border rounded px-2 py-1 text-xs">
                                                            @elseif ($parametro->tipo === 'select' && $parametro->grupoSelector)
                                                                <select
                                                                    :name="'respuestas[' + (i - 1) + '][{{ $parametro->id }}]'"
                                                                    class="w-full border rounded px-2 py-1 text-xs">
                                                                    <option value="">-- Seleccione --</option>
                                                                    @foreach ($parametro->grupoSelector->opciones as $op)
                                                                        <option value="{{ $op->valor }}">
                                                                            {{ $op->etiqueta }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @elseif ($parametro->tipo === 'checkbox')
                                                                <input
                                                                    :name="'respuestas[' + (i - 1) + '][{{ $parametro->id }}]'"
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
                                                                    :name="'metodos[' + (i - 1) + '][{{ $parametro->id }}]'"
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
                                                @endforeach

                                                <!-- Parámetros dinámicos -->
                                                <tr x-data="{
                                                    extraParams: [],
                                                    base: {{ $seccion->parametros->count() }},
                                                    max: {{ $seccion->cantidad_parametros }}
                                                }">
                                                    <td colspan="4" class="p-2 text-center">
                                                        <template x-for="(param, index) in extraParams"
                                                            :key="index">
                                                            <div class="flex items-center gap-2 mb-2 justify-center">
                                                                <input
                                                                    :name="'extra[' + (i - 1) + '][{{ $seccion->id }}][' +
                                                                    index + '][nombre]'"
                                                                    type="text" placeholder="Nuevo parámetro"
                                                                    class="border rounded px-2 py-1 text-xs w-1/3">

                                                                <!-- Valor del parámetro -->
                                                                <input
                                                                    :name="'extra[' + (i - 1) + '][{{ $seccion->id }}][' +
                                                                    index + '][valor]'"
                                                                    type="text" placeholder="Valor"
                                                                    class="border rounded px-2 py-1 text-xs w-1/3">

                                                                <!-- Botón Quitar -->
                                                                <button type="button"
                                                                    @click="extraParams.splice(index,1)"
                                                                    class="px-2 py-1 bg-red-500 text-white rounded text-xs">
                                                                    Quitar
                                                                </button>
                                                            </div>
                                                        </template>

                                                        <!-- Botón agregar -->
                                                        <button type="button"
                                                            @click="if (base + extraParams.length < max) extraParams.push({})"
                                                            class="px-3 py-1 bg-green-500 text-white rounded text-xs">
                                                            + Agregar parámetro
                                                        </button>

                                                        <!-- Info de cuantos faltan -->
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            (<span x-text="max - (base + extraParams.length)"></span>
                                                            disponibles)
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                                <div class="border p-4 rounded mb-4">
                                    <h3 class="bg-gray-100 font-bold px-2 py-1 text-sm uppercase mb-3">
                                        Observación
                                    </h3>
                                    <textarea :name="'observacion[' + (i - 1) + ']'" rows="3" placeholder="Escriba alguna observación..."
                                        class="w-full border rounded px-2 py-1 text-sm"></textarea>
                                </div>

                                <div class="flex justify-between items-center">
                                    <div>
                                        <button type="button" @click="if (actual > 0) actual--"
                                            class="px-3 py-1 bg-gray-200 rounded">Anterior</button>
                                        <button type="button" @click="if (actual < cantidad-1) actual++"
                                            class="px-3 py-1 bg-gray-200 rounded">Siguiente</button>
                                    </div>
                                    <a href="{{ route('lab.inscritos-ensayos.index') }}"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        Cancelar
                                    </a>

                                    <div>
                                        <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded">Guardar
                                            este formulario</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </template>
                </div>

            </div>
        </template>

    </div>
</x-app-layout>
