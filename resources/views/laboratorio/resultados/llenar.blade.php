@php
    $primaryColor = $formulario->color_primario;
    $secondaryColor = $formulario->color_secundario;
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 rounded shadow bg-white" style="border-top: 6px solid {{ $primaryColor }}">

        <div x-data="{ cantidad: 0, cantidadTemp: 1, actual: 0 }">
            {{-- Paso inicial --}}
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
            {{-- Formularios --}}
            <template x-if="cantidad > 0">

                <div x-init="$nextTick(() => {
                    tippy($el.querySelectorAll('[data-tippy-content]'), {
                        theme: 'minimal',
                        animation: 'fade',
                        placement: 'top',
                        allowHTML: true,
                        maxWidth: 220,
                        interactive: true,
                    });
                
                    $el.querySelectorAll('.campo-entrada').forEach(input => {
                        if (input.dataset.validacionActiva) return;
                        input.dataset.validacionActiva = true;
                
                        let errorMsg = document.createElement('small');
                        errorMsg.classList.add('text-red-600', 'text-xs', 'hidden');
                        input.insertAdjacentElement('afterend', errorMsg);
                        input.addEventListener('input', () => validarCampo(input, errorMsg));
                        input.addEventListener('change', () => validarCampo(input, errorMsg));
                    });
                
                    function validarCampo(input, errorMsg) {
                        console.log('uno');
                        const pattern = new RegExp(input.getAttribute('pattern'));
                        console.log(pattern.test(input.value))
                        if (input.checkValidity()) {
                            console.log('dos');
                            errorMsg.textContent = '';
                            errorMsg.classList.add('hidden');
                            input.classList.remove('border-red-500');
                            input.classList.add('border-green-500');
                        } else {
                            console.log('tres');
                            let mensaje = input.dataset.mensajeError || 'Valor inválido';
                            if (input.validity.valueMissing) mensaje = 'Este campo es obligatorio';
                            if (input.validity.tooShort) mensaje = `Mínimo ${input.minLength} caracteres`;
                            if (input.validity.tooLong) mensaje = `Máximo ${input.maxLength} caracteres`;
                            if (input.validity.rangeUnderflow) mensaje = `El valor debe ser >= ${input.min}`;
                            if (input.validity.rangeOverflow) mensaje = `El valor debe ser <= ${input.max}`;
                            if (input.validity.patternMismatch) {
                                mensaje = input.dataset.mensajeError || 'Formato inválido';
                            }
                
                            errorMsg.textContent = mensaje;
                            errorMsg.classList.remove('hidden');
                            input.classList.add('border-red-500');
                            input.classList.remove('border-green-500');
                        }
                    }
                })">
                    {{-- Botón volver --}}
                    <div class="mb-4">
                        <button @click="cantidad = 0; actual = 0"
                            class="inline-flex items-center px-4 py-2 text-sm rounded-md transition"
                            style="background-color: {{ $primaryColor }}; color: #fff;">
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </button>
                    </div>

                    {{-- Información del formulario --}}
                    <div class="grid grid-cols-2 gap-6 mb-4 text-center">
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

                    {{-- Slides de formularios --}}
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
                                                                            data-mensaje-error="{{ $campo->nota_validacion ?? 'Valor inválido' }}"
                                                                            >
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
                                                                            data-mensaje-error="{{ $campo->nota_validacion ?? 'Valor inválido' }}"
                                                                            >
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
                                                                            <input type="text"
                                                                                list="datalist_{{ $campo->id }}"
                                                                                :name="'respuestas[' + (i - 1) +
                                                                                '][{{ $parametro->id }}][{{ $campo->id }}]'"
                                                                                class="w-full border rounded px-2 py-1 text-xs campo-entrada"
                                                                                placeholder="{{ $campo->placeholder }}"
                                                                                value="{{ old('respuestas.' . $parametro->id . '.' . $campo->id, '') }}"
                                                                                @if ($campo->requerido) required @endif
                                                                                @if ($campo->label) data-tippy-content="{{ $campo->label }}" @endif
                                                                                data-mensaje-error="{{ $campo->nota_validacion ?? 'Valor inválido' }}">

                                                                            <datalist
                                                                                id="datalist_{{ $campo->id }}">
                                                                                @foreach ($campo->grupoSelector->opciones as $op)
                                                                                    <option
                                                                                        value="{{ $op->valor }}">
                                                                                    </option>
                                                                                @endforeach
                                                                            </datalist>
                                                                        </div>
                                                                    @elseif ($campo->tipo === 'checkbox')
                                                                        <input type="checkbox"
                                                                            @if ($campo->requerido) required @endif
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

                                    {{-- Navegación --}}
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <button type="button" @click="if(actual>0) actual--"
                                                class="px-3 py-1 rounded"
                                                style="background-color: {{ $secondaryColor }}20">
                                                Anterior
                                            </button>
                                            <button type="button" @click="if(actual< cantidad-1) actual++"
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
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".campo-entrada").forEach(input => {
                    let errorMsg = document.createElement("small");
                    errorMsg.classList.add("text-red-600", "text-xs", "hidden");
                    input.insertAdjacentElement("afterend", errorMsg);
                    input.addEventListener("input", () => validarCampo(input, errorMsg));
                    input.addEventListener("change", () => validarCampo(input, errorMsg));
                });

                function validarCampo(input, errorMsg) {
                    if (input.checkValidity()) {
                        errorMsg.textContent = "";
                        errorMsg.classList.add("hidden");
                        input.classList.remove("border-red-500");
                        input.classList.add("border-green-500");
                    } else {
                        let mensaje = input.dataset.mensajeError || "Valor inválido";
                        // Mensaje específico según el error
                        if (input.validity.valueMissing) mensaje = "Este campo es obligatorio";
                        if (input.validity.tooShort) mensaje = `Mínimo ${input.minLength} caracteres`;
                        if (input.validity.tooLong) mensaje = `Máximo ${input.maxLength} caracteres`;
                        if (input.validity.rangeUnderflow) mensaje = `El valor debe ser >= ${input.min}`;
                        if (input.validity.rangeOverflow) mensaje = `El valor debe ser <= ${input.max}`;
                        if (input.validity.patternMismatch) mensaje = "Formato inválido";

                        errorMsg.textContent = mensaje;
                        errorMsg.classList.remove("hidden");
                        input.classList.add("border-red-500");
                        input.classList.remove("border-green-500");
                    }
                }
            });
        </script>
    @endpush

</x-app-layout>
