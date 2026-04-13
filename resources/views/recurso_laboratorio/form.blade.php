@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

@php
    $r = $recurso ?? null;
@endphp

<div
    x-data="{
        urlValue:     '{{ old('url', $r?->url ?? '') }}',
        tieneArchivo: {{ ($r?->archivo && !old('url', $r?->url)) ? 'true' : 'false' }},
        icono:        '{{ old('icono', $r?->icono ?? 'fas fa-file') }}',

        get urlDeshabilitado() { return this.tieneArchivo; },
        get archivoDeshabilitado() { return this.urlValue.trim() !== ''; },

        onUrlInput(val) {
            this.urlValue = val;
            if (val.trim() !== '') this.tieneArchivo = false;
        },
        onArchivoChange(e) {
            this.tieneArchivo = e.target.files.length > 0;
            if (this.tieneArchivo) this.urlValue = '';
        },
        quitarArchivo() {
            this.tieneArchivo = false;
            this.$refs.inputArchivo.value = '';
        }
    }"
    class="space-y-4"
>

    <!-- Título -->
    <div>
        <label for="titulo" class="label">Título <span class="text-red-500">*</span></label>
        <input type="text" name="titulo" id="titulo"
            value="{{ old('titulo', $r?->titulo ?? '') }}"
            class="input-standard @error('titulo') border-red-500 @enderror"
            placeholder="Ej: Convocatoria">
        @error('titulo')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Icono -->
    <div>
        <label for="icono" class="label">
            Icono FontAwesome <span class="text-red-500">*</span>
        </label>
        <div class="flex items-center gap-3">
            <input type="text" name="icono" id="icono"
                x-model="icono"
                class="input-standard flex-1 @error('icono') border-red-500 @enderror"
                placeholder="Ej: fas fa-bullhorn">
            <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 text-xl">
                <i :class="icono || 'fas fa-file'"></i>
            </span>
        </div>
        <p class="text-xs text-gray-400 mt-1">
            Clases de <a href="https://fontawesome.com/icons" target="_blank" class="text-indigo-500 hover:underline">Font Awesome 6</a>.
            Ej: <code class="bg-gray-100 px-1 rounded">fas fa-gavel</code>, <code class="bg-gray-100 px-1 rounded">fas fa-file-pdf</code>
        </p>
        @error('icono')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- URL externa -->
    <div>
        <label for="url" class="label">URL externa</label>
        <input
            type="url"
            name="url"
            id="url"
            :value="urlValue"
            @input="onUrlInput($event.target.value)"
            :disabled="urlDeshabilitado"
            class="input-standard transition-opacity"
            :class="urlDeshabilitado ? 'opacity-40 cursor-not-allowed bg-gray-100' : ''"
            placeholder="https://drive.google.com/..."
        >
        <p class="text-xs text-gray-400 mt-1">
            Si ingresas una URL, el campo de archivo se deshabilitará.
        </p>
        @error('url')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Divisor -->
    <div class="flex items-center gap-3 text-gray-400 text-xs">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span>o</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <!-- Archivo -->
    <div>
        <label class="label">Subir archivo</label>

        {{-- Archivo actual en edición --}}
        @if ($r?->archivo)
            <div class="flex items-center gap-2 mb-2 text-sm text-gray-600 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2"
                x-show="tieneArchivo">
                <i class="fas fa-paperclip text-indigo-400"></i>
                <span class="truncate">{{ basename($r->archivo) }}</span>
                <button type="button" @click="quitarArchivo()"
                    class="ml-auto text-red-500 hover:text-red-700 text-xs">
                    <i class="fas fa-times"></i> Quitar
                </button>
            </div>
        @endif

        <input
            type="file"
            name="archivo"
            id="archivo"
            x-ref="inputArchivo"
            @change="onArchivoChange($event)"
            :disabled="archivoDeshabilitado"
            class="block w-full text-sm text-gray-600 border border-gray-300 rounded-lg cursor-pointer bg-gray-50
                   file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0
                   file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                   hover:file:bg-indigo-100 transition-opacity"
            :class="archivoDeshabilitado ? 'opacity-40 cursor-not-allowed' : ''"
            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
        >
        <p class="text-xs text-gray-400 mt-1">
            Formatos: PDF, Word, imagen (JPG, PNG). Máx. 20 MB.
            Si subes un archivo, el campo URL se vaciará.
        </p>
        @error('archivo')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Orden y Estado en fila -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="orden" class="label">Orden <span class="text-red-500">*</span></label>
            <input type="number" name="orden" id="orden"
                value="{{ old('orden', $r?->orden ?? 0) }}"
                min="0"
                class="input-standard @error('orden') border-red-500 @enderror">
            <p class="text-xs text-gray-400 mt-1">Menor número aparece primero.</p>
            @error('orden')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="label">Estado <span class="text-red-500">*</span></label>
            <select name="status" id="status"
                class="input-standard @error('status') border-red-500 @enderror">
                <option value="1" {{ old('status', $r?->status ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('status', $r?->status ?? 1) === 0 ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

</div>
