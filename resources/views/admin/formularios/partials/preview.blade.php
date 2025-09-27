{{-- Secciones --}}
@foreach ($formulario->secciones as $secIdx => $seccion)
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
                    @foreach ($seccion->headers as $header)
                        <th class="border px-2 py-1">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($seccion->parametros as $paramIdx => $parametro)
                    <tr>
                        <td class="border px-2 py-1">{{ $parametro->nombre }}</td>
                        @foreach ($parametro->campos as $campoIdx => $campo)
                            <td class="border px-2 py-1">

                                @php
                                    $inputName = "secciones[$secIdx][parametros][$paramIdx][campos][$campoIdx][valor]";
                                @endphp

                                @if ($campo->tipo === 'text' || $campo->tipo === 'number' || $campo->tipo === 'date')
                                    <input type="{{ $campo->tipo }}" name="{{ $inputName }}"
                                        value="{{ old($inputName, '') }}" placeholder="{{ $campo->placeholder }}"
                                        @if ($campo->requerido) required @endif
                                        @if ($campo->min !== null) min="{{ $campo->min }}" @endif
                                        @if ($campo->max !== null) max="{{ $campo->max }}" @endif
                                        step="{{ $campo->step ?? 1 }}"
                                        class="w-full border rounded px-2 py-1 text-xs campo-entrada">
                                @elseif ($campo->tipo === 'textarea')
                                    <textarea name="{{ $inputName }}" class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                        placeholder="{{ $campo->placeholder }}" @if ($campo->requerido) required @endif></textarea>
                                @elseif ($campo->tipo === 'select' && $campo->grupoSelector)
                                    <select name="{{ $inputName }}"
                                        class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                        @if ($campo->requerido) required @endif>
                                        <option value="">Seleccione</option>
                                        @foreach ($campo->grupoSelector->opciones as $op)
                                            <option value="{{ $op->valor }}">{{ $op->valor }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($campo->tipo === 'datalist' && $campo->grupoSelector)
                                    <input type="text" list="datalist_{{ $campo->id }}"
                                        name="{{ $inputName }}"
                                        class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                        placeholder="{{ $campo->placeholder }}"
                                        @if ($campo->requerido) required @endif>
                                    <datalist id="datalist_{{ $campo->id }}">
                                        @foreach ($campo->grupoSelector->opciones as $op)
                                            <option value="{{ $op->valor }}"></option>
                                        @endforeach
                                    </datalist>
                                @elseif ($campo->tipo === 'checkbox')
                                    <input type="checkbox" name="{{ $inputName }}" value="1"
                                        @if ($campo->requerido) required @endif>
                                @endif

                                {{-- @if ($campo->unidad)
                                    <div class="text-center text-xs">{{ $campo->unidad }}</div>
                                @endif --}}

                                {{-- Campos hidden para mantener estructura JSON --}}
                                <input type="hidden" name="secciones[{{ $secIdx }}][id]"
                                    value="{{ $seccion->id }}">
                                <input type="hidden" name="secciones[{{ $secIdx }}][nombre]"
                                    value="{{ $seccion->nombre }}">
                                <input type="hidden" name="secciones[{{ $secIdx }}][descripcion]"
                                    value="{{ $seccion->descripcion }}">
                                <input type="hidden"
                                    name="secciones[{{ $secIdx }}][parametros][{{ $paramIdx }}][id]"
                                    value="{{ $parametro->id }}">
                                <input type="hidden"
                                    name="secciones[{{ $secIdx }}][parametros][{{ $paramIdx }}][nombre]"
                                    value="{{ $parametro->nombre }}">
                                <input type="hidden"
                                    name="secciones[{{ $secIdx }}][parametros][{{ $paramIdx }}][visible_nombre]"
                                    value="{{ $parametro->visible_nombre }}">
                                <input type="hidden"
                                    name="secciones[{{ $secIdx }}][parametros][{{ $paramIdx }}][campos][{{ $campoIdx }}][id]"
                                    value="{{ $campo->id }}">
                                <input type="hidden"
                                    name="secciones[{{ $secIdx }}][parametros][{{ $paramIdx }}][campos][{{ $campoIdx }}][label]"
                                    value="{{ $campo->label }}">
                                <input type="hidden"
                                    name="secciones[{{ $secIdx }}][parametros][{{ $paramIdx }}][campos][{{ $campoIdx }}][tipo]"
                                    value="{{ $campo->tipo }}">
                                @if ($campo->unidad)
                                    <input type="hidden"
                                        name="secciones[{{ $secIdx }}][parametros][{{ $paramIdx }}][campos][{{ $campoIdx }}][unidad]"
                                        value="{{ $campo->unidad }}">
                                @endif
                            </td>
                            @if ($campo->unidad)
                                <td class="border px-2 py-1 text-center">
                                    {{ $campo->unidad }}
                                </td>
                            @endif
                        @endforeach
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
    <textarea name="observaciones" rows="3" placeholder="Escriba alguna observación..."
        class="w-full border rounded px-2 py-1 text-sm" style="border-color: {{ $primaryColor }}"></textarea>
</div>
