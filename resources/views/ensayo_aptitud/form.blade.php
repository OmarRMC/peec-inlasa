@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<!-- Paquete -->
<div class="mb-4">
    <label for="id_paquete" class="label">Paquete</label>
    <select name="id_paquete" id="id_paquete" class="input-standard @error('id_paquete') border-red-500 @enderror"
        required>
        <option value="">Seleccione un paquete</option>
        @foreach ($paquetes as $paquete)
            <option value="{{ $paquete->id }}"
                {{ old('id_paquete', $ensayo_aptitud->id_paquete ?? '') == $paquete->id ? 'selected' : '' }}>
                {{ $paquete->descripcion }}
            </option>
        @endforeach
    </select>
    @error('id_paquete')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Descripción -->
<div class="mb-4">
    <label for="descripcion" class="label">Descripción</label>
    <input type="text" name="descripcion" id="descripcion"
        value="{{ old('descripcion', $ensayo_aptitud->descripcion ?? '') }}"
        pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s]{3,100}$" title="Solo letras, números y espacios. Entre 3 y 100 caracteres."
        required maxlength="100" class="input-standard @error('descripcion') border-red-500 @enderror">
    @error('descripcion')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Estado -->
<div class="mb-6">
    <label for="status" class="label">Estado</label>
    <select name="status" id="status" class="input-standard @error('status') border-red-500 @enderror" required>
        <option value="1" {{ old('status', $ensayo_aptitud->status ?? '') == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('status', $ensayo_aptitud->status ?? '') === 0 ? 'selected' : '' }}>Inactivo
        </option>
    </select>
    @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
