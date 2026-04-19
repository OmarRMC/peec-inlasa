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
