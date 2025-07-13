@php
    $paquete ??= new \App\Models\Paquete();
@endphp


<!-- Área -->
<div>
    <label for="id_area" class="block text-sm font-semibold text-gray-700 mb-1">Área</label>
    <select name="id_area" id="id_area"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        required>
        <option value="">Seleccione un área</option>
        @foreach ($areas as $area)
            <option value="{{ $area->id }}" @selected(old('id_area', $paquete->id_area) == $area->id)>
                {{ $area->descripcion }}
            </option>
        @endforeach
    </select>
    @error('id_area')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Descripción -->
<div>
    <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
    <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion', $paquete->descripcion) }}"
        pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s]{3,100}$"
        title="Solo letras, números y espacios. Mínimo 3 y máximo 100 caracteres." maxlength="100" required
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    @error('descripcion')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Costo del Paquete -->
<div>
    <label for="costo_paquete" class="block text-sm font-semibold text-gray-700 mb-1">Costo del Paquete (Bs)</label>
    <input type="number" name="costo_paquete" id="costo_paquete"
        value="{{ old('costo_paquete', $paquete->costo_paquete) }}" min="0" max="10000" required
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    @error('costo_paquete')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div>
    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
    <select name="status" id="status"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        required>
        <option value="1" @selected(old('status', $paquete->status) == 1)>Activo</option>
        <option value="0" @selected(old('status', $paquete->status) == 0)>Inactivo</option>
    </select>
    @error('status')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
