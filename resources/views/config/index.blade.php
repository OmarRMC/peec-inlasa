@php use App\Models\Configuracion;  @endphp
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

            @foreach ([['id' => 'inscripcion', 'title' => 'Periodo de Inscripción'], ['id' => 'pago', 'title' => 'Periodo de Pago'], ['id' => 'vigencia', 'title' => 'Periodo de Vigencia'], ['id' => 'gestion', 'title' => 'Gestión Actual'], ['id' => 'notificacion', 'title' => 'Notificaciones']] as $item)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button type="button"
                        class="w-full flex justify-between items-center px-5 py-4 bg-indigo-50 hover:bg-indigo-100 font-semibold transition duration-300"
                        onclick="toggleCollapse('{{ $item['id'] }}')">
                        <span>{{ $item['title'] }}</span>
                        <svg id="icon-{{ $item['id'] }}" class="w-5 h-5 transform transition-transform duration-300"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

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
                                                <label for="{{ Configuracion::FECHA_INICIO_INSCRIPCION }}"
                                                    class="block text-sm font-medium">Fecha Inicio</label>
                                                <input type="date" name="{{ Configuracion::FECHA_INICIO_INSCRIPCION }}"
                                                    id="{{ Configuracion::FECHA_INICIO_INSCRIPCION }}"
                                                    value="{{ old(Configuracion::FECHA_INICIO_INSCRIPCION, configuracion(Configuracion::FECHA_INICIO_INSCRIPCION) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::FECHA_FIN_INSCRIPCION }}"
                                                    class="block text-sm font-medium">Fecha Fin</label>
                                                <input type="date" name="{{ Configuracion::FECHA_FIN_INSCRIPCION }}"
                                                    id="{{ Configuracion::FECHA_FIN_INSCRIPCION }}"
                                                    value="{{ old(Configuracion::FECHA_FIN_INSCRIPCION, configuracion(Configuracion::FECHA_FIN_INSCRIPCION) ?? '') }}"
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
                                                <label for="{{ Configuracion::FECHA_INICIO_PAGO }}"
                                                    class="block text-sm font-medium">Fecha Inicio</label>
                                                <input type="date" name="{{ Configuracion::FECHA_INICIO_PAGO }}"
                                                    id="{{ Configuracion::FECHA_INICIO_PAGO }}"
                                                    value="{{ old(Configuracion::FECHA_INICIO_PAGO, configuracion(Configuracion::FECHA_INICIO_PAGO) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::FECHA_FIN_PAGO }}"
                                                    class="block text-sm font-medium">Fecha Fin</label>
                                                <input type="date" name="{{ Configuracion::FECHA_FIN_PAGO }}"
                                                    id="{{ Configuracion::FECHA_FIN_PAGO }}"
                                                    value="{{ old(Configuracion::FECHA_FIN_PAGO, configuracion(Configuracion::FECHA_FIN_PAGO) ?? '') }}"
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
                                                <label for="{{ Configuracion::FECHA_INICIO_VIGENCIA }}"
                                                    class="block text-sm font-medium">Fecha Inicio</label>
                                                <input type="date" name="{{ Configuracion::FECHA_INICIO_VIGENCIA }}"
                                                    id="{{ Configuracion::FECHA_INICIO_VIGENCIA }}"
                                                    value="{{ old(Configuracion::FECHA_INICIO_VIGENCIA, configuracion(Configuracion::FECHA_INICIO_VIGENCIA) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::FECHA_FIN_VIGENCIA }}"
                                                    class="block text-sm font-medium">Fecha Fin</label>
                                                <input type="date" name="{{ Configuracion::FECHA_FIN_VIGENCIA }}"
                                                    id="{{ Configuracion::FECHA_FIN_VIGENCIA }}"
                                                    value="{{ old(Configuracion::FECHA_FIN_VIGENCIA, configuracion(Configuracion::FECHA_FIN_VIGENCIA) ?? '') }}"
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
                                            <label for="{{ Configuracion::GESTION_ACTUAL }}"
                                                class="block text-sm font-medium">Año de Gestión</label>
                                            <input type="number" name="{{ Configuracion::GESTION_ACTUAL }}"
                                                id="{{ Configuracion::GESTION_ACTUAL }}"
                                                value="{{ old(Configuracion::GESTION_ACTUAL, configuracion(Configuracion::GESTION_ACTUAL) ?? date('Y')) }}"
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
                                                <label for="{{ Configuracion::NOTIFICACION_KEY }}"
                                                    class="block text-sm font-medium">Key</label>
                                                <input type="text" name="{{ Configuracion::NOTIFICACION_KEY }}"
                                                    id="{{ Configuracion::NOTIFICACION_KEY }}"
                                                    value="{{ old(Configuracion::NOTIFICACION_KEY, configuracion(Configuracion::NOTIFICACION_KEY) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::NOTIFICACION_TITULO }}"
                                                    class="block text-sm font-medium">Título</label>
                                                <input type="text" name="{{ Configuracion::NOTIFICACION_TITULO }}"
                                                    id="{{ Configuracion::NOTIFICACION_TITULO }}"
                                                    value="{{ old(Configuracion::NOTIFICACION_TITULO, configuracion(Configuracion::NOTIFICACION_TITULO) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="{{ Configuracion::NOTIFICACION_DESCRIPCION }}"
                                                class="block text-sm font-medium">Descripción</label>
                                            <input type="text" name="{{ Configuracion::NOTIFICACION_DESCRIPCION }}"
                                                id="{{ Configuracion::NOTIFICACION_DESCRIPCION }}"
                                                value="{{ old(Configuracion::NOTIFICACION_DESCRIPCION, configuracion(Configuracion::NOTIFICACION_DESCRIPCION) ?? '') }}"
                                                class="mt-1 input-standard" required>
                                        </div>

                                        <div>
                                            <label for="{{ Configuracion::NOTIFICACION_MENSAJE }}"
                                                class="block text-sm font-medium">Mensaje (HTML permitido)</label>
                                            <textarea name="{{ Configuracion::NOTIFICACION_MENSAJE }}" id="{{ Configuracion::NOTIFICACION_MENSAJE }}"
                                                rows="4" class="input-standard" required>{{ old(Configuracion::NOTIFICACION_MENSAJE, configuracion(Configuracion::NOTIFICACION_MENSAJE) ?? '') }}</textarea>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="{{ Configuracion::NOTIFICACION_FECHA_INICIO }}"
                                                    class="block text-sm font-medium">Fecha de Inicio</label>
                                                <input type="date" name="{{ Configuracion::NOTIFICACION_FECHA_INICIO }}"
                                                    id="{{ Configuracion::NOTIFICACION_FECHA_INICIO }}"
                                                    value="{{ old(Configuracion::NOTIFICACION_FECHA_INICIO, configuracion(Configuracion::NOTIFICACION_FECHA_INICIO) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::NOTIFICACION_FECHA_FIN }}"
                                                    class="block text-sm font-medium">Fecha de Fin</label>
                                                <input type="date" name="{{ Configuracion::NOTIFICACION_FECHA_FIN }}"
                                                    id="{{ Configuracion::NOTIFICACION_FECHA_FIN }}"
                                                    value="{{ old(Configuracion::NOTIFICACION_FECHA_FIN, configuracion(Configuracion::NOTIFICACION_FECHA_FIN) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                        </div>

                                        <p class="text-xs text-gray-500 mt-2">La fecha fin debe ser posterior o igual a la
                                            fecha inicio.</p>

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
