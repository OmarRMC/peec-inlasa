@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Nombre -->
<div class="mb-4">
    <label for="descripcion_nivel" class="label">Descripcion del Nivel</label>
    <input type="text" name="descripcion_nivel" id="descripcion_nivel"
        value="{{ old('descripcion_nivel', $nivelLaboratorio->descripcion_nivel ?? '') }}"
        pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s ]{3,50}$" title="Solo letras, números y espacios. Entre 3 y 50 caracteres."
        required maxlength="50" class="input-standard @error('descripcion_nivel') border-red-500 @enderror">
    @error('descripcion_nivel')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status_nivel" class="label">Estado</label>
    <select name="status_nivel" id="status_nivel" class="input-standard @error('status_nivel') border-red-500 @enderror"
        required>
        <option value="1" {{ old('status_nivel', $nivelLaboratorio->status_nivel ?? '') == 1 ? 'selected' : '' }}>
            Activo
        </option>
        <option value="0" {{ old('status_nivel', $nivelLaboratorio->status_nivel ?? '') === 0 ? 'selected' : '' }}>
            Inactivo
        </option>
    </select>
    @error('status_nivel')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
