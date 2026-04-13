<x-app-layout>
<div x-data="{
    /* ── Modal Crear ── */
    modalCrear: {{ $errors->any() ? 'true' : 'false' }},
    crearUrl:    '{{ old('url', '') }}',
    crearArchivo: false,
    crearNombreArchivo: '',
    crearDragging: false,

    get crearUrlDis() { return this.crearArchivo; },
    get crearArcDis() { return this.crearUrl.trim() !== ''; },

    onCrearUrl(v) {
        this.crearUrl = v;
        if (v.trim() !== '') { this.crearArchivo = false; this.crearNombreArchivo = ''; }
    },
    onCrearArc(e) {
        const f = e.target.files[0];
        if (!f) return;
        this.crearArchivo = true;
        this.crearNombreArchivo = f.name;
        this.crearUrl = '';
    },
    onCrearDrop(e) {
        this.crearDragging = false;
        const f = e.dataTransfer.files[0];
        if (!f) return;
        const dt = new DataTransfer(); dt.items.add(f);
        this.$refs.crearInputArc.files = dt.files;
        this.crearArchivo = true;
        this.crearNombreArchivo = f.name;
        this.crearUrl = '';
    },
    quitarCrearArc() {
        this.crearArchivo = false;
        this.crearNombreArchivo = '';
        this.$refs.crearInputArc.value = '';
    },

    /* ── Modal Editar ── */
    modalEditar: false,
    editTitulo:  '',
    editUrl:     '',
    editArchivo: false,
    editArchivoNombre: '',
    editArchivoActual: '',
    editStatus:  1,
    editFormAction: '',
    editDragging: false,

    get editUrlDis() { return this.editArchivo; },
    get editArcDis() { return this.editUrl.trim() !== ''; },

    onEditUrl(v) {
        this.editUrl = v;
        if (v.trim() !== '') { this.editArchivo = false; this.editArchivoNombre = ''; }
    },
    onEditArc(e) {
        const f = e.target.files[0];
        if (!f) return;
        this.editArchivo = true;
        this.editArchivoNombre = f.name;
        this.editArchivoActual = '';
        this.editUrl = '';
    },
    onEditDrop(e) {
        this.editDragging = false;
        const f = e.dataTransfer.files[0];
        if (!f) return;
        const dt = new DataTransfer(); dt.items.add(f);
        this.$refs.editInputArc.files = dt.files;
        this.editArchivo = true;
        this.editArchivoNombre = f.name;
        this.editArchivoActual = '';
        this.editUrl = '';
    },
    quitarEditArc() {
        this.editArchivo = false;
        this.editArchivoNombre = '';
        this.editArchivoActual = '';
        this.$refs.editInputArc.value = '';
    },

    abrirEditar(recurso, baseUrl) {
        this.editTitulo        = recurso.titulo;
        this.editUrl           = recurso.url ?? '';
        this.editArchivo       = !!recurso.archivo;
        this.editArchivoNombre = recurso.archivo ? recurso.archivo.split('/').pop() : '';
        this.editArchivoActual = recurso.archivo ? recurso.archivo.split('/').pop() : '';
        this.editStatus        = recurso.status ? 1 : 0;
        this.editFormAction    = baseUrl.replace('__ID__', recurso.id);
        this.modalEditar       = true;
    }
}">

    {{-- ── Listado ── --}}
    <div class="container py-6 max-w-4xl">

        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">
                <i class="fas fa-folder-open text-indigo-500 mr-2"></i> Recursos de Laboratorio
            </h1>
            <button @click="modalCrear = true" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Recurso
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        @if ($verIds)
                        <th class="w-12 text-center">Nro</th>
                        @endif
                        <th>Título</th>
                        <th>Enlace / Archivo</th>
                        <th class="w-24 text-center">Estado</th>
                        <th class="w-24 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recursos as $recurso)
                        <tr>
                            @if ($verIds)
                            <td class="text-center text-xs text-gray-400">{{ $recurso->id }}</td>
                            @endif
                            <td class="font-medium">{{ $recurso->titulo }}</td>
                            <td class="text-sm max-w-xs truncate">
                                @if ($recurso->archivo)
                                    <a href="{{ $recurso->url_final }}" target="_blank"
                                        class="text-green-700 hover:underline inline-flex items-center gap-1"
                                        title="{{ basename($recurso->archivo) }}">
                                        <i class="fas fa-paperclip text-xs"></i>
                                        {{ basename($recurso->archivo) }}
                                    </a>
                                @elseif ($recurso->url)
                                    <a href="{{ $recurso->url }}" target="_blank"
                                        class="text-indigo-600 hover:underline inline-flex items-center gap-1"
                                        title="{{ $recurso->url }}">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                        {{ Str::limit($recurso->url, 55) }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <x-status-badge :value="$recurso->status ? '1' : '0'" />
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center space-x-1">
                                    <button type="button"
                                        @click="abrirEditar({{ json_encode(['id' => $recurso->id, 'titulo' => $recurso->titulo, 'url' => $recurso->url, 'archivo' => $recurso->archivo, 'status' => $recurso->status]) }}, '{{ route('recursos_lab.update', '__ID__') }}')"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST"
                                        action="{{ route('recursos_lab.destroy', $recurso) }}"
                                        class="delete-form inline"
                                        data-nombre="{{ $recurso->titulo }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="delete-button bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
                                            data-tippy-content="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-muted">
                                No hay recursos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $recursos->links() }}</div>

    </div>


    {{-- ══════ MODAL CREAR ══════ --}}
    <div x-show="modalCrear" x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        @keydown.escape.window="modalCrear = false">

        <div class="bg-white w-full max-w-sm mx-4 rounded-2xl shadow-lg max-h-[88vh] flex flex-col overflow-hidden"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.outside="modalCrear = false">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 shrink-0">
                <span class="font-semibold text-gray-800 text-sm">Nuevo recurso</span>
                <button @click="modalCrear = false"
                    class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <div class="h-px bg-gray-100 shrink-0"></div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1">
                <form id="form-crear"
                    action="{{ route('recursos_lab.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="px-5 py-4 space-y-4">
                    @csrf

                    <div>
                        <label class="label">Título <span class="text-red-500">*</span></label>
                        <input type="text" name="titulo"
                            value="{{ old('titulo') }}"
                            class="input-standard @error('titulo') border-red-500 @enderror"
                            placeholder="Ej: Convocatoria 2026">
                        @error('titulo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="label">URL externa</label>
                        <input type="url" name="url"
                            :value="crearUrl"
                            @input="onCrearUrl($event.target.value)"
                            :disabled="crearUrlDis"
                            class="input-standard transition-opacity"
                            :class="crearUrlDis ? 'opacity-40 cursor-not-allowed bg-gray-50' : ''"
                            placeholder="https://...">
                        @error('url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-3 text-xs">
                        <div class="flex-1 h-px bg-gray-100"></div>
                        <span class="text-gray-400">o sube un archivo</span>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>

                    <div>
                        <input type="file" name="archivo" x-ref="crearInputArc"
                            @change="onCrearArc($event)" class="hidden">

                        <div x-show="!crearArchivo"
                            @click="if (!crearArcDis) $refs.crearInputArc.click()"
                            @dragover.prevent="if (!crearArcDis) crearDragging = true"
                            @dragleave.prevent="crearDragging = false"
                            @drop.prevent="if (!crearArcDis) onCrearDrop($event)"
                            :class="crearArcDis
                                ? 'opacity-40 cursor-not-allowed border-gray-200 bg-gray-50'
                                : crearDragging
                                    ? 'border-indigo-400 bg-indigo-50 cursor-pointer'
                                    : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50 cursor-pointer'"
                            class="border-2 border-dashed rounded-xl px-4 py-5 text-center transition-colors select-none">
                            <i class="fas fa-cloud-upload-alt text-xl mb-1 block"
                                :class="crearDragging ? 'text-indigo-400' : 'text-gray-300'"></i>
                            <p class="text-xs text-gray-500">Arrastra o <span class="text-indigo-500 font-medium">selecciona</span> un archivo</p>
                            <p class="text-xs text-gray-400 mt-0.5">Cualquier tipo de archivo · máx. 20 MB</p>
                        </div>

                        <div x-show="crearArchivo"
                            class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                            <i class="fas fa-file-alt text-indigo-400 shrink-0"></i>
                            <span class="text-sm text-gray-700 truncate flex-1" x-text="crearNombreArchivo"></span>
                            <button type="button" @click="quitarCrearArc()"
                                class="text-gray-400 hover:text-red-500 shrink-0 transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>

                        @error('archivo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="label">Estado <span class="text-red-500">*</span></label>
                        <select name="status" class="input-standard @error('status') border-red-500 @enderror">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('status', 1) === 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                </form>
            </div>

            <div class="h-px bg-gray-100 shrink-0"></div>

            {{-- Footer --}}
            <div class="flex justify-end gap-2 px-5 py-4 shrink-0">
                <button type="button" @click="modalCrear = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    Cancelar
                </button>
                <button type="submit" form="form-crear" class="btn-primary text-sm">
                    <i class="fas fa-save mr-1"></i> Guardar
                </button>
            </div>

        </div>
    </div>


    {{-- ══════ MODAL EDITAR ══════ --}}
    <div x-show="modalEditar" x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        @keydown.escape.window="modalEditar = false">

        <div class="bg-white w-full max-w-sm mx-4 rounded-2xl shadow-lg max-h-[88vh] flex flex-col overflow-hidden"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.outside="modalEditar = false">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 shrink-0">
                <span class="font-semibold text-gray-800 text-sm">Editar recurso</span>
                <button @click="modalEditar = false"
                    class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <div class="h-px bg-gray-100 shrink-0"></div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1">
                <form id="form-editar"
                    :action="editFormAction"
                    method="POST"
                    enctype="multipart/form-data"
                    class="px-5 py-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="label">Título <span class="text-red-500">*</span></label>
                        <input type="text" name="titulo"
                            x-model="editTitulo"
                            class="input-standard"
                            placeholder="Ej: Convocatoria 2026">
                    </div>

                    <div>
                        <label class="label">URL externa</label>
                        <input type="url" name="url"
                            :value="editUrl"
                            @input="onEditUrl($event.target.value)"
                            :disabled="editUrlDis"
                            class="input-standard transition-opacity"
                            :class="editUrlDis ? 'opacity-40 cursor-not-allowed bg-gray-50' : ''"
                            placeholder="https://...">
                    </div>

                    <div class="flex items-center gap-3 text-xs">
                        <div class="flex-1 h-px bg-gray-100"></div>
                        <span class="text-gray-400">o sube un archivo</span>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>

                    <div>
                        <input type="file" name="archivo" x-ref="editInputArc"
                            @change="onEditArc($event)" class="hidden">

                        <div x-show="!editArchivo"
                            @click="if (!editArcDis) $refs.editInputArc.click()"
                            @dragover.prevent="if (!editArcDis) editDragging = true"
                            @dragleave.prevent="editDragging = false"
                            @drop.prevent="if (!editArcDis) onEditDrop($event)"
                            :class="editArcDis
                                ? 'opacity-40 cursor-not-allowed border-gray-200 bg-gray-50'
                                : editDragging
                                    ? 'border-indigo-400 bg-indigo-50 cursor-pointer'
                                    : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50 cursor-pointer'"
                            class="border-2 border-dashed rounded-xl px-4 py-5 text-center transition-colors select-none">
                            <i class="fas fa-cloud-upload-alt text-xl mb-1 block"
                                :class="editDragging ? 'text-indigo-400' : 'text-gray-300'"></i>
                            <p class="text-xs text-gray-500">Arrastra o <span class="text-indigo-500 font-medium">selecciona</span> un archivo</p>
                            <p class="text-xs text-gray-400 mt-0.5">Cualquier tipo de archivo · máx. 20 MB</p>
                        </div>

                        <div x-show="editArchivo"
                            class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                            <i class="fas fa-file-alt text-indigo-400 shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-700 truncate"
                                    x-text="editArchivoNombre || editArchivoActual"></p>
                                <p x-show="editArchivoActual && !editArchivoNombre"
                                    class="text-xs text-gray-400">Archivo actual</p>
                            </div>
                            <button type="button" @click="quitarEditArc()"
                                class="text-gray-400 hover:text-red-500 shrink-0 transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="label">Estado <span class="text-red-500">*</span></label>
                        <select name="status" x-model="editStatus" class="input-standard">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                </form>
            </div>

            <div class="h-px bg-gray-100 shrink-0"></div>

            {{-- Footer --}}
            <div class="flex justify-end gap-2 px-5 py-4 shrink-0">
                <button type="button" @click="modalEditar = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    Cancelar
                </button>
                <button type="submit" form="form-editar" class="btn-primary text-sm">
                    <i class="fas fa-save mr-1"></i> Actualizar
                </button>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar recurso?',
                text: form.dataset.nombre,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'swal2-sm', title: 'text-base', htmlContainer: 'text-sm' },
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
</script>
@endpush

</x-app-layout>
