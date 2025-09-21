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

        <div class="grid grid-cols-[75%_25%] gap-2">
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
                                    <label class="block text-xs font-semibold text-gray-600">Cantidad de
                                        parámetros</label>
                                    <input type="number" name="secciones[{{ $i }}][cantidad_parametros]"
                                        min="0" value="{{ $seccion->cantidad_parametros }}"
                                        class="w-full border border-gray-300 rounded text-xs px-2 py-1" />
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="block text-xs font-semibold text-gray-600">Descripción</label>
                                <textarea name="secciones[{{ $i }}][descripcion]"
                                    class="w-full border border-gray-300 rounded text-xs px-2 py-1">{{ $seccion->descripcion }}</textarea>
                            </div>

                            <!-- Tabla de parámetros -->
                            <!-- Tabla de parámetros -->
                            <table class="w-full text-xs border parametros-table">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="px-2 py-1">Nombre parámetro</th>
                                        <th class="px-2 py-1">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($seccion->parametros as $j => $parametro)
                                        <tr>
                                            <td>
                                                <input type="text"
                                                    name="secciones[{{ $i }}][parametros][{{ $j }}][nombre]"
                                                    value="{{ $parametro->nombre }}"
                                                    class="w-full border border-gray-300 rounded text-xs px-1">
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="delete-button eliminar-parametro">🗑</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <!-- Subtabla de resultados -->
                                                <table class="w-full text-xs border resultados-table">
                                                    <thead>
                                                        <tr class="bg-gray-100 border-b">
                                                            <th>Tipo</th>
                                                            <th>Unidad</th>
                                                            <th>Validación</th>
                                                            <th>Requerido</th>
                                                            <th>Posición</th>
                                                            <th>Grupo Selector</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($parametro->resultados as $k => $resultado)
                                                            <tr>
                                                                <td>
                                                                    <select
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][resultados][{{ $k }}][tipo]">
                                                                        <option value="text"
                                                                            @selected($resultado->tipo === 'text')>Texto</option>
                                                                        <option value="number"
                                                                            @selected($resultado->tipo === 'number')>Número</option>
                                                                    </select>
                                                                </td>
                                                                <td><input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][resultados][{{ $k }}][unidad]"
                                                                        value="{{ $resultado->unidad }}"></td>
                                                                <td><input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][resultados][{{ $k }}][validacion]"
                                                                        value="{{ $resultado->validacion }}"></td>
                                                                <td><input type="checkbox"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][resultados][{{ $k }}][requerido]"
                                                                        value="1" @checked($resultado->requerido)>
                                                                </td>
                                                                <td><input type="number"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][resultados][{{ $k }}][posicion]"
                                                                        value="{{ $resultado->posicion }}"></td>
                                                                <td>
                                                                    <select
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][resultados][{{ $k }}][grupo_selector_id]"
                                                                        class="grupo-selector-select"
                                                                        data-selected="{{ $resultado->grupoSelector?->id }}">
                                                                        <option value="">Seleccione un grupo
                                                                        </option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="eliminar-resultado">🗑</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <button type="button" class="add-resultado"
                                                    data-seccion-idx="{{ $i }}"
                                                    data-parametro-idx="{{ $j }}">
                                                    + Resultado
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

            <!-- Grupos de Selectores -->
            <div class="border p-4">
                <h2 class="font-bold mb-3">Grupos de selectores</h2>

                <!-- Buscar -->
                <div class="flex gap-2 mb-3">
                    <input type="text" id="busqueda-grupo" placeholder="Buscar..."
                        class="flex-1 border border-gray-300 rounded text-sm px-2 py-1">

                    <button id="btn-buscar-grupo"
                        class="px-3 py-1 bg-gray-800 text-white text-sm rounded flex items-center justify-center hover:bg-gray-700 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <!-- Lista de grupos -->
                <div id="lista-grupos" class="border p-2 mb-3 h-40 overflow-y-auto text-sm">
                    {{-- @foreach ($formulario->secciones->flatMap->parametros->pluck('grupoSelector')->filter()->unique('id') as $grupo)
                        <div class="border-b py-2">
                            <strong>{{ $grupo->nombre }}</strong>
                            @foreach ($grupo->opciones as $opcion)
                                <div class="ml-2 text-xs">{{ $opcion->etiqueta }}</div>
                            @endforeach
                        </div>
                    @endforeach --}}
                </div>

                <!-- Crear grupo nuevo -->
                <div class="border p-2">
                    <h3 class="text-sm font-bold mb-2">Crear un nuevo grupo</h3>
                    <input type="text" id="nuevo-nombre-grupo" placeholder="Nombre del grupo"
                        class="w-full border border-gray-300 rounded text-sm mb-2">
                    <textarea id="nuevas-opciones" placeholder="Opciones separadas por coma"
                        class="w-full border border-gray-300 rounded text-sm"></textarea>
                    <button id="btn-guardar-grupo" class="mt-2 px-3 py-1 bg-green-600 text-white text-sm">Guardar
                        Grupo</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            var seccionIndex = document.querySelectorAll('#secciones-wrapper .seccion').length ?? 0;
            // --- API: Buscar grupos ---
            async function fetchGrupos(query = "") {
                const url = "{{ route('admin.grupos-selectores.buscar') }}?q=" + encodeURIComponent(query);
                const res = await fetch(url);
                return await res.json();
            }

            // --- Renderizar <select> con grupos ---
            async function renderGrupoSelect(selectEl, selectedId = null) {
                const grupos = await fetchGrupos();
                selectEl.innerHTML = `<option value="">Seleccione un grupo</option>`;
                grupos.forEach(g => {
                    const option = document.createElement('option');
                    option.value = g.id;
                    option.textContent = g.nombre;

                    if (selectedId && parseInt(selectedId) === g.id) {
                        option.selected = true;
                    }
                    // selectEl.appendChild(option);
                });
            }

            // --- Renderizar lista de grupos en panel derecho ---
            function renderListaGrupos(container, grupos) {
                container.innerHTML = "";
                if (!grupos || grupos.length === 0) {
                    container.innerHTML = "<p class='text-gray-500 text-sm'>No se encontraron grupos.</p>";
                    return;
                }

                grupos.forEach(g => {
                    const div = document.createElement('div');
                    div.classList.add('border-b', 'py-2');
                    let html = `<strong>${g.nombre}</strong>`;
                    html += `
                    <button type="button" 
                        class="delete-button text-red-600 px-2 py-1 rounded shadow-sm eliminar-grupo" 
                        data-id="${g.id}" 
                        data-tippy-content="Eliminar grupo">
                        <i class="fas fa-times"></i>
                    </button>
                    `;
                    if (g.opciones && g.opciones.length > 0) {
                        html += `<ul class="ml-4 mt-1 text-xs list-disc text-gray-700">`;
                        g.opciones.forEach(op => {
                            html += `<li>${op.etiqueta} (${op.valor})</li>`;
                        });
                        html += `</ul>`;
                    } else {
                        html += `<p class="ml-4 text-xs text-gray-400">Sin opciones</p>`;
                    }

                    div.innerHTML = html;
                    container.appendChild(div);
                });
            }

            // --- Guardar nuevo grupo ---
            async function guardarGrupo() {
                const nombre = document.getElementById('nuevo-nombre-grupo').value.trim();
                const opciones = document.getElementById('nuevas-opciones').value
                    .split(',')
                    .map(o => o.trim())
                    .filter(o => o);

                if (!nombre) {
                    alert("El nombre del grupo es obligatorio.");
                    return;
                }

                const res = await fetch("{{ route('admin.grupos-selectores.guardar') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        nombre,
                        opciones
                    })
                });

                if (res.ok) {
                    const data = await res.json();
                    alert("Grupo creado: " + data.grupo.nombre);
                    document.getElementById('nuevo-nombre-grupo').value = "";
                    document.getElementById('nuevas-opciones').value = "";
                    refrescarSelects();
                } else {
                    const error = await res.json();
                    alert("Error: " + error.message);
                }
            }

            function refrescarSelects() {
                document.querySelectorAll('.grupo-selector-select').forEach(selectEl => {
                    renderGrupoSelect(selectEl, selectEl.dataset.selected);
                });
            }

            async function cargarListaGrupos(query = "") {
                const lista = document.getElementById('lista-grupos');
                lista.innerHTML = "Cargando...";
                const grupos = await fetchGrupos(query);
                renderListaGrupos(lista, grupos);
            }

            function LimpiarListaGrupos() {
                const lista = document.getElementById('lista-grupos');
                lista.innerHTML = "";
            }

            function crearFilaParametro(seccionIdx, parametroIdx) {
                const trParam = document.createElement('tr');
                trParam.innerHTML = `
                    <td><input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][nombre]" class="w-full border rounded text-xs px-1"></td>
                    <td><button type="button" class="eliminar-parametro">🗑</button></td>
                `;

                const trResultados = document.createElement('tr');
                trResultados.innerHTML = `
                <td colspan="2">
                <table class="w-full text-xs border resultados-table">
                            <thead>
                                <tr>
                                    <th>Tipo</th><th>Unidad</th><th>Validación</th>
                                    <th>Requerido</th><th>Posición</th><th>Grupo</th><th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                <button type="button" class="add-resultado" data-seccion-idx="${seccionIdx}" data-parametro-idx="${parametroIdx}">+ Resultado</button>
                </td>
                `;

                return [trParam, trResultados];
            }

            function crearFilaResultado(seccionIdx, parametroIdx, resultadoIdx) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
        <td>
            <select name="secciones[${seccionIdx}][parametros][${parametroIdx}][resultados][${resultadoIdx}][tipo]">
                <option value="text">Texto</option>
                <option value="number">Número</option>
            </select>
        </td>
        <td><input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][resultados][${resultadoIdx}][unidad]"></td>
        <td><input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][resultados][${resultadoIdx}][validacion]"></td>
        <td><input type="checkbox" name="secciones[${seccionIdx}][parametros][${parametroIdx}][resultados][${resultadoIdx}][requerido]" value="1"></td>
        <td><input type="number" name="secciones[${seccionIdx}][parametros][${parametroIdx}][resultados][${resultadoIdx}][posicion]"></td>
        <td>
            <select name="secciones[${seccionIdx}][parametros][${parametroIdx}][resultados][${resultadoIdx}][grupo_selector_id]" class="grupo-selector-select">
                <option value="">Seleccione un grupo</option>
            </select>
        </td>
        <td><button type="button" class="eliminar-resultado">🗑</button></td>
    `;

                renderGrupoSelect(tr.querySelector('.grupo-selector-select'));

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
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600">Cantidad de
                                        parámetros</label>
                                    <input type="number" name="secciones[${seccionIdx}][cantidad_parametros]" min="0"
                                        class="w-full border border-gray-300 rounded text-xs px-2 py-1" />
                                </div>
                            </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label class="block text-xs font-semibold text-gray-600">Descripción</label>
                        <textarea name="secciones[${seccionIdx}][descripcion]"
                            class="w-full border border-gray-300 rounded text-xs px-2 py-1"></textarea>
                    </div>


                    <button type="button" class="mt-2 text-xs text-blue-600 add-parametro" 
                        data-seccion-idx="${seccionIdx}">+ Parámetro
                    </button>
                `;
                return div;
            }

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.grupo-selector-select').forEach(selectEl => {
                    const selectedId = selectEl.dataset.selected;
                    renderGrupoSelect(selectEl, selectedId);
                });

                document.getElementById('btn-buscar-grupo').addEventListener('click', () => {
                    const query = document.getElementById('busqueda-grupo').value;
                    cargarListaGrupos(query);
                });

                // Guardar grupo
                document.getElementById('btn-guardar-grupo').addEventListener('click', guardarGrupo);
                // Agregar secciones
                // document.getElementById('add-seccion').addEventListener('click', () => {
                //     const wrapper = document.getElementById('secciones-wrapper');
                //     const nuevaSeccion = crearSeccion();
                //     wrapper.insertBefore(nuevaSeccion, document.getElementById('add-seccion'));
                // });
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
                    if (e.target && e.target.classList.contains('add-parametro')) {
                        const seccionIdx = e.target.dataset.seccionIdx;
                        const tbody = e.target.closest('.seccion').querySelector('tbody');
                        const parametroIdx = tbody.querySelectorAll('tr')?.length??0;
                        tbody.appendChild(crearFilaParametro(seccionIdx, parametroIdx));
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
                            fila.remove();
                        }
                        const filaResultados = fila.nextElementSibling;
                        if (filaResultados && filaResultados.querySelector('.resultados-table')) {
                            filaResultados.remove();
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
                            refrescarSelects();
                        } else {
                            alert("Error al eliminar grupo");
                        }
                    }
                    if (e.target && e.target.classList.contains('add-resultado')) {
                        const btn = e.target;
                        const seccionIdx = btn.dataset.seccionIdx;
                        const parametroIdx = btn.dataset.parametroIdx;

                        // Encuentra el tbody de resultados correspondiente
                        const resultadosTbody = btn.closest('tr').querySelector('tbody');

                        // Calcula el índice correcto de resultado
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
