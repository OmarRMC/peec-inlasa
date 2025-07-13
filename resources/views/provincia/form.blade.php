@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Departamento -->
<div class="mb-4">
    <label for="id_dep" class="label">Departamento</label>
    <select name="id_dep" id="id_dep" class="input-standard @error('id_dep') border-red-500 @enderror" required>
        <option value="">Seleccione un departamento</option>
        @foreach ($departamentos as $dep)
            <option value="{{ $dep->id }}"
                {{ old('id_dep', $provincia->id_dep ?? '') == $dep->id ? 'selected' : '' }}>
                {{ $dep->nombre_dep }}
            </option>
        @endforeach
    </select>
    @error('id_dep')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Nombre -->
<div class="mb-4">
    <label for="nombre_prov" class="label">Nombre de la Provincia</label>
    <input type="text" name="nombre_prov" id="nombre_prov"
        value="{{ old('nombre_prov', $provincia->nombre_prov ?? '') }}" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,50}$"
        title="Solo letras y espacios. Mínimo 3 y máximo 50 caracteres." maxlength="50" required
        class="input-standard @error('nombre_prov') border-red-500 @enderror">
    @error('nombre_prov')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Código -->
<div class="mb-4">
    <label for="cod_prov" class="label">Código</label>
    <input type="number" name="cod_prov" id="cod_prov" value="{{ old('cod_prov', $provincia->cod_prov ?? '') }}"
        required class="input-standard @error('cod_prov') border-red-500 @enderror">
    @error('cod_prov')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status_prov" class="label">Estado</label>
    <select name="status_prov" id="status_prov" class="input-standard @error('status_prov') border-red-500 @enderror"
        required>
        <option value="1" {{ old('status_prov', $provincia->status_prov ?? '') == 1 ? 'selected' : '' }}>Activo
        </option>
        <option value="0" {{ old('status_prov', $provincia->status_prov ?? '') === 0 ? 'selected' : '' }}>Inactivo
        </option>
    </select>
    @error('status_prov')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
