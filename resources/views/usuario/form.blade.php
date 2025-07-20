@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif
@php
    $ensayosSeleccionados = $ensayosSeleccionados ?? []; // array de IDs
    $ensayoMap = $ensayoA->pluck('descripcion', 'id')->toArray(); // [id => descripcion]
@endphp


@php
    $permisosAsignados = old('permisos');
    if (!isset($permisosAsignados)) {
        $permisosAsignados = isset($usuario) ? $usuario->permisos->pluck('id')->toArray() : [];
    }
@endphp

<div class="max-w-3xl mx-auto flex flex-col gap-6">

    {{-- SECCIÓN: Información Personal --}}
    <section>
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-user-circle text-blue-600"></i> Información Personal
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-user text-gray-400"></i> Username
                </label>
                <input type="text" name="username" id="username"
                    value="{{ old('username', $usuario->username ?? '') }}"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror">
                @error('username')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-id-badge text-gray-400"></i> Nombre
                </label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $usuario->nombre ?? '') }}"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('nombre') border-red-500 @enderror">
                @error('nombre')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Apellido Paterno -->
            <div>
                <label for="ap_paterno" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-user-tag text-gray-400"></i> Apellido Paterno
                </label>
                <input type="text" name="ap_paterno" id="ap_paterno"
                    value="{{ old('ap_paterno', $usuario->ap_paterno ?? '') }}"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('ap_paterno') border-red-500 @enderror">
                @error('ap_paterno')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Apellido Materno -->
            <div>
                <label for="ap_materno" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-user-tag text-gray-400"></i> Apellido Materno
                </label>
                <input type="text" name="ap_materno" id="ap_materno"
                    value="{{ old('ap_materno', $usuario->ap_materno ?? '') }}"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('ap_materno') border-red-500 @enderror">
                @error('ap_materno')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- CI -->
            <div>
                <label for="ci" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-id-card text-gray-400"></i> CI
                </label>
                <input type="text" name="ci" id="ci" value="{{ old('ci', $usuario->ci ?? '') }}"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('ci') border-red-500 @enderror">
                @error('ci')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Teléfono -->
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-phone text-gray-400"></i> Teléfono
                </label>
                <input type="text" name="telefono" id="telefono"
                    value="{{ old('telefono', $usuario->telefono ?? '') }}"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('telefono') border-red-500 @enderror">
                @error('telefono')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Email -->
            <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-envelope text-gray-400"></i> Email
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $usuario->email ?? '') }}"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-key text-gray-400"></i> Contraseña
                </label>
                <input type="password" name="password" id="password"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmation"
                    class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-key text-gray-400"></i> Confirmar Contraseña
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </section>

    {{-- SECCIÓN: Cargo y Estado --}}
    <section>
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-briefcase text-blue-600"></i> Cargo y Estado
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cargo -->
            <div>
                <label for="id_cargo" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-clipboard-list text-gray-400"></i> Cargo
                </label>
                <select name="id_cargo" id="id_cargo"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value=""> Selecciona un cargo </option>
                    @foreach ($cargos as $cargo)
                        <option value="{{ $cargo->id }}"
                            {{ old('id_cargo', $usuario->id_cargo ?? '') == $cargo->id ? 'selected' : '' }}>
                            {{ $cargo->nombre_cargo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-toggle-on text-gray-400"></i> Estado
                </label>
                <select name="status" id="status"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="1" {{ old('status', $usuario->status ?? '') == 1 ? 'selected' : '' }}>Activo
                    </option>
                    <option value="0" {{ old('status', $usuario->status ?? '') === 0 ? 'selected' : '' }}>
                        Inactivo</option>
                </select>
            </div>
            <div class="">
                <input type="hidden" name="is_responsable" value="0">
                <input type="checkbox" name="is_responsable" id="is_responsable" value="1"
                    class="text-blue-600 focus:ring-blue-300 focus:ring-1 rounded 
                    @error('is_responsable') border-red-400 @enderror">
                <label for="is_responsable" class="text-sm text-gray-700">
                    @if (empty($ensayosSeleccionados))
                        Responsable
                    @else 
                        Agregar REA
                    @endif
                </label>
            </div>

            <div class="hidden" id="ensayoSelect">
                <label for="ensayo_ap" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                    <i class="fas fa-clipboard-list text-gray-400"></i> Ensayo de aptitud
                </label>
                <select id="ensayo_ap"
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecciona ensayo de aptitud</option>
                    @foreach ($ensayoA as $ensayo)
                        <option value="{{ $ensayo->id }}">{{ $ensayo->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                @if (!empty($ensayosSeleccionados))
                    <span>
                        Responsable de los EA: 
                    </span>
                @endif
                <div id="ensayoChipsContainer" class="flex flex-wrap gap-2 mt-3">
                    @foreach ($ensayosSeleccionados as $id)
                        <div class="flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm gap-2 ensayo-chip"
                            data-id="{{ $id }}">
                            <span>
                                {{ $ensayoMap[$id] ?? 'Sin descripción' }}</span>
                            <input type="hidden" name="ensayos_ap[]" value="{{ $id }}">
                            <button type="button"
                                class="text-blue-600 hover:text-red-500 font-bold eliminar-chip">&times;</button>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    {{-- SECCIÓN: Permisos --}}
    <section>
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-shield-alt text-blue-600"></i> Permisos
        </h2>

        <div
            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-48 overflow-y-auto border border-gray-300 rounded p-3 bg-gray-50">
            @foreach ($permisos as $permiso)
                <label
                    class="flex items-center gap-2 text-sm cursor-pointer hover:bg-blue-100 rounded px-2 py-1 transition">
                    <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}" class="accent-blue-600"
                        {{ in_array($permiso->id, $permisosAsignados) ? 'checked' : '' }}>
                    <span>{{ $permiso->nombre_permiso }}</span>
                </label>
            @endforeach
        </div>
    </section>
    <script>
        const isResponsableCheckbox = document.getElementById('is_responsable');
        const ensayoSelectWrapper = document.getElementById('ensayoSelect');
        const ensayoSelect = document.getElementById('ensayo_ap');
        const chipsContainer = document.getElementById('ensayoChipsContainer');

        let selectedEnsayos = [...chipsContainer.querySelectorAll('input[name="ensayos_ap[]"]')].map(input => input.value);

        if (isResponsableCheckbox.checked) {
            ensayoSelectWrapper.classList.remove('hidden');
            ensayoSelectWrapper.classList.add('block');
        }

        isResponsableCheckbox.addEventListener('change', () => {
            if (isResponsableCheckbox.checked) {
                ensayoSelectWrapper.classList.remove('hidden');
                ensayoSelectWrapper.classList.add('block');
            } else {
                ensayoSelectWrapper.classList.add('hidden');
                ensayoSelectWrapper.classList.remove('block');
                selectedEnsayos = [];
                chipsContainer.innerHTML = '';
            }
        });

        ensayoSelect.addEventListener('change', () => {
            const ensayoId = ensayoSelect.value;
            const ensayoText = ensayoSelect.options[ensayoSelect.selectedIndex].text;

            if (!ensayoId || selectedEnsayos.includes(ensayoId)) return;

            selectedEnsayos.push(ensayoId);

            const chip = document.createElement('div');
            chip.className =
                'flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm gap-2 ensayo-chip';
            chip.setAttribute('data-id', ensayoId);
            chip.innerHTML = `
            <span>${ensayoText}</span>
            <input type="hidden" name="ensayos_ap[]" value="${ensayoId}">
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

</div>
