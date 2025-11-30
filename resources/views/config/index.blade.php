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
            {{-- , ['id' => 'vigencia', 'title' => 'Periodo de Vigencia'] --}}
            @foreach ([['id' => 'registro', 'title' => 'Periodo de registro de nuevos laboratorios'], ['id' => 'inscripcion', 'title' => 'Periodo de Inscripción'], ['id' => 'pago', 'title' => 'Periodo de Pago'], ['id' => 'notificacion', 'title' => 'Notificaciones'], ['id' => 'email.informacion', 'title' => 'Información personalizada para email'], ['id' => 'habilitar.docs.inscripcion', 'title' => 'Habilitar la subida de documentos de inscripción'], ['id' => 'habilitar.docs.pagos', 'title' => 'Habilitar la subida de comprobantes de pagos']] as $item)
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
                                @case('registro')
                                    <form action="{{ route($config, 'periodo-registro') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="{{ Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB }}"
                                                    class="block text-sm font-medium">Fecha Inicio</label>
                                                <input type="date" name="{{ Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB }}"
                                                    id="{{ Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB }}"
                                                    value="{{ old(Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB, configuracion(Configuracion::FECHA_INICIO_REGISTRO_NUEVO_LAB) ?? '') }}"
                                                    class="mt-1 input-standard" required>
                                            </div>
                                            <div>
                                                <label for="{{ Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB }}"
                                                    class="block text-sm font-medium">Fecha Fin</label>
                                                <input type="date" name="{{ Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB }}"
                                                    id="{{ Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB }}"
                                                    value="{{ old(Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB, configuracion(Configuracion::FECHA_FIN_REGISTRO_NUEVO_LAB) ?? '') }}"
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

                                @case('inscripcion')
                                    <form action="{{ route($config, 'periodo-inscripcion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="{{ Configuracion::GESTION_INSCRIPCION }}"
                                                class="block text-sm font-medium">Gestion de inscripcion</label>
                                            <input type="number" name="{{ Configuracion::GESTION_INSCRIPCION }}"
                                                id="{{ Configuracion::GESTION_INSCRIPCION }}"
                                                value="{{ old(Configuracion::GESTION_INSCRIPCION, configuracion(Configuracion::GESTION_INSCRIPCION) ?? date('Y')) }}"
                                                class="mt-1 input-standard" min="2020" max="2100" required>
                                        </div>
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

                                @case('gestion.filter')
                                    {{-- @php
                                        $gestiones = configuracion(Configuracion::KEY_GESTION_FILTER);
                                        $gestionesStr = implode(',', $gestiones);
                                    @endphp
                                    <form action="{{ route($config, 'gestion.filter') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="{{ Configuracion::KEY_GESTION_FILTER }}"
                                                class="block text-sm font-medium">Gestiones para filtros</label>
                                            <input type="string" name="{{ Configuracion::KEY_GESTION_FILTER }}"
                                                id="{{ Configuracion::KEY_GESTION_FILTER }}"
                                                value="{{ old(Configuracion::KEY_GESTION_FILTER, $gestionesStr ?? date('Y')) }}"
                                                class="mt-1 input-standard" min="2020" max="2100" required>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form> --}}
                                @break
                                    
                                @case('email.informacion')
                                    <form action="{{ route($config, 'email.informacion') }}" method="POST">
                                        @csrf @method('PUT')
                                        <div>
                                            <label for="{{ Configuracion::EMAIL_INFORMACION }}"
                                                class="block text-sm font-medium">Informacion (HTML permitido)</label>
                                            <textarea name="{{ Configuracion::EMAIL_INFORMACION }}" id="{{ Configuracion::EMAIL_INFORMACION }}" rows="5"
                                                class="input-standard" required>{{ old(Configuracion::EMAIL_INFORMACION, configuracion(Configuracion::EMAIL_INFORMACION) ?? '') }}</textarea>
                                        </div>

                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary"><i
                                                    class="fas fa-save mr-1"></i>Guardar</button>
                                        </div>
                                    </form>
                                @break

                                @case('habilitar.docs.inscripcion')
                                    <form action="{{ route($config, 'habilitar.docs.inscripcion') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="flex items-center mt-2">
                                                <input type="checkbox"
                                                    name="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION }}"
                                                    id="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION }}"
                                                    {{ configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION) ? 'checked' : '' }}
                                                    class="mr-2 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                <label for="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION }}"
                                                    class="text-sm font-medium">
                                                    Habilitar la subida de documentos de inscripción
                                                </label>
                                            </div>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary">
                                                <i class="fas fa-save mr-1"></i>Guardar
                                            </button>
                                        </div>
                                    </form>
                                @break

                                @case('habilitar.docs.pagos')
                                    <form action="{{ route($config, 'habilitar.docs.pagos') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="flex items-center mt-2">
                                                <input type="checkbox"
                                                    name="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS }}"
                                                    id="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS }}"
                                                    {{ configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS) ? 'checked' : '' }}
                                                    class="mr-2 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                <label for="{{ Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS }}"
                                                    class="text-sm font-medium">
                                                    Habilitar la subida de comprobantes de pagos
                                                </label>
                                            </div>
                                        </div>
                                        <div class="text-right mt-4">
                                            <button type="submit" class="btn-primary">
                                                <i class="fas fa-save mr-1"></i>Guardar
                                            </button>
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
