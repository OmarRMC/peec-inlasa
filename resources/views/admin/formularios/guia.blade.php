<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-6 space-y-10">

        {{-- Encabezado --}}
        <div>
            <button onclick="window.close()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg border border-gray-200 transition">
                <i class="fas fa-arrow-left text-xs"></i> Cerrar
            </button>
            <h1 class="text-2xl font-bold text-gray-800 mt-4">
                <i class="fas fa-book-open text-indigo-500 mr-2"></i>Guía para crear formularios
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Referencia completa de todos los campos del constructor - qué hace cada uno y qué valores puede tomar.
            </p>
        </div>

        {{-- ==================== ESTRUCTURA GENERAL ==================== --}}
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5">
            <h2 class="font-bold text-indigo-800 mb-2"><i class="fas fa-sitemap mr-2"></i>¿Cómo está organizado un formulario?</h2>
            <div class="flex flex-wrap gap-2 text-sm items-center text-indigo-700">
                <span class="bg-white border border-indigo-300 rounded-lg px-3 py-1.5 font-semibold">Formulario</span>
                <i class="fas fa-arrow-right text-xs text-indigo-400"></i>
                <span class="bg-white border border-indigo-300 rounded-lg px-3 py-1.5">Secciones (grupos)</span>
                <i class="fas fa-arrow-right text-xs text-indigo-400"></i>
                <span class="bg-white border border-indigo-300 rounded-lg px-3 py-1.5">Parámetros (filas)</span>
                <i class="fas fa-arrow-right text-xs text-indigo-400"></i>
                <span class="bg-white border border-indigo-300 rounded-lg px-3 py-1.5">Campos (celdas)</span>
            </div>
            <p class="text-indigo-700 text-xs mt-3">Cada nivel contiene al siguiente. Un formulario puede tener varias secciones, cada sección varias filas, y cada fila varias celdas.</p>
        </div>

        {{-- ==================== SECCIONES ==================== --}}
        <div id="sec-secciones" class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">1</span>
                <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-layer-group text-indigo-400 mr-1"></i>Sección</h2>
                <span class="text-xs text-gray-400">- agrupa las filas del formulario</span>
            </div>

            <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                        <tr>
                            <th class="px-4 py-3 text-left w-36">Campo</th>
                            <th class="px-4 py-3 text-left w-48">Valores posibles</th>
                            <th class="px-4 py-3 text-left">¿Para qué sirve?</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-gray-700">
                        <tr>
                            <td class="px-4 py-3 font-medium">Nombre</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Cualquier texto<br><span class="text-gray-400">Ej: "Datos Físicos"</span></td>
                            <td class="px-4 py-3 text-sm">Título del grupo. Aparece encima de la tabla de esa sección cuando el laboratorio llena el formulario.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Descripción</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Cualquier texto (opcional)<br><span class="text-gray-400">Ej: "Registre los valores del análisis físico"</span></td>
                            <td class="px-4 py-3 text-sm">Indicación breve que aparece debajo del título de la sección. Orienta al laboratorio sobre qué registrar.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Encabezados</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Lista de textos separados<br><span class="text-gray-400">Ej: "Parámetro", "Unidad", "Resultado"</span></td>
                            <td class="px-4 py-3 text-sm">Títulos de las columnas de la tabla. Deben coincidir con las etiquetas de los campos definidos en los parámetros.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ==================== PARÁMETROS ==================== --}}
        <div id="sec-parametros" class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold">2</span>
                <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-list text-purple-400 mr-1"></i>Parámetro</h2>
                <span class="text-xs text-gray-400">- una fila de la tabla</span>
            </div>

            <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                        <tr>
                            <th class="px-4 py-3 text-left w-36">Campo</th>
                            <th class="px-4 py-3 text-left w-48">Valores posibles</th>
                            <th class="px-4 py-3 text-left">¿Para qué sirve?</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-gray-700">
                        <tr>
                            <td class="px-4 py-3 font-medium">Nombre</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Cualquier texto<br><span class="text-gray-400">Ej: "Turbidez", "pH"</span></td>
                            <td class="px-4 py-3 text-sm">Identifica lo que se mide en esa fila. Si "Mostrar nombre" está activo, aparece como primera celda de la fila.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Mostrar nombre</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs mb-1"><i class="fas fa-eye text-xs"></i> Activo</span><br>
                                <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs"><i class="fas fa-eye-slash text-xs"></i> Inactivo</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <strong>Activo:</strong> el nombre del parámetro se muestra como una celda en la fila.<br>
                                <strong>Inactivo:</strong> la fila solo muestra los campos, sin etiqueta de nombre.
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Si llena un campo, los obligatorios también deben completarse</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs mb-1"><i class="fas fa-check text-xs"></i> Activo</span><br>
                                <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">Inactivo</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <strong>Activo:</strong> si el laboratorio escribe en cualquier campo de esa fila, todos los campos marcados como <em>Obligatorio</em> también son requeridos automáticamente.<br>
                                <span class="text-xs text-gray-400">Útil cuando los datos de una fila siempre van juntos: si pones el valor, también debes poner la unidad.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ==================== CAMPOS ==================== --}}
        <div id="sec-campos" class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold">3</span>
                <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-table-cells text-green-500 mr-1"></i>Campo</h2>
                <span class="text-xs text-gray-400">- una celda donde el laboratorio escribe o elige un valor</span>
            </div>

            <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                        <tr>
                            <th class="px-4 py-3 text-left w-36">Campo</th>
                            <th class="px-4 py-3 text-left w-64">Valores posibles</th>
                            <th class="px-4 py-3 text-left">¿Para qué sirve?</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-gray-700">
                        <tr>
                            <td class="px-4 py-3 font-medium">Etiqueta</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Cualquier texto<br><span class="text-gray-400">Ej: "Resultado", "Unidad"</span></td>
                            <td class="px-4 py-3 text-sm">Nombre de la columna en la tabla. Debe coincidir con uno de los encabezados de la sección.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Tipo de campo</td>
                            <td class="px-4 py-3">
                                <div class="space-y-1 text-xs">
                                    <div><span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">number</span> <span class="text-gray-500">Solo números</span></div>
                                    <div><span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">text</span> <span class="text-gray-500">Cualquier texto</span></div>
                                    <div><span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">date</span> <span class="text-gray-500">Selector de fecha</span></div>
                                    <div><span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">select</span> <span class="text-gray-500">Lista desplegable</span></div>
                                    <div><span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">datalist</span> <span class="text-gray-500">Lista con buscador</span></div>
                                    <div><span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">checkbox</span> <span class="text-gray-500">Marcar / desmarcar</span></div>
                                    <div><span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">textarea</span> <span class="text-gray-500">Texto largo (varias líneas)</span></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">Define qué tipo de dato ingresará el laboratorio. Usar <strong>select</strong> o <strong>datalist</strong> requiere también asignar una Lista de opciones.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Texto de ayuda</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Cualquier texto (opcional)<br><span class="text-gray-400">Ej: "Ej: 7.5"</span></td>
                            <td class="px-4 py-3 text-sm">Texto que aparece dentro del campo vacío para orientar al laboratorio. Desaparece al comenzar a escribir.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Obligatorio</td>
                            <td class="px-4 py-3">
                                <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-xs">✓ Sí</span><br>
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs mt-1 inline-block">No</span>
                            </td>
                            <td class="px-4 py-3 text-sm">Si está marcado, el laboratorio no puede enviar el formulario sin completar ese campo.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Orden</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Número entero<br><span class="text-gray-400">Ej: 1, 2, 3…</span></td>
                            <td class="px-4 py-3 text-sm">Posición de la columna dentro de la fila. El número más bajo aparece primero (izquierda).</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Editable</td>
                            <td class="px-4 py-3">
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">✓ Puede editar</span><br>
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs mt-1 inline-block">Solo lectura</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <strong>Marcado:</strong> el laboratorio puede escribir o cambiar el valor.<br>
                                <strong>Desmarcado:</strong> el valor es de solo lectura, no se puede modificar. Útil para unidades fijas como "mg/L".
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Recordar valor</td>
                            <td class="px-4 py-3">
                                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">✓ Auto-completar</span><br>
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs mt-1 inline-block">No</span>
                            </td>
                            <td class="px-4 py-3 text-sm">Si está marcado, el campo se pre-llena con el valor ingresado en el ciclo anterior. Útil para datos que casi no cambian (nombre de analista, unidades de medida).</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Valor fijo</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Cualquier texto (opcional)<br><span class="text-gray-400">Ej: "NTU", "mg/L", "°C"</span></td>
                            <td class="px-4 py-3 text-sm">Valor que ya viene escrito en el campo. Si <em>Editable</em> está desmarcado, el laboratorio lo ve pero no lo cambia. Si está marcado, sirve como valor por defecto que el lab puede modificar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ==================== LÍMITES Y FORMATO ==================== --}}
        <div id="sec-limites" class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">4</span>
                <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-shield-halved text-orange-400 mr-1"></i>Validación del campo</h2>
                <span class="text-xs text-gray-400">- limitar o restringir lo que se puede ingresar</span>
            </div>

            <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                        <tr>
                            <th class="px-4 py-3 text-left w-36">Campo</th>
                            <th class="px-4 py-3 text-left w-64">Valores posibles</th>
                            <th class="px-4 py-3 text-left">¿Para qué sirve?</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-gray-700">
                        <tr>
                            <td class="px-4 py-3 font-medium">Rango (mín-máx)</td>
                            <td class="px-4 py-3">
                                <code class="bg-gray-100 text-indigo-600 px-2 py-0.5 rounded text-xs">mínimo-máximo</code>
                                <div class="text-xs text-gray-400 mt-1">Ej: <code class="bg-gray-100 px-1 rounded">0-14</code> &nbsp; <code class="bg-gray-100 px-1 rounded">0.5-100</code></div>
                                <div class="text-xs text-gray-400">Dejar vacío = sin límite</div>
                            </td>
                            <td class="px-4 py-3 text-sm">El número ingresado debe estar entre el mínimo y el máximo indicados. Solo aplica a campos de tipo <strong>Número</strong>.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Formato</td>
                            <td class="px-4 py-3 text-xs text-indigo-600">
                                Código de expresión regular<br>
                                <span class="text-gray-400">Ver tabla completa abajo</span><br>
                                <span class="text-gray-400">Dejar vacío = sin restricción</span>
                            </td>
                            <td class="px-4 py-3 text-sm">Obliga a que el valor tenga una forma específica. Se usa un código llamado <em>expresión regular</em> - no hace falta entender la sintaxis, solo copia el código de la tabla de abajo según lo que necesites.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Mensaje de error</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Cualquier texto (opcional)<br><span class="text-gray-400">Ej: "Solo se aceptan valores entre 0 y 14"</span></td>
                            <td class="px-4 py-3 text-sm">Texto que verá el laboratorio si el valor no cumple el rango o el formato. Si se deja vacío, se muestra un mensaje genérico.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Tabla de expresiones regulares --}}
            <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                <div class="bg-orange-50 px-4 py-3 border-b">
                    <p class="font-semibold text-orange-800 text-sm"><i class="fas fa-code mr-2"></i>Tabla de formatos - copia el código según lo que necesites</p>
                    <p class="text-orange-600 text-xs mt-0.5">Pega el código exactamente como aparece en el campo <strong>Formato</strong> del editor.</p>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">¿Qué quiero permitir?</th>
                            <th class="px-4 py-3 text-left w-56">Código a copiar</th>
                            <th class="px-4 py-3 text-left w-32 text-green-700">✓ Acepta</th>
                            <th class="px-4 py-3 text-left w-32 text-red-600">✗ Rechaza</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-gray-700 text-xs">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Sin restricción (cualquier texto)</td>
                            <td class="px-4 py-3"><span class="text-gray-400 italic">Dejar vacío</span></td>
                            <td class="px-4 py-3 text-green-700">Todo</td>
                            <td class="px-4 py-3 text-red-500">Nada</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Solo números enteros<br><span class="font-normal text-gray-400">Sin punto decimal, sin negativos</span></td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^\d+$</code></td>
                            <td class="px-4 py-3 text-green-700">5 &nbsp; 100 &nbsp; 0</td>
                            <td class="px-4 py-3 text-red-500">3.5 &nbsp; -2 &nbsp; abc</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Número entero o decimal hasta 2 cifras<br><span class="font-normal text-gray-400">El punto es opcional</span></td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^\d+(\.\d{1,2})?$</code></td>
                            <td class="px-4 py-3 text-green-700">7 &nbsp; 7.5 &nbsp; 7.52</td>
                            <td class="px-4 py-3 text-red-500">7.523 &nbsp; -1 &nbsp; abc</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Número entero o decimal hasta 4 cifras</td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^\d+(\.\d{1,4})?$</code></td>
                            <td class="px-4 py-3 text-green-700">3 &nbsp; 3.14 &nbsp; 3.1416</td>
                            <td class="px-4 py-3 text-red-500">3.14159 &nbsp; -1</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Número positivo o negativo (con o sin decimal)</td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^-?\d+(\.\d+)?$</code></td>
                            <td class="px-4 py-3 text-green-700">5 &nbsp; -3 &nbsp; -0.5</td>
                            <td class="px-4 py-3 text-red-500">abc &nbsp; 1,5 &nbsp; --2</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Número positivo o negativo hasta 2 decimales</td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^-?\d+(\.\d{1,2})?$</code></td>
                            <td class="px-4 py-3 text-green-700">-1.5 &nbsp; 20.03 &nbsp; 0</td>
                            <td class="px-4 py-3 text-red-500">-1.555 &nbsp; abc</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Solo letras (sin números ni símbolos)<br><span class="font-normal text-gray-400">Incluye tildes y ñ</span></td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$</code></td>
                            <td class="px-4 py-3 text-green-700">Agua &nbsp; pH Ácido</td>
                            <td class="px-4 py-3 text-red-500">pH7 &nbsp; NTU/L &nbsp; 100</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Letras y números (sin símbolos especiales)</td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$</code></td>
                            <td class="px-4 py-3 text-green-700">Lab01 &nbsp; Muestra A2</td>
                            <td class="px-4 py-3 text-red-500">Lab#1 &nbsp; @valor</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Notación científica<br><span class="font-normal text-gray-400">Ej: para conteos microbiológicos</span></td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^\d+(\.\d+)?([eE][+-]?\d+)?$</code></td>
                            <td class="px-4 py-3 text-green-700">1.5e3 &nbsp; 2E-4 &nbsp; 100</td>
                            <td class="px-4 py-3 text-red-500">1,5e3 &nbsp; abc</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Número menor que (&lt;) o mayor que (&gt;)<br><span class="font-normal text-gray-400">Ej: &lt;0.1 , &gt;100</span></td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^[<>]?\d+(\.\d+)?$</code></td>
                            <td class="px-4 py-3 text-green-700">&lt;0.1 &nbsp; &gt;50 &nbsp; 7.5</td>
                            <td class="px-4 py-3 text-red-500">&lt;&lt;1 &nbsp; abc &nbsp; =5</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Resultado con operador y decimales<br><span class="font-normal text-gray-400">Ej: &lt;0.05 , &gt;=1.2 , 7.4</span></td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^([<>]=?)?-?\d+(\.\d+)?$</code></td>
                            <td class="px-4 py-3 text-green-700">&lt;0.05 &nbsp; &gt;=1 &nbsp; -3.2</td>
                            <td class="px-4 py-3 text-red-500">abc &nbsp; &lt;&lt;1</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Porcentaje<br><span class="font-normal text-gray-400">Número con % al final</span></td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^\d+(\.\d+)?%?$</code></td>
                            <td class="px-4 py-3 text-green-700">95% &nbsp; 98.5% &nbsp; 100</td>
                            <td class="px-4 py-3 text-red-500">abc% &nbsp; 1%%</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm">Formato de hora HH:MM<br><span class="font-normal text-gray-400">Para registrar horas de análisis</span></td>
                            <td class="px-4 py-3"><code class="bg-orange-50 text-orange-700 px-2 py-1 rounded select-all block w-fit">^([01]\d|2[0-3]):[0-5]\d$</code></td>
                            <td class="px-4 py-3 text-green-700">08:30 &nbsp; 14:00 &nbsp; 23:59</td>
                            <td class="px-4 py-3 text-red-500">25:00 &nbsp; 8:5 &nbsp; hora</td>
                        </tr>
                    </tbody>
                </table>
                <div class="bg-gray-50 px-4 py-2.5 border-t text-xs text-gray-400">
                    <i class="fas fa-hand-pointer mr-1"></i> Haz clic sobre el código para seleccionarlo y copiarlo fácilmente.
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                <i class="fas fa-lightbulb mr-2"></i><strong>Consejo:</strong> Si solo necesitas limitar el mínimo y el máximo de un número, usa <strong>Rango (mín-máx)</strong> y deja <strong>Formato</strong> vacío. Es suficiente para la mayoría de los casos.
            </div>
        </div>

        {{-- ==================== LISTAS DE OPCIONES ==================== --}}
        <div id="sec-opciones" class="space-y-4 pb-10">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold">5</span>
                <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-list-check text-teal-500 mr-1"></i>Lista de opciones</h2>
                <span class="text-xs text-gray-400">- cuando el laboratorio debe elegir de una lista predefinida</span>
            </div>

            <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b">
                        <tr>
                            <th class="px-4 py-3 text-left w-36">Campo</th>
                            <th class="px-4 py-3 text-left w-64">Valores posibles</th>
                            <th class="px-4 py-3 text-left">¿Para qué sirve?</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-gray-700">
                        <tr>
                            <td class="px-4 py-3 font-medium">Lista de opciones</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Un grupo creado previamente en <strong>Grupos de Selectores</strong><br><span class="text-gray-400">Ej: "Unidades de pH", "Métodos de ensayo"</span></td>
                            <td class="px-4 py-3 text-sm">Lista de valores que verá el laboratorio al abrir el campo. Se crea en el módulo de Grupos de Selectores y luego se asigna al campo desde el editor.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Tipo de campo requerido</td>
                            <td class="px-4 py-3">
                                <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs">select</span><br>
                                <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs mt-1 inline-block">datalist</span>
                            </td>
                            <td class="px-4 py-3 text-sm">Para asignar una lista de opciones a un campo, el <strong>Tipo de campo</strong> debe ser <em>select</em> o <em>datalist</em>. Con otro tipo, la lista no aparece.</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium">Depende de</td>
                            <td class="px-4 py-3 text-indigo-600 text-xs">Otro campo del mismo parámetro (opcional)<br><span class="text-gray-400">Ej: "Depende de: Campo 1 (Departamento)"</span><br><span class="text-gray-400">Dejar vacío = independiente</span></td>
                            <td class="px-4 py-3 text-sm">Permite que las opciones de este campo cambien automáticamente según lo que el laboratorio eligió en otro campo anterior. El campo padre debe ser también de tipo <em>select</em>.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 text-sm text-teal-800">
                <i class="fas fa-info-circle mr-2"></i><strong>¿Dónde creo las listas?</strong> Las listas se administran desde el botón <strong>"Configurar Grupos de Selectores"</strong> en la página del ensayo. Primero crea el grupo con sus opciones, luego asígnalo al campo en el editor.
            </div>
        </div>

    </div>
</x-app-layout>
