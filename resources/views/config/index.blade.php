@php use App\Models\Configuracion;  @endphp
<x-app-layout>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="bg-white border border-gray-200 shadow rounded-2xl p-6 space-y-6">
            <h1 class="text-xl font-bold text-primary mb-4">⚙️ Configuración del Sistema</h1>

            @if ($errors->any())
                <div class="bg-red-100 text-red-800 border border-red-200 p-4 mb-6 rounded-xl">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- , ['id' => 'vigencia', 'title' => 'Periodo de Vigencia'] --}}
            @foreach ([['id' => 'registro', 'title' => 'Periodo de registro de nuevos laboratorios'], ['id' => 'inscripcion', 'title' => 'Periodo de Inscripción'], ['id' => 'pago', 'title' => 'Periodo de Pago'], ['id' => 'notificacion', 'title' => 'Notificaciones'], ['id' => 'email.aprobacion', 'title' => 'Información personalizada para email de aprobacion'], ['id' => 'email.observacion', 'title' => 'Información personalizada para email de observacion'], ['id' => 'habilitar.docs.inscripcion', 'title' => 'Habilitar la subida de documentos de inscripción'], ['id' => 'habilitar.docs.pagos', 'title' => 'Habilitar la subida de comprobantes de pagos']] as $item)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button type="button"
                        class="w-full flex justify-between items-center px-5 py-4 bg-indigo-50 hover:bg-indigo-100 font-semibold transition duration-300"
                        onclick="toggleCollapse('{{ $item['id'] }}')">
                        <span>{{ $item['title'] }}</span>
                        <svg id="icon-{{ $item['id'] }}" class="w-5 h-5 transform transition-transform duration-300"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="content-{{ $item['id'] }}"
                        class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <div class="px-5 py-4 space-y-4 text-sm text-gray-700">
                            @php $config = 'configuracion.update'; @endphp

                            @switch($item['id'])
                                @case('registro')
                                    <form action="{{ route($config, 'periodo-registro') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="{{ Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB }}"
                                                    class="block text-sm font-medium">Fecha Inicio</label>
                                                <input type="date"
                                                    name="{{ Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB }}"
                                                    id="{{ Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB }}"
                                                    value="{{ old(Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB, configuracion(Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB }}"
                                                    class="block text-sm font-medium">Fecha Fin</label>
                                                <input type="date" name="{{ Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB }}"
                                                    id="{{ Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB }}"
                                                    value="{{ old(Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB, configuracion(Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">La fecha fin debe ser posterior a la fecha inicio.
                                        </p>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('inscripcion')
                                    <form action="{{ route($config, 'periodo-inscripcion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="{{ Configuracion::GESTION_INSCRIPCION }}"
                                                class="block text-sm font-medium">Gestion de inscripcion</label>
                                            <input type="number" name="{{ Configuracion::GESTION_INSCRIPCION }}"
                                                id="{{ Configuracion::GESTION_INSCRIPCION }}"
                                                value="{{ old(Configuracion::GESTION_INSCRIPCION, configuracion(Configuracion::GESTION_INSCRIPCION) ?? date('Y')) }}"
                                                class="mt-1 input-standard" min="2020" max="2100" required>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="{{ Configuracion::FECHA_INICIO_INSCRIPCION }}"
                                                    class="block text-sm font-medium">Fecha Inicio</label>
                                                <input type="date" name="{{ Configuracion::FECHA_INICIO_INSCRIPCION }}"
                                                    id="{{ Configuracion::FECHA_INICIO_INSCRIPCION }}"
                                                    value="{{ old(Configuracion::FECHA_INICIO_INSCRIPCION, configuracion(Configuracion::FECHA_INICIO_INSCRIPCION) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::FECHA_FIN_INSCRIPCION }}"
                                                    class="block text-sm font-medium">Fecha Fin</label>
                                                <input type="date" name="{{ Configuracion::FECHA_FIN_INSCRIPCION }}"
                                                    id="{{ Configuracion::FECHA_FIN_INSCRIPCION }}"
                                                    value="{{ old(Configuracion::FECHA_FIN_INSCRIPCION, configuracion(Configuracion::FECHA_FIN_INSCRIPCION) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">La fecha fin debe ser posterior a la fecha inicio.
                                        </p>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('pago')
                                    <form action="{{ route($config, 'periodo-pago') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="{{ Configuracion::FECHA_INICIO_PAGO }}"
                                                    class="block text-sm font-medium">Fecha Inicio</label>
                                                <input type="date" name="{{ Configuracion::FECHA_INICIO_PAGO }}"
                                                    id="{{ Configuracion::FECHA_INICIO_PAGO }}"
                                                    value="{{ old(Configuracion::FECHA_INICIO_PAGO, configuracion(Configuracion::FECHA_INICIO_PAGO) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::FECHA_FIN_PAGO }}"
                                                    class="block text-sm font-medium">Fecha Fin</label>
                                                <input type="date" name="{{ Configuracion::FECHA_FIN_PAGO }}"
                                                    id="{{ Configuracion::FECHA_FIN_PAGO }}"
                                                    value="{{ old(Configuracion::FECHA_FIN_PAGO, configuracion(Configuracion::FECHA_FIN_PAGO) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">La fecha fin debe ser posterior a la fecha inicio.
                                        </p>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('notificacion')
                                    <form action="{{ route($config, 'notificacion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="{{ Configuracion::NOTIFICACION_KEY }}"
                                                    class="block text-sm font-medium">Key</label>
                                                <input type="text" name="{{ Configuracion::NOTIFICACION_KEY }}"
                                                    id="{{ Configuracion::NOTIFICACION_KEY }}"
                                                    value="{{ old(Configuracion::NOTIFICACION_KEY, configuracion(Configuracion::NOTIFICACION_KEY) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::NOTIFICACION_TITULO }}"
                                                    class="block text-sm font-medium">Título</label>
                                                <input type="text" name="{{ Configuracion::NOTIFICACION_TITULO }}"
                                                    id="{{ Configuracion::NOTIFICACION_TITULO }}"
                                                    value="{{ old(Configuracion::NOTIFICACION_TITULO, configuracion(Configuracion::NOTIFICACION_TITULO) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="{{ Configuracion::NOTIFICACION_DESCRIPCION }}"
                                                class="block text-sm font-medium">Descripción</label>
                                            <input type="text" name="{{ Configuracion::NOTIFICACION_DESCRIPCION }}"
                                                id="{{ Configuracion::NOTIFICACION_DESCRIPCION }}"
                                                value="{{ old(Configuracion::NOTIFICACION_DESCRIPCION, configuracion(Configuracion::NOTIFICACION_DESCRIPCION) ?? '') }}"
                                                class="mt-1 input-standard" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium mb-2">Mensaje</label>

                                            <!-- Tabs para alternar entre editor visual y HTML -->
                                            <div class="flex border-b border-gray-200">
                                                <button type="button" id="tab-visual" onclick="switchEditorTab('visual')"
                                                    class="px-4 py-2 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600">
                                                    <i class="fas fa-edit mr-1"></i> Editor Visual
                                                </button>
                                                <button type="button" id="tab-html" onclick="switchEditorTab('html')"
                                                    class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                                                    <i class="fas fa-code mr-1"></i> HTML
                                                </button>
                                            </div>

                                            <!-- Barra de herramientas del editor visual -->
                                            <div id="editor-toolbar" class="flex flex-wrap gap-1 p-2 bg-gray-100 border border-b-0 border-gray-300">
                                                <!-- Fuente -->
                                                <select onchange="execCmdArg('fontName', this.value);" class="toolbar-select" title="Fuente" style="width: 120px;">
                                                    <option value="">Fuente</option>
                                                    <option value="Arial" style="font-family: Arial;">Arial</option>
                                                    <option value="Georgia" style="font-family: Georgia;">Georgia</option>
                                                    <option value="Times New Roman" style="font-family: Times New Roman;">Times New Roman</option>
                                                    <option value="Verdana" style="font-family: Verdana;">Verdana</option>
                                                    <option value="Tahoma" style="font-family: Tahoma;">Tahoma</option>
                                                    <option value="Trebuchet MS" style="font-family: Trebuchet MS;">Trebuchet MS</option>
                                                    <option value="Courier New" style="font-family: Courier New;">Courier New</option>
                                                    <option value="Comic Sans MS" style="font-family: Comic Sans MS;">Comic Sans</option>
                                                </select>
                                                <select onchange="execCmdArg('fontSize', this.value); this.value='';" class="toolbar-select" title="Tamaño">
                                                    <option value="">Tamaño</option>
                                                    <option value="1">Pequeño</option>
                                                    <option value="3">Normal</option>
                                                    <option value="5">Grande</option>
                                                    <option value="7">Muy Grande</option>
                                                </select>
                                                <span class="border-l border-gray-300 mx-1"></span>
                                                <button type="button" onclick="execCmd('bold')" class="toolbar-btn" title="Negrita (Ctrl+B)">
                                                    <i class="fas fa-bold"></i>
                                                </button>
                                                <button type="button" onclick="execCmd('italic')" class="toolbar-btn" title="Cursiva (Ctrl+I)">
                                                    <i class="fas fa-italic"></i>
                                                </button>
                                                <button type="button" onclick="execCmd('underline')" class="toolbar-btn" title="Subrayado (Ctrl+U)">
                                                    <i class="fas fa-underline"></i>
                                                </button>
                                                <button type="button" onclick="execCmd('strikeThrough')" class="toolbar-btn" title="Tachado">
                                                    <i class="fas fa-strikethrough"></i>
                                                </button>
                                                <span class="border-l border-gray-300 mx-1"></span>
                                                <select onchange="execCmdArg('formatBlock', this.value); this.value='';" class="toolbar-select" title="Formato">
                                                    <option value="">Formato</option>
                                                    <option value="h2">Título Grande</option>
                                                    <option value="h3">Título</option>
                                                    <option value="h4">Subtítulo</option>
                                                    <option value="p">Párrafo</option>
                                                </select>
                                                <span class="border-l border-gray-300 mx-1"></span>
                                                <input type="color" id="textColor" onchange="execCmdArg('foreColor', this.value)" class="w-8 h-8 p-0 border border-gray-300 rounded cursor-pointer" title="Color de texto" value="#000000">
                                                <input type="color" id="bgColor" onchange="execCmdArg('hiliteColor', this.value)" class="w-8 h-8 p-0 border border-gray-300 rounded cursor-pointer" title="Color de fondo" value="#ffff00">
                                                <span class="border-l border-gray-300 mx-1"></span>
                                                <button type="button" onclick="execCmd('justifyLeft')" class="toolbar-btn" title="Alinear izquierda">
                                                    <i class="fas fa-align-left"></i>
                                                </button>
                                                <button type="button" onclick="execCmd('justifyCenter')" class="toolbar-btn" title="Centrar">
                                                    <i class="fas fa-align-center"></i>
                                                </button>
                                                <button type="button" onclick="execCmd('justifyRight')" class="toolbar-btn" title="Alinear derecha">
                                                    <i class="fas fa-align-right"></i>
                                                </button>
                                                <span class="border-l border-gray-300 mx-1"></span>
                                                <button type="button" onclick="execCmd('insertUnorderedList')" class="toolbar-btn" title="Lista con viñetas">
                                                    <i class="fas fa-list-ul"></i>
                                                </button>
                                                <button type="button" onclick="execCmd('insertOrderedList')" class="toolbar-btn" title="Lista numerada">
                                                    <i class="fas fa-list-ol"></i>
                                                </button>
                                                <span class="border-l border-gray-300 mx-1"></span>
                                                <button type="button" onclick="insertLink()" class="toolbar-btn" title="Insertar enlace">
                                                    <i class="fas fa-link"></i>
                                                </button>
                                                <button type="button" onclick="insertImage()" class="toolbar-btn" title="Insertar imagen">
                                                    <i class="fas fa-image"></i>
                                                </button>
                                                <button type="button" onclick="resizeSelectedImage()" class="toolbar-btn" title="Redimensionar imagen seleccionada">
                                                    <i class="fas fa-expand-arrows-alt"></i>
                                                </button>
                                                <span class="border-l border-gray-300 mx-1"></span>
                                                <button type="button" onclick="execCmd('removeFormat')" class="toolbar-btn" title="Quitar formato">
                                                    <i class="fas fa-eraser"></i>
                                                </button>
                                            </div>

                                            <!-- Editor Visual (contenteditable) - muestra contenido formateado -->
                                            <div id="visual-editor-wrapper" class="border border-gray-300 rounded-b-lg overflow-hidden">
                                                <div id="visual-editor" contenteditable="true"
                                                    class="visual-editor-content p-4 bg-white focus:outline-none"
                                                    style="overflow-y: auto; height: 250px; max-height: 250px;">{!! old(Configuracion::NOTIFICACION_MENSAJE, configuracion(Configuracion::NOTIFICACION_MENSAJE) ?? '') !!}</div>
                                            </div>

                                            <!-- Editor HTML (textarea) - oculto por defecto, muestra código HTML -->
                                            <div id="html-editor-wrapper" class="hidden">
                                                <textarea id="{{ Configuracion::NOTIFICACION_MENSAJE }}-textarea"
                                                    class="w-full p-4 border border-gray-300 rounded-b-lg font-mono text-sm bg-gray-900 text-green-400"
                                                    style="height: 250px; max-height: 250px; overflow-y: auto; resize: none;"></textarea>
                                            </div>

                                            <!-- Campo oculto para enviar el HTML -->
                                            <input type="hidden" name="{{ Configuracion::NOTIFICACION_MENSAJE }}" id="{{ Configuracion::NOTIFICACION_MENSAJE }}"
                                                value="{{ old(Configuracion::NOTIFICACION_MENSAJE, configuracion(Configuracion::NOTIFICACION_MENSAJE) ?? '') }}">
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="{{ Configuracion::NOTIFICACION_FECHA_INICIO }}"
                                                    class="block text-sm font-medium">Fecha de Inicio</label>
                                                <input type="date" name="{{ Configuracion::NOTIFICACION_FECHA_INICIO }}"
                                                    id="{{ Configuracion::NOTIFICACION_FECHA_INICIO }}"
                                                    value="{{ old(Configuracion::NOTIFICACION_FECHA_INICIO, configuracion(Configuracion::NOTIFICACION_FECHA_INICIO) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::NOTIFICACION_FECHA_FIN }}"
                                                    class="block text-sm font-medium">Fecha de Fin</label>
                                                <input type="date" name="{{ Configuracion::NOTIFICACION_FECHA_FIN }}"
                                                    id="{{ Configuracion::NOTIFICACION_FECHA_FIN }}"
                                                    value="{{ old(Configuracion::NOTIFICACION_FECHA_FIN, configuracion(Configuracion::NOTIFICACION_FECHA_FIN) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>

                                        <p class="text-xs text-gray-500 mt-2">La fecha fin debe ser posterior o igual a la
                                            fecha inicio.</p>

                                        <div class="flex justify-between items-center mt-4">
                                            <button type="button" onclick="previewNotificacion()" class="btn-secondary">
                                                <i class="fas fa-eye mr-1"></i> Previsualizar Notificación
                                            </button>
                                            <button type="submit" class="btn-primary">
                                                <i class="fas fa-save mr-1"></i>Guardar
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Modal de previsualización -->
                                    <div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                                        <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6 relative mx-4">
                                            <button type="button" onclick="closePreviewModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                                            <h2 id="preview-titulo" class="text-xl font-bold mb-3"></h2>
                                            <p id="preview-descripcion" class="mb-4 text-gray-600"></p>
                                            <div id="preview-mensaje" class="mb-6 text-gray-800"></div>
                                        </div>
                                    </div>
                                @break

                                @case('gestion.filter')
                                    {{-- @php
                                        $gestiones = configuracion(Configuracion::KEY_GESTION_FILTER);
                                        $gestionesStr = implode(',', $gestiones);
                                    @endphp
                                    <form action="{{ route($config, 'gestion.filter') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="{{ Configuracion::KEY_GESTION_FILTER }}"
                                                class="block text-sm font-medium">Gestiones para filtros</label>
                                            <input type="string" name="{{ Configuracion::KEY_GESTION_FILTER }}"
                                                id="{{ Configuracion::KEY_GESTION_FILTER }}"
                                                value="{{ old(Configuracion::KEY_GESTION_FILTER, $gestionesStr ?? date('Y')) }}"
                                                class="mt-1 input-standard" min="2020" max="2100" required>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form> --}}
                                @break

                                @case('email.aprobacion')
                                    <form action="{{ route($config, 'email.aprobacion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="{{ Configuracion::EMAIL_APROBACION }}"
                                                class="block text-sm font-medium">Informacion (HTML permitido)</label>
                                            <textarea name="{{ Configuracion::EMAIL_APROBACION }}" id="{{ Configuracion::EMAIL_APROBACION }}" rows="5"
                                                class="input-standard" required>{{ old(Configuracion::EMAIL_APROBACION, configuracion(Configuracion::EMAIL_APROBACION) ?? '') }}</textarea>
                                        </div>

                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('email.observacion')
                                    <form action="{{ route($config, 'email.observacion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="{{ Configuracion::EMAIL_OBSERVACION }}"
                                                class="block text-sm font-medium">Informacion (HTML permitido)</label>
                                            <textarea name="{{ Configuracion::EMAIL_OBSERVACION }}" id="{{ Configuracion::EMAIL_OBSERVACION }}" rows="5"
                                                class="input-standard" required>{{ old(Configuracion::EMAIL_OBSERVACION, configuracion(Configuracion::EMAIL_OBSERVACION) ?? '') }}</textarea>
                                        </div>

                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('habilitar.docs.inscripcion')
                                    <form action="{{ route($config, 'habilitar.docs.inscripcion') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="flex items-center mt-2">
                                                <input type="checkbox"
                                                    name="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION }}"
                                                    id="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION }}"
                                                    {{ configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION) ? 'checked' : '' }}
                                                    class="mr-2 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                <label for="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION }}"
                                                    class="text-sm font-medium">
                                                    Habilitar la subida de documentos de inscripción
                                                </label>
                                            </div>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary">
                                                <i class="fas fa-save mr-1"></i>Guardar
                                            </button>
                                        </div>
                                    </form>
                                @break

                                @case('habilitar.docs.pagos')
                                    <form action="{{ route($config, 'habilitar.docs.pagos') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="flex items-center mt-2">
                                                <input type="checkbox"
                                                    name="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS }}"
                                                    id="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS }}"
                                                    {{ configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS) ? 'checked' : '' }}
                                                    class="mr-2 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                <label for="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS }}"
                                                    class="text-sm font-medium">
                                                    Habilitar la subida de comprobantes de pagos
                                                </label>
                                            </div>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary">
                                                <i class="fas fa-save mr-1"></i>Guardar
                                            </button>
                                        </div>
                                    </form>
                                @break
                            @endswitch
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function toggleCollapse(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);

            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = '0px';
                icon.classList.remove('rotate-180');
            } else {
                document.querySelectorAll('[id^="content-"]').forEach(el => el.style.maxHeight = '0px');
                document.querySelectorAll('[id^="icon-"]').forEach(el => el.classList.remove('rotate-180'));

                content.style.maxHeight = content.scrollHeight + 'px';
                icon.classList.add('rotate-180');
            }
        }

        // ========== Editor Visual WYSIWYG ==========

        const hiddenInput = document.getElementById('{{ Configuracion::NOTIFICACION_MENSAJE }}');
        const visualEditor = document.getElementById('visual-editor');
        const htmlTextarea = document.getElementById('{{ Configuracion::NOTIFICACION_MENSAJE }}-textarea');
        const toolbar = document.getElementById('editor-toolbar');
        const tabVisual = document.getElementById('tab-visual');
        const tabHtml = document.getElementById('tab-html');

        // Sincronizar contenido al campo oculto
        function syncToHidden() {
            hiddenInput.value = visualEditor.innerHTML;
        }

        // Inicializar al cargar
        document.addEventListener('DOMContentLoaded', function() {
            syncToHidden();
        });

        // Sincronizar cuando se escribe en el editor visual
        visualEditor.addEventListener('input', syncToHidden);
        visualEditor.addEventListener('blur', syncToHidden);

        // Sincronizar cuando se edita el HTML directamente - guardar exactamente como está
        htmlTextarea.addEventListener('input', function() {
            hiddenInput.value = htmlTextarea.value;
        });

        // También sincronizar al perder el foco
        htmlTextarea.addEventListener('blur', function() {
            hiddenInput.value = htmlTextarea.value;
        });

        const visualWrapper = document.getElementById('visual-editor-wrapper');
        const htmlWrapper = document.getElementById('html-editor-wrapper');

        // Cambiar entre tabs
        function switchEditorTab(tab) {
            if (tab === 'visual') {
                // Solo cargar del HTML si venimos del modo HTML
                if (currentMode === 'html' && htmlTextarea.value.trim() !== '') {
                    visualEditor.innerHTML = htmlTextarea.value;
                }
                currentMode = 'visual';
                visualWrapper.classList.remove('hidden');
                htmlWrapper.classList.add('hidden');
                toolbar.classList.remove('hidden');
                tabVisual.classList.add('border-indigo-500', 'text-indigo-600');
                tabVisual.classList.remove('border-transparent', 'text-gray-500');
                tabHtml.classList.remove('border-indigo-500', 'text-indigo-600');
                tabHtml.classList.add('border-transparent', 'text-gray-500');
                syncToHidden();
            } else {
                // Pasar del editor visual al HTML - muestra el código exacto
                htmlTextarea.value = visualEditor.innerHTML;
                currentMode = 'html';
                visualWrapper.classList.add('hidden');
                htmlWrapper.classList.remove('hidden');
                toolbar.classList.add('hidden');
                tabHtml.classList.add('border-indigo-500', 'text-indigo-600');
                tabHtml.classList.remove('border-transparent', 'text-gray-500');
                tabVisual.classList.remove('border-indigo-500', 'text-indigo-600');
                tabVisual.classList.add('border-transparent', 'text-gray-500');
            }
        }

        // Ejecutar comando de edición
        function execCmd(command) {
            visualEditor.focus();
            document.execCommand(command, false, null);
            syncToHidden();
        }

        // Ejecutar comando con argumento
        function execCmdArg(command, arg) {
            if (!arg) return;
            visualEditor.focus();
            document.execCommand(command, false, arg);
            syncToHidden();
        }

        // Insertar enlace
        function insertLink() {
            const url = prompt('Ingrese la URL del enlace:', 'https://');
            if (url) {
                visualEditor.focus();
                document.execCommand('createLink', false, url);
                syncToHidden();
            }
        }

        // Insertar imagen con tamaño
        function insertImage() {
            const url = prompt('Ingrese la URL de la imagen:', 'https://');
            if (!url) return;

            const width = prompt('Ancho de la imagen en píxeles (dejar vacío para tamaño original):', '300');

            visualEditor.focus();

            // Crear imagen con tamaño personalizado
            const img = document.createElement('img');
            img.src = url;
            img.style.cursor = 'pointer';
            img.onclick = function() { selectImage(this); };

            if (width && !isNaN(width)) {
                img.style.width = width + 'px';
                img.style.height = 'auto';
            }

            // Insertar en la posición del cursor
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                range.deleteContents();
                range.insertNode(img);
                range.setStartAfter(img);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
            } else {
                visualEditor.appendChild(img);
            }

            syncToHidden();
        }

        // Variable para imagen seleccionada
        let selectedImage = null;

        // Seleccionar imagen al hacer clic
        function selectImage(img) {
            // Quitar selección anterior
            if (selectedImage) {
                selectedImage.style.outline = 'none';
            }
            // Seleccionar nueva imagen
            selectedImage = img;
            selectedImage.style.outline = '3px solid #6366f1';
        }

        // Redimensionar imagen seleccionada
        function resizeSelectedImage() {
            if (!selectedImage) {
                alert('Primero haz clic en una imagen para seleccionarla');
                return;
            }

            const currentWidth = selectedImage.offsetWidth;
            const newWidth = prompt('Nuevo ancho en píxeles:', currentWidth);

            if (newWidth && !isNaN(newWidth)) {
                selectedImage.style.width = newWidth + 'px';
                selectedImage.style.height = 'auto';
                syncToHidden();
            }
        }

        // Detectar clic en imágenes dentro del editor
        visualEditor.addEventListener('click', function(e) {
            if (e.target.tagName === 'IMG') {
                selectImage(e.target);
            } else {
                // Deseleccionar si se hace clic fuera de una imagen
                if (selectedImage) {
                    selectedImage.style.outline = 'none';
                    selectedImage = null;
                }
            }
        });

        // Previsualizar notificación en modal
        function previewNotificacion() {
            const titulo = document.getElementById('{{ Configuracion::NOTIFICACION_TITULO }}').value || 'Sin título';
            const descripcion = document.getElementById('{{ Configuracion::NOTIFICACION_DESCRIPCION }}').value || '';
            syncToHidden();
            const mensaje = hiddenInput.value || '';

            document.getElementById('preview-titulo').textContent = titulo;
            document.getElementById('preview-descripcion').textContent = descripcion;
            document.getElementById('preview-mensaje').innerHTML = mensaje;
            document.getElementById('preview-modal').classList.remove('hidden');
        }

        function closePreviewModal() {
            document.getElementById('preview-modal').classList.add('hidden');
        }

        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePreviewModal();
            }
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('preview-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePreviewModal();
            }
        });

        // Variable para saber en qué modo estamos
        let currentMode = 'visual';

        // Sincronizar antes de enviar el formulario
        document.querySelector('form[action*="notificacion"]')?.addEventListener('submit', function(e) {
            if (currentMode === 'html') {
                // Si estamos en modo HTML, usar el valor del textarea directamente
                hiddenInput.value = htmlTextarea.value;
            } else {
                // Si estamos en modo visual, usar el contenido del editor
                syncToHidden();
            }
        });
    </script>

    <style>
        #preview-modal {
            animation: fadeIn 0.3s ease forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Estilos del editor WYSIWYG */
        .toolbar-btn {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.15s;
        }
        .toolbar-btn:hover {
            background-color: #e0e7ff;
            border-color: #6366f1;
        }
        .toolbar-btn:active {
            background-color: #c7d2fe;
        }
        .toolbar-select {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            cursor: pointer;
        }
        .toolbar-select:hover {
            border-color: #6366f1;
        }

        /* Editor Visual - Estilo tipo Word/documento */
        #visual-editor-wrapper {
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
            background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 20px);
        }

        .visual-editor-content {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 14px;
            line-height: 1.8;
            color: #1f2937;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            margin: 8px;
            border-radius: 4px;
            min-height: 200px !important;
        }

        .visual-editor-content:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2), 0 0 10px rgba(0,0,0,0.05);
        }

        .visual-editor-content:empty:before {
            content: 'Escribe tu mensaje aquí... Puedes usar la barra de herramientas para dar formato al texto.';
            color: #9ca3af;
            font-style: italic;
        }

        .visual-editor-content img {
            max-width: 100%;
            height: auto;
            margin: 0.75rem 0;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .visual-editor-content a {
            color: #2563eb;
            text-decoration: underline;
        }

        .visual-editor-content a:hover {
            color: #1d4ed8;
        }

        .visual-editor-content ul,
        .visual-editor-content ol {
            margin-left: 1.5rem;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            padding-left: 0.5rem;
        }

        .visual-editor-content ul {
            list-style-type: disc;
        }

        .visual-editor-content ol {
            list-style-type: decimal;
        }

        .visual-editor-content li {
            margin: 0.25rem 0;
        }

        .visual-editor-content h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1rem 0 0.5rem 0;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.25rem;
        }

        .visual-editor-content h3 {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 0.75rem 0 0.5rem 0;
            color: #1f2937;
        }

        .visual-editor-content h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0.5rem 0;
            color: #374151;
        }

        .visual-editor-content p {
            margin: 0.5rem 0;
        }

        .visual-editor-content blockquote {
            border-left: 4px solid #6366f1;
            padding-left: 1rem;
            margin: 0.75rem 0;
            color: #4b5563;
            font-style: italic;
        }

        /* Editor HTML - Estilo código */
        #html-editor-wrapper textarea {
            font-family: 'Monaco', 'Consolas', 'Courier New', monospace;
            resize: vertical;
        }
    </style>
</x-app-layout>
