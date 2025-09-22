<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <strong>Se encontraron errores en el formulario:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="border p-4 mb-6">
            <h2 class="font-bold mb-2">Información del formulario</h2>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <p><span class="font-semibold">Nombre:</span> {{ $formulario->nombre }}</p>
                </div>
                <div>
                    <p><span class="font-semibold">Código:</span> {{ $formulario->codigo }}</p>
                </div>
                <div class="col-span-2">
                    <p><span class="font-semibold">Nota:</span> {{ $formulario->nota }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-[100%] gap-2">
            <!-- Secciones -->
            <form method="POST" action="{{ route('admin.formularios.updateEstructura', $formulario->id) }}">
                @csrf
                @method('PUT')
                <div id="secciones-wrapper" class="col-span-2 border p-4">
                    <h2 class="font-bold mb-2">Secciones</h2>
                    @foreach ($formulario->secciones as $i => $seccion)
                        <div class="border p-2 mb-4 seccion relative">
                            <!-- Nombre y Descripción -->
                            <button type="button"
                                class="absolute -right-2 -top-2 text-red-600 hover:text-white 
                                    bg-red-100 hover:bg-red-600 
                                    px-2 py-0 rounded-sm shadow transition duration-200 eliminar-seccion"
                                data-tippy-content="Eliminar sección">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                            <div class="mb-2 flex gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600">Nombre</label>
                                    <input type="text" name="secciones[{{ $i }}][nombre]"
                                        value="{{ $seccion->nombre }}"
                                        class="w-full border border-gray-300 rounded text-xs px-2 py-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600">Descripción</label>
                                    <textarea name="secciones[{{ $i }}][descripcion]" class=" border border-gray-300 rounded text-xs px-2 py-1">{{ $seccion->descripcion }}</textarea>
                                </div>
                            </div>
                            <div class="mb-3 headers-wrapper">
                                <label class="block text-xs font-semibold text-gray-600">Encabezado</label>
                                <div class="headers-list flex flex-wrap gap-2">
                                    @foreach ($seccion->headers ?? [] as $h => $header)
                                        <div class="flex items-center gap-1 mb-1 header-item">
                                            <input type="text" name="secciones[{{ $i }}][headers][]"
                                                value="{{ $header }}"
                                                class="w-full border border-gray-300 rounded text-xs px-2 py-1">
                                            <button type="button" data-tippy-content="Eliminar"
                                                class="px-2 py-1 bg-red-500 text-white text-xs rounded eliminar-header"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                    class="mt-1 px-3 py-1 text-xs bg-blue-500 text-white rounded add-header"
                                    data-seccion-idx="{{ $i }}">
                                    + Encabezado
                                </button>
                            </div>
                            <!-- Tabla de parámetros -->
                            <table class="text-xs parametros-table">
                                <tbody>
                                    @foreach ($seccion->parametros as $j => $parametro)
                                        <tr>
                                            <td class="flex gap-2">
                                                <input type="text"
                                                    name="secciones[{{ $i }}][parametros][{{ $j }}][nombre]"
                                                    placeholder="Nombre del parámetro" value="{{ $parametro->nombre }}"
                                                    class="text-xs border rounded px-1 py-0.5">
                                                <button type="button" data-tippy-content="Eliminar"
                                                    class="eliminar-parametro px-2 py-1 bg-red-500 text-white text-xs rounded"><i
                                                        class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table class="text-xs border border-gray-300 rounded resultados-table">
                                                    <thead class="bg-gray-100 text-gray-600">
                                                        <tr>
                                                            <th class="px-2 py-1 border">Label</th>
                                                            <th class="px-2 py-1 border">Tipo</th>
                                                            <th class="px-2 py-1 border">Placeholder</th>
                                                            <th class="px-2 py-1 border">Unidad</th>
                                                            <th class="px-2 py-1 border text-center">Requerido</th>
                                                            <th class="px-2 py-1 border">Posición</th>
                                                            {{-- <th class="px-2 py-1 border">Step</th> --}}
                                                            <th class="px-2 py-1 border">Validación(ExpReg)</th>
                                                            <th class="px-2 py-1 border">Nota Validacion</th>
                                                            <th class="px-2 py-1 border">Rango</th>
                                                            <th class="px-2 py-1 border">Grupo Selector</th>
                                                            <th class="px-2 py-1 border text-center">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($parametro->campos as $k => $campo)
                                                            <tr class="text-xs">
                                                                <td class="px-2 py-1 border">
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][label]"
                                                                        value="{{ $campo->label }}" placeholder="Label"
                                                                        class="w-full text-xs border rounded px-1 py-0.5">
                                                                </td>
                                                                <td class="px-2 py-1 border">
                                                                    <select
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][tipo]"
                                                                        class="w-full text-xs border rounded px-1 py-0.5">
                                                                        <option value="text"
                                                                            @selected($campo->tipo === 'text')>Texto</option>
                                                                        <option value="number"
                                                                            @selected($campo->tipo === 'number')>Número</option>
                                                                        <option value="date"
                                                                            @selected($campo->tipo === 'date')>Fecha</option>
                                                                        <option value="select"
                                                                            @selected($campo->tipo === 'select')>Select</option>
                                                                        <option value="checkbox"
                                                                            @selected($campo->tipo === 'checkbox')>Checkbox
                                                                        </option>
                                                                        <option value="textarea"
                                                                            @selected($campo->tipo === 'textarea')>Textarea
                                                                        </option>
                                                                        <option value="email"
                                                                            @selected($campo->tipo === 'email')>Email</option>
                                                                        <option value="datalist"
                                                                            @selected($campo->tipo === 'datalist')>Datalist
                                                                        </option>
                                                                        <option value="radio"
                                                                            @selected($campo->tipo === 'radio')>Radio</option>
                                                                    </select>
                                                                </td>
                                                                <td class="px-2 py-1 border">
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][placeholder]"
                                                                        value="{{ $campo->placeholder }}"
                                                                        placeholder="Placeholder"
                                                                        class="w-full text-xs border rounded px-1 py-0.5">
                                                                </td>
                                                                <td class="px-2 py-1 border">
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][unidad]"
                                                                        value="{{ $campo->unidad }}"
                                                                        placeholder="Unidad"
                                                                        class="w-full text-xs border rounded px-1 py-0.5">
                                                                </td>
                                                                <td class="px-2 py-1 border text-center">
                                                                    <input type="checkbox"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][requerido]"
                                                                        value="1" @checked($campo->requerido)
                                                                        class="mx-auto">
                                                                </td>
                                                                <td class="px-2 py-1 border">
                                                                    <input type="number"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][posicion]"
                                                                        value="{{ $campo->posicion }}"
                                                                        placeholder="#"
                                                                        class="w-full text-xs border rounded px-1 py-0.5">
                                                                </td>
                                                                {{-- <td class="px-2 py-1 border">
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][step]"
                                                                        value="{{ $campo->step }}"
                                                                        placeholder="Step"
                                                                        class="w-full text-xs border rounded px-1 py-0.5 mt-1">
                                                                </td> --}}
                                                                <td class="px-2 py-1 border">
                                                                    <textarea
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][pattern]"
                                                                        placeholder="" class="w-full text-xs border rounded px-1 py-0.5 mt-1">{{ $campo->pattern }}</textarea>
                                                                </td>
                                                                <td class="px-2 py-1 border">
                                                                    <textarea
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][mensaje]"
                                                                        placeholder="" class="w-full text-xs border rounded px-1 py-0.5 mt-1">{{ $campo->mensaje }}</textarea>
                                                                </td>
                                                                <td class="px-2 py-1 border">
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][range]"
                                                                        value="{{ $campo->range }}"
                                                                        placeholder="Rango"
                                                                        class="w-full text-xs border rounded px-1 py-0.5">
                                                                </td>
                                                                <td class="px-2 py-1 border">
                                                                    <select
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][id_grupo_selector]"
                                                                        class="grupo-selector-select w-full text-xs border rounded px-1 py-0.5"
                                                                        data-selected="{{ $campo->grupoSelector?->id }}">
                                                                        <option value="">Seleccione un grupo
                                                                        </option>
                                                                        @foreach ($grupos as $grupo)
                                                                            <option value="{{ $grupo->id }}"
                                                                                {{ $campo->grupoSelector?->id == $grupo->id ? 'selected' : '' }}>
                                                                                {{ $grupo->nombre }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td class="px-2 py-1 border text-center">
                                                                    <button type="button"
                                                                        data-tippy-content="Eliminar"
                                                                        class="eliminar-resultado text-red-500 text-xs"><i
                                                                            class="fas fa-trash-alt"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <button type="button"
                                                    class="add-resultado block text-center text-blue-600 my-2 w-full"
                                                    data-seccion-idx="{{ $i }}"
                                                    data-parametro-idx="{{ $j }}">
                                                    + Campo
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="mt-2 text-xs text-blue-600 add-parametro"
                                data-seccion-idx="{{ $i }}">+ Parámetro</button>
                        </div>
                    @endforeach

                    <button type="button" id="add-seccion" class="text-sm text-green-600 font-semibold">
                        + Secciones
                    </button>
                </div>
                <div class="mt-6 text-center">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-500">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
        <script>
            var seccionIndex = document.querySelectorAll('#secciones-wrapper .seccion').length ?? 0;
            const urlGrupos = @json(route('admin.grupos.selectores.json', ['ensayoId' => $ensayo->id]));
            let firstLoad = true;

            // --- API: Buscar grupos ---
            async function fetchGrupos(query = "") {
                const url = "{{ route('admin.grupos-selectores.buscar') }}?q=" + encodeURIComponent(query);
                const res = await fetch(url);
                return await res.json();
            }

            function crearHeaderInput(seccionIdx, valor = "") {
                const div = document.createElement('div');
                div.classList.add('flex', 'items-center', 'gap-2', 'mb-1', 'header-item');
                div.innerHTML = `
                <input type="text" 
                        name="secciones[${seccionIdx}][headers][]" 
                        value="${valor}" 
                        class="flex-1 border border-gray-300 rounded text-xs px-2 py-1">
                    <button type="button" data-tippy-content="Eliminar" class="px-2 py-1 bg-red-500 text-white text-xs rounded  eliminar-header"><i class="fas fa-trash-alt"></i></button>
                `;
                const btnEliminar = div.querySelector('.eliminar-header');
                btnEliminar.addEventListener('click', function() {
                    div.remove();
                });
                return div;
            }

            // --- Renderizar lista de grupos en panel derecho ---
            function crearFilaParametro(seccionIdx, parametroIdx) {
                const trParam = document.createElement('tr');
                trParam.innerHTML = `
                    <td class='flex gap-2'>
                        <input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][nombre]" class="text-xs border rounded px-1 py-0.5"
                        placeholder="Nombre del parámetro" 
                        >
                        <button type="button" data-tippy-content="Eliminar" class="eliminar-parametro px-2 py-1 bg-red-500 text-white text-xs rounded"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;

                const trResultados = document.createElement('tr');
                trResultados.innerHTML = `
                <td>
                <table class="w-full text-xs border border-gray-300 rounded resultados-table">
                        <thead class="bg-gray-100 text-gray-600">
                              <tr>
                                    <th class="px-2 py-1 border">Label</th>
                                    <th class="px-2 py-1 border">Tipo</th>
                                    <th class="px-2 py-1 border">Placeholder</th>
                                    <th class="px-2 py-1 border">Unidad</th>
                                    <th class="px-2 py-1 border text-center">Requerido</th>
                                    <th class="px-2 py-1 border">Posición</th>
                                    <th class="px-2 py-1 border">Validación(ExpReg)</th>
                                    <th class="px-2 py-1 border">Nota Validacion</th>
                                    <th class="px-2 py-1 border">Rango</th>
                                    <th class="px-2 py-1 border">Grupo Selector</th>
                                    <th class="px-2 py-1 border text-center">Acciones</th>
                               </tr>
                        </thead>
                        <tbody></tbody>
                 </table>
                 <button type="button"
                    class="add-resultado block text-center text-blue-600 my-2 w-full"
                    data-seccion-idx="${seccionIdx}" data-parametro-idx="${parametroIdx}">
                    + Campo
                 </button>
                </td>
                `;

                return [trParam, trResultados];
            }

            function crearFilaResultado(seccionIdx, parametroIdx, resultadoIdx) {
                const tr = document.createElement('tr');
                tr.classList.add('text-xs');

                tr.innerHTML = `
                <td class="px-2 py-1 border">
                    <input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][label]"
                        placeholder="Label" class="w-full text-xs border rounded px-1 py-0.5">
                </td>
                <td class="px-2 py-1 border">
                    <select name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][tipo]"
                            class="w-full text-xs border rounded px-1 py-0.5">
                        <option value="text">Texto</option>
                        <option value="number">Número</option>
                        <option value="date">Fecha</option>
                        <option value="select">Select</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="textarea">Textarea</option>
                        <option value="email">Email</option>
                        <option value="datalist">Datalist</option>
                        <option value="radio">Radio</option>
                    </select>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][placeholder]"
                        placeholder="Placeholder" class="w-full text-xs border rounded px-1 py-0.5">
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][unidad]"
                        placeholder="Unidad" class="w-full text-xs border rounded px-1 py-0.5">
                </td>
                <td class="px-2 py-1 border text-center">
                    <input type="checkbox" name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][requerido]" class="mx-auto">
                </td>
                <td class="px-2 py-1 border">
                    <input type="number" name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][posicion]"
                        placeholder="#" class="w-full text-xs border rounded px-1 py-0.5">
                </td>
                <td class="px-2 py-1 border">
                    <textarea name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][pattern]"
                      placeholder='Validacion' class="w-full text-xs border rounded px-1 py-0.5 mt-1"></textarea>
                </td>
                <td class="px-2 py-1 border">
                    <textarea name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][mensaje]"
                      placeholder='' class="w-full text-xs border rounded px-1 py-0.5 mt-1">
                      </textarea>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][range]"
                        placeholder="Rango" class="w-full text-xs border rounded px-1 py-0.5">
                </td>
                <td class="px-2 py-1 border">
                     <select name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][id_grupo_selector]" class="grupo-selector-select w-full text-xs border rounded px-1 py-0.5" >
                        <option value="">Seleccione un grupo</option>
                      </select>
                </td>
                <td class="px-2 py-1 border text-center">
                    <button type="button" data-tippy-content="Eliminar" class="eliminar-resultado text-red-500 text-xs"><i class="fas fa-trash-alt"></i></button>
                </td>
                `;
                const selectGrupo = tr.querySelector('.grupo-selector-select');
                asignarFocusGrupo(selectGrupo);
                return tr;
            }

            function crearSeccion() {
                const seccionIdx = seccionIndex++;
                const div = document.createElement('div');
                div.classList.add('border', 'p-2', 'mb-4', 'seccion', 'relative');
                div.innerHTML = `
                     <button type="button"
                                class="absolute -right-2 -top-2 text-red-600 hover:text-white 
                                    bg-red-100 hover:bg-red-600 
                                    px-2 py-0 rounded-sm shadow transition duration-200 eliminar-seccion"
                                data-tippy-content="Eliminar sección">
                                <i class="fas fa-times text-sm"></i>
                    </button>
                     <div class="mb-2 flex gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600">Nombre</label>
                                    <input type="text" name="secciones[${seccionIdx}][nombre]"
                                        class="w-full border border-gray-300 rounded text-xs px-2 py-1">
                                </div>
                            </div>

                    <!-- Descripción -->
                    <div class="">
                        <label class="block text-xs font-semibold text-gray-600">Descripción</label>
                        <textarea name="secciones[${seccionIdx}][descripcion]"
                            class="w-full border border-gray-300 rounded text-xs px-2 py-1"></textarea>
                    </div>

                  <div class="mb-3 headers-wrapper">
                        <label class="block text-xs font-semibold text-gray-600">Encabezado</label>
                        <div class="headers-list flex flex-wrap gap-2">
                            <!-- Aquí se irán agregando los inputs de headers -->
                        </div>
                        <button type="button"
                            class="mt-1 px-3 py-1 text-xs bg-blue-500 text-white rounded add-header"
                            data-seccion-idx="${seccionIdx}">
                            + Encabezado
                        </button>
                    </div>

                    <table>
                        <tbody class="parametros-table"></tbody>
                    </table>

                    <button type="button" class="mt-2 text-xs text-blue-600 add-parametro" 
                        data-seccion-idx="${seccionIdx}">+ Parámetro
                    </button>
                `;
                return div;
            }

            function asignarFocusGrupo(selectElement) {
                selectElement.addEventListener('focus', function() {
                    const selectedId = this.dataset.selected;
                    fetch(urlGrupos)
                        .then(res => res.json())
                        .then(data => {
                            this.querySelectorAll('option:not(:first-child)').forEach(o => o
                                .remove());

                            data.forEach(grupo => {
                                const option = document.createElement('option');
                                option.value = grupo.id;
                                option.textContent = grupo.nombre;
                                if (grupo.id == selectedId) option.selected = true;
                                this.appendChild(option);
                            });
                        })
                        .catch(err => console.error(err));
                });
            }


            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.grupo-selector-select').forEach(select => {
                    asignarFocusGrupo(select);
                });

                document.getElementById('add-seccion').addEventListener('click', () => {
                    const wrapper = document.getElementById('secciones-wrapper');
                    const nuevaSeccion = crearSeccion();
                    wrapper.insertBefore(nuevaSeccion, document.getElementById('add-seccion'));
                });

                // Delegar evento para agregar parámetros
                // document.addEventListener('click', function(e) {
                //     if (e.target && e.target.classList.contains('add-parametro')) {
                //         const tbody = e.target.closest('.seccion').querySelector('tbody');
                //         tbody.appendChild(crearFilaParametro());
                //     }
                // });

                document.addEventListener('click', async (e) => {

                    if (e.target && e.target.classList.contains('add-header')) {
                        const seccionIdx = e.target.dataset.seccionIdx;
                        const headersList = e.target.closest('.headers-wrapper').querySelector(
                            '.headers-list');
                        headersList.appendChild(crearHeaderInput(seccionIdx));
                    }

                    // Eliminar header
                    if (e.target && e.target.classList.contains('eliminar-header')) {
                        const item = e.target.closest('.header-item');
                        if (item) item.remove();
                    }

                    if (e.target && e.target.classList.contains('add-parametro')) {
                        const seccionIdx = e.target.dataset.seccionIdx;
                        const tbody = e.target.closest('.seccion').querySelector('tbody');
                        const parametroIdx = tbody ? (tbody.querySelectorAll('tr')?.length ?? 0) : 0;
                        // tbody.appendChild(crearFilaParametro(seccionIdx, parametroIdx));
                        const [trParam, trResultados] = crearFilaParametro(seccionIdx, parametroIdx);
                        tbody.appendChild(trParam);
                        tbody.appendChild(trResultados);
                    }
                    const btn = e.target.closest('.eliminar-seccion');
                    if (btn) {
                        const contenedor = btn.closest('.seccion');
                        if (contenedor) {
                            contenedor.remove();
                        }
                    }
                    const btnParam = e.target.closest('.eliminar-parametro');
                    if (btnParam) {
                        const fila = btnParam.closest('tr');
                        if (fila) {
                            const filaResultados = fila.nextElementSibling;
                            fila.remove();
                            if (filaResultados && filaResultados.querySelector('.resultados-table')) {
                                filaResultados.remove();
                            }
                        }
                    }
                    if (e.target.closest('.eliminar-grupo')) {
                        const id = e.target.closest('.eliminar-grupo').dataset.id;
                        if (!confirm("¿Seguro que quieres eliminar este grupo?")) return;

                        const url = "{{ route('admin.grupos-selectores.eliminar', ':id') }}".replace(':id',
                            id);
                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        });
                        if (res.ok) {
                            alert("Grupo eliminado correctamente");
                            LimpiarListaGrupos();
                        } else {
                            alert("Error al eliminar grupo");
                        }
                    }
                    if (e.target && e.target.classList.contains('add-resultado')) {
                        const btn = e.target;
                        const seccionIdx = btn.dataset.seccionIdx;
                        const parametroIdx = btn.dataset.parametroIdx;

                        const resultadosTbody = btn.closest('tr').querySelector('tbody');
                        const resultadoIdx = resultadosTbody.querySelectorAll('tr').length;

                        const nuevaFila = crearFilaResultado(seccionIdx, parametroIdx, resultadoIdx);
                        resultadosTbody.appendChild(nuevaFila);
                    }
                    const btnResultado = e.target.closest('.eliminar-resultado');
                    if (btnResultado) {
                        const filaResultado = btnResultado.closest('tr');
                        if (filaResultado) {
                            filaResultado.remove();
                        }
                    }
                });
            });
        </script>
    @endpush


</x-app-layout>
