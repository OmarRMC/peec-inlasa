<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="bg-white border border-gray-200 shadow rounded-2xl p-6 space-y-6">
            <h1 class="text-xl font-bold text-primary mb-4">⚙️ Configuración del Sistema</h1>
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 border border-red-200 p-4 mb-6 rounded-xl">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Bloques de configuración -->
            @foreach ([['id' => 'inscripcion', 'title' => 'Periodo de Inscripción'], ['id' => 'pago', 'title' => 'Periodo de Pago'], ['id' => 'vigencia', 'title' => 'Periodo de Vigencia'], ['id' => 'gestion', 'title' => 'Gestión Actual'], ['id' => 'notificacion', 'title' => 'Notificaciones']] as $i => $item)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <!-- Botón encabezado -->
                    <button type="button"
                        class="w-full flex justify-between items-center px-5 py-4 bg-indigo-50 hover:bg-indigo-100 font-semibold  transition duration-300"
                        onclick="toggleCollapse('{{ $item['id'] }}')">
                        <span>{{ $item['title'] }}</span>
                        <svg id="icon-{{ $item['id'] }}" class="w-5 h-5 transform transition-transform duration-300"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>backTo

                    <!-- Contenido colapsable -->
                    <div id="content-{{ $item['id'] }}"
                        class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <div class="px-5 py-4 space-y-4 text-sm text-gray-700">
                            @php $config = 'configuracion.update'; @endphp

                            @switch($item['id'])
                                @case('inscripcion')
                                    <form action="{{ route($config, 'periodo-inscripcion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="fecha_inicio_inscripcion" class="block text-sm font-medium">Fecha
                                                    Inicio</label>
                                                <input type="date" name="fecha_inicio_inscripcion"
                                                    id="fecha_inicio_inscripcion"
                                                    value="{{ old('fecha_inicio_inscripcion', configuracion('fecha_inicio_inscripcion') ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="fecha_fin_inscripcion" class="block text-sm font-medium">Fecha
                                                    Fin</label>
                                                <input type="date" name="fecha_fin_inscripcion" id="fecha_fin_inscripcion"
                                                    value="{{ old('fecha_fin_inscripcion', configuracion('fecha_fin_inscripcion') ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">La fecha fin debe ser posterior a la fecha inicio.
                                        </p>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('pago')
                                    <form action="{{ route($config, 'periodo-pago') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="fecha_inicio_pago" class="block text-sm font-medium">Fecha
                                                    Inicio</label>
                                                <input type="date" name="fecha_inicio_pago" id="fecha_inicio_pago"
                                                    value="{{ old('fecha_inicio_pago', configuracion('fecha_inicio_pago') ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="fecha_fin_pago" class="block text-sm font-medium">Fecha Fin</label>
                                                <input type="date" name="fecha_fin_pago" id="fecha_fin_pago"
                                                    value="{{ old('fecha_fin_pago', configuracion('fecha_fin_pago') ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">La fecha fin debe ser posterior a la fecha inicio.
                                        </p>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('vigencia')
                                    <form action="{{ route($config, 'periodo-vigencia') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="fecha_inicio_vigencia" class="block text-sm font-medium">Fecha
                                                    Inicio</label>
                                                <input type="date" name="fecha_inicio_vigencia" id="fecha_inicio_vigencia"
                                                    value="{{ old('fecha_inicio_vigencia', configuracion('fecha_inicio_vigencia') ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="fecha_fin_vigencia" class="block text-sm font-medium">Fecha
                                                    Fin</label>
                                                <input type="date" name="fecha_fin_vigencia" id="fecha_fin_vigencia"
                                                    value="{{ old('fecha_fin_vigencia', configuracion('fecha_fin_vigencia') ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">La fecha fin debe ser posterior a la fecha inicio.
                                        </p>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('gestion')
                                    <form action="{{ route($config, 'gestion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="gestion_actual" class="block text-sm font-medium">Año de Gestión</label>
                                            <input type="number" name="gestion_actual" id="gestion_actual"
                                                value="{{ old('gestion_actual', configuracion('gestion_actual') ?? date('Y')) }}"
                                                class="mt-1 input-standard" min="2020" max="2100" required>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('notificacion')
                                    <form action="{{ route($config, 'notificacion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="notificacion_key" class="block text-sm font-medium">Key</label>
                                                <input type="text" name="notificacion_key" id="notificacion_key"
                                                    value="{{ old('notificacion_key', configuracion('notificacion.key') ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="notificacion_titulo"
                                                    class="block text-sm font-medium">Título</label>
                                                <input type="text" name="notificacion_titulo" id="notificacion_titulo"
                                                    value="{{ old('notificacion_titulo', configuracion('notificacion.titulo') ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="notificacion_descripcion"
                                                class="block text-sm font-medium">Descripción</label>
                                            <input type="text" name="notificacion_descripcion"
                                                id="notificacion_descripcion"
                                                value="{{ old('notificacion_descripcion', configuracion('notificacion.descripcion') ?? '') }}"
                                                class="mt-1 input-standard" required>
                                        </div>
                                        <div>
                                            <label for="notificacion_mensaje" class="block text-sm font-medium">Mensaje (HTML
                                                permitido)</label>
                                            <textarea name="notificacion_mensaje" id="notificacion_mensaje" rows="4" class="input-standard" required>{{ old('notificacion_mensaje', configuracion('notificacion.mensaje') ?? '') }}</textarea>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break
                            @endswitch
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function toggleCollapse(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);

            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = '0px';
                icon.classList.remove('rotate-180');
            } else {
                document.querySelectorAll('[id^="content-"]').forEach(el => el.style.maxHeight = '0px');
                document.querySelectorAll('[id^="icon-"]').forEach(el => el.classList.remove('rotate-180'));

                content.style.maxHeight = content.scrollHeight + 'px';
                icon.classList.add('rotate-180');
            }
        }
    </script>
</x-app-layout>
