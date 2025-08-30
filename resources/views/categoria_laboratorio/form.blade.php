@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Descripción -->
<div class="mb-4">
    <label for="descripcion" class="label">Descripción</label>
    <input type="text" name="descripcion" id="descripcion"
        value="{{ old('descripcion', $categoria_laboratorio->descripcion ?? '') }}" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s _.-()]{3,50}$"
        title="Solo letras, números y espacios. Entre 3 y 50 caracteres." required maxlength="50"
        class="input-standard @error('descripcion') border-red-500 @enderror">
    @error('descripcion')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status" class="label">Estado</label>
    <select name="status" id="status" class="input-standard @error('status') border-red-500 @enderror" required>
        <option value="1" {{ old('status', $categoria_laboratorio->status ?? '') == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('status', $categoria_laboratorio->status ?? '') === 0 ? 'selected' : '' }}>Inactivo</option>
    </select>
    @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
