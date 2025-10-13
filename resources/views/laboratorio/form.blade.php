@php use App\Models\Permiso; @endphp
@csrf
@php
    $edit = false;

@endphp
@if (isset($method) && $method === 'PUT')
    @method('PUT')
    @php
        $edit = true;
    @endphp
@endif
@php
    $esSoloLectura = $edit && !Gate::any([Permiso::GESTION_LABORATORIO, Permiso::ADMIN]);
@endphp

{{-- Datos Básicos --}}
<fieldset class="form-fieldset border p-4 mb-8 rounded-md max-w-3xl mx-auto">
    {{-- <fieldset class="border p-6 mb-8 rounded-md max-w-5xl mx-auto shadow-sm"> --}}
    <legend class="flex items-center gap-2 text-lg font-semibold mb-2 text-gray-700">
        <i class="fas fa-flask text-primary"></i> Datos Básicos
    </legend>

    <div class="container-inputs grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Num. Registro Sedes --}}
        <div>
            <label for="numsedes_lab" class="label">Su Numero de Registro Sedes</label>
            <input type="text" name="numsedes_lab" id="numsedes_lab" maxlength="20"
                value="{{ old('numsedes_lab', $laboratorio->numsedes_lab ?? '') }}"
                class="input-standard w-full @error('numsedes_lab') border-red-500 @enderror"
                placeholder="0000">
            @error('numsedes_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if (Gate::any([Permiso::GESTION_LABORATORIO, Permiso::ADMIN]) && false)
            {{-- Código PEEC --}}
            <div>
                <label for="antcod_peec" class="label">Código PEEC</label>
                <input type="text" name="antcod_peec" id="antcod_peec" maxlength="20"
                    value="{{ old('antcod_peec', $laboratorio->antcod_peec ?? '') }}"
                    class="input-standard  uppercase-input w-full @error('antcod_peec') border-red-500 @enderror"
                    placeholder="Si dispone de un código PEEC, ingréselo aquí">
                @error('antcod_peec')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif
        {{-- Sigla Laboratorio --}}
        <div>
            <label for="sigla_lab" class="label">Sigla Laboratorio</label>
            <input type="text" name="sigla_lab" id="sigla_lab" maxlength="20"
                value="{{ old('sigla_lab', $laboratorio->sigla_lab ?? '') }}"
                class="input-standard w-full @error('sigla_lab') border-red-500 @enderror">
            @error('sigla_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- NIT --}}
        <div>
            <label for="nit_lab" class="label required-label">NIT</label>
            <input type="number" name="nit_lab" id="nit_lab"
                value="{{ old('nit_lab', $laboratorio->nit_lab ?? '') }}" min="0" minlength="5"
                class="input-standard w-full @error('nit_lab') border-red-500 @enderror" required>
            @error('nit_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nombre Laboratorio --}}
        <div class="md:col-span-2 lg:col-span-3">
            <label for="nombre_lab" class="label required-label">Nombre Laboratorio</label>
            <input type="text" name="nombre_lab" id="nombre_lab" maxlength="100"
                value="{{ old('nombre_lab', $laboratorio->nombre_lab ?? '') }}"
                class="input-standard w-full @error('nombre_lab') border-red-500 @enderror"
                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ][\s\S]*[^\s]$"
                title="Debe comenzar con una letra, no puede ser solo números ni solo espacios." required>
            @error('nombre_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
</fieldset>

{{-- Información Responsable --}}
<fieldset class="border p-4 mb-8 rounded-md max-w-3xl mx-auto">
    <legend class="flex items-center gap-2 text-lg font-semibold mb-2">
        <i class="fas fa-user-check text-primary"></i> Responsable del Laboratorio
    </legend>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="respo_lab" class="label required-label">Nombre Responsable</label>
            <input type="text" name="respo_lab" id="respo_lab" maxlength="50"
                value="{{ old('respo_lab', $laboratorio->respo_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('respo_lab') border-red-500 @enderror"
                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ][\s\S]*[^\s]$"
                title="Debe comenzar con una letra, no puede ser solo números ni solo espacios." required>
            @error('respo_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="ci_respo_lab" class="label required-label">CI Responsable</label>
            <input type="text" name="ci_respo_lab" id="ci_respo_lab" maxlength="15"
                value="{{ old('ci_respo_lab', $laboratorio->ci_respo_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('ci_respo_lab') border-red-500 @enderror"
                pattern="^[0-9]{5,}[A-Za-z0-9\s\-]*$"
                title="Debe comenzar con un número y no puede estar vacío ni contener solo espacios." required>
        </div>
    </div>
</fieldset>

{{-- Representante Legal --}}
<fieldset class="border p-4 mb-8 rounded-md max-w-3xl mx-auto">
    <legend class="flex items-center gap-2 text-lg font-semibold mb-2">
        <i class="fas fa-user-tie text-primary"></i> Representante Legal
    </legend>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="repreleg_lab" class="label required-label">Nombre Representante Legal</label>
            <input type="text" name="repreleg_lab" id="repreleg_lab" maxlength="50"
                value="{{ old('repreleg_lab', $laboratorio->repreleg_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('repreleg_lab') border-red-500 @enderror"
                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ][\s\S]*[^\s]$"
                title="Debe comenzar con una letra, no puede ser solo números ni solo espacios." required>
            @error('repreleg_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="ci_repreleg_lab" class="label required-label">CI Representante Legal</label>
            <input type="text" name="ci_repreleg_lab" id="ci_repreleg_lab" maxlength="15"
                value="{{ old('ci_repreleg_lab', $laboratorio->ci_repreleg_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('ci_repreleg_lab') border-red-500 @enderror"
                pattern="^[0-9]{5,}[A-Za-z0-9\s\-]*$"
                title="Debe comenzar con un número y no puede estar vacío ni contener solo espacios." required>
            @error('ci_repreleg_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</fieldset>

{{-- Ubicación --}}
<fieldset class="border p-4 mb-8 rounded-md max-w-3xl mx-auto">
    <legend class="flex items-center gap-2 text-lg font-semibold mb-2">
        <i class="fas fa-map-marker-alt text-primary"></i> Datos de referencia
    </legend>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="id_pais" class="label required-label">País</label>
            <select name="id_pais" id="id_pais"
                class="input-standard max-w-md w-full @error('id_pais') border-red-500 @enderror" required
                onchange="cargarDepartamentos()">
                <option value="">Seleccione un país</option>
                @foreach ($paises as $pais)
                    <option value="{{ $pais->id }}"
                        {{ old('id_pais', $laboratorio->id_pais ?? '') == $pais->id ? 'selected' : '' }}>
                        {{ $pais->nombre_pais }}
                    </option>
                @endforeach
            </select>
            @error('id_pais')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="id_dep" class="label required-label">Departamento</label>
            <select name="id_dep" id="id_dep"
                class="input-standard max-w-md w-full @error('id_dep') border-red-500 @enderror" required
                onchange="cargarProvincias()">
                <option value="">Seleccione un departamento</option>
                @foreach ($departamentos as $dep)
                    <option value="{{ $dep->id }}"
                        {{ old('id_dep', $laboratorio->id_dep ?? '') == $dep->id ? 'selected' : '' }}>
                        {{ $dep->nombre_dep }}
                    </option>
                @endforeach
            </select>
            @error('id_dep')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="id_prov" class="label required-label">Provincia</label>
            <select name="id_prov" id="id_prov"
                class="input-standard max-w-md w-full @error('id_prov') border-red-500 @enderror" required
                onchange="cargarMunicipios()">
                <option value="">Seleccione una provincia</option>
                @foreach ($provincias as $prov)
                    <option value="{{ $prov->id }}"
                        {{ old('id_prov', $laboratorio->id_prov ?? '') == $prov->id ? 'selected' : '' }}>
                        {{ $prov->nombre_prov }}
                    </option>
                @endforeach
            </select>
            @error('id_prov')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="id_municipio" class="label required-label">Municipio</label>
            <select name="id_municipio" id="id_municipio"
                class="input-standard max-w-md w-full @error('id_municipio') border-red-500 @enderror" required>
                <option value="">Seleccione un municipio</option>
                @foreach ($municipios as $mun)
                    <option value="{{ $mun->id }}"
                        {{ old('id_municipio', $laboratorio->id_municipio ?? '') == $mun->id ? 'selected' : '' }}>
                        {{ $mun->nombre_municipio }}
                    </option>
                @endforeach
            </select>
            @error('id_municipio')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="zona_lab" class="label required-label">Zona/Barrio</label>
            <input type="text" name="zona_lab" id="zona_lab" maxlength="50"
                value="{{ old('zona_lab', $laboratorio->zona_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('zona_lab') border-red-500 @enderror"
                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ][\s\S]*[^\s]$"
                title="Debe comenzar con una letra, no puede ser solo números ni solo espacios." required>
            @error('zona_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Calle/Avenida --}}
        <div>
            <label for="calle_lab" class="label required-label">Calle/Avenida</label>
            <input type="text" name="calle_lab" id="calle_lab" maxlength="100"
                value="{{ old('calle_lab', $laboratorio->calle ?? '') }}"
                class="input-standard max-w-md w-full @error('calle_lab') border-red-500 @enderror" required>
            @error('calle_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="numero_lab" class="label required-label">Número/Lote</label>
            <input type="text" name="numero_lab" id="numero_lab" maxlength="10"
                value="{{ old('numero_lab', $laboratorio->numero ?? '') }}"
                class="input-standard max-w-md w-full @error('numero_lab') border-red-500 @enderror"
                pattern="^[0-9]+$" title="Solo se permiten números." required>
            @error('numero_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="md:col-span-2">
            <label for="referencia" class="label">Referencia/Detalle adicional</label>
            <input type="text" name="referencia" id="referencia" maxlength="150"
                value="{{ old('referencia', $laboratorio->referencia ?? '') }}"
                class="input-standard w-full @error('referencia') border-red-500 @enderror"
                placeholder="Ejemplo: Frente a la plaza principal, cerca del hospital...">
            @error('referencia')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</fieldset>

{{-- Contacto --}}
<fieldset class="border p-4 mb-8 rounded-md max-w-3xl mx-auto">
    <legend class="flex items-center gap-2 text-lg font-semibold mb-2">
        <i class="fas fa-phone text-primary"></i> Contacto
    </legend>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="wapp_lab" class="label required-label">WhatsApp Principal</label>
            <input type="tel" name="wapp_lab" id="wapp_lab"
                value="{{ old('wapp_lab', $laboratorio->wapp_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('wapp_lab') border-red-500 @enderror" required
                maxlength="8" pattern="^(6|7)\d{7}$"
                title="Debe ser un número válido de Bolivia (8 dígitos y comenzar con 6 o 7)">
            @error('wapp_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="wapp2_lab" class="label">WhatsApp Secundario</label>
            <input type="tel" name="wapp2_lab" id="wapp2_lab"
                value="{{ old('wapp2_lab', $laboratorio->wapp2_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('wapp2_lab') border-red-500 @enderror" maxlength="8"
                pattern="^(6|7)\d{7}$" title="Debe ser un número válido de Bolivia (8 dígitos y comenzar con 6 o 7)">
            @error('wapp2_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="mail_lab" class="label required-label">Correo Principal</label>
            <input type="email" name="mail_lab" id="mail_lab" maxlength="50"
                value="{{ old('mail_lab', $laboratorio->mail_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('mail_lab') border-red-500 @enderror {{ $esSoloLectura ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}"
                required @readonly($esSoloLectura)>

            @error('mail_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="mail2_lab" class="label">Correo Secundario</label>
            <input type="email" name="mail2_lab" id="mail2_lab" maxlength="50"
                value="{{ old('mail2_lab', $laboratorio->mail2_lab ?? '') }}"
                class="input-standard max-w-md w-full @error('mail2_lab') border-red-500 @enderror">
            @error('mail2_lab')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefono" class="label">Telefono fijo</label>
            <input type="number" name="telefono" id="telefono" min="1000" maxlength="15"
                value="{{ old('telefono', $laboratorio->telefono ?? '') }}"
                class="input-standard max-w-md w-full @error('telefono') border-red-500 @enderror">
            @error('telefono')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</fieldset>

{{-- Clasificaciones --}}
<fieldset class="border p-4 mb-8 rounded-md max-w-3xl mx-auto">
    <legend class="flex items-center gap-2 text-lg font-semibold mb-2">
        <i class="fas fa-tags text-primary"></i> Caracterizacion
    </legend>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="id_nivel" class="label required-label">Nivel Laboratorio</label>
            <select name="id_nivel" id="id_nivel"
                class="input-standard max-w-md w-full @error('id_nivel') border-red-500 @enderror" required>
                <option value="">Seleccione un nivel</option>
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}"
                        {{ old('id_nivel', $laboratorio->id_nivel ?? '') == $nivel->id ? 'selected' : '' }}>
                        {{ $nivel->descripcion_nivel }}
                    </option>
                @endforeach
            </select>
            @error('id_nivel')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="id_tipo" class="label required-label">Tipo de Servicio</label>
            <select name="id_tipo" id="id_tipo"
                class="input-standard max-w-md w-full @error('id_tipo') border-red-500 @enderror" required>
                <option value="">Seleccione un tipo</option>
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo->id }}"
                        {{ old('id_tipo', $laboratorio->id_tipo ?? '') == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->descripcion }}
                    </option>
                @endforeach
            </select>
            @error('id_tipo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="id_categoria" class="label required-label">Categoría Laboratorio</label>
            <select name="id_categoria" id="id_categoria"
                class="input-standard max-w-md w-full @error('id_categoria') border-red-500 @enderror" required>
                <option value="">Seleccione una categoría</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}"
                        {{ old('id_categoria', $laboratorio->id_categoria ?? '') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->descripcion }}
                    </option>
                @endforeach
            </select>
            @error('id_categoria')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</fieldset>
{{-- Contraseña --}}
<fieldset class="border p-4 mb-8 rounded-md max-w-3xl mx-auto">
    <legend class="flex items-center gap-2 text-lg font-semibold mb-2">
        <i class="fas fa-lock text-primary"></i> Seguridad / Contraseña
    </legend>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Contraseña --}}
        <div>
            <label for="password" class="label  {{ !$edit ? 'required-label' : '' }}">Contraseña</label>
            <div class="relative">
                <input type="password" name="password" id="password" autocomplete="off" maxlength="15"
                    minlength="5" @unless ($edit) required @endunless
                    class="input-standard pr-10 w-full @error('password') border-red-500 @enderror"
                    placeholder="Mínimo 8 caracteres">
                <span onclick="togglePassword('password')"
                    class="absolute right-3 top-2.5 cursor-pointer text-gray-500">
                    <i id="icon-password" class="fas fa-eye"></i>
                </span>
            </div>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirmación --}}
        <div>
            <label for="password_confirmation" class="label {{ !$edit ? 'required-label' : '' }}">Confirmar
                Contraseña</label>
            <div class="relative">
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="off"
                    @unless ($edit) required @endunless maxlength="15" minlength="5"
                    class="input-standard pr-10 w-full @error('password_confirmation') border-red-500 @enderror"
                    placeholder="Repetir contraseña">

                <span onclick="togglePassword('password_confirmation')"
                    class="absolute right-3 top-2.5 cursor-pointer text-gray-500">
                    <i id="icon-password_confirmation" class="fas fa-eye"></i>
                </span>
            </div>
            @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
</fieldset>

@if ($edit)
    {{-- Estado --}}
    @if (Gate::any([Permiso::GESTION_LABORATORIO, Permiso::ADMIN]))
        <fieldset class="border p-4 mb-8 rounded-md max-w-3xl mx-auto">
            <legend class="flex items-center gap-2 text-lg font-semibold mb-2">
                <i class="fas fa-toggle-on text-primary"></i> Estado del Laboratorio
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="label">Estado</label>
                    <select name="status" id="status"
                        class="input-standard max-w-md w-full @error('status') border-red-500 @enderror" required>
                        <option value="1" {{ old('status', $laboratorio->status ?? '') == 1 ? 'selected' : '' }}>
                            Activo</option>
                        <option value="0"
                            {{ old('status', $laboratorio->status ?? '') === 0 ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="inline-flex items-center space-x-2">
                        <input type="checkbox" name="email_verified_at" value="1"
                            {{ $laboratorio->usuario->email_verified_at ? 'checked disabled' : '' }}
                            class="rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">
                            {{ $laboratorio->usuario->email_verified_at ? 'Correo verificado' : 'Verficar el correo principal del Laboratorio' }}
                        </span>
                    </label>

                    @if ($laboratorio->usuario->email_verified_at)
                        <p class="text-sm text-green-600 mt-1">
                            Verificado el {{ $laboratorio->usuario->email_verified_at }}
                        </p>
                    @endif
                </div>

        </fieldset>
    @endif
@endif


{{-- JavaScript para cargar selects dinámicos --}}
<script>
    const url = '/api/admin';

    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(`icon-${inputId}`);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    document.addEventListener("DOMContentLoaded", async function() {
        const paisId = "{{ old('id_pais', $laboratorio->id_pais ?? '') }}";
        if (paisId) {
            await cargarDepartamentos();
        }
    });
    async function cargarDepartamentos() {

        const paisIdSelect = "{{ old('id_pais', $laboratorio->id_pais ?? null) }}";
        const depIdSelect = "{{ old('id_dep', $laboratorio->id_dep ?? null) }}";
        const paisId = document.getElementById('id_pais').value ?? paisIdSelect;
        const depSelect = document.getElementById('id_dep');
        depSelect.innerHTML = '<option value="">Cargando...</option>';

        if (!paisId) {
            depSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
            limpiarProvincias();
            limpiarMunicipios();
            return;
        }

        try {
            const response = await fetch(`${url}/departamento/${paisId}`);
            if (!response.ok) throw new Error('Error cargando departamentos');
            const departamentos = await response.json();

            let options = '<option value="">Seleccione un departamento</option>';
            departamentos.forEach(dep => {
                options +=
                    `<option value="${dep.id}" ${depIdSelect == dep.id ?'selected':''  }>${dep.nombre_dep}</option>`;
            });

            depSelect.innerHTML = options;

            limpiarProvincias();
            limpiarMunicipios();

            if (paisIdSelect) {
                cargarProvincias();
            }
        } catch (error) {
            depSelect.innerHTML = '<option value="">Error cargando departamentos</option>';
            console.error(error);
        }
    }

    async function cargarProvincias() {
        const depIdSelect = "{{ old('id_dep', $laboratorio->id_dep ?? '') }}";
        const provIdSelect = "{{ old('id_prov', $laboratorio->id_prov ?? null) }}";
        const depId = document.getElementById('id_dep').value ?? depIdSelect;
        const provSelect = document.getElementById('id_prov');
        provSelect.innerHTML = '<option value="">Cargando...</option>';

        if (!depId) {
            limpiarProvincias();
            limpiarMunicipios();
            return;
        }

        try {
            const response = await fetch(`${url}/provincia/${depId}`);
            if (!response.ok) throw new Error('Error cargando provincias');
            const provincias = await response.json();

            let options = '<option value="">Seleccione una provincia</option>';
            provincias.forEach(prov => {
                options +=
                    `<option value="${prov.id}" ${provIdSelect == prov.id ?'selected':''  } >${prov.nombre_prov}</option>`;
            });
            provSelect.innerHTML = options;

            limpiarMunicipios();

            if (depIdSelect) {
                cargarMunicipios();
            }

        } catch (error) {
            provSelect.innerHTML = '<option value="">Error cargando provincias</option>';
            console.error(error);
        }
    }

    async function cargarMunicipios() {
        const provIdSelect = "{{ old('id_prov', $laboratorio->id_prov ?? '') }}";
        const munIdSelect = "{{ old('id_municipio', $laboratorio->id_municipio ?? '') }}";

        const provId = document.getElementById('id_prov').value ?? provIdSelect;
        const munSelect = document.getElementById('id_municipio');
        munSelect.innerHTML = '<option value="">Cargando...</option>';

        if (!provId) {
            limpiarMunicipios();
            return;
        }

        try {
            const response = await fetch(`${url}/municipio/${provId}`);
            if (!response.ok) throw new Error('Error cargando municipios');
            const municipios = await response.json();

            let options = '<option value="">Seleccione un municipio</option>';
            municipios.forEach(mun => {
                options +=
                    `<option value="${mun.id}" ${munIdSelect == mun.id ?'selected':''  }>${mun.nombre_municipio}</option>`;
            });
            munSelect.innerHTML = options;

        } catch (error) {
            munSelect.innerHTML = '<option value="">Error cargando municipios</option>';
            console.error(error);
        }
    }

    function limpiarProvincias() {
        document.getElementById('id_prov').innerHTML = '<option value="">Seleccione una provincia</option>';
    }

    function limpiarMunicipios() {
        document.getElementById('id_municipio').innerHTML = '<option value="">Seleccione un municipio</option>';
    }

    document.addEventListener("DOMContentLoaded", () => {
        const reglas = {
            numsedes_lab: /^[0-9]{1,20}$/, // solo números
            sigla_lab: /^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s.-]{2,20}$/, // letras, números, guiones, puntos
            nit_lab: /^[0-9]{5,20}$/, // NIT: solo números, mínimo 5
            nombre_lab: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s1-9\.]{4,20}$/, // solo letras y espacios
            respo_lab: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.]{5,50}$/, // nombre responsable
            ci_respo_lab: /^[0-9]{5,15}(?:-[A-Za-z](?:\s[A-Za-z]{2})?|\s[A-Za-z]{2})?$/, // empieza con número, puede tener letra o guion
            repreleg_lab: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.]{5,50}$/,
            ci_repreleg_lab: /^[0-9]{5,15}(?:-[A-Za-z](?:\s[A-Za-z]{2})?|\s[A-Za-z]{2})?$/,
            zona_lab: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,50}$/,
            calle_lab: /^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s.,-]{3,100}$/,
            numero_lab: /^[0-9]{1,10}$/,
            referencia: /^.{0,150}$/,
            wapp_lab: /^(6|7)[0-9]{7}$/,
            wapp2_lab: /^(6|7)[0-9]{7}$/,
            mail_lab: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/,
            mail2_lab: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/,
            telefono: /^[0-9]{6,20}$/
        };

        function validarCampo(input) {
            const regla = reglas[input.name];
            if (!regla) return;
            console.log(regla);
            const valido = regla.test(input.value.trim());
            input.classList.remove("border-red-500", "border-green-500");
            if (valido || input.value === "") {
                if (valido) input.style.border = "1px solid #22c55e"
            } else {
                input.style.border = "1px solid #ef4444";
            }
        }

        function manejarCampo(input) {
            if(input.type !='email'){
                input.value = input.value.toUpperCase();
            }
            validarCampo(input);
        }   

        Object.keys(reglas).forEach(nombre => {
            const input = document.querySelector(`[name="${nombre}"]`);
            if (input) {
                ["input", "blur"].forEach(evento => {
                    input.addEventListener(evento, () => manejarCampo(input));
                });
            }
        });

        // Validación global al enviar el form
        const form = document.querySelector("form");
        form.addEventListener("submit", (e) => {
            let valido = true;
            Object.keys(reglas).forEach(nombre => {
                const input = document.querySelector(`[name="${nombre}"]`);
                if (input && !reglas[nombre].test(input.value.trim()) && input.hasAttribute(
                        "required")) {
                    valido = false;
                    input.classList.add("border-red-500");
                }
            });
            if (!valido) {
                e.preventDefault();
                alert("Por favor, complete todos los campos correctamente antes de enviar.");
            }
        });
    });
</script>
