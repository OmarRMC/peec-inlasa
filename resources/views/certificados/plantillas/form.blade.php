 @php

 $disenoValue = old('diseno');
 if ($disenoValue === null) {
 $disenoValue = $plantilla->diseno
 ? json_encode($plantilla->diseno, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
 : '';
 }

 $firmas = old('firmas', $plantilla->firmas ?? []);
 if (is_string($firmas)) {
 $decoded = json_decode($firmas, true);
 $firmas = is_array($decoded) ? $decoded : [];
 }
 if (!is_array($firmas)) {
 $firmas = [];
 }
 @endphp
 <x-app-layout>
     <div class="px-4 py-6 max-w-5xl mx-auto">
         <div class="bg-white shadow-md rounded-xl p-6 space-y-5 border border-gray-200">
             <x-shared.btn-volver :url="route('plantillas-certificados.index')" />

             <div class="flex items-center justify-between flex-wrap gap-3">
                 <h1 class="text-xl font-bold text-primary">
                     {{ $plantilla->exists ? 'Editar Plantilla' : 'Nueva Plantilla' }}
                 </h1>

                 @if ($plantilla->exists)
                 <a href="{{ route('plantillas-certificados.preview', $plantilla) }}"
                     target="_blank"
                     class="bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-3 py-2 rounded-md shadow-sm text-sm">
                     <i class="fas fa-eye"></i> Previsualizar
                 </a>
                 @endif
             </div>

             {{-- Errores generales --}}
             @if ($errors->any())
             <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 text-sm">
                 <div class="font-semibold mb-1">Revise los campos marcados:</div>
                 <ul class="list-disc pl-5 space-y-1">
                     @foreach ($errors->all() as $err)
                     <li>{{ $err }}</li>
                     @endforeach
                 </ul>
             </div>
             @endif

             <form
                 action="{{ $plantilla->exists ? route('plantillas-certificados.update', $plantilla) : route('plantillas-certificados.store') }}"
                 method="POST"
                 enctype="multipart/form-data"
                 class="flex flex-col gap-4">
                 @csrf
                 @if ($plantilla->exists)
                 @method('PUT')
                 @endif

                 {{-- ========================= DATOS BÁSICOS ========================= --}}
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     {{-- Nombre --}}
                     <div>
                         <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-1">Nombre</label>
                         <input
                             type="text"
                             name="nombre"
                             id="nombre"
                             value="{{ old('nombre', $plantilla->nombre) }}"
                             maxlength="150"
                             required
                             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                         @error('nombre')
                         <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                         @enderror
                     </div>

                     {{-- Estado --}}
                     <div>
                         <label for="activo" class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                         <select
                             name="activo"
                             id="activo"
                             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                             required>
                             @foreach (\App\Models\PlantillaCertificado::ESTADOS as $value => $label)
                                 <option value="{{ $value }}" @selected((int) old('activo', $plantilla->activo ?? 1) === $value)>{{ $label }}</option>
                             @endforeach
                         </select>
                         @error('activo')
                         <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                         @enderror
                     </div>
                 </div>

                 {{-- Descripción --}}
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div>
                         <label for="descripcion_desmp" class="block text-sm font-semibold text-gray-700 mb-1">Descripción(key=descripcion_desmp)</label>
                         <textarea
                             name="descripcion_desmp"
                             id="descripcion_desmp"
                             rows="6"
                             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('descripcion_desmp', json_encode($plantilla->descripcion_desmp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                         @error('descripcion_desmp')
                         <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                         @enderror
                     </div>
                     <div>
                         <label for="descripcion_part" class="block text-sm font-semibold text-gray-700 mb-1">Descripción(key=descripcion_part)</label>
                         <textarea
                             name="descripcion_part"
                             id="descripcion_part"
                            rows="6"
                             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('descripcion_part', json_encode($plantilla->descripcion_part, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                         @error('descripcion_part')
                         <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                         @enderror
                     </div>
                 </div>


                 {{-- ========================= FONDO (preview dentro del mismo input visual) ========================= --}}
                 <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3">
                     <div>
                         <h3 class="text-sm font-semibold text-gray-800">Imagen de fondo</h3>
                         <p class="text-xs text-gray-500">Haga clic para subir o cambiar el fondo (PNG, JPG, SVG, WEBP, BMP, GIF - máx. 50 MB).</p>
                     </div>

                     {{-- Input real (oculto) --}}
                     <input
                         type="file"
                         name="imagen_fondo_file"
                         id="imagen_fondo_file"
                         accept="image/*"
                         class="hidden">

                     {{-- Guardamos el fondo actual para restaurar si el usuario quita el archivo --}}
                     <input type="hidden" id="fondoActualUrl" value="{{ $plantilla->imagen_fondo ?? '' }}">

                     {{-- Campo visual --}}
                     <div
                         id="fondoDropzone"
                         class="cursor-pointer border border-dashed border-gray-300 rounded-lg bg-white overflow-hidden
                               hover:border-indigo-400 transition">
                         <div class="flex items-center justify-between gap-3 px-4 py-3 border-b bg-gray-50">
                             <div class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                 <i class="fas fa-image text-gray-500"></i>
                                 <span id="fondoLabel">
                                     {{ !empty($plantilla->imagen_fondo) ? 'Cambiar imagen' : 'Subir imagen' }}
                                 </span>
                             </div>

                             <div class="flex items-center gap-2">
                                 <span id="fondoFileName" class="text-xs text-gray-500 truncate max-w-[220px]">
                                     {{ !empty($plantilla->imagen_fondo) ? 'Fondo actual cargado' : 'Ningún archivo seleccionado' }}
                                 </span>

                                 <button
                                     type="button"
                                     id="btnQuitarFondo"
                                     class="hidden bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm text-xs"
                                     title="Quitar selección">
                                     <i class="fas fa-times"></i>
                                 </button>
                             </div>
                         </div>

                         <div class="w-full h-56 bg-gray-100 flex items-center justify-center">
                             <img
                                 id="fondoPreview"
                                 src="{{ $plantilla->imagen_fondo ?? '' }}"
                                 alt="Fondo preview"
                                 class="w-full h-56 object-cover"
                                 style="{{ !empty($plantilla->imagen_fondo) ? '' : 'display:none;' }}">
                             <div
                                 id="fondoEmpty"
                                 class="text-sm text-gray-400 px-6 text-center"
                                 style="{{ !empty($plantilla->imagen_fondo) ? 'display:none;' : '' }}">
                                 <div class="font-semibold text-gray-500">Sin fondo</div>
                                 <div class="text-xs mt-1">Haga clic aquí para seleccionar una imagen</div>
                             </div>
                         </div>
                     </div>

                     @error('imagen_fondo_file')
                     <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 {{-- ========================= DIMENSIONES + ESTILOS ========================= --}}
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div>
                         <label for="ancho_mm" class="block text-sm font-semibold text-gray-700 mb-1">Ancho (mm)</label>
                         <input
                             type="number"
                             name="ancho_mm"
                             id="ancho_mm"
                             step="0.01"
                             min="1"
                             value="{{ old('ancho_mm', $plantilla->ancho_mm) }}"
                             required
                             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                         @error('ancho_mm')
                         <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                         @enderror
                     </div>

                     <div>
                         <label for="alto_mm" class="block text-sm font-semibold text-gray-700 mb-1">Alto (mm)</label>
                         <input
                             type="number"
                             name="alto_mm"
                             id="alto_mm"
                             step="0.01"
                             min="1"
                             value="{{ old('alto_mm', $plantilla->alto_mm) }}"
                             required
                             class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                         @error('alto_mm')
                         <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                         @enderror
                     </div>
                 </div>

                 {{-- ========================= FIRMAS (dinámico) ========================= --}}
                 <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                     <div class="flex items-center justify-between gap-3 flex-wrap mb-3">
                         <div>
                             <h3 class="text-sm font-semibold text-gray-800">Firmas</h3>
                             <p class="text-xs text-gray-500">Agregue firmantes con nombre, cargo y su imagen de firma.</p>
                         </div>

                         <button type="button" id="btnAddFirma"
                             class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-md shadow-sm text-sm">
                             <i class="fas fa-plus-circle"></i> Agregar firmante
                         </button>
                     </div>

                     <div id="firmasContainer" class="space-y-3">
                         @forelse ($firmas as $i => $firma)
                         @php
                         $nombre = $firma['nombre'] ?? '';
                         $cargo = $firma['cargo'] ?? '';
                         $firmaUrl = $firma['firma'] ?? null;
                         @endphp

                         <div class="firma-item bg-white border border-gray-200 rounded-lg p-4" data-index="{{ $i }}">
                             <div class="flex items-center justify-between gap-3 mb-3">
                                 <div class="text-sm font-semibold text-gray-700">
                                     Firmante <span class="firma-num">{{ $loop->iteration }}</span>
                                 </div>

                                 <button type="button"
                                     class="btnRemoveFirma bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
                                     title="Eliminar firmante">
                                     <i class="fas fa-trash-alt"></i>
                                 </button>
                             </div>

                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                 <div>
                                     <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre</label>
                                     <input type="text"
                                         name="firmas[{{ $i }}][nombre]"
                                         value="{{ old("firmas.$i.nombre", $nombre) }}"
                                         required
                                         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                     @error("firmas.$i.nombre")
                                     <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                     @enderror
                                 </div>

                                 <div>
                                     <label class="block text-sm font-semibold text-gray-700 mb-1">Cargo</label>
                                     <input type="text"
                                         name="firmas[{{ $i }}][cargo]"
                                         value="{{ old("firmas.$i.cargo", $cargo) }}"
                                         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                     @error("firmas.$i.cargo")
                                     <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                     @enderror
                                 </div>
                             </div>

                             {{-- Firma: preview dentro del mismo input visual --}}
                             <div class="mt-4 space-y-2">
                                 <input type="hidden"
                                     name="firmas[{{ $i }}][firma]"
                                     value="{{ old("firmas.$i.firma", $firmaUrl) }}">

                                 <input
                                     type="file"
                                     name="firmas[{{ $i }}][firma_file]"
                                     accept="image/*"
                                     class="firma-file-input hidden">

                                 <div
                                     class="firma-dropzone cursor-pointer border border-dashed border-gray-300 rounded-lg bg-white overflow-hidden
                                               hover:border-indigo-400 transition">
                                     <div class="flex items-center justify-between gap-3 px-4 py-3 border-b bg-gray-50">
                                         <div class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                             <i class="fas fa-signature text-gray-500"></i>
                                             <span class="firma-label">
                                                 {{ $firmaUrl ? 'Cambiar firma' : 'Subir firma' }}
                                             </span>
                                         </div>

                                         <div class="flex items-center gap-2">
                                             <span class="firma-file-name text-xs text-gray-500 truncate max-w-[220px]">
                                                 {{ $firmaUrl ? 'Firma actual cargada' : 'Ningún archivo seleccionado' }}
                                             </span>

                                             <button
                                                 type="button"
                                                 class="btnQuitarFirma hidden bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm text-xs"
                                                 title="Quitar selección">
                                                 <i class="fas fa-times"></i>
                                             </button>
                                         </div>
                                     </div>

                                     <div class="w-full h-32 bg-gray-100 flex items-center justify-center">
                                         <img
                                             class="firma-preview max-h-28 object-contain"
                                             src="{{ $firmaUrl ?: '' }}"
                                             alt="Firma preview"
                                             style="{{ $firmaUrl ? '' : 'display:none;' }}">
                                         <div class="firma-empty text-xs text-gray-400 px-6 text-center" style="{{ $firmaUrl ? 'display:none;' : '' }}">
                                             <div class="font-semibold text-gray-500">Sin firma</div>
                                             <div class="text-[11px] mt-1">Haga clic aquí para seleccionar una imagen</div>
                                         </div>
                                     </div>
                                 </div>

                                 @error("firmas.$i.firma_file")
                                 <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                 @enderror
                             </div>
                         </div>
                         @empty
                         <div class="text-sm text-gray-500 bg-white border border-dashed border-gray-300 rounded-lg p-4 text-center">
                             No se han agregado firmantes.
                         </div>
                         @endforelse
                     </div>
                 </div>

                 {{-- ========================= DISEÑO JSON ========================= --}}
                 <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                     <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                         <div>
                             <h3 class="text-sm font-semibold text-gray-800">Diseño (JSON)</h3>
                             <p class="text-xs text-gray-500">Defina positions/elements (text/qr/image), unit:mm</p>
                         </div>
                         <a href="{{ asset('docs/plantilla-certificado-ejemplos.txt') }}"
                             target="_blank"
                             class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-md text-xs font-medium transition"
                             title="Ver ejemplos de JSON">
                             <i class="fas fa-file-alt"></i>
                             <span>Ver ejemplos JSON</span>
                         </a>
                     </div>

                     <textarea
                         name="diseno"
                         id="diseno"
                         rows="10"
                         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         placeholder='{
  "schemaVersion": 1,
  "unit": "mm",
  "elements": []
}'>{{ $disenoValue }}</textarea>

                     @error('diseno')
                     <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                     @enderror
                 </div>
                 {{-- ========================= ACCIONES ========================= --}}
                 <div class="flex justify-end gap-2 pt-2">
                     <a href="{{ route('plantillas-certificados.index') }}"
                         class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md shadow-sm">
                         <i class="fas fa-times"></i> Cancelar
                     </a>

                     <button type="submit" class="btn-primary">
                         <i class="fas fa-save"></i>
                         {{ $plantilla->exists ? 'Actualizar' : 'Guardar' }}
                     </button>
                 </div>
             </form>
         </div>
     </div>

     {{-- ========================= TEMPLATE FIRMANTE ========================= --}}
     <template id="firmaTemplate">
         <div class="firma-item bg-white border border-gray-200 rounded-lg p-4" data-index="__INDEX__">
             <div class="flex items-center justify-between gap-3 mb-3">
                 <div class="text-sm font-semibold text-gray-700">
                     Firmante <span class="firma-num">__NUM__</span>
                 </div>

                 <button type="button"
                     class="btnRemoveFirma bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
                     title="Eliminar firmante">
                     <i class="fas fa-trash-alt"></i>
                 </button>
             </div>

             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre</label>
                     <input type="text"
                         name="firmas[__INDEX__][nombre]"
                         required
                         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                 </div>

                 <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-1">Cargo</label>
                     <input type="text"
                         name="firmas[__INDEX__][cargo]"
                         class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                 </div>
             </div>

             {{-- Firma (preview dentro del mismo input visual) --}}
             <div class="mt-4 space-y-2">
                 <input type="hidden" name="firmas[__INDEX__][firma]" value="">

                 <input
                     type="file"
                     name="firmas[__INDEX__][firma_file]"
                     accept="image/*"
                     class="firma-file-input hidden">

                 <div
                     class="firma-dropzone cursor-pointer border border-dashed border-gray-300 rounded-lg bg-white overflow-hidden
                           hover:border-indigo-400 transition">
                     <div class="flex items-center justify-between gap-3 px-4 py-3 border-b bg-gray-50">
                         <div class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                             <i class="fas fa-signature text-gray-500"></i>
                             <span class="firma-label">Subir firma</span>
                         </div>

                         <div class="flex items-center gap-2">
                             <span class="firma-file-name text-xs text-gray-500 truncate max-w-[220px]">
                                 Ningún archivo seleccionado
                             </span>

                             <button
                                 type="button"
                                 class="btnQuitarFirma hidden bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm text-xs"
                                 title="Quitar selección">
                                 <i class="fas fa-times"></i>
                             </button>
                         </div>
                     </div>

                     <div class="w-full h-32 bg-gray-100 flex items-center justify-center">
                         <img class="firma-preview max-h-28 object-contain" src="" alt="Firma preview" style="display:none;">
                         <div class="firma-empty text-xs text-gray-400 px-6 text-center">
                             <div class="font-semibold text-gray-500">Sin firma</div>
                             <div class="text-[11px] mt-1">Haga clic aquí para seleccionar una imagen</div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </template>

     <script>
         // ========================= FIRMAS =========================
         const firmasContainer = document.getElementById('firmasContainer');
         const btnAddFirma = document.getElementById('btnAddFirma');
         const firmaTemplate = document.getElementById('firmaTemplate');

         // Guardamos objectURL por item (para liberar memoria)
         const firmaObjectUrls = new WeakMap();

         function setFirmaUI(item, {
             src,
             fileName,
             showRemove,
             currentUrl
         }) {
             const previewImg = item.querySelector('.firma-preview');
             const emptyText = item.querySelector('.firma-empty');
             const fileNameEl = item.querySelector('.firma-file-name');
             const labelEl = item.querySelector('.firma-label');
             const btnQuitar = item.querySelector('.btnQuitarFirma');

             if (!src) {
                 previewImg.style.display = 'none';
                 previewImg.src = '';
                 emptyText.style.display = '';
             } else {
                 previewImg.src = src;
                 previewImg.style.display = '';
                 emptyText.style.display = 'none';
             }

             fileNameEl.textContent = fileName || 'Ningún archivo seleccionado';
             btnQuitar.classList.toggle('hidden', !showRemove);

             // Label según estado: si hay src o firma actual
             labelEl.textContent = (src || currentUrl) ? 'Cambiar firma' : 'Subir firma';
         }

         function bindFirmaItemEvents(item) {
             // Eliminar firmante
             const btnRemoveFirmante = item.querySelector('.btnRemoveFirma');
             btnRemoveFirmante.addEventListener('click', () => {
                 const oldUrl = firmaObjectUrls.get(item);
                 if (oldUrl) URL.revokeObjectURL(oldUrl);
                 item.remove();
                 reindexFirmas();
             });

             const dropzone = item.querySelector('.firma-dropzone');
             const fileInput = item.querySelector('.firma-file-input');
             const btnQuitar = item.querySelector('.btnQuitarFirma');

             const hiddenUrlInput = item.querySelector('input[name$="[firma]"]');
             const currentUrl = hiddenUrlInput?.value || '';

             // Click abre selector
             dropzone.addEventListener('click', () => fileInput.click());

             // Selección archivo
             fileInput.addEventListener('change', (e) => {
                 const file = e.target.files && e.target.files[0];

                 // limpiar objectURL anterior
                 const oldUrl = firmaObjectUrls.get(item);
                 if (oldUrl) {
                     URL.revokeObjectURL(oldUrl);
                     firmaObjectUrls.delete(item);
                 }

                 // canceló selección => restaurar
                 if (!file) {
                     setFirmaUI(item, {
                         src: currentUrl || '',
                         fileName: currentUrl ? 'Firma actual cargada' : 'Ningún archivo seleccionado',
                         showRemove: false,
                         currentUrl
                     });
                     return;
                 }

                 const newUrl = URL.createObjectURL(file);
                 firmaObjectUrls.set(item, newUrl);

                 setFirmaUI(item, {
                     src: newUrl,
                     fileName: file.name,
                     showRemove: true,
                     currentUrl
                 });
             });

             // Quitar selección => vuelve a firma actual
             btnQuitar.addEventListener('click', (e) => {
                 e.stopPropagation();
                 fileInput.value = '';

                 const oldUrl = firmaObjectUrls.get(item);
                 if (oldUrl) {
                     URL.revokeObjectURL(oldUrl);
                     firmaObjectUrls.delete(item);
                 }

                 setFirmaUI(item, {
                     src: currentUrl || '',
                     fileName: currentUrl ? 'Firma actual cargada' : 'Ningún archivo seleccionado',
                     showRemove: false,
                     currentUrl
                 });
             });

             // Estado inicial
             setFirmaUI(item, {
                 src: currentUrl || '',
                 fileName: currentUrl ? 'Firma actual cargada' : 'Ningún archivo seleccionado',
                 showRemove: false,
                 currentUrl
             });
         }

         function reindexFirmas() {
             const items = firmasContainer.querySelectorAll('.firma-item');

             items.forEach((item, index) => {
                 item.dataset.index = index;

                 // Firmante N
                 const num = item.querySelector('.firma-num');
                 if (num) num.textContent = index + 1;

                 // Reindex names: firmas[old][campo] -> firmas[index][campo]
                 item.querySelectorAll('input, select, textarea').forEach(el => {
                     if (!el.name) return;
                     el.name = el.name.replace(/firmas\[\d+\]/g, `firmas[${index}]`);
                 });
             });
         }

         // Inicial
         document.querySelectorAll('.firma-item').forEach(bindFirmaItemEvents);

         btnAddFirma.addEventListener('click', () => {
             const currentCount = firmasContainer.querySelectorAll('.firma-item').length;

             const html = firmaTemplate.innerHTML
                 .replaceAll('__INDEX__', currentCount)
                 .replaceAll('__NUM__', currentCount + 1);

             const wrapper = document.createElement('div');
             wrapper.innerHTML = html.trim();

             const newItem = wrapper.firstElementChild;
             firmasContainer.appendChild(newItem);

             bindFirmaItemEvents(newItem);
             reindexFirmas();
         });

         // ========================= FONDO =========================
         const fondoInput = document.getElementById('imagen_fondo_file');
         const fondoDropzone = document.getElementById('fondoDropzone');
         const fondoPreview = document.getElementById('fondoPreview');
         const fondoEmpty = document.getElementById('fondoEmpty');
         const fondoActualUrl = document.getElementById('fondoActualUrl')?.value || "";

         const fondoLabel = document.getElementById('fondoLabel');
         const fondoFileName = document.getElementById('fondoFileName');
         const btnQuitarFondo = document.getElementById('btnQuitarFondo');

         let fondoObjectUrl = null;

         function setFondoUI({
             src,
             fileName,
             showRemove
         }) {
             if (!src) {
                 fondoPreview.style.display = 'none';
                 fondoPreview.src = '';
                 fondoEmpty.style.display = '';
             } else {
                 fondoPreview.src = src;
                 fondoPreview.style.display = '';
                 fondoEmpty.style.display = 'none';
             }

             fondoFileName.textContent = fileName || 'Ningún archivo seleccionado';
             btnQuitarFondo.classList.toggle('hidden', !showRemove);

             fondoLabel.textContent = (src || fondoActualUrl) ? 'Cambiar imagen' : 'Subir imagen';
         }

         // Click en el campo abre selector
         fondoDropzone.addEventListener('click', () => fondoInput.click());

         // Cambio de archivo
         fondoInput.addEventListener('change', (e) => {
             const file = e.target.files && e.target.files[0];

             if (fondoObjectUrl) {
                 URL.revokeObjectURL(fondoObjectUrl);
                 fondoObjectUrl = null;
             }

             if (!file) {
                 if (fondoActualUrl) {
                     setFondoUI({
                         src: fondoActualUrl,
                         fileName: 'Fondo actual cargado',
                         showRemove: false
                     });
                 } else {
                     setFondoUI({
                         src: '',
                         fileName: 'Ningún archivo seleccionado',
                         showRemove: false
                     });
                 }
                 return;
             }

             fondoObjectUrl = URL.createObjectURL(file);
             setFondoUI({
                 src: fondoObjectUrl,
                 fileName: file.name,
                 showRemove: true
             });
         });

         // Quitar selección fondo
         btnQuitarFondo.addEventListener('click', (e) => {
             e.stopPropagation();
             fondoInput.value = '';

             if (fondoObjectUrl) {
                 URL.revokeObjectURL(fondoObjectUrl);
                 fondoObjectUrl = null;
             }

             if (fondoActualUrl) {
                 setFondoUI({
                     src: fondoActualUrl,
                     fileName: 'Fondo actual cargado',
                     showRemove: false
                 });
             } else {
                 setFondoUI({
                     src: '',
                     fileName: 'Ningún archivo seleccionado',
                     showRemove: false
                 });
             }
         });

         if (fondoActualUrl) {
             setFondoUI({
                 src: fondoActualUrl,
                 fileName: 'Fondo actual cargado',
                 showRemove: false
             });
         } else {
             setFondoUI({
                 src: '',
                 fileName: 'Ningún archivo seleccionado',
                 showRemove: false
             });
         }

         // ========================= VALIDACIÓN JSON =========================
         const jsonFields = ['descripcion_desmp', 'descripcion_part', 'diseno'];

         function validateJson(value) {
             if (!value || value.trim() === '') {
                 return { valid: true, parsed: null };
             }
             try {
                 const parsed = JSON.parse(value);
                 // Debe ser un objeto o array, no un primitivo
                 if (typeof parsed !== 'object' || parsed === null) {
                     return { valid: false, error: 'Debe ser un objeto JSON {} o un array []' };
                 }
                 return { valid: true, parsed };
             } catch (e) {
                 return { valid: false, error: e.message };
             }
         }

         function showJsonError(field, message) {
             const container = field.parentElement;
             let errorEl = container.querySelector('.json-error');

             if (!errorEl) {
                 errorEl = document.createElement('p');
                 errorEl.className = 'json-error text-red-600 text-xs mt-1';
                 container.appendChild(errorEl);
             }

             errorEl.textContent = 'JSON inválido: ' + message;
             field.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
             field.classList.remove('border-gray-300', 'focus:ring-indigo-500', 'focus:border-indigo-500');
         }

         function clearJsonError(field) {
             const container = field.parentElement;
             const errorEl = container.querySelector('.json-error');
             if (errorEl) errorEl.remove();

             field.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
             field.classList.add('border-gray-300', 'focus:ring-indigo-500', 'focus:border-indigo-500');
         }

         function validateJsonField(field) {
             const result = validateJson(field.value);
             if (!result.valid) {
                 showJsonError(field, result.error);
                 return false;
             }
             clearJsonError(field);
             return true;
         }

         // Validar en tiempo real (cada cambio)
         jsonFields.forEach(fieldId => {
             const field = document.getElementById(fieldId);
             if (!field) return;

             field.addEventListener('input', () => validateJsonField(field));
             field.addEventListener('blur', () => validateJsonField(field));
         });

         // Validar al enviar formulario
         const form = document.querySelector('form');
         form.addEventListener('submit', (e) => {
             let hasError = false;

             jsonFields.forEach(fieldId => {
                 const field = document.getElementById(fieldId);
                 if (!field) return;

                 if (!validateJsonField(field)) {
                     hasError = true;
                 }
             });

             if (hasError) {
                 e.preventDefault();
                 const firstError = document.querySelector('.json-error');
                 if (firstError) {
                     firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 }
             }
         });
     </script>
 </x-app-layout>