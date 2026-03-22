<x-app-layout>

<div x-data="galeria()">

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <div class="container py-6 max-w-7xl">

        {{-- HEADER --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-images text-indigo-500 mr-2"></i> Galería de archivos
                </h1>
                <p class="text-sm text-gray-500 mt-1">Archivos almacenados en el servidor - accesibles por URL pública.</p>
            </div>
            <button @click="modalAbierto = true"
                class="btn-primary flex items-center gap-2">
                <i class="fas fa-upload"></i> Subir archivo
            </button>
        </div>

        {{-- ALERTAS --}}
        @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        {{-- BARRA DE FILTRO --}}
        <div class="flex flex-wrap gap-2 mb-6">
            <button @click="filtro = 'all'"
                :class="filtro === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                Todos <span class="ml-1 opacity-70">({{ count($archivos) }})</span>
            </button>
            <button @click="filtro = 'image'"
                :class="filtro === 'image' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                <i class="fas fa-image mr-1"></i> Imágenes
            </button>
            <button @click="filtro = 'pdf'"
                :class="filtro === 'pdf' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                <i class="fas fa-file-pdf mr-1"></i> PDFs
            </button>
            <button @click="filtro = 'other'"
                :class="filtro === 'other' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50'"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                <i class="fas fa-file mr-1"></i> Otros
            </button>

            <div class="ml-auto">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" x-model="busqueda" placeholder="Buscar archivo..."
                        class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none w-56">
                </div>
            </div>
        </div>

        {{-- GRID DE ARCHIVOS --}}
        @if (count($archivos) === 0)
        <div class="text-center py-20 text-gray-400">
            <i class="fas fa-folder-open text-5xl mb-4 block"></i>
            <p class="text-lg font-medium">No hay archivos aún</p>
            <p class="text-sm mt-1">Haz clic en "Subir archivo" para agregar el primero.</p>
        </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach ($archivos as $archivo)
            @php
                $iconClass = match($archivo['type']) {
                    'pdf'   => 'fas fa-file-pdf text-red-500',
                    'word'  => 'fas fa-file-word text-blue-500',
                    'excel' => 'fas fa-file-excel text-green-600',
                    'zip'   => 'fas fa-file-archive text-yellow-500',
                    default => 'fas fa-file text-gray-400',
                };
                $sizeHuman = match(true) {
                    $archivo['size'] >= 1048576 => round($archivo['size'] / 1048576, 1) . ' MB',
                    $archivo['size'] >= 1024    => round($archivo['size'] / 1024, 1) . ' KB',
                    default                     => $archivo['size'] . ' B',
                };
            @endphp

            <div x-show="archivoVisible('{{ $archivo['type'] }}', '{{ addslashes($archivo['filename']) }}')"
                class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">

                <a href="{{ $archivo['url'] }}" target="_blank" class="block">
                    @if ($archivo['type'] === 'image')
                    <div class="w-full h-32 bg-gray-100 overflow-hidden">
                        <img src="{{ $archivo['url'] }}" alt="{{ $archivo['filename'] }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                            onerror="this.parentElement.innerHTML='<div class=\'w-full h-32 flex items-center justify-center text-gray-300\'><i class=\'fas fa-image text-4xl\'></i></div>'">
                    </div>
                    @else
                    <div class="w-full h-32 bg-gray-50 flex items-center justify-center">
                        <i class="{{ $iconClass }} text-5xl"></i>
                    </div>
                    @endif
                </a>

                <div class="p-2 flex flex-col gap-1 flex-1">
                    <p class="text-xs font-medium text-gray-700 truncate" title="{{ $archivo['filename'] }}">
                        {{ $archivo['filename'] }}
                    </p>
                    <p class="text-[10px] text-gray-400">
                        {{ $sizeHuman }} &bull; {{ date('d/m/Y', $archivo['modified']) }}
                    </p>

                    <div class="flex gap-1 mt-auto pt-1">
                        <button type="button"
                            @click="copiarUrl('{{ $archivo['url'] }}')"
                            class="flex-1 flex items-center justify-center gap-1 text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg py-1 transition">
                            <i class="fas fa-link text-[10px]"></i> Copiar
                        </button>

                        <form method="POST"
                            action="{{ route('galeria.destroy', $archivo['filename']) }}"
                            @submit.prevent="confirmarEliminar($event, '{{ addslashes($archivo['filename']) }}')"
                            class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 rounded-lg py-1 transition">
                                <i class="fas fa-trash text-[10px]"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- ===== MODAL DE SUBIDA — dentro del mismo x-data ===== --}}
    <div x-show="modalAbierto" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @keydown.escape.window="cerrarModal()">

        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden"
            @click.outside="cerrarModal()">

            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-upload text-indigo-500"></i> Subir archivo
                </h2>
                <button type="button" @click="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('galeria.upload') }}" enctype="multipart/form-data"
                class="p-6 space-y-4" @submit="subiendo = true">
                @csrf

                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-indigo-400 transition-colors bg-gray-50"
                    @click="$refs.inputArchivo.click()"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="onDrop($event)"
                    :class="dragging ? 'border-indigo-500 bg-indigo-50' : ''">

                    <input type="file" name="archivo" x-ref="inputArchivo" class="hidden"
                        @change="onFileChange($event)">

                    <div x-show="!archivoSeleccionado">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-2 block"></i>
                        <p class="text-sm text-gray-500">Haz clic o arrastra un archivo aquí</p>
                        <p class="text-xs text-gray-400 mt-1">Cualquier formato - máx. 50 MB</p>
                    </div>

                    <div x-show="archivoSeleccionado" class="space-y-1">
                        <i class="fas fa-file text-3xl text-indigo-500 block mb-1"></i>
                        <p class="text-sm font-medium text-gray-700 break-all" x-text="nombreArchivo"></p>
                        <p class="text-xs text-gray-400" x-text="tamanoArchivo"></p>
                        <button type="button" @click.stop="limpiarArchivo()"
                            class="text-xs text-red-500 hover:underline mt-1">Quitar</button>
                    </div>
                </div>

                @error('archivo')
                <p class="text-red-600 text-xs">{{ $message }}</p>
                @enderror

                <div class="flex gap-3 justify-end pt-2">
                    <button type="button" @click="cerrarModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        :disabled="!archivoSeleccionado || subiendo"
                        class="px-5 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <i class="fas fa-spinner fa-spin" x-show="subiendo"></i>
                        <span x-text="subiendo ? 'Subiendo...' : 'Subir'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TOAST --}}
    <div x-show="toast" x-cloak x-transition
        class="fixed bottom-6 right-6 z-50 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2">
        <i class="fas fa-check-circle text-green-400"></i>
        <span>¡Enlace copiado al portapapeles!</span>
    </div>

