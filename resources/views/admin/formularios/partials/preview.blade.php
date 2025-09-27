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
                                @if ($campo->tipo === 'text' || $campo->tipo === 'number' || $campo->tipo === 'date')
                                    <input type="{{ $campo->tipo }}"
                                        :name="'respuestas[' + (i - 1) +
                                        '][{{ $parametro->id }}][{{ $campo->id }}]'"
                                        value="{{ old('respuestas.' . $parametro->id . '.' . $campo->id, '') }}"
                                        placeholder="{{ $campo->placeholder }}"
                                        @if ($campo->requerido) required @endif
                                        @if ($campo->min !== null) min="{{ $campo->min }}" @endif
                                        @if ($campo->max !== null) max="{{ $campo->max }}" @endif
                                        @if ($campo->minlength !== null) minlength="{{ $campo->minlength }}" @endif
                                        @if ($campo->maxlength !== null) maxlength="{{ $campo->maxlength }}" @endif
                                        @if ($campo->pattern) pattern="{{ $campo->pattern }}" @endif
                                        step="{{ $campo->step ?? 1 }}"
                                        class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                        @if ($campo->label) data-tippy-content="{{ $campo->label ?? '' }}" @endif
                                        data-mensaje-error="{{ $campo->nota_validacion ?? 'Valor inválido' }}">
                                @elseif ($campo->tipo === 'textarea')
                                    <textarea @if ($campo->label) data-tippy-content="{{ $campo->label ?? '' }}" @endif
                                        :name="'respuestas[' + (i - 1) +
                                        '][{{ $parametro->id }}][{{ $campo->id }}]'"
                                        class="w-full border rounded px-2 py-1 text-xs campo-entrada" placeholder="{{ $campo->placeholder }}"
                                        data-mensaje-error="{{ $campo->nota_validacion ?? 'Valor inválido' }}"
                                        @if ($campo->requerido) required @endif></textarea>
                                @elseif ($campo->tipo === 'select' && $campo->grupoSelector)
                                    <select
                                        @if ($campo->label) data-tippy-content="{{ $campo->label ?? '' }}" @endif
                                        :name="'respuestas[' + (i - 1) +
                                        '][{{ $parametro->id }}][{{ $campo->id }}]'"
                                        class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                        @if ($campo->requerido) required @endif
                                        data-mensaje-error="{{ $campo->nota_validacion ?? 'Valor inválido' }}">
                                        <option value="">Seleccione
                                        </option>
                                        @foreach ($campo->grupoSelector->opciones as $op)
                                            <option value="{{ $op->valor }}">
                                                {{ $op->valor }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif ($campo->tipo === 'datalist' && $campo->grupoSelector)
                                    <div class="relative">
                                        <input type="text" list="datalist_{{ $campo->id }}"
                                            :name="'respuestas[' + (i - 1) +
                                            '][{{ $parametro->id }}][{{ $campo->id }}]'"
                                            class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                            placeholder="{{ $campo->placeholder }}"
                                            value="{{ old('respuestas.' . $parametro->id . '.' . $campo->id, '') }}"
                                            @if ($campo->requerido) required @endif
                                            @if ($campo->label) data-tippy-content="{{ $campo->label }}" @endif
                                            data-mensaje-error="{{ $campo->nota_validacion ?? 'Valor inválido' }}">

                                        <datalist id="datalist_{{ $campo->id }}">
                                            @foreach ($campo->grupoSelector->opciones as $op)
                                                <option value="{{ $op->valor }}">
                                                </option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                @elseif ($campo->tipo === 'checkbox')
                                    <input type="checkbox" @if ($campo->requerido) required @endif
                                        @if ($campo->label) data-tippy-content="{{ $campo->label }}" @endif
                                        :name="'respuestas[' + (i - 1) +
                                        '][{{ $parametro->id }}][{{ $campo->id }}]'"
                                        value="1">
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
    <textarea :name="'observacion[' + (i - 1) + ']'" rows="3" placeholder="Escriba alguna observación..."
        class="w-full border rounded px-2 py-1 text-sm" style="border-color: {{ $primaryColor }}"></textarea>
</div>
