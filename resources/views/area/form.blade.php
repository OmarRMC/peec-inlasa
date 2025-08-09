@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Programa -->
<div class="mb-4">
    <label for="id_programa" class="label">Programa</label>
    <select name="id_programa" id="id_programa" class="input-standard @error('id_programa') border-red-500 @enderror">
        <option value="">Selecciona un programa</option>
        @foreach ($programas as $programa)
            <option value="{{ $programa->id }}"
                {{ old('id_programa', $area->id_programa ?? '') == $programa->id ? 'selected' : '' }}>
                {{ $programa->descripcion }}
            </option>
        @endforeach
    </select>
    @error('id_programa')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
<!-- Descripción -->
<div class="mb-4">
    <label for="descripcion" class="label">Descripción</label>
    <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion', $area->descripcion ?? '') }}"
        class="input-standard @error('descripcion') border-red-500 @enderror" pattern="^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{3,100}$"
        title="Solo letras y espacios. Mínimo 3 caracteres, máximo 100." required>
    @error('descripcion')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Máx. paquetes -->
<div class="mb-4">
    <label for="max_paquetes_inscribir" class="label">Máx. Paquetes a Inscribir</label>
    <input type="number" name="max_paquetes_inscribir" id="max_paquetes_inscribir"
        value="{{ old('max_paquetes_inscribir', $area->max_paquetes_inscribir ?? '') }}"
        class="input-standard @error('max_paquetes_inscribir') border-red-500 @enderror" min="1"
        pattern="^[1-9][0-9]*$" title="Debe ser un número entero mayor a cero" required>
    @error('max_paquetes_inscribir')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status" class="label">Estado</label>
    <select name="status" id="status" class="input-standard @error('status') border-red-500 @enderror">
        <option value="1" {{ old('status', $area->status ?? '') == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('status', $area->status ?? '') === 0 ? 'selected' : '' }}>Inactivo</option>
    </select>
    @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
