@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Descripción -->
<div class="mb-4">
    <label for="descripcion" class="label">Descripción</label>
    <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion', $programa->descripcion ?? '') }}"
        class="input-standard @error('descripcion') border-red-500 @enderror">
    @error('descripcion')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status" class="label">Estado</label>
    <select name="status" id="status" class="input-standard @error('status') border-red-500 @enderror">
        <option value="1" {{ old('status', $programa->status ?? '') == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('status', $programa->status ?? '') === 0 ? 'selected' : '' }}>Inactivo</option>
    </select>
    @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
