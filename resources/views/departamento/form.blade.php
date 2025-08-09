@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- País -->
<div class="mb-4">
    <label for="id_pais" class="label">País</label>
    <select name="id_pais" id="id_pais" class="input-standard @error('id_pais') border-red-500 @enderror" required>
        <option value="">Seleccione un país</option>
        @foreach ($paises as $pais)
            <option value="{{ $pais->id }}"
                {{ old('id_pais', $departamento->id_pais ?? '') == $pais->id ? 'selected' : '' }}>
                {{ $pais->nombre_pais }}
            </option>
        @endforeach
    </select>
    @error('id_pais')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Nombre -->
<div class="mb-4">
    <label for="nombre_dep" class="label">Nombre del Departamento</label>
    <input type="text" name="nombre_dep" id="nombre_dep"
        value="{{ old('nombre_dep', $departamento->nombre_dep ?? '') }}" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,20}$"
        title="Solo letras y espacios. Mínimo 3 y máximo 20 caracteres." maxlength="20" required
        class="input-standard @error('nombre_dep') border-red-500 @enderror">
    @error('nombre_dep')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Sigla -->
<div class="mb-4">
    <label for="sigla_dep" class="label">Sigla</label>
    <input type="text" name="sigla_dep" id="sigla_dep" value="{{ old('sigla_dep', $departamento->sigla_dep ?? '') }}"
        pattern="^[A-Z]{1,5}$" title="Solo letras mayúsculas. Máximo 5 caracteres." maxlength="5" required
        class="input-standard @error('sigla_dep') border-red-500 @enderror">
    @error('sigla_dep')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status_dep" class="label">Estado</label>
    <select name="status_dep" id="status_dep" class="input-standard @error('status_dep') border-red-500 @enderror"
        required>
        <option value="1" {{ old('status_dep', $departamento->status_dep ?? '') == 1 ? 'selected' : '' }}>Activo
        </option>
        <option value="0" {{ old('status_dep', $departamento->status_dep ?? '') === 0 ? 'selected' : '' }}>Inactivo
        </option>
    </select>
    @error('status_dep')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
