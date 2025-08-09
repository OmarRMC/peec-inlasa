@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Nombre del Permiso -->
<div class="mb-4">
    <label for="nombre_permiso" class="block text-sm font-medium text-gray-700">Nombre del Permiso</label>
    <input type="text" name="nombre_permiso" id="nombre_permiso"
        value="{{ old('nombre_permiso', $permiso->nombre_permiso ?? '') }}"
        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('nombre_permiso') border-red-500 @enderror">
    @error('nombre_permiso')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Descripción -->
<div class="mb-4">
    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
    <textarea name="descripcion" id="descripcion" rows="3"
        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $permiso->descripcion ?? '') }}</textarea>
    @error('descripcion')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
    <select name="status" id="status"
        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
        <option value="1" {{ old('status', $permiso->status ?? '') == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('status', $permiso->status ?? '') === 0 ? 'selected' : '' }}>Inactivo</option>
    </select>
    @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
