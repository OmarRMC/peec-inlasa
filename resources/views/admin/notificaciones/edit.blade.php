<x-app-layout>
<div class="max-w-6xl mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-edit text-indigo-500"></i>
            Editar notificación #{{ $notificacion->id }}
        </h1>
        <a href="{{ route('admin.notificaciones.index') }}"
            class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @if (session('success'))
        <div class="mb-3 p-3 bg-green-50 border border-green-300 text-green-700 rounded text-sm">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="mb-3 p-3 bg-blue-50 border border-blue-300 text-blue-700 rounded text-sm">{{ session('info') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-700 p-3 rounded mb-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4 text-sm text-yellow-900">
        <i class="fas fa-info-circle"></i>
        Los cambios que guardes acá afectarán a <strong>todos los destinatarios</strong> de esta notificación
        ({{ $notificacion->recepciones->count() }} laboratorio(s)).
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2 bg-white rounded shadow p-4 space-y-3">
            <form method="POST" action="{{ route('admin.notificaciones.update', $notificacion->id) }}">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Título</label>
                    <input type="text" name="titulo" id="edit-titulo" required maxlength="255"
                        value="{{ old('titulo', $notificacion->mensaje['titulo'] ?? $notificacion->titulo) }}"
                        class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                </div>

                <div class="mt-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mensaje</label>

                    <div class="flex border-b border-gray-200">
                        <button type="button" id="edit-tab-visual" onclick="editSwitchTab('visual')"
                            class="px-4 py-2 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600">
                            <i class="fas fa-edit mr-1"></i> Editor Visual
                        </button>
                        <button type="button" id="edit-tab-html" onclick="editSwitchTab('html')"
                            class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                            <i class="fas fa-code mr-1"></i> HTML
                        </button>
                    </div>

                    <div id="edit-toolbar" class="flex flex-wrap gap-1 p-2 bg-gray-100 border border-b-0 border-gray-300">
                        <select onchange="editExecArg('fontName', this.value);" class="text-sm border rounded px-1" title="Fuente" style="width: 120px;">
                            <option value="">Fuente</option>
                            <option value="Arial">Arial</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Verdana">Verdana</option>
                            <option value="Tahoma">Tahoma</option>
                            <option value="Trebuchet MS">Trebuchet MS</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Comic Sans MS">Comic Sans</option>
                        </select>
                        <select onchange="editExecArg('fontSize', this.value); this.value='';" class="text-sm border rounded px-1" title="Tamaño">
                            <option value="">Tamaño</option>
                            <option value="1">Pequeño</option>
                            <option value="3">Normal</option>
                            <option value="5">Grande</option>
                            <option value="7">Muy grande</option>
                        </select>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="editExec('bold')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Negrita"><i class="fas fa-bold"></i></button>
                        <button type="button" onclick="editExec('italic')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Cursiva"><i class="fas fa-italic"></i></button>
                        <button type="button" onclick="editExec('underline')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Subrayado"><i class="fas fa-underline"></i></button>
                        <button type="button" onclick="editExec('strikeThrough')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Tachado"><i class="fas fa-strikethrough"></i></button>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <select onchange="editExecArg('formatBlock', this.value); this.value='';" class="text-sm border rounded px-1" title="Formato">
                            <option value="">Formato</option>
                            <option value="h2">Título grande</option>
                            <option value="h3">Título</option>
                            <option value="h4">Subtítulo</option>
                            <option value="p">Párrafo</option>
                        </select>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <input type="color" onchange="editExecArg('foreColor', this.value)" class="w-8 h-8 p-0 border rounded cursor-pointer" title="Color texto" value="#000000">
                        <input type="color" onchange="editExecArg('hiliteColor', this.value)" class="w-8 h-8 p-0 border rounded cursor-pointer" title="Color fondo" value="#ffff00">
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="editExec('justifyLeft')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Izquierda"><i class="fas fa-align-left"></i></button>
                        <button type="button" onclick="editExec('justifyCenter')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Centro"><i class="fas fa-align-center"></i></button>
                        <button type="button" onclick="editExec('justifyRight')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Derecha"><i class="fas fa-align-right"></i></button>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="editExec('insertUnorderedList')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Lista viñetas"><i class="fas fa-list-ul"></i></button>
                        <button type="button" onclick="editExec('insertOrderedList')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Lista numerada"><i class="fas fa-list-ol"></i></button>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="editInsertLink()" class="px-2 py-1 hover:bg-gray-200 rounded" title="Insertar enlace"><i class="fas fa-link"></i></button>
                        <button type="button" onclick="editExec('removeFormat')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Quitar formato"><i class="fas fa-eraser"></i></button>
                    </div>

                    <div id="edit-visual-wrapper" class="border border-gray-300 rounded-b-lg overflow-hidden">
                        <div id="edit-visual" contenteditable="true"
                            class="p-4 bg-white focus:outline-none"
                            style="overflow-y: auto; height: 220px; max-height: 220px;">{!! old('mensaje', $notificacion->mensaje['html'] ?? '') !!}</div>
                    </div>
                    <div id="edit-html-wrapper" class="hidden">
                        <textarea id="edit-html-textarea"
                            class="w-full p-3 border border-gray-300 rounded-b-lg font-mono text-sm bg-gray-900 text-green-400"
                            style="height: 220px; max-height: 220px; resize: none;"></textarea>
                    </div>
                    <input type="hidden" name="mensaje" id="edit-mensaje"
                        value="{{ old('mensaje', $notificacion->mensaje['html'] ?? '') }}">
                </div>

                <div class="mt-3" x-data="{ modo: '{{ old('modo', $notificacion->modo) }}' }">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Modo</label>
                    <div class="flex gap-4 text-sm">
                        <label class="flex items-center gap-1">
                            <input type="radio" name="modo" value="una_vez" x-model="modo" class="text-indigo-600">
                            Una vez
                        </label>
                        <label class="flex items-center gap-1">
                            <input type="radio" name="modo" value="persistente" x-model="modo" class="text-indigo-600">
                            Persistente
                        </label>
                    </div>

                    <div x-show="modo === 'persistente'" x-transition class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-xs text-yellow-800 mb-2">Vigencia requerida para modo persistente.</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha inicio</label>
                                <input type="date" name="fecha_inicio"
                                    value="{{ old('fecha_inicio', optional($notificacion->fecha_inicio)->format('Y-m-d')) }}"
                                    :required="modo === 'persistente'"
                                    class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha fin</label>
                                <input type="date" name="fecha_fin"
                                    value="{{ old('fecha_fin', optional($notificacion->fecha_fin)->format('Y-m-d')) }}"
                                    :required="modo === 'persistente'"
                                    class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow p-4 space-y-3" x-data="{ busqueda: '' }">
            <h2 class="text-sm font-semibold text-gray-700">
                Destinatarios ({{ $notificacion->recepciones->count() }})
            </h2>

            @if ($notificacion->recepciones->whereNull('resuelta_en')->isNotEmpty())
                <form method="POST" action="{{ route('admin.notificaciones.resolverTodos', $notificacion->id) }}"
                    onsubmit="return confirm('¿Resolver para TODOS los destinatarios? Ninguno volverá a ver esta notificación.');">
                    @csrf
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                        <i class="fas fa-check-double"></i> Resolver para todos
                    </button>
                </form>
            @else
                <div class="p-2 bg-gray-100 text-gray-700 text-xs rounded text-center">
                    <i class="fas fa-check-double"></i>
                    Resuelta para todos el {{ optional($notificacion->recepciones->max('resuelta_en'))->format('d/m/Y H:i') }}
                </div>
            @endif

            <input type="text" x-model="busqueda"
                placeholder="Buscar laboratorio..."
                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">

            <div class="border border-gray-200 rounded overflow-y-auto" style="height: 400px;">
                @foreach ($notificacion->recepciones as $r)
                    @php
                        $estado = !is_null($r->resuelta_en) ? ['Resuelta', 'bg-gray-200 text-gray-700']
                            : (!is_null($r->leido_en) ? ['Leída', 'bg-blue-100 text-blue-800']
                            : ['Pendiente', 'bg-yellow-200 text-yellow-900']);
                        $nombreLab = strtolower(($r->laboratorio?->nombre_lab ?? '') . ' ' . ($r->laboratorio?->cod_lab ?? ''));
                    @endphp
                    <div x-show="busqueda === '' || '{{ $nombreLab }}'.includes(busqueda.toLowerCase())"
                         class="px-3 py-2 border-b border-gray-100 text-xs flex items-center justify-between">
                        <div class="min-w-0 flex-1 mr-2">
                            <div class="font-semibold text-gray-800 truncate">{{ $r->laboratorio?->nombre_lab }}</div>
                            <div class="text-gray-500">{{ $r->laboratorio?->cod_lab }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded {{ $estado[1] }} whitespace-nowrap">{{ $estado[0] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    const editHidden = document.getElementById('edit-mensaje');
    const editVisual = document.getElementById('edit-visual');
    const editTextarea = document.getElementById('edit-html-textarea');
    const editVisualWrap = document.getElementById('edit-visual-wrapper');
    const editHtmlWrap = document.getElementById('edit-html-wrapper');
    const editToolbar = document.getElementById('edit-toolbar');
    const editTabV = document.getElementById('edit-tab-visual');
    const editTabH = document.getElementById('edit-tab-html');
    let editMode = 'visual';

    function editSync() { editHidden.value = editVisual.innerHTML; }
    editVisual.addEventListener('input', editSync);
    editVisual.addEventListener('blur', editSync);
    editTextarea.addEventListener('input', () => { editHidden.value = editTextarea.value; });

    function editSwitchTab(tab) {
        if (tab === 'visual') {
            if (editMode === 'html' && editTextarea.value.trim() !== '') {
                editVisual.innerHTML = editTextarea.value;
            }
            editMode = 'visual';
            editVisualWrap.classList.remove('hidden');
            editHtmlWrap.classList.add('hidden');
            editToolbar.classList.remove('hidden');
            editTabV.classList.add('border-indigo-500', 'text-indigo-600');
            editTabV.classList.remove('border-transparent', 'text-gray-500');
            editTabH.classList.remove('border-indigo-500', 'text-indigo-600');
            editTabH.classList.add('border-transparent', 'text-gray-500');
            editSync();
        } else {
            editTextarea.value = editVisual.innerHTML;
            editMode = 'html';
            editVisualWrap.classList.add('hidden');
            editHtmlWrap.classList.remove('hidden');
            editToolbar.classList.add('hidden');
            editTabH.classList.add('border-indigo-500', 'text-indigo-600');
            editTabH.classList.remove('border-transparent', 'text-gray-500');
            editTabV.classList.remove('border-indigo-500', 'text-indigo-600');
            editTabV.classList.add('border-transparent', 'text-gray-500');
        }
    }
    function editExec(cmd) { editVisual.focus(); document.execCommand(cmd, false, null); editSync(); }
    function editExecArg(cmd, arg) { if (!arg) return; editVisual.focus(); document.execCommand(cmd, false, arg); editSync(); }
    function editInsertLink() {
        const url = prompt('URL del enlace:', 'https://');
        if (url) { editVisual.focus(); document.execCommand('createLink', false, url); editSync(); }
    }

    document.querySelector('form[action*="notificaciones"]').addEventListener('submit', function () {
        if (editMode === 'html') editHidden.value = editTextarea.value;
        else editHidden.value = editVisual.innerHTML;
    });
</script>
</x-app-layout>
