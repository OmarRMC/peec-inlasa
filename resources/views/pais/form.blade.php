@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Nombre del País -->
<div class="mb-4">
    <label for="nombre_pais" class="label">Nombre del País</label>
    <input type="text" name="nombre_pais" id="nombre_pais" value="{{ old('nombre_pais', $pais->nombre_pais ?? '') }}"
        pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,15}$" title="Solo letras y espacios. Entre 3 y 15 caracteres." required
        maxlength="15" class="input-standard @error('nombre_pais') border-red-500 @enderror">
    @error('nombre_pais')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Sigla -->
<div class="mb-4">
    <label for="sigla_pais" class="label">Sigla</label>
    <input type="text" name="sigla_pais" id="sigla_pais" value="{{ old('sigla_pais', $pais->sigla_pais ?? '') }}"
        pattern="^[A-Z]{2,5}$" title="Solo letras mayúsculas. Entre 2 y 5 caracteres." required maxlength="5"
        class="input-standard @error('sigla_pais') border-red-500 @enderror">
    @error('sigla_pais')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Código -->
<div class="mb-4">
    <label for="cod_pais" class="label">Código</label>
    <input type="number" name="cod_pais" id="cod_pais" value="{{ old('cod_pais', $pais->cod_pais ?? '') }}" required
        min="1" class="input-standard @error('cod_pais') border-red-500 @enderror">
    @error('cod_pais')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status_pais" class="label">Estado</label>
    <select name="status_pais" id="status_pais" class="input-standard @error('status_pais') border-red-500 @enderror"
        required>
        <option value="1" {{ old('status_pais', $pais->status_pais ?? '') == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('status_pais', $pais->status_pais ?? '') === 0 ? 'selected' : '' }}>Inactivo
        </option>
    </select>
    @error('status_pais')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
