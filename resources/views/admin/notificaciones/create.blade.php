<x-app-layout>
<div class="max-w-6xl mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-paper-plane text-indigo-500"></i>
            Nueva notificación
        </h1>
        <a href="{{ route('admin.notificaciones.index') }}"
            class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-700 p-3 rounded mb-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.notificaciones.store') }}"
        x-data="formNotif({{ $labs->toJson() }}, {{ json_encode($preseleccionados) }})">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            <div class="bg-white rounded shadow p-4 space-y-3">
                <h2 class="text-sm font-semibold text-gray-700 mb-2">Destinatarios</h2>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">País</label>
                        <select x-model="fPais" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="">Todos</option>
                            @foreach ($paises as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre_pais }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Departamento</label>
                        <select x-model="fDep" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="">Todos</option>
                            @foreach ($departamentos as $d)
                                <option value="{{ $d->id }}">{{ $d->nombre_dep }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Municipio</label>
                        <select x-model="fMunicipio" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="">Todos</option>
                            @foreach ($municipios as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre_municipio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Tipo</label>
                        <select x-model="fTipo" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="">Todos</option>
                            @foreach ($tipos as $t)
                                <option value="{{ $t->id }}">{{ $t->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Categoría</label>
                        <select x-model="fCategoria" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="">Todas</option>
                            @foreach ($categorias as $c)
                                <option value="{{ $c->id }}">{{ $c->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Buscar</label>
                        <input type="text" x-model="buscar"
                            placeholder="Nombre o código..."
                            class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded p-2">
                    <input type="checkbox" x-model="soloDeudores" class="rounded text-red-600">
                    <i class="fas fa-money-bill-wave"></i>
                    Solo laboratorios con deudas pendientes
                </label>

                <div class="flex items-center justify-between text-xs text-gray-600">
                    <span>
                        <span x-text="filtrados().length"></span> laboratorio(s) en el filtro
                    </span>
                    <div class="flex gap-2">
                        <button type="button" @click="seleccionarFiltrados()"
                            class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded">
                            Seleccionar todos
                        </button>
                        <button type="button" @click="ids = []"
                            class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
                            Limpiar
                        </button>
                    </div>
                </div>

                <div class="max-h-64 overflow-y-auto border border-gray-200 rounded">
                    <template x-for="lab in filtrados()" :key="lab.id">
                        <label class="flex items-center gap-2 px-3 py-1 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100">
                            <input type="checkbox" :value="lab.id"
                                :checked="ids.includes(lab.id)"
                                @change="toggle(lab.id)"
                                class="rounded border-gray-300 text-indigo-600">
                            <span x-text="lab.cod_lab + ' - ' + lab.nombre_lab"></span>
                            <span x-show="lab.deudor"
                                class="ml-auto inline-flex items-center gap-1 px-1.5 py-0.5 bg-red-100 text-red-800 rounded text-[10px] font-semibold">
                                <i class="fas fa-money-bill-wave"></i> Deudor
                            </span>
                        </label>
                    </template>
                    <div x-show="filtrados().length === 0" class="p-3 text-xs text-gray-500 text-center">
                        No se encontraron laboratorios.
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-700 mb-1">
                        Seleccionados: <span x-text="ids.length"></span>
                    </p>
                    <div class="flex flex-wrap gap-1 min-h-[2rem] max-h-32 overflow-y-auto p-2 border border-gray-200 rounded bg-gray-50">
                        <template x-for="lab in seleccionados()" :key="lab.id">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-indigo-100 text-indigo-800 h-fit">
                                <span x-text="lab.cod_lab + ' - ' + lab.nombre_lab"></span>
                                <button type="button" @click="toggle(lab.id)"
                                    class="text-indigo-600 hover:text-indigo-900 font-bold">&times;</button>
                            </span>
                        </template>
                        <span x-show="ids.length === 0" class="text-xs text-gray-400">Sin destinatarios</span>
                    </div>
                </div>

                <template x-for="id in ids" :key="id">
                    <input type="hidden" name="id_labs[]" :value="id">
                </template>
            </div>

            <div class="bg-white rounded shadow p-4 space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Título</label>
                    <input type="text" name="titulo" id="notif-titulo" required maxlength="255"
                        value="{{ old('titulo') }}"
                        class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mensaje</label>

                    <div class="flex border-b border-gray-200">
                        <button type="button" id="notif-tab-visual" onclick="notifSwitchTab('visual')"
                            class="px-4 py-2 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600">
                            <i class="fas fa-edit mr-1"></i> Editor Visual
                        </button>
                        <button type="button" id="notif-tab-html" onclick="notifSwitchTab('html')"
                            class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                            <i class="fas fa-code mr-1"></i> HTML
                        </button>
                    </div>

                    <div id="notif-toolbar" class="flex flex-wrap gap-1 p-2 bg-gray-100 border border-b-0 border-gray-300">
                        <select onchange="notifExecArg('fontName', this.value);" class="text-sm border rounded px-1" title="Fuente" style="width: 120px;">
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
                        <select onchange="notifExecArg('fontSize', this.value); this.value='';" class="text-sm border rounded px-1" title="Tamaño">
                            <option value="">Tamaño</option>
                            <option value="1">Pequeño</option>
                            <option value="3">Normal</option>
                            <option value="5">Grande</option>
                            <option value="7">Muy grande</option>
                        </select>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="notifExec('bold')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Negrita"><i class="fas fa-bold"></i></button>
                        <button type="button" onclick="notifExec('italic')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Cursiva"><i class="fas fa-italic"></i></button>
                        <button type="button" onclick="notifExec('underline')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Subrayado"><i class="fas fa-underline"></i></button>
                        <button type="button" onclick="notifExec('strikeThrough')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Tachado"><i class="fas fa-strikethrough"></i></button>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <select onchange="notifExecArg('formatBlock', this.value); this.value='';" class="text-sm border rounded px-1" title="Formato">
                            <option value="">Formato</option>
                            <option value="h2">Título grande</option>
                            <option value="h3">Título</option>
                            <option value="h4">Subtítulo</option>
                            <option value="p">Párrafo</option>
                        </select>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <input type="color" onchange="notifExecArg('foreColor', this.value)" class="w-8 h-8 p-0 border rounded cursor-pointer" title="Color texto" value="#000000">
                        <input type="color" onchange="notifExecArg('hiliteColor', this.value)" class="w-8 h-8 p-0 border rounded cursor-pointer" title="Color fondo" value="#ffff00">
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="notifExec('justifyLeft')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Izquierda"><i class="fas fa-align-left"></i></button>
                        <button type="button" onclick="notifExec('justifyCenter')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Centro"><i class="fas fa-align-center"></i></button>
                        <button type="button" onclick="notifExec('justifyRight')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Derecha"><i class="fas fa-align-right"></i></button>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="notifExec('insertUnorderedList')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Lista viñetas"><i class="fas fa-list-ul"></i></button>
                        <button type="button" onclick="notifExec('insertOrderedList')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Lista numerada"><i class="fas fa-list-ol"></i></button>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="notifInsertLink()" class="px-2 py-1 hover:bg-gray-200 rounded" title="Insertar enlace"><i class="fas fa-link"></i></button>
                        <button type="button" onclick="notifInsertImage()" class="px-2 py-1 hover:bg-gray-200 rounded" title="Insertar imagen"><i class="fas fa-image"></i></button>
                        <button type="button" onclick="notifResizeSelectedImage()" class="px-2 py-1 hover:bg-gray-200 rounded" title="Redimensionar imagen seleccionada"><i class="fas fa-expand-arrows-alt"></i></button>
                        <span class="border-l border-gray-300 mx-1"></span>
                        <button type="button" onclick="notifExec('removeFormat')" class="px-2 py-1 hover:bg-gray-200 rounded" title="Quitar formato"><i class="fas fa-eraser"></i></button>
                    </div>

                    <div id="notif-visual-wrapper" class="border border-gray-300 rounded-b-lg overflow-hidden">
                        <div id="notif-visual" contenteditable="true"
                            class="p-4 bg-white focus:outline-none"
                            style="overflow-y: auto; height: 220px; max-height: 220px;">{!! old('mensaje', '') !!}</div>
                    </div>

                    <div id="notif-html-wrapper" class="hidden">
                        <textarea id="notif-html-textarea"
                            class="w-full p-3 border border-gray-300 rounded-b-lg font-mono text-sm bg-gray-900 text-green-400"
                            style="height: 220px; max-height: 220px; resize: none;"></textarea>
                    </div>

                    <input type="hidden" name="mensaje" id="notif-mensaje" value="{{ old('mensaje', '') }}">
                </div>

                <div x-data="{ modo: 'una_vez' }">
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
                        <p class="text-xs text-yellow-800 mb-2">
                            <i class="fas fa-info-circle"></i>
                            Las notificaciones persistentes requieren un rango de vigencia.
                            Solo aparecerán al laboratorio dentro de este rango.
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha inicio</label>
                                <input type="date" name="fecha_inicio"
                                    value="{{ old('fecha_inicio', now()->toDateString()) }}"
                                    :required="modo === 'persistente'"
                                    class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha fin</label>
                                <input type="date" name="fecha_fin"
                                    value="{{ old('fecha_fin') }}"
                                    :required="modo === 'persistente'"
                                    class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 flex justify-end gap-2">
                <button type="button" onclick="notifPreview()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded text-sm">
                    <i class="fas fa-eye"></i> Vista previa
                </button>
                <a href="{{ route('admin.notificaciones.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm">Cancelar</a>
                <button type="submit"
                    :disabled="ids.length === 0"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-paper-plane"></i> Enviar
                </button>
            </div>
        </div>
    </form>
</div>

<div id="notif-preview-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="relative bg-white rounded-xl shadow-lg max-w-4xl w-full mx-auto overflow-y-auto" style="max-height:90vh; width:fit-content">
        <button type="button" onclick="document.getElementById('notif-preview-modal').classList.add('hidden')"
            class="absolute top-3 right-3 z-10 w-8 h-8 flex items-center justify-center bg-white hover:bg-gray-100 border border-gray-300 rounded-full text-gray-600 hover:text-gray-900 text-xl font-bold shadow-sm"
            title="Cerrar">&times;</button>
        <div class="p-6 sm:p-10">
            <h2 id="notif-preview-titulo" class="text-2xl font-bold mb-3 pr-8"></h2>
            <div id="notif-preview-mensaje" class="text-gray-800"></div>
        </div>
    </div>
</div>

<script>
    function formNotif(labs, preseleccionados) {
        return {
            labs: labs,
            ids: preseleccionados.map(Number),
            buscar: '',
            fPais: '',
            fDep: '',
            fMunicipio: '',
            fTipo: '',
            fCategoria: '',
            soloDeudores: false,
            filtrados() {
                const q = this.buscar.trim().toLowerCase();
                return this.labs.filter(l => {
                    if (this.fPais && String(l.id_pais) !== String(this.fPais)) return false;
                    if (this.fDep && String(l.id_dep) !== String(this.fDep)) return false;
                    if (this.fMunicipio && String(l.id_municipio) !== String(this.fMunicipio)) return false;
                    if (this.fTipo && String(l.id_tipo) !== String(this.fTipo)) return false;
                    if (this.fCategoria && String(l.id_categoria) !== String(this.fCategoria)) return false;
                    if (this.soloDeudores && !l.deudor) return false;
                    if (q && !(
                        (l.nombre_lab || '').toLowerCase().includes(q) ||
                        (l.cod_lab || '').toLowerCase().includes(q)
                    )) return false;
                    return true;
                });
            },
            seleccionados() {
                return this.labs.filter(l => this.ids.includes(l.id));
            },
            toggle(id) {
                id = Number(id);
                const i = this.ids.indexOf(id);
                if (i >= 0) this.ids.splice(i, 1);
                else this.ids.push(id);
            },
            seleccionarFiltrados() {
                const ids = this.filtrados().map(l => l.id);
                ids.forEach(id => { if (!this.ids.includes(id)) this.ids.push(id); });
            },
        };
    }

    // ===== Editor visual (basado en el de config/index) =====
    const notifHidden = document.getElementById('notif-mensaje');
    const notifVisual = document.getElementById('notif-visual');
    const notifTextarea = document.getElementById('notif-html-textarea');
    const notifVisualWrap = document.getElementById('notif-visual-wrapper');
    const notifHtmlWrap = document.getElementById('notif-html-wrapper');
    const notifToolbar = document.getElementById('notif-toolbar');
    const notifTabV = document.getElementById('notif-tab-visual');
    const notifTabH = document.getElementById('notif-tab-html');
    let notifMode = 'visual';

    function notifSync() { notifHidden.value = notifVisual.innerHTML; }
    notifVisual.addEventListener('input', notifSync);
    notifVisual.addEventListener('blur', notifSync);
    notifTextarea.addEventListener('input', () => { notifHidden.value = notifTextarea.value; });

    function notifSwitchTab(tab) {
        if (tab === 'visual') {
            if (notifMode === 'html' && notifTextarea.value.trim() !== '') {
                notifVisual.innerHTML = notifTextarea.value;
            }
            notifMode = 'visual';
            notifVisualWrap.classList.remove('hidden');
            notifHtmlWrap.classList.add('hidden');
            notifToolbar.classList.remove('hidden');
            notifTabV.classList.add('border-indigo-500', 'text-indigo-600');
            notifTabV.classList.remove('border-transparent', 'text-gray-500');
            notifTabH.classList.remove('border-indigo-500', 'text-indigo-600');
            notifTabH.classList.add('border-transparent', 'text-gray-500');
            notifSync();
        } else {
            notifTextarea.value = notifVisual.innerHTML;
            notifMode = 'html';
            notifVisualWrap.classList.add('hidden');
            notifHtmlWrap.classList.remove('hidden');
            notifToolbar.classList.add('hidden');
            notifTabH.classList.add('border-indigo-500', 'text-indigo-600');
            notifTabH.classList.remove('border-transparent', 'text-gray-500');
            notifTabV.classList.remove('border-indigo-500', 'text-indigo-600');
            notifTabV.classList.add('border-transparent', 'text-gray-500');
        }
    }
    function notifExec(cmd) { notifVisual.focus(); document.execCommand(cmd, false, null); notifSync(); }
    function notifExecArg(cmd, arg) { if (!arg) return; notifVisual.focus(); document.execCommand(cmd, false, arg); notifSync(); }
    function notifInsertLink() {
        const url = prompt('URL del enlace:', 'https://');
        if (url) { notifVisual.focus(); document.execCommand('createLink', false, url); notifSync(); }
    }

    // ----- Imágenes: insertar, seleccionar, redimensionar -----
    let notifSelectedImg = null;

    function notifInsertImage() {
        const url = prompt('Ingrese la URL de la imagen:', 'https://');
        if (!url) return;
        const width = prompt('Ancho en píxeles (vacío para tamaño original):', '300');

        notifVisual.focus();
        const img = document.createElement('img');
        img.src = url;
        img.style.cursor = 'pointer';
        if (width && !isNaN(width)) {
            img.style.width = width + 'px';
            img.style.height = 'auto';
        }

        const selection = window.getSelection();
        if (selection.rangeCount > 0 && notifVisual.contains(selection.anchorNode)) {
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(img);
            range.setStartAfter(img);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            notifVisual.appendChild(img);
        }
        notifSync();
    }

    function notifSelectImage(img) {
        if (notifSelectedImg) notifSelectedImg.style.outline = 'none';
        notifSelectedImg = img;
        notifSelectedImg.style.outline = '3px solid #6366f1';
    }

    function notifResizeSelectedImage() {
        if (!notifSelectedImg) {
            alert('Primero haz clic sobre una imagen para seleccionarla.');
            return;
        }
        const actual = notifSelectedImg.offsetWidth;
        const nuevo = prompt('Nuevo ancho en píxeles:', actual);
        if (nuevo && !isNaN(nuevo)) {
            notifSelectedImg.style.width = nuevo + 'px';
            notifSelectedImg.style.height = 'auto';
            notifSync();
        }
    }

    notifVisual.addEventListener('click', function (e) {
        if (e.target.tagName === 'IMG') {
            notifSelectImage(e.target);
        } else if (notifSelectedImg) {
            notifSelectedImg.style.outline = 'none';
            notifSelectedImg = null;
        }
    });
    function notifPreview() {
        if (notifMode === 'html') notifHidden.value = notifTextarea.value;
        else notifHidden.value = notifVisual.innerHTML;
        document.getElementById('notif-preview-titulo').textContent =
            document.getElementById('notif-titulo').value || 'Sin título';
        document.getElementById('notif-preview-mensaje').innerHTML = notifHidden.value || '';
        document.getElementById('notif-preview-modal').classList.remove('hidden');
    }

    // Antes de enviar: si está en modo HTML, sincronizar
    document.querySelector('form[action*="notificaciones"]').addEventListener('submit', function() {
        if (notifMode === 'html') notifHidden.value = notifTextarea.value;
        else notifHidden.value = notifVisual.innerHTML;
    });
</script>
</x-app-layout>
