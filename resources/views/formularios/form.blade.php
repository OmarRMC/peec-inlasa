@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Código -->
<div class="mb-4">
    <label for="codigo" class="label">Código</label>
    <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $formulario->codigo ?? '') }}"
        class="input-standard @error('codigo') border-red-500 @enderror" maxlength="25" required>
    @error('codigo')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
<div class="mb-4">
    <label for="version" class="label">Versión</label>
    <input type="number" name="version" id="version" value="{{ old('version', $formulario->version ?? '') }}"
        class="input-standard @error('version') border-red-500 @enderror" min="1" maxlength="10">
    @error('version')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
<!-- Proceso -->
<div class="mb-4">
    <label for="proceso" class="label">Proceso</label>
    <input type="text" name="proceso" id="proceso" value="{{ old('proceso', $formulario->proceso ?? '') }}"
        class="input-standard @error('proceso') border-red-500 @enderror" maxlength="50" required>
    @error('proceso')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Título -->
<div class="mb-4">
    <label for="titulo" class="label">Título</label>
    <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $formulario->titulo ?? '') }}"
        class="input-standard @error('titulo') border-red-500 @enderror" required>
    @error('titulo')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Fecha -->
<div class="mb-4">
    <label for="fec_formulario" class="label">Fecha del Formulario</label>
    <input type="date" name="fec_formulario" id="fec_formulario"
        value="{{ old('fec_formulario', \Carbon\Carbon::createFromFormat('d/m/Y', $formulario->fec_formulario)->format('Y-m-d')) }}"
        class="input-standard @error('fec_formulario') border-red-500 @enderror">
    @error('fec_formulario')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status" class="label">Estado</label>
    <select name="status" id="status" class="input-standard @error('status') border-red-500 @enderror" required>
        <option value="1" {{ old('status', $formulario->status ?? '') == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('status', $formulario->status ?? '') === 0 ? 'selected' : '' }}>Inactivo</option>
    </select>
    @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