</div>{{-- fin x-data --}}

<script>
function galeria() {
    return {
        filtro: 'all',
        busqueda: '',
        modalAbierto: {{ $errors->has('archivo') ? 'true' : 'false' }},
        dragging: false,
        archivoSeleccionado: false,
        nombreArchivo: '',
        tamanoArchivo: '',
        subiendo: false,
        toast: false,

        cerrarModal() {
            this.modalAbierto = false;
            this.limpiarArchivo();
            this.subiendo = false;
        },

        onFileChange(e) {
            const file = e.target.files[0];
            if (file) this.setArchivo(file);
        },

        onDrop(e) {
            this.dragging = false;
            const file = e.dataTransfer.files[0];
            if (!file) return;
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.inputArchivo.files = dt.files;
            this.setArchivo(file);
        },

        setArchivo(file) {
            this.archivoSeleccionado = true;
            this.nombreArchivo = file.name;
            const mb = file.size / 1048576;
            this.tamanoArchivo = mb >= 1
                ? mb.toFixed(1) + ' MB'
                : (file.size / 1024).toFixed(1) + ' KB';
        },

        limpiarArchivo() {
            this.archivoSeleccionado = false;
            this.nombreArchivo = '';
            this.tamanoArchivo = '';
            if (this.$refs.inputArchivo) this.$refs.inputArchivo.value = '';
        },

        archivoVisible(tipo, nombre) {
            const matchFiltro = this.filtro === 'all'
                || (this.filtro === 'other' && tipo !== 'image' && tipo !== 'pdf')
                || tipo === this.filtro;
            const matchBusqueda = nombre.toLowerCase().includes(this.busqueda.toLowerCase());
            return matchFiltro && matchBusqueda;
        },

        copiarUrl(url) {
            // navigator.clipboard solo funciona en HTTPS.
            // Fallback compatible con HTTP (XAMPP) usando execCommand.
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    this.mostrarToast();
                });
            } else {
                const el = document.createElement('textarea');
                el.value = url;
                el.style.position = 'fixed';
                el.style.opacity = '0';
                document.body.appendChild(el);
                el.focus();
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                this.mostrarToast();
            }
        },

        mostrarToast() {
            this.toast = true;
            setTimeout(() => this.toast = false, 2500);
        },

        confirmarEliminar(e, nombre) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Eliminar archivo?',
                    text: nombre,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'swal2-sm',
                        title: 'text-base',
                        htmlContainer: 'text-sm',
                    },
                }).then((result) => {
                    if (result.isConfirmed) e.target.submit();
                });
            } else {
                if (confirm('¿Eliminar "' + nombre + '"?')) e.target.submit();
            }
        },
    };
}
</script>

</x-app-layout>
