<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 bg-white rounded shadow">
        <!-- Información general -->
        <div class="mb-6 border p-4 bg-gray-50 rounded">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-sm">Gestión:</label>
                    <span class="ml-2">{{ date('Y') }}</span>
                </div>
                <div>
                    <label class="font-semibold text-sm">Ciclo:</label>
                    <span class="ml-2">1</span>
                </div>
                <div>
                    <label class="font-semibold text-sm">Laboratorio:</label>
                    <span class="ml-2">{{ $laboratorio->nombre_lab }}</span>
                </div>
                <div>
                    <label class="font-semibold text-sm">Código LAB:</label>
                    <span class="ml-2">{{ $laboratorio->cod_lab }}</span>
                </div>
                <div class="col-span-2">
                    <label class="font-semibold text-sm">Responsable:</label>
                    <span class="ml-2">{{ $laboratorio->respo_lab }}</span>
                </div>
            </div>
        </div>
        <h2 class="font-bold text-lg mb-3">Resultados Área de {{ $formulario->nombre }}</h2>
        <!-- Formulario para llenar resultados -->
        <form method="POST" action="{{ route('laboratorio.formularios.guardar', $formulario->id) }}">
            @csrf
            <div class="space-y-6">
                @foreach ($formulario->secciones as $i => $seccion)
                    <div class="border p-4 rounded">
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
                                @foreach ($seccion->parametros as $j => $parametro)
                                    <tr>
                                        <!-- Nombre -->
                                        <td class="border px-2 py-1">{{ $parametro->nombre }}</td>

                                        <!-- Resultado -->
                                        <td class="border px-2 py-1">
                                            @if ($parametro->tipo === 'text')
                                                <input type="text" name="respuestas[{{ $parametro->id }}]"
                                                    class="w-full border rounded px-2 py-1 text-xs"
                                                    @if ($parametro->validacion) pattern="{{ $parametro->validacion }}" title="Formato esperado" @endif>
                                            @elseif($parametro->tipo === 'number')
                                                <input type="number" step="any"
                                                    name="respuestas[{{ $parametro->id }}]"
                                                    class="w-full border rounded px-2 py-1 text-xs"
                                                    @if ($parametro->validacion) pattern="{{ $parametro->validacion }}" title="Formato esperado" @endif>
                                            @elseif($parametro->tipo === 'date')
                                                <input type="date" name="respuestas[{{ $parametro->id }}]"
                                                    class="w-full border rounded px-2 py-1 text-xs">
                                            @elseif($parametro->tipo === 'select' && $parametro->grupoSelector)
                                                <select name="respuestas[{{ $parametro->id }}]"
                                                    class="w-full border rounded px-2 py-1 text-xs">
                                                    <option value="">-- Seleccione --</option>
                                                    @foreach ($parametro->grupoSelector->opciones as $op)
                                                        <option value="{{ $op->valor }}">{{ $op->etiqueta }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @elseif($parametro->tipo === 'checkbox')
                                                <input type="checkbox" name="respuestas[{{ $parametro->id }}]"
                                                    value="1">
                                            @elseif($parametro->tipo === 'textarea')
                                                <textarea name="respuestas[{{ $parametro->id }}]" class="w-full border rounded px-2 py-1 text-xs"
                                                    @if ($parametro->validacion) pattern="{{ $parametro->validacion }}" title="Formato esperado" @endif></textarea>
                                            @endif
                                        </td>

                                        <!-- Unidad (solo si aplica) -->
                                        @if ($seccion->parametros->whereNotNull('unidad')->count() > 0)
                                            <td class="border px-2 py-1 text-center">
                                                {{ $parametro->unidad ?? '--' }}
                                            </td>
                                        @endif

                                        @if (
                                            $parametro->grupoSelector &&
                                                $parametro->grupoSelector->opciones &&
                                                $parametro->grupoSelector->opciones->count() > 0)
                                            <td class="border px-2 py-1 text-center">
                                                <select name="metodos[{{ $parametro->id }}]"
                                                    class="w-full border rounded px-2 py-1 text-xs">
                                                    <option value="">-- Seleccione --</option>
                                                    @foreach ($parametro->grupoSelector->opciones as $op)
                                                        <option value="{{ $op->valor }}">{{ $op->etiqueta }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 text-center">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-500">
                    Guardar Resultados
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
