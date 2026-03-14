@php
    $formIdx = $i ?? 1;
    $fromRes = $respuestas[$formIdx - 1] ?? null;
    $observaciones = $respuestas[$formIdx - 1]['observaciones'] ?? '';
    $res = $respuestas[$formIdx - 1]['respuestas'] ?? [];
    $respuestasActuales = collect($res);
    dump($fromRes['id'] ?? '');
    $readonly = $readonly ?? false;

    // Obtener las respuestas anteriores para este índice de formulario (para auto_guardar)
    $respuestasAnterioresList = $respuestasAnterioresList ?? collect([]);
    $respuestaAnteriorActual = $respuestasAnterioresList[$formIdx - 1] ?? null;
    $respuestasAnteriores = $respuestaAnteriorActual ? $respuestaAnteriorActual->respuestas : collect([]);

    // Prefijo para los nombres de los campos (para múltiples formularios)
    $namePrefix = "formularios[$formIdx]";
@endphp
@if ($fromRes)
    <input type="hidden" name="{{ $namePrefix }}[id_formulario_ensayos_resultados]" value="{{ $fromRes['id'] }}">
@endif
{{-- Secciones --}}
@foreach ($formulario->secciones as $secIdx => $seccion)
    <div class="border p-4 rounded mb-4">
        <h3 class="font-bold px-2 py-1 text-sm uppercase  text-white rounded"
            style="background-color: {{ $primaryColor }}">
            {{ $seccion->nombre }}
        </h3>
        @if ($seccion->descripcion)
            <p class="text-xs mb-3 font-bold p-2" style="border-bottom: 2px double  {{ $secondaryColor }}; ">
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
                    @php
                        $valorParametro = $respuestasActuales->firstWhere('id_parametro', $parametro->id);
                        $campoAnterior = '';
                    @endphp
                    <tr data-parametro-id="{{ $parametro->id }}"
                        data-requerido-si-completa="{{ $parametro->requerido_si_completa ? '1' : '0' }}">
                        @if ($parametro->visible_nombre)
                            <td class="border px-2 py-1">
                                <div class="flex flex-wrap gap-[2px] justify-center ">
                                    {{ $parametro->nombre }}
                                </div>
                            </td>
                        @endif
                        @foreach ($parametro->campos as $campoIdx => $campo)
                            @if ($campoAnterior != $campo->label)
                                <td class="border px-2 py-1">
                                    <div class="flex flex-wrap gap-[2px] justify-center">
                                        @php
                                            $campoAnterior = $campo->label;
                                        @endphp
                            @endif
                            @php
                                $inputName = "{$namePrefix}[secciones][$secIdx][parametros][$paramIdx][campos][$campoIdx][valor]";
                                $inputNameText = "{$namePrefix}[secciones][$secIdx][parametros][$paramIdx][campos][$campoIdx][valorTexto]";
                                $parametroRespuestas = collect($valorParametro->respuestas ?? []);
                                $valorCampos = $parametroRespuestas->firstWhere('id', $campo->id);
                                if( $readonly &&  $campo->tipo == 'select'){
                                    $valorCampo = $valorCampos['valorTexto'] ?? null;
                                }else {
                                    $valorCampo = $valorCampos['valor'] ?? null;
                                }

                                // Auto-guardar: si el campo tiene auto_guardar y no tiene valor actual, buscar en respuestas anteriores
                                if ($campo->auto_guardar && empty($valorCampo) && $respuestasAnteriores->isNotEmpty()) {
                                    $respuestaAnteriorParam = $respuestasAnteriores->firstWhere('id_parametro', $parametro->id);
                                    if ($respuestaAnteriorParam) {
                                        $respuestasAnteriorCampos = collect($respuestaAnteriorParam->respuestas ?? []);
                                        $valorAnterior = $respuestasAnteriorCampos->firstWhere('id', $campo->id);
                                        $valorCampo = $valorAnterior['valor'] ?? null;
                                    }
                                }

                                $valorCampoText = '';
                                if ($campo->grupoSelector) {
                                    foreach ($campo->grupoSelector->opciones as $op) {
                                        if ($op->id == $valorCampo) {
                                            $valorCampoText = $op->valor;
                                            break;
                                        }
                                    }
                                }
                                if ($readonly) {
                                    $campo->modificable = false;
                                }
                            @endphp
                            
                            @if($readonly)
                                <span>{{$valorCampo}}</span>
                            @else

                            @if ($campo->tipo === 'text' || $campo->tipo === 'number' || $campo->tipo === 'date')
                                <input type="{{ $campo->tipo }}" name="{{ $inputName }}"
                                    value="{{ old($inputName, $campo->valor ?? $valorCampo) }}"
                                    placeholder="{{ $campo->placeholder }}"
                                    data-es-requerido={{ $campo->requerido ? 1 : 0 }}
                                    @if (!$parametro->requerido_si_completa) @if ($campo->requerido) required @endif
                                    @endif
                                @if ($campo->min !== null) min="{{ $campo->min }}" @endif
                                @if ($campo->max !== null) max="{{ $campo->max }}" @endif
                                @if ($campo->min !== null) minlength="{{ $campo->min }}" @endif
                                @if ($campo->max !== null) maxlength="{{ $campo->max }}" @endif
                                @if ($campo->step) step="{{ $campo->step ?? 1 }}" @endif
                                @readonly(!$campo->modificable) @if ($campo->pattern)
                                    pattern="{{ $campo->pattern }}"
                                @endif
                                class="w-full px-2 py-1 text-xs campo-entrada 
                                        {{ !$campo->modificable ? 'border-0 bg-transparent text-gray-700 focus:ring-0 cursor-default' : 'border rounded' }}"
                                @if ($campo->mensaje)
                                    title="{{ $campo->mensaje }}"
                                    data-mensaje-validacion="{{ $campo->mensaje }}"
                                @endif>
                            @elseif ($campo->tipo === 'textarea')
                                <textarea name="{{ $inputName }}"
                                    class="w-full {{ !$campo->modificable ? 'border-0 bg-transparent cursor-default pointer-events-none' : 'border rounded px-2 py-1' }} text-xs"
                                    placeholder="{{ $campo->placeholder }}"
                                    @if (!$parametro->requerido_si_completa) @if ($campo->requerido) required @endif @endif @readonly(!$campo->modificable) 
                                    @if ($campo->mensaje) title="{{ $campo->mensaje }}"
                                    data-mensaje-validacion="{{ $campo->mensaje }}"@endif>{{ $valorCampo??'' }}</textarea>
                            @elseif ($campo->tipo === 'select' && ($campo->grupoSelector || $campo->id_campo_padre))
                                <select name="{{ $inputName }}"
                                    class="w-full border rounded px-2 py-1 text-xs campo-entrada select-dependiente"
                                    @if ($campo->id_campo_padre || !$campo->modificable) disabled @endif data-id="{{ $campo->id }}"
                                    data-id-padre="{{ $campo->id_campo_padre ?? '' }}"
                                    @if (!$parametro->requerido_si_completa) @if ($campo->requerido) required @endif
                                    @endif
                                    @if ($campo->mensaje) title="{{ $campo->mensaje }}"
                                        data-mensaje-validacion="{{ $campo->mensaje }}" @endif
                                    >
                                    @if ($campo->id_campo_padre && $valorCampo)
                                        <option value="{{ $valorCampo }}" selected>{{ $valorCampo }}</option>
                                    @else
                                        <option value="">Seleccione</option>
                                    @endif
                                    @if ($campo->grupoSelector)
                                        @foreach ($campo->grupoSelector->opciones as $op)
                                            <option value="{{ $op->id }}"
                                                @if ($op->id == $valorCampo) selected @endif>
                                                {{ $op->valor }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if (!$readonly)
                                <input type="hidden" name="{{ $inputNameText }}" id="inputTexto{{ $campo->id }}"
                                    value="{{ $valorCampoText }}">
                                @endif
                            @elseif ($campo->tipo === 'datalist' && $campo->grupoSelector)
                                <input type="text" list="datalist_{{ $campo->id }}"
                                    @if ($campo->mensaje) title="{{ $campo->mensaje }}"
                                        data-mensaje-validacion="{{ $campo->mensaje }}" @endif
                                    @if (isset($valorCampo)) value="{{ $valorCampo }}" @endif
                                    name="{{ $inputName }}"
                                    class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                    placeholder="{{ $campo->placeholder }}"
                                    @if (!$parametro->requerido_si_completa) @if ($campo->requerido) required @endif
                                    @endif
                                >
                                <datalist id="datalist_{{ $campo->id }}">
                                    @foreach ($campo->grupoSelector->opciones as $op)
                                        <option value="{{ $op->valor }}"></option>
                                    @endforeach
                                </datalist>
                            @elseif ($campo->tipo === 'checkbox')
                                <input type="hidden" name="{{ $inputName }}" value="0">
                                <input type="checkbox" name="{{ $inputName }}" value="1"
                                    @if ($campo->mensaje) title="{{ $campo->mensaje }}"
                                        data-mensaje-validacion="{{ $campo->mensaje }}" @endif
                                    @if ($valorCampo) checked @endif
                                    @if (!$parametro->requerido_si_completa && $campo->requerido) required @endif>
                            @endif
                            {{-- Campos hidden para mantener estructura JSON --}}
                            <input type="hidden" name="{{ $namePrefix }}[secciones][{{ $secIdx }}][id]"
                                value="{{ $seccion->id }}">
                            <input type="hidden" name="{{ $namePrefix }}[secciones][{{ $secIdx }}][nombre]"
                                value="{{ $seccion->nombre }}">
                            <input type="hidden" name="{{ $namePrefix }}[secciones][{{ $secIdx }}][descripcion]"
                                value="{{ $seccion->descripcion }}">
                            <input type="hidden"
                                name="{{ $namePrefix }}[secciones][{{ $secIdx }}][parametros][{{ $paramIdx }}][id]"
                                value="{{ $parametro->id }}">
                            <input type="hidden"
                                name="{{ $namePrefix }}[secciones][{{ $secIdx }}][parametros][{{ $paramIdx }}][nombre]"
                                value="{{ $parametro->nombre }}">
                            <input type="hidden"
                                name="{{ $namePrefix }}[secciones][{{ $secIdx }}][parametros][{{ $paramIdx }}][visible_nombre]"
                                value="{{ $parametro->visible_nombre }}">
                            <input type="hidden"
                                name="{{ $namePrefix }}[secciones][{{ $secIdx }}][parametros][{{ $paramIdx }}][campos][{{ $campoIdx }}][id]"
                                value="{{ $campo->id }}">
                            <input type="hidden"
                                name="{{ $namePrefix }}[secciones][{{ $secIdx }}][parametros][{{ $paramIdx }}][campos][{{ $campoIdx }}][label]"
                                value="{{ $campo->label }}">
                            <input type="hidden"
                                name="{{ $namePrefix }}[secciones][{{ $secIdx }}][parametros][{{ $paramIdx }}][campos][{{ $campoIdx }}][tipo]"
                                value="{{ $campo->tipo }}">
                            @if ($campo->unidad)
                                <input type="hidden"
                                    name="{{ $namePrefix }}[secciones][{{ $secIdx }}][parametros][{{ $paramIdx }}][campos][{{ $campoIdx }}][unidad]"
                                    value="{{ $campo->unidad }}">
                            @endif
                            @if ($campoAnterior != $campo->label)
    </div>
    </td>
@endif
@endif
@endforeach
</tr>
@endforeach
</tbody>
</table>
</div>
@endforeach

{{-- Observación --}}
{{-- <div class="border p-4 rounded mb-4">
    <h3 class="font-bold px-2 py-1 text-sm uppercase mb-3 text-white rounded"
        style="background-color: {{ $primaryColor }}">
        Observación
    </h3>
    <textarea name="observaciones" rows="3" placeholder="Escriba alguna observación..."
        class="w-full border rounded px-2 py-1 text-sm" style="border-color: {{ $primaryColor }}">{{ $observaciones }}</textarea>
</div> --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select').forEach((select) => {
                const actualizarTexto = () => {
                    const texto = select.options[select.selectedIndex]?.text || '';
                    const campoId = select.dataset.id;
                    const valor = select.value;
                    const input = document.getElementById('inputTexto' + campoId);
                    if (input) {
                        input.value = valor ? texto : '';
                    }
                };

                actualizarTexto();

                select.addEventListener('change', actualizarTexto);
            });


            document.querySelectorAll('tr[data-requerido-si-completa="1"]').forEach(function(row) {
                const inputs = row.querySelectorAll('.campo-entrada');

                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        // Verificar si alguno de los campos tiene valor
                        const algunoLleno = Array.from(inputs).some(i => i.value.trim() !==
                            "");

                        if (algunoLleno) {
                            inputs.forEach(i => {
                                if (i.dataset.esRequerido === "1") {
                                    i.setAttribute('required', 'required');
                                } else {
                                    i.removeAttribute('required');
                                }
                            });
                        } else {
                            // Si ninguno tiene valor → todos opcionales
                            inputs.forEach(i => i.removeAttribute('required'));
                        }
                    });
                });
            });

            document.querySelectorAll('.select-dependiente').forEach(function(select) {
                const idPadre = select.dataset.idPadre;

                if (idPadre) {
                    const padre = document.querySelector(`.select-dependiente[data-id="${idPadre}"]`);
                    if (padre) {
                        padre.addEventListener('change', async function() {
                            const valorPadre = padre.value;

                            if (!valorPadre) {
                                select.innerHTML = '<option value="">Seleccione</option>';
                                select.disabled = true;
                                return;
                            }
                            try {
                                const routeOpciones = @json(route('admin.grupos-selectores.opciones', ['id' => ':id']));
                                let url = routeOpciones.replace(':id', valorPadre);
                                const response = await fetch(url);
                                const res = await response.json();
                                const data = res.data;
                                select.innerHTML = '<option value="">Seleccione</option>';
                                data?.forEach(op => {
                                    const option = document.createElement('option');
                                    option.value = op.id;
                                    option.textContent = op.valor ?? op.id;
                                    select.appendChild(option);
                                });
                                select.disabled = false;
                            } catch (error) {
                                console.error("Error cargando opciones:", error);
                            }
                        });
                    }
                }
            });
        });
    </script>
@endpush
