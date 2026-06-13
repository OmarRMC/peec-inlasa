@php
    $primaryColor = $formulario->color_primario;
    $secondaryColor = $formulario->color_secundario;
    $cantidad = $cantidad ?? 1;
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 rounded shadow bg-white" style="border-top: 6px solid {{ $primaryColor }}">

        {{-- Cabecera para laboratorio: Paquete / Ensayo / Ciclo --}}
        <div class="sticky top-0 z-10 bg-white border-b border-gray-200 shadow-sm -mx-6 px-6 -mt-6 pt-3 pb-3 flex flex-col text-sm mb-4" style="top: -24px;">
            <span class="font-bold text-gray-800">{{ $ensayoAptitud->paquete->descripcion ?? '-' }}</span>
            <span class="text-gray-500">{{ $ensayoAptitud->descripcion }}</span>
            <span class="text-gray-500">{{ $cicloActivo->nombre ?? '-' }}</span>
        </div>
                <div class="mb-4">
            <a href="{{ route('lab.inscritos-ensayos.formularios', $idEA) }}"
                class="inline-flex items-center px-4 py-2 text-sm rounded-md transition"
                style="background-color: {{ $primaryColor }}; color: #fff;">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>


        {{-- Descripción --}}
        @if (!empty($formulario->descripcion))
            <div class="p-2 rounded-lg mb-4 bg-yellow-100 border border-yellow-300">
                <span class="text-sm text-yellow-800">{{ $formulario->descripcion }}</span>
            </div>
        @endif

        {{-- Formularios en lista --}}
        <form method="POST" action="{{ route('lab.resultados.store') }}">
            @csrf
            <input type="hidden" value="{{ $formulario->id }}" name="id_formulario">
            <input type="hidden" name="id_ensayo" value="{{ $idEA }}">
            <input type="hidden" name="cantidad" value="{{ $cantidad }}">

            @for ($i = 1; $i <= $cantidad; $i++)
                <div class="space-y-4 mb-8 {{ $i < $cantidad ? 'border-b pb-6' : '' }}">
                    <h2 class="font-bold text-lg mb-2" style="color: {{ $primaryColor }}">
                        Formulario {{ $i }} / {{ $cantidad }}
                    </h2>

                    {{-- Secciones --}}
                    @include('admin.formularios.partials.preview')
                </div>
            @endfor

            {{-- Botón único de guardar --}}
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                @if ($respuestas->isNotEmpty())
                    <button type="submit" class="px-6 py-2 text-white rounded font-semibold"
                        style="background-color: {{ $primaryColor }}">
                        <i class="fas fa-rotate mr-2"></i>
                        Actualizar {{ $cantidad > 1 ? 'todos los formularios' : 'formulario' }}
                    </button>   
                @else
                    <button type="submit" class="px-6 py-2 text-white rounded font-semibold"
                        style="background-color: {{ $primaryColor }}">
                        <i class="fas fa-save mr-2"></i>
                        Enviar {{ $cantidad > 1 ? 'todos los formularios' : 'formulario' }}
                    </button>
                @endif
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".campo-entrada").forEach(input => {
                    // Envolvemos el input en un contenedor relativo para anclar el globo de
                    // error de forma absoluta: así flota debajo del campo SIN ocupar espacio
                    // en la celda (no descuadra la tabla).
                    const wrap = document.createElement("span");
                    wrap.className = "relative block w-full";
                    input.parentNode.insertBefore(wrap, input);
                    wrap.appendChild(input);

                    const bubble = document.createElement("small");
                    bubble.className = "absolute left-0 top-full mt-0.5 z-20 bg-red-600 " +
                        "text-white text-xs rounded px-2 py-0.5 shadow whitespace-nowrap hidden";
                    wrap.appendChild(bubble);

                    const validar = () => validarCampo(input, bubble);
                    input.addEventListener("input", validar);
                    input.addEventListener("change", validar);
                });

                function validarCampo(input, bubble) {
                    if (input.checkValidity()) {
                        bubble.classList.add("hidden");
                        input.classList.remove("border-red-500");
                        input.classList.add("border-green-500");
                        return;
                    }

                    // Mensaje personalizado configurado por el admin (data-mensaje-validacion).
                    const custom = input.dataset.mensajeValidacion;
                    let mensaje = custom || "Valor inválido";
                    if (input.validity.valueMissing) mensaje = "Este campo es obligatorio";
                    else if (input.validity.tooShort) mensaje = `Mínimo ${input.minLength} caracteres`;
                    else if (input.validity.tooLong) mensaje = `Máximo ${input.maxLength} caracteres`;
                    else if (input.validity.rangeUnderflow) mensaje = `El valor debe ser ≥ ${input.min}`;
                    else if (input.validity.rangeOverflow) mensaje = `El valor debe ser ≤ ${input.max}`;
                    else if (input.validity.patternMismatch) mensaje = custom || "Formato inválido";

                    bubble.innerHTML = `<i class="fas fa-triangle-exclamation mr-1"></i>${mensaje}`;
                    bubble.classList.remove("hidden");
                    input.classList.add("border-red-500");
                    input.classList.remove("border-green-500");
                }
            });
        </script>
    @endpush
</x-app-layout>
