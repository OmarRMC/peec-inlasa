@php
    $fromRes = $respuestas[$i - 1] ?? null;
    $observaciones = $respuestas[$i - 1]['observaciones'] ?? '';
    $res = $respuestas[$i - 1]['respuestas'] ?? [];
    $respuestas = collect($res);
    dump($fromRes['id'] ?? '');
@endphp
@if ($fromRes)
    <input type="hidden" name="id_formulario_ensayos_resultados" value="{{ $fromRes['id'] }}">
@endif
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
                    @php
                        $valorParametro = $respuestas->firstWhere('id_parametro', $parametro->id);
                    @endphp
                    <tr data-parametro-id="{{ $parametro->id }}"
                        data-requerido-si-completa="{{ $parametro->requerido_si_completa ? '1' : '0' }}">
                        @if ($parametro->visible_nombre)
                            <td class="border px-2 py-1">{{ $parametro->nombre }}
                        @endif
                        </td>
                        @foreach ($parametro->campos as $campoIdx => $campo)
                            <td class="border px-2 py-1">
                                @php
                                    $inputName = "secciones[$secIdx][parametros][$paramIdx][campos][$campoIdx][valor]";
                                    $parametroRespuestas = collect($valorParametro->respuestas ?? []);
                                    $valorCampos = $parametroRespuestas->firstWhere('id', $campo->id);
                                    $valorCampo = $valorCampos['valor'] ?? null;
                                @endphp

                                @if ($campo->tipo === 'text' || $campo->tipo === 'number' || $campo->tipo === 'date')
                                    <input type="{{ $campo->tipo }}" name="{{ $inputName }}"
                                        value="{{ old($inputName, $campo->valor ?? $valorCampo) }}"
                                        placeholder="{{ $campo->placeholder }}"
                                        @if ($campo->requerido) required @endif
                                        @if ($campo->min !== null) min="{{ $campo->min }}" @endif
                                        @if ($campo->max !== null) max="{{ $campo->max }}" @endif
                                        step="{{ $campo->step ?? 1 }}" @readonly(!$campo->modificable)
                                        pattern="{{$campo->pattern}}"
                                        class="w-full px-2 py-1 text-xs campo-entrada 
                                        {{ !$campo->modificable ? 'border-0 bg-transparent text-gray-700 focus:ring-0 cursor-default' : 'border rounded' }}">
                                @elseif ($campo->tipo === 'textarea')
                                    <textarea name="{{ $inputName }}" class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                        placeholder="{{ $campo->placeholder }}" @if ($campo->requerido) required @endif @readonly(!$campo->modificable)></textarea>
                                @elseif ($campo->tipo === 'select' && ($campo->grupoSelector || $campo->id_campo_padre))
                                    <select name="{{ $inputName }}"
                                        class="w-full border rounded px-2 py-1 text-xs campo-entrada select-dependiente"
                                        @if ($campo->id_campo_padre) disabled @endif
                                        data-id="{{ $campo->id }}"
                                        data-id-padre="{{ $campo->id_campo_padre ?? '' }}">
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
        class="w-full border rounded px-2 py-1 text-sm" style="border-color: {{ $primaryColor }}">{{ $observaciones }}</textarea>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('tr[data-requerido-si-completa="1"]').forEach(function(row) {
                const inputs = row.querySelectorAll('.campo-entrada');

                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        // Verificar si alguno de los campos tiene valor
                        const algunoLleno = Array.from(inputs).some(i => i.value.trim() !==
                            "");

                        if (algunoLleno) {
                            // Si alguno tiene valor → todos required
                            inputs.forEach(i => i.setAttribute('required', 'required'));
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
