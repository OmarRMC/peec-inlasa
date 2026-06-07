<x-app-layout>
<div class="max-w-7xl mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-paper-plane text-indigo-500"></i>
            Notificaciones enviadas a laboratorios
        </h1>
        <a href="{{ route('admin.notificaciones.create') }}"
            class="inline-flex items-center gap-1 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
            <i class="fas fa-plus"></i> Nueva notificación
        </a>
    </div>

    @if (session('success'))
        <div class="mb-3 p-3 bg-green-50 border border-green-300 text-green-700 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-end mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo</label>
            <select name="tipo" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">Todos</option>
                @foreach ($tipos as $key => $label)
                    <option value="{{ $key }}" @selected($filtros['tipo'] === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Estado</label>
            <select name="estado" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">Todos</option>
                <option value="pendiente" @selected($filtros['estado'] === 'pendiente')>Pendiente</option>
                <option value="leida" @selected($filtros['estado'] === 'leida')>Leída</option>
                <option value="resuelta" @selected($filtros['estado'] === 'resuelta')>Resuelta</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Enviado por</label>
            <select name="creado_por" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">Todos</option>
                @foreach ($admins as $a)
                    <option value="{{ $a->id }}" @selected((string) $filtros['creado_por'] === (string) $a->id)>
                        {{ trim($a->nombre . ' ' . $a->ap_paterno . ' ' . $a->ap_materno) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] }}"
                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] }}"
                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Buscar (nombre o código de lab)</label>
            <input type="text" name="q" value="{{ $filtros['q'] }}"
                placeholder="Ej. LAB-001 o Hospital..."
                class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
        </div>

        <div class="md:col-span-4 flex gap-2 justify-end">
            <a href="{{ route('admin.notificaciones.index') }}"
                class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">Limpiar</a>
            <button type="submit"
                class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
                Filtrar
            </button>
        </div>
    </form>

    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left text-gray-700">
                <tr>
                    <th class="p-2">Laboratorio</th>
                    <th class="p-2">Tipo</th>
                    <th class="p-2">Título</th>
                    <th class="p-2">Fecha envío</th>
                    <th class="p-2">Enviado por</th>
                    <th class="p-2">Estado</th>
                    <th class="p-2">Modo / Vigencia</th>
                    <th class="p-2 text-center">Ver</th>
                    <th class="p-2 text-center">Ir a</th>
                    <th class="p-2 text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notificaciones as $n)
                    @php
                        $estadoBadge = match (true) {
                            !is_null($n->resuelta_en) => ['Resuelta', 'bg-gray-300 text-gray-800'],
                            !is_null($n->leido_en)    => ['Leída', 'bg-blue-200 text-blue-800'],
                            default                   => ['Pendiente', 'bg-yellow-300 text-yellow-900 font-semibold'],
                        };
                    @endphp
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="p-2">
                            <div class="font-semibold text-gray-800">{{ $n->laboratorio?->nombre_lab ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $n->laboratorio?->cod_lab }}</div>
                        </td>
                        <td class="p-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-{{ $n->color }}-100 text-{{ $n->color }}-800">
                                <i class="{{ $n->icono }}"></i>
                                {{ $tipos[$n->tipo] ?? $n->tipo }}
                            </span>
                        </td>
                        <td class="p-2">{{ $n->titulo }}</td>
                        <td class="p-2 text-gray-600 text-xs">{{ $n->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-2 text-xs">
                            <div class="text-gray-700">
                                {{ $n->creador ? trim($n->creador->nombre . ' ' . $n->creador->ap_paterno) : '-' }}
                            </div>
                            <div class="text-gray-400">{{ $n->creador?->email }}</div>
                        </td>
                        <td class="p-2">
                            <span class="px-2 py-0.5 rounded text-xs {{ $estadoBadge[1] }}">
                                {{ $estadoBadge[0] }}
                            </span>
                        </td>
                        <td class="p-2 text-xs">
                            <div class="text-gray-700">
                                {{ $n->modo === 'persistente' ? 'Persistente' : 'Una vez' }}
                            </div>
                            @if ($n->fecha_inicio || $n->fecha_fin)
                                <div class="text-gray-500 mt-0.5">
                                    {{ $n->fecha_inicio?->format('d/m/Y') ?? '-' }}
                                    →
                                    {{ $n->fecha_fin?->format('d/m/Y') ?? '-' }}
                                </div>
                                @if ($n->modo === 'persistente' && is_null($n->resuelta_en))
                                    @if ($n->esta_vigente)
                                        <span class="inline-block mt-0.5 px-1.5 py-0.5 bg-green-100 text-green-800 rounded text-[10px]">Vigente</span>
                                    @else
                                        <span class="inline-block mt-0.5 px-1.5 py-0.5 bg-gray-200 text-gray-700 rounded text-[10px]">Fuera de vigencia</span>
                                    @endif
                                @endif
                            @endif
                        </td>
                        <td class="p-2 text-center">
                            <button type="button"
                                onclick="verNotificacion({{ $n->id }}, '{{ addslashes($n->titulo) }}')"
                                class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded text-xs">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            <template id="notif-html-{{ $n->id }}">{!! $n->html_mensaje !!}</template>
                        </td>
                        <td class="p-2 text-center">
                            @if ($n->url_admin_accion)
                                <a href="{{ $n->url_admin_accion }}"
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 hover:bg-green-100 text-green-700 rounded text-xs">
                                    <i class="fas fa-arrow-right"></i> Ir
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-2 text-center whitespace-nowrap">
                            @if ($n->modo === 'persistente' && is_null($n->resuelta_en))
                                <form method="POST" action="{{ route('admin.notificaciones.resolver', $n->id) }}"
                                    onsubmit="return confirm('¿Marcar como resuelta para este laboratorio?');"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 rounded text-xs">
                                        <i class="fas fa-check"></i> Resolver
                                    </button>
                                </form>
                            @endif
                            @if ($n->notificacion)
                                <a href="{{ route('admin.notificaciones.edit', $n->notificacion->id) }}"
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded text-xs"
                                    title="Editar plantilla y/o resolver para todos">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="p-6 text-center text-gray-500">
                            No hay notificaciones que coincidan con los filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $notificaciones->links() }}
    </div>
</div>

<div id="modal-ver-notif" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full mx-auto overflow-y-auto" style="max-height:90vh">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-bell text-indigo-500 text-xl"></i>
            <h2 id="modal-ver-notif-titulo" class="text-lg font-bold text-gray-800"></h2>
            <button type="button" onclick="cerrarVerNotificacion()"
                class="ml-auto text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
        </div>
        <div id="modal-ver-notif-body" class="px-6 py-5 text-gray-800"></div>
    </div>
</div>

<script>
    function verNotificacion(id, titulo) {
        const tpl = document.getElementById('notif-html-' + id);
        document.getElementById('modal-ver-notif-titulo').innerText = titulo;
        document.getElementById('modal-ver-notif-body').innerHTML = tpl ? tpl.innerHTML : '';
        document.getElementById('modal-ver-notif').classList.remove('hidden');
    }
    function cerrarVerNotificacion() {
        document.getElementById('modal-ver-notif').classList.add('hidden');
        document.getElementById('modal-ver-notif-body').innerHTML = '';
    }
</script>
</x-app-layout>
