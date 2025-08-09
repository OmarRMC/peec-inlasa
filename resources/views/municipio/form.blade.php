@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Provincia -->
<div class="mb-4">
    <label for="id_prov" class="label">Provincia</label>
    <select name="id_prov" id="id_prov" class="input-standard @error('id_prov') border-red-500 @enderror" required>
        <option value="">Seleccione una provincia</option>
        @foreach ($provincias as $prov)
            <option value="{{ $prov->id }}"
                {{ old('id_prov', $municipio->id_prov ?? '') == $prov->id ? 'selected' : '' }}>
                {{ $prov->nombre_prov }}
            </option>
        @endforeach
    </select>
    @error('id_prov')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Nombre -->
<div class="mb-4">
    <label for="nombre_municipio" class="label">Nombre del Municipio</label>
    <input type="text" name="nombre_municipio" id="nombre_municipio"
        value="{{ old('nombre_municipio', $municipio->nombre_municipio ?? '') }}"
        pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,70}$" title="Solo letras y espacios. Mínimo 3 y máximo 70 caracteres."
        maxlength="70" required class="input-standard @error('nombre_municipio') border-red-500 @enderror">
    @error('nombre_municipio')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Código -->
<div class="mb-4">
    <label for="cod_municipio" class="label">Código</label>
    <input type="number" name="cod_municipio" id="cod_municipio"
        value="{{ old('cod_municipio', $municipio->cod_municipio ?? '') }}" required
        class="input-standard @error('cod_municipio') border-red-500 @enderror">
    @error('cod_municipio')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status_municipio" class="label">Estado</label>
    <select name="status_municipio" id="status_municipio"
        class="input-standard @error('status_municipio') border-red-500 @enderror" required>
        <option value="1" {{ old('status_municipio', $municipio->status_municipio ?? '') == 1 ? 'selected' : '' }}>
            Activo</option>
        <option value="0"
            {{ old('status_municipio', $municipio->status_municipio ?? '') === 0 ? 'selected' : '' }}>Inactivo</option>
    </select>
    @error('status_municipio')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
