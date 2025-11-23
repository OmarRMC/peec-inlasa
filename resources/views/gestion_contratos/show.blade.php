<x-app-layout>
    <div class="container py-6 max-w-3xl">
        <x-shared.btn-volver :url="route('contratos.index')" />
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-primary">
                Contrato #{{ $contrato->id }}
            </h1>
            <p class="text-sm text-gray-600">
                Firmante: <strong>{{ $contrato->firmante_nombre }}</strong><br>
                {{ $contrato->firmante_cargo }} - {{ $contrato->firmante_institucion }}
            </p>
        </div>
        <!-- Nota minimalista colapsable -->
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin-bottom: 15px;">

            <!-- Título con icono y toggle -->
            <div style="display: flex; align-items: center; cursor: pointer;"
                onclick="this.nextElementSibling.classList.toggle('hidden')">
                <span style="font-size: 18px; margin-right: 6px;">❓</span>
                <strong>Nota: Haz clic para mostrar/ocultar claves de ejemplo</strong>
            </div>

            <!-- Contenido ocultable -->
            <div class="hidden" style="margin-top: 8px; padding-left: 24px; color: #333;">
                <ul style="list-style-type: disc; padding-left: 0; margin: 0;">
                    <li><strong>generado_por</strong> → Juan Pérez</li>
                    <li><strong>fecha_generacion</strong> → 23/11/2025 | 15:45</li>
                    <li><strong>fecha_contrato</strong> → 23/11/2025</li>
                    <li><strong>gestionInscripcion</strong> → 2025</li>
                    <li><strong>fechaLimitePago</strong> → 30/11/2025</li>
                    <li><strong>convocatoria</strong> → Convocatoria del PEEC INLASA Gestión 2025</li>
                    <li><strong>contrato_numero</strong> → MSyD/INLASA/PEEC/001/2025</li>
                    <li><strong>departamento_raw</strong> → La Paz</li>
                    <li><strong>laboratorioNombreLab</strong></li>
                    <li><strong>laboratorioZonaLab</strong></li>
                    <li><strong>laboratorioDireccionLab</strong></li>
                    <li><strong>laboratorioReprelegLab</strong></li>
                    <li><strong>laboratorioCiReprelegLab</strong></li>
                </ul>
                <p style="margin-top: 4px; font-size: 0.9em;"><em>💡 Usa las claves en la plantilla como
                        <code>{{ '{' . '{ ' }}clave{{ ' }' . '}' }}</code></em></p>
            </div>
        </div>


        <form action="{{ route('contrato_detalles.saveAll') }}" method="POST">
            @csrf
            <input type="hidden" name="id_contrato" value="{{ $contrato->id }}">
            <input type="hidden" name="secciones_eliminar" id="secciones_eliminar" value="">
            <div id="contenedorSecciones" class="space-y-4">
                @foreach ($contrato->detalles as $s)
                    <div class="bg-white shadow rounded-lg p-4 section-item flex flex-col gap-2"
                        data-id="{{ $s->id }}">

                        <div class="flex items-center gap-2">

                            <input type="text" name="secciones[{{ $s->id }}][titulo]"
                                value="{{ $s->titulo }}" class="border rounded px-2 py-1 text-sm flex-1"
                                placeholder="Título">

                            <input type="number" name="secciones[{{ $s->id }}][posicion]"
                                value="{{ $s->posicion }}" class="border rounded px-2 py-1 text-sm w-16">

                            <label class="flex items-center gap-1 text-sm">
                                <input type="checkbox" name="secciones[{{ $s->id }}][estado]" value="1"
                                    {{ $s->estado ? 'checked' : '' }}>
                                <i class="fas fa-check text-green-600"></i>
                            </label>

                            <button type="button" class="text-red-600 hover:text-red-800 ml-auto btnRemove"
                                title="Eliminar sección" data-id="{{ $s->id }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <textarea name="secciones[{{ $s->id }}][descripcion]" class="border rounded p-2 text-sm w-full"
                            placeholder="Descripción...">{{ $s->descripcion }}</textarea>
                    </div>
                @endforeach
            </div>

            <div class="text-right mt-4">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded shadow text-sm flex items-center gap-2">
                    <i class="fas fa-save"></i> Guardar los cambios
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <button id="btnAdd"
                class="px-4 py-2 bg-green-600 text-white rounded shadow text-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Nueva sección
            </button>
        </div>

    </div>

    <template id="templateSeccion">
        <div class="bg-white shadow rounded-lg p-4 section-item flex flex-col gap-2">

            <div class="flex items-center gap-2">

                <input type="text" name="__replace_titulo__" class="border rounded px-2 py-1 text-sm flex-1"
                    placeholder="Título">

                <input type="number" name="__replace_posicion__" value="1"
                    class="border rounded px-2 py-1 text-sm w-16">

                <label class="flex items-center gap-1 text-sm">
                    <input type="checkbox" name="__replace_estado__" checked value="1">
                    <i class="fas fa-check text-green-600"></i>
                </label>

                <button type="button" class="text-red-600 hover:text-red-800 ml-auto btnRemove"
                    title="Eliminar sección">
                    <i class="fas fa-times"></i>
                </button>

            </div>

            <textarea name="__replace_desc__" class="border rounded p-2 text-sm w-full" placeholder="Descripción..."></textarea>
        </div>
    </template>
    @push('scripts')
        <script>
            let index = 0;
            let eliminar = [];

            document.getElementById("btnAdd").addEventListener("click", () => {
                let tpl = document.getElementById("templateSeccion").innerHTML;
                tpl = tpl.replace("__replace_titulo__", "nuevas[" + index + "][titulo]")
                    .replace("__replace_posicion__", "nuevas[" + index + "][posicion]")
                    .replace("__replace_estado__", "nuevas[" + index + "][estado]")
                    .replace("__replace_desc__", "nuevas[" + index + "][descripcion]");
                const cont = document.getElementById("contenedorSecciones");
                const wrapper = document.createElement("div");
                wrapper.innerHTML = tpl;
                cont.appendChild(wrapper);

                index++;
            });
            document.addEventListener("click", e => {
                if (e.target.closest(".btnRemove")) {
                    const btn = e.target.closest(".btnRemove");
                    const section = btn.closest(".section-item");
                    const id = section.dataset.id;

                    if (id) {
                        confirmDialog({
                            title: '¿Eliminar sección?',
                            text: 'Para que se complete esta accion se tiene que Guardar los cambios.',
                            confirmText: 'Sí, eliminar',
                            cancelText: 'Cancelar'
                        }).then(result => {
                            if (result.isConfirmed) {
                                eliminar.push(id);
                                document.getElementById('secciones_eliminar').value = eliminar.join(',');
                                section.remove();
                            }
                        });
                    } else {
                        section.remove();
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
