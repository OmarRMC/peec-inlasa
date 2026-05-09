<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
        <div class="mb-2 flex items-center justify-between">
            <x-shared.btn-volver :url="route('admin.formularios.show',$ensayo->id )" />
            <a href="{{ route('admin.formularios.guia') }}" target="_blank"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium rounded-lg border border-indigo-200 transition">
                <i class="fas fa-book-open"></i> Guía rápida
            </a>
        </div>
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
            <div class="flex justify-between items-center mb-2">
                <h2 class="font-bold">Información del formulario</h2>
                <a href="{{ route('admin.formularios.preview', $formulario->id) }}" target="_blank"
                    class="text-blue-600 hover:text-blue-800" data-tippy-content="Previsualizar formulario">
                    <i class="fas fa-eye text-xl"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <p><span class="font-semibold">Nombre:</span> {{ $formulario->nombre }}</p>
                </div>
                <div class="col-span-2">
                    <p><span class="font-semibold">Descripción:</span> {{ $formulario->descripcion }}</p>
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
                                    <input type="text" name="secciones[{{ $i }}][id]" hidden
                                        value="{{ $seccion->id }}"
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
                                                <input type="text"
                                                    name="secciones[{{ $i }}][parametros][{{ $j }}][id]"
                                                    hidden value="{{ $parametro->id }}"
                                                    class="w-full border border-gray-300 rounded text-xs px-2 py-1">

                                                <button type="button"
                                                    class="toggle-visible text-blue-500 bg-blue-100 px-2 py-1 rounded"
                                                    data-tippy-content="{{ $parametro->visible_nombre ? 'Ocultar label en formulario' : 'Mostrar label en formulario' }}">
                                                    @if ($parametro->visible_nombre)
                                                        <i class="fas fa-eye"></i>
                                                    @else
                                                        <i class="fas fa-eye-slash"></i>
                                                    @endif
                                                </button>
                                                <input type="hidden"
                                                    name="secciones[{{ $i }}][parametros][{{ $j }}][visible_nombre]"
                                                    value="{{ $parametro->visible_nombre ? 1 : 0 }}"
                                                    class="visible-input">
                                                <label class="flex items-center gap-1 text-xs">
                                                    <input type="checkbox"
                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][requerido_si_completa]"
                                                        value="1" @checked($parametro->requerido_si_completa)
                                                        class="border rounded">
                                                    Si el laboratorio llena cualquier campo de esta fila, los obligatorios también deben completarse.
                                                </label>
                                                <button type="button" data-tippy-content="Eliminar"
                                                    class="eliminar-parametro px-2 py-1 bg-red-500 text-white text-xs rounded"><i
                                                        class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table class="text-xs border border-gray-300 rounded resultados-table">
                                                    <thead class="bg-gray-100 text-gray-600">
                                                        <tr>
                                                            <th class="px-2 py-1 border">Etiqueta</th>
                                                            <th class="px-2 py-1 border">Tipo de campo</th>
                                                            <th class="px-2 py-1 border">Texto de ayuda</th>
                                                            <th class="px-2 py-1 border text-center">Obligatorio</th>
                                                            <th class="px-2 py-1 border">Orden</th>
                                                            <th class="px-2 py-1 border">Formato</th>
                                                            <th class="px-2 py-1 border w-[100px]">Mensaje de error</th>
                                                            <th class="px-2 py-1 border">Rango (mín-máx)</th>
                                                            <th class="px-2 py-1 border">Valor fijo</th>
                                                            <th class="px-2 py-1 border text-center">Editable</th>
                                                            <th class="px-2 py-1 border text-center">Recordar valor</th>
                                                            <th class="px-2 py-1 border">Lista de opciones</th>
                                                            <th class="px-2 py-1 border">Depende de</th>
                                                            <th class="px-2 py-1 border text-center">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($parametro->campos as $k => $campo)
                                                            <tr class="text-xs">
                                                                <td class="px-2 py-1 border">
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][id]"
                                                                        hidden value="{{ $campo->id }}"
                                                                        class="w-full border border-gray-300 rounded text-xs px-2 py-1">
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][label]"
                                                                        value="{{ $campo->label }}"
                                                                        placeholder="Label"
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
                                                                        <option value="datalist"
                                                                            @selected($campo->tipo === 'datalist')>Datalist
                                                                        </option>
                                                                        {{-- <option value="radio"
                                                                            @selected($campo->tipo === 'radio')>Radio</option> --}}
                                                                    </select>
                                                                </td>
                                                                <td class="px-2 py-1 border">
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][placeholder]"
                                                                        value="{{ $campo->placeholder }}"
                                                                        placeholder="Placeholder"
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
                                                                    <input type="text"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][valor]"
                                                                        value="{{ $campo->valor }}"
                                                                        placeholder="Valor"
                                                                        class="w-full text-xs border rounded px-1 py-0.5">
                                                                </td>
                                                                <td class="px-2 py-1 border text-center">
                                                                    <input type="checkbox"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][modificable]"
                                                                        value="1" @checked($campo->modificable)
                                                                        class="mx-auto">
                                                                </td>
                                                                <td class="px-2 py-1 border text-center">
                                                                    <input type="checkbox"
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][auto_guardar]"
                                                                        value="1" @checked($campo->auto_guardar)
                                                                        class="mx-auto">
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
                                                                <td class="px-2 py-1 border">
                                                                    <select
                                                                        name="secciones[{{ $i }}][parametros][{{ $j }}][campos][{{ $k }}][id_campo_padre]"
                                                                        class="grupo-selector-dependencia w-full text-xs border rounded px-1 py-0.5"
                                                                        data-selected="{{ $campo->id_campo_padre }}">
                                                                        <option value="">Seleccione un campo
                                                                        </option>
                                                                        @foreach ($camposBD as $campoBD)
                                                                            @if ($campoBD->id !== $campo->id)
                                                                                <option value="{{ $campoBD->id }}"
                                                                                    {{ $campo->id_campo_padre == $campoBD->id ? 'selected' : '' }}>
                                                                                    {{ $campoBD->id . ' - ' . $campoBD->label }}
                                                                                </option>
                                                                            @endif
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
    {{-- Modal Guía rápida --}}
    <div id="modal-guia" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-book-open text-indigo-500 mr-2"></i>Guía rápida del constructor de formularios</h2>
                <button onclick="cerrarGuia()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            {{-- Tabs --}}
            <div class="flex border-b overflow-x-auto text-sm font-medium shrink-0">
                @foreach ([
                    ['id' => 'secciones',   'label' => '📦 Secciones'],
                    ['id' => 'parametros',  'label' => '📋 Parámetros'],
                    ['id' => 'campos',      'label' => '✏️ Campos'],
                    ['id' => 'limites',     'label' => '🔒 Límites'],
                    ['id' => 'opciones',    'label' => '📌 Listas de opciones'],
                ] as $tab)
                    <button type="button"
                        onclick="cambiarTab('{{ $tab['id'] }}')"
                        id="tab-btn-{{ $tab['id'] }}"
                        class="tab-btn px-4 py-3 whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-indigo-600 transition">
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Contenido --}}
            <div class="overflow-y-auto p-6 text-sm text-gray-700 space-y-4">

                {{-- Tab: Secciones --}}
                <div id="tab-secciones" class="tab-content space-y-3">
                    <p class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-indigo-800">
                        Una sección es como un <strong>grupo de filas</strong> dentro del formulario. Por ejemplo: si el formulario tiene datos físicos y datos químicos, cada uno sería una sección diferente.
                    </p>
                    <table class="w-full border text-xs rounded overflow-hidden">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr><th class="px-3 py-2 text-left">Elemento</th><th class="px-3 py-2 text-left">¿Para qué sirve?</th><th class="px-3 py-2 text-left">Ejemplo</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr><td class="px-3 py-2 font-medium">Nombre</td><td class="px-3 py-2">El título que verá el laboratorio encima de la tabla</td><td class="px-3 py-2 text-indigo-600">"Datos Físicos"</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Descripción</td><td class="px-3 py-2">Una explicación breve de qué tiene que llenar en esta sección</td><td class="px-3 py-2 text-indigo-600">"Registre los valores del análisis físico"</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Encabezados</td><td class="px-3 py-2">Los títulos de las columnas de la tabla</td><td class="px-3 py-2 text-indigo-600">"Parámetro", "Unidad", "Resultado"</td></tr>
                        </tbody>
                    </table>
                    <div class="bg-gray-50 border rounded p-3 text-xs text-gray-500">
                        <strong>Vista previa:</strong><br>
                        <div class="mt-2 border rounded overflow-hidden">
                            <div class="bg-indigo-600 text-white px-3 py-1 text-xs font-bold">Datos Físicos</div>
                            <div class="px-3 py-1 text-gray-500 text-xs italic border-b">Registre los valores del análisis físico</div>
                            <table class="w-full text-xs"><thead class="bg-gray-100"><tr><th class="px-2 py-1 border text-left">Parámetro</th><th class="px-2 py-1 border text-left">Unidad</th><th class="px-2 py-1 border text-left">Resultado</th></tr></thead><tbody><tr><td class="px-2 py-1 border text-gray-400 italic" colspan="3">filas aquí...</td></tr></tbody></table>
                        </div>
                    </div>
                </div>

                {{-- Tab: Parámetros --}}
                <div id="tab-parametros" class="tab-content hidden space-y-3">
                    <p class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-indigo-800">
                        Un parámetro es una <strong>fila</strong> de la tabla. Representa algo que el laboratorio tiene que medir o registrar. Por ejemplo: Turbidez, pH, Temperatura.
                    </p>
                    <table class="w-full border text-xs rounded overflow-hidden">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr><th class="px-3 py-2 text-left">Elemento</th><th class="px-3 py-2 text-left">¿Para qué sirve?</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr><td class="px-3 py-2 font-medium">Nombre</td><td class="px-3 py-2">Cómo se llama lo que se mide. Ej: <span class="text-indigo-600">"Turbidez"</span></td></tr>
                            <tr>
                                <td class="px-3 py-2 font-medium">Mostrar nombre <i class="fas fa-eye text-blue-400"></i></td>
                                <td class="px-3 py-2">Si está activo (<i class="fas fa-eye text-blue-500"></i>), el nombre del parámetro aparece como una columna visible en la tabla. Si está desactivado (<i class="fas fa-eye-slash text-gray-400"></i>), la fila no tiene etiqueta.</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 font-medium">Si el laboratorio llena cualquier campo de esta fila, los obligatorios también deben completarse</td>
                                <td class="px-3 py-2">Si el laboratorio escribe en <strong>algún campo</strong> de esta fila, todos los campos marcados como <em>Obligatorio</em> también se vuelven requeridos. Útil cuando los datos de una fila van siempre juntos.</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="bg-gray-50 border rounded p-3 text-xs">
                        <strong>Vista previa:</strong>
                        <table class="w-full mt-2 text-xs"><thead class="bg-gray-100"><tr><th class="px-2 py-1 border">Parámetro</th><th class="px-2 py-1 border">Unidad</th><th class="px-2 py-1 border">Resultado</th></tr></thead>
                        <tbody><tr><td class="px-2 py-1 border font-medium">Turbidez</td><td class="px-2 py-1 border text-gray-400">NTU</td><td class="px-2 py-1 border"><input type="text" class="border rounded px-1 w-full text-xs" placeholder="Ej: 0.5" disabled></td></tr>
                        <tr><td class="px-2 py-1 border font-medium">pH</td><td class="px-2 py-1 border text-gray-400">—</td><td class="px-2 py-1 border"><input type="text" class="border rounded px-1 w-full text-xs" placeholder="Ej: 7.2" disabled></td></tr></tbody></table>
                    </div>
                </div>

                {{-- Tab: Campos --}}
                <div id="tab-campos" class="tab-content hidden space-y-3">
                    <p class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-indigo-800">
                        Un campo es una <strong>celda</strong> donde el laboratorio escribe o elige un valor. Una fila puede tener varios campos: por ejemplo, el valor numérico y la unidad.
                    </p>
                    <table class="w-full border text-xs rounded overflow-hidden">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr><th class="px-3 py-2 text-left">Columna</th><th class="px-3 py-2 text-left">¿Para qué sirve?</th><th class="px-3 py-2 text-left">Ejemplo</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr><td class="px-3 py-2 font-medium">Etiqueta</td><td class="px-3 py-2">El nombre de esa columna en la tabla</td><td class="px-3 py-2 text-indigo-600">"Resultado"</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Tipo de campo</td><td class="px-3 py-2">Qué tipo de dato ingresa el laboratorio</td><td class="px-3 py-2 text-indigo-600">Número, Texto, Fecha, Lista de opciones, Casilla</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Texto de ayuda</td><td class="px-3 py-2">Lo que aparece dentro del campo vacío como orientación al laboratorio</td><td class="px-3 py-2 text-indigo-600">"Ej: 7.5"</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Obligatorio</td><td class="px-3 py-2">El laboratorio no puede enviar el formulario sin llenar este campo</td><td class="px-3 py-2">✓ marcado</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Orden</td><td class="px-3 py-2">En qué posición aparece esta columna (1 = primera)</td><td class="px-3 py-2 text-indigo-600">1, 2, 3...</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Editable</td><td class="px-3 py-2">Si está marcado, el laboratorio puede modificar el valor. Si está desmarcado, solo lo puede ver.</td><td class="px-3 py-2">✓ marcado = el lab puede editar</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Recordar valor</td><td class="px-3 py-2">Se llena automáticamente con lo que el laboratorio escribió el ciclo anterior. Útil para datos que casi no cambian.</td><td class="px-3 py-2">✓ marcado</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Valor fijo</td><td class="px-3 py-2">Un valor que aparece por defecto en el campo. Si <em>Editable</em> está desmarcado, el laboratorio lo verá pero no podrá cambiarlo.</td><td class="px-3 py-2 text-indigo-600">"NTU", "mg/L"</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Tab: Límites --}}
                <div id="tab-limites" class="tab-content hidden space-y-3">
                    <p class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-indigo-800">
                        Puedes poner reglas para que el laboratorio no ingrese valores incorrectos o fuera de rango.
                    </p>
                    <table class="w-full border text-xs rounded overflow-hidden">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr><th class="px-3 py-2 text-left">Opción</th><th class="px-3 py-2 text-left">¿Para qué sirve?</th><th class="px-3 py-2 text-left">Ejemplo</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr><td class="px-3 py-2 font-medium">Rango (mín-máx)</td><td class="px-3 py-2">El número tiene que estar entre dos valores. Se escribe como <strong>mínimo-máximo</strong></td><td class="px-3 py-2 text-indigo-600">0-14 → solo acepta del 0 al 14</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Formato</td><td class="px-3 py-2">Obliga a que el valor tenga una forma específica de escritura</td><td class="px-3 py-2">Ver tabla abajo</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Mensaje de error</td><td class="px-3 py-2">Lo que verá el laboratorio si escribe algo incorrecto</td><td class="px-3 py-2 text-indigo-600">"Solo valores entre 0 y 14"</td></tr>
                        </tbody>
                    </table>
                    <p class="font-semibold text-gray-700 mt-3">Formatos de validación más usados:</p>
                    <table class="w-full border text-xs rounded overflow-hidden">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr><th class="px-3 py-2 text-left">¿Qué quiero permitir?</th><th class="px-3 py-2 text-left">Código a poner</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr><td class="px-3 py-2">Solo números enteros (sin decimales)</td><td class="px-3 py-2 font-mono text-indigo-600">^\d+$</td></tr>
                            <tr><td class="px-3 py-2">Números con decimales (hasta 2 cifras)</td><td class="px-3 py-2 font-mono text-indigo-600">^\d+(\.\d{1,2})?$</td></tr>
                            <tr><td class="px-3 py-2">Solo letras (sin números ni símbolos)</td><td class="px-3 py-2 font-mono text-indigo-600">^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$</td></tr>
                            <tr><td class="px-3 py-2">Número con signo negativo o positivo</td><td class="px-3 py-2 font-mono text-indigo-600">^-?\d+(\.\d+)?$</td></tr>
                        </tbody>
                    </table>
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-xs text-yellow-800">
                        <i class="fas fa-lightbulb mr-1"></i> Si no necesitas un formato especial, deja ese campo vacío. El campo <strong>Rango (mín-máx)</strong> es suficiente para la mayoría de los casos.
                    </div>
                </div>

                {{-- Tab: Listas de opciones --}}
                <div id="tab-opciones" class="tab-content hidden space-y-3">
                    <p class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-indigo-800">
                        Si quieres que el laboratorio <strong>elija</strong> un valor de una lista en lugar de escribirlo, necesitas un Grupo de opciones.
                    </p>
                    <table class="w-full border text-xs rounded overflow-hidden">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr><th class="px-3 py-2 text-left">Concepto</th><th class="px-3 py-2 text-left">¿Para qué sirve?</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr><td class="px-3 py-2 font-medium">Grupo selector</td><td class="px-3 py-2">Es la lista de opciones que verá el laboratorio al desplegar el campo. Se crea en el módulo de <strong>Grupos de Selectores</strong>.</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Tipo del campo</td><td class="px-3 py-2">Para usar un grupo, el tipo del campo debe ser <span class="bg-indigo-100 text-indigo-700 px-1 rounded">Select</span> o <span class="bg-indigo-100 text-indigo-700 px-1 rounded">Lista con búsqueda</span>.</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Campo dependiente</td><td class="px-3 py-2">La lista de opciones de este campo cambia según lo que el laboratorio eligió en otro campo anterior.</td></tr>
                        </tbody>
                    </table>
                    <div class="bg-gray-50 border rounded p-3 text-xs">
                        <strong>Ejemplo de campo dependiente:</strong>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-200 px-2 py-1 rounded">Campo 1 → Departamento:</span>
                                <select class="border rounded px-2 py-1 text-xs" disabled><option>La Paz</option></select>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-200 px-2 py-1 rounded">Campo 2 → Provincia:</span>
                                <select class="border rounded px-2 py-1 text-xs" disabled><option>Cambia según Departamento</option></select>
                            </div>
                        </div>
                        <p class="text-gray-500 mt-2">En "Dependencia del campo", el Campo 2 apunta al Campo 1. Así, cuando el laboratorio elige un Departamento, las Provincias se actualizan automáticamente.</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-xs text-yellow-800">
                        <i class="fas fa-lightbulb mr-1"></i> Los grupos de opciones se administran desde el botón <strong>"Configurar Grupos de Selectores"</strong> en la página de formularios del ensayo.
                    </div>
                </div>

            </div>{{-- /contenido --}}
        </div>
    </div>

    <script>
        function abrirGuia() {
            document.getElementById('modal-guia').classList.remove('hidden');
            cambiarTab(sessionStorage.getItem('guiaTab') || 'secciones');
        }
        function cerrarGuia() {
            document.getElementById('modal-guia').classList.add('hidden');
        }
        function cambiarTab(id) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-indigo-600', 'text-indigo-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });
            document.getElementById('tab-' + id)?.classList.remove('hidden');
            const btn = document.getElementById('tab-btn-' + id);
            if (btn) {
                btn.classList.add('border-indigo-600', 'text-indigo-600');
                btn.classList.remove('border-transparent', 'text-gray-500');
            }
            sessionStorage.setItem('guiaTab', id);
        }
        document.getElementById('modal-guia').addEventListener('click', function(e) {
            if (e.target === this) cerrarGuia();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') cerrarGuia();
        });
    </script>

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
                        <button type="button"
                            class="toggle-visible text-blue-500 bg-blue-100 px-2 py-1 rounded">
                            <i class="fas fa-eye"></i>
                        </button>
                        <input 
                            type="hidden"
                            name="secciones[${seccionIdx}][parametros][${parametroIdx}][visible_nombre]"
                            value="1" 
                            class="visible-input">
                        
                        <label class="flex items-center gap-1 text-xs">
                            <input type="checkbox"
                              name="secciones[${seccionIdx}][parametros][${parametroIdx}][requerido_si_completa]"
                              value="1" checked
                              class="border rounded">
                              Si el laboratorio llena cualquier campo de esta fila, los obligatorios también deben completarse.
                        </label>
                        <button type="button" data-tippy-content="Eliminar" class="eliminar-parametro px-2 py-1 bg-red-500 text-white text-xs rounded"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;

                const trResultados = document.createElement('tr');
                trResultados.innerHTML = `
                <td>
                <table class="w-full text-xs border border-gray-300 rounded resultados-table">
                        <thead class="bg-gray-100 text-gray-600">
                              <tr>
                                    <th class="px-2 py-1 border">Etiqueta</th>
                                    <th class="px-2 py-1 border">Tipo de campo</th>
                                    <th class="px-2 py-1 border">Texto de ayuda</th>
                                    <th class="px-2 py-1 border text-center">Obligatorio</th>
                                    <th class="px-2 py-1 border">Orden</th>
                                    <th class="px-2 py-1 border">Formato</th>
                                    <th class="px-2 py-1 border w-[50px]">Mensaje de error</th>
                                    <th class="px-2 py-1 border">Rango (mín-máx)</th>
                                    <th class="px-2 py-1 border">Valor fijo</th>
                                    <th class="px-2 py-1 border text-center">Editable</th>
                                    <th class="px-2 py-1 border text-center">Recordar valor</th>
                                    <th class="px-2 py-1 border">Lista de opciones</th>
                                    <th class="px-2 py-1 border">Depende de</th>
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
                        <option value="datalist">Datalist</option>
                    </select>
                </td>
                <td class="px-2 py-1 border">
                    <input type="text" name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][placeholder]"
                        placeholder="Placeholder" class="w-full text-xs border rounded px-1 py-0.5">
                </td>
                <td class="px-2 py-1 border text-center">
                    <input type="checkbox" name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][requerido]"
                    class="mx-auto">
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
                <input type="text"
                    name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][valor]"
                    placeholder="Valor"
                    class="w-full text-xs border rounded px-1 py-0.5">
                </td>
                <td class="px-2 py-1 border text-center">
                    <input type="checkbox"
                    name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][modificable]"
                    value="1" checked
                    class="mx-auto">
                 </td>
                <td class="px-2 py-1 border text-center">
                    <input type="checkbox"
                    name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][auto_guardar]"
                    value="1"
                    class="mx-auto">
                 </td>
                <td class="px-2 py-1 border">
                     <select name="secciones[${seccionIdx}][parametros][${parametroIdx}][campos][${resultadoIdx}][id_grupo_selector]" class="grupo-selector-select w-full text-xs border rounded px-1 py-0.5" >
                        <option value="">Seleccione un grupo</option>
                      </select>
                </td>
                <td class="px-2 py-1 border">
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
                    const btnParamentroVisible = e.target.closest('.toggle-visible');
                    if (btnParamentroVisible) {
                        const td = btnParamentroVisible.closest('td');
                        const hiddenInput = td.querySelector('.visible-input');
                        const icon = btnParamentroVisible.querySelector('i');
                        if (hiddenInput.value === "1") {
                            hiddenInput.value = "0";
                            icon.classList.remove("fa-eye");
                            icon.classList.add("fa-eye-slash");
                            btnParamentroVisible._tippy?.setContent("Mostrar label en formulario");
                        } else {
                            hiddenInput.value = "1";
                            icon.classList.remove("fa-eye-slash");
                            icon.classList.add("fa-eye");
                            btnParamentroVisible._tippy?.setContent("Ocultar label en formulario");
                        }

                    }
                });
            });
        </script>
    @endpush


</x-app-layout>
