@php
    $primaryColor = $formulario->color_primario;
    $secondaryColor = $formulario->color_secundario;
    $cantidad = $cantidad ?? 1;
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 rounded shadow bg-white" style="border-top: 6px solid {{ $primaryColor }}">

        <div class="mb-4">
            <a href="{{ route('lab.inscritos-ensayos.formularios', $idEA) }}"
                class="inline-flex items-center px-4 py-2 text-sm rounded-md transition"
                style="background-color: {{ $primaryColor }}; color: #fff;">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
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
            <div class="p-2 rounded-lg mb-4 bg-yellow-100 border border-yellow-300">
                <span class="text-sm text-yellow-800">{{ $formulario->nota }}</span>
            </div>
        @endif

        {{-- Formularios en lista --}}
        @for ($i = 1; $i <= $cantidad; $i++)
            <div class="space-y-4 mb-8 border-b pb-6">
                <form method="POST" action="{{ route('lab.resultados.store') }}">
                    <input type="hidden" value="{{ $formulario->id }}" name="id_formulario">
                    @csrf

                    <h2 class="font-bold text-lg mb-2" style="color: {{ $primaryColor }}">
                        Formulario {{ $i }} / {{ $cantidad }}
                    </h2>

                    {{-- Secciones --}}
                    @include('admin.formularios.partials.preview')
                    {{-- Acciones --}}
                    <div class="flex justify-end gap-2">
                        {{-- <a href="{{ route('lab.inscritos-ensayos.index') }}" class="px-3 py-1 text-white rounded"
                            style="background-color: red;">
                            Cancelar
                        </a> --}}
                        <button type="submit" class="px-3 py-1 text-white rounded"
                            style="background-color: {{ $primaryColor }}">
                            Guardar este formulario
                        </button>
                    </div>
                </form>
            </div>
        @endfor
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
