@php
    $paquete ??= new \App\Models\Paquete();
    $tiposLaboratoriosSelecionados = $tiposLaboratoriosSelecionados ?? [];
@endphp


<!-- Área -->
<div>
    <label for="id_area" class="block text-sm font-semibold text-gray-700 mb-1">Área</label>
    <select name="id_area" id="id_area"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        required>
        <option value="">Seleccione un área</option>
        @foreach ($areas as $area)
            <option value="{{ $area->id }}" @selected(old('id_area', $paquete->id_area) == $area->id)>
                {{ $area->descripcion }}
            </option>
        @endforeach
    </select>
    @error('id_area')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Descripción -->
<div>
    <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
    <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion', $paquete->descripcion) }}"
        pattern="^(?!\d+$)(?!\s+$).{3,100}$"
        title="Debe tener entre 3 y 100 caracteres y no puede ser solo números ni solo espacios." maxlength="100"
        required
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    @error('descripcion')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Max participantes -->
<div>
    <label for="max_participantes" class="block text-sm font-semibold text-gray-700 mb-1">Numero de participantes
        (Max)</label>
    <input type="number" name="max_participantes" id="max_participantes"
        value="{{ old('max_participantes', $paquete->max_participantes) }}" pattern="^[1-9][0-9]*$"
        title="Debe ser un número entero mayor a cero" required
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    @error('max_participantes')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="costo_paquete" class="block text-sm font-semibold text-gray-700 mb-1">Costo del Paquete (Bs)</label>
    <input type="number" name="costo_paquete" id="costo_paquete"
        value="{{ old('costo_paquete', $paquete->costo_paquete) }}" min="0" max="15000" step="0.01"
        required
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    @error('costo_paquete')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>


<!-- Tipos de laboratorio -->
<div class="mt-4">
    <label for="tipo_laboratorio_ids" class="block text-sm font-semibold text-gray-700 mb-1">Tipos de
        Laboratorio</label>
    <select id="tipo_lab_app"
        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
        <option value="">Selecciona los tipos de laboratorio</option>
        @foreach ($tiposLaboratorios as $tipo)
            <option value="{{ $tipo->id }}">{{ $tipo->descripcion }}</option>
        @endforeach
    </select>

    @error('tipo_laboratorio_ids')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
    <div id="ensayoChipsContainer" class="flex flex-wrap gap-2 mt-3">
        @foreach ($tiposLaboratoriosSelecionados as $tiposLab)
            <div class="flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm gap-2 ensayo-chip"
                data-id="{{ $tiposLab->id }}">
                <span>
                    {{ $tiposLab->descripcion ?? 'Sin descripción' }}</span>
                <input type="hidden" name="tipo_laboratorio_ids[]" value="{{ $tiposLab->id }}">
                <button type="button" class="text-blue-600 hover:text-red-500 font-bold eliminar-chip">&times;</button>
            </div>
        @endforeach
    </div>
</div>

<!-- Estado -->
<div>
    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
    <select name="status" id="status"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        required>
        <option value="1" @selected(old('status', $paquete->status) == 1)>Activo</option>
        <option value="0" @selected(old('status', $paquete->status) == 0)>Inactivo</option>
    </select>
    @error('status')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<script>
    const isResponsableCheckbox = document.getElementById('is_responsable');
    const ensayoSelectWrapper = document.getElementById('ensayoSelect');
    const ensayoSelect = document.getElementById('tipo_lab_app');
    const chipsContainer = document.getElementById('ensayoChipsContainer');

    let selectedEnsayos = [...chipsContainer.querySelectorAll('input[name="tipo_laboratorio_ids[]"]')].map(input =>
        input.value);

    ensayoSelect.addEventListener('change', () => {

        const ensayoId = ensayoSelect.value;
        console.log(ensayoId);
        const ensayoText = ensayoSelect.options[ensayoSelect.selectedIndex].text;

        if (!ensayoId || selectedEnsayos.includes(ensayoId)) return;

        selectedEnsayos.push(ensayoId);

        const chip = document.createElement('div');
        chip.className =
            'flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm gap-2 ensayo-chip';
        chip.setAttribute('data-id', ensayoId);
        chip.innerHTML = `
            <span>${ensayoText}</span>
            <input type="hidden" name="tipo_laboratorio_ids[]" value="${ensayoId}">
            <button type="button" class="text-blue-600 hover:text-red-500 font-bold eliminar-chip">&times;</button>
        `;

        chip.querySelector('.eliminar-chip').addEventListener('click', () => {
            chip.remove();
            selectedEnsayos = selectedEnsayos.filter(id => id !== ensayoId);
        });

        chipsContainer.appendChild(chip);
        ensayoSelect.value = '';
    });

    document.querySelectorAll('.eliminar-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            const chip = this.closest('.ensayo-chip');
            const ensayoId = chip.dataset.id;
            chip.remove();
            selectedEnsayos = selectedEnsayos.filter(id => id !== ensayoId);
        });
    });
</script>
