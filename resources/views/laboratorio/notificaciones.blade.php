<x-app-layout>
<div class="max-w-6xl mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-bell text-indigo-500"></i> Mis notificaciones
        </h1>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 items-end mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo</label>
            <select name="tipo" class="border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">Todos</option>
                @foreach ($tipos as $key => $label)
                    <option value="{{ $key }}" @selected($filtroTipo === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Estado</label>
            <select name="estado" class="border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">Todas</option>
                <option value="no_leidas" @selected($estado === 'no_leidas')>No leídas</option>
                <option value="leidas" @selected($estado === 'leidas')>Leídas</option>
                <option value="resueltas" @selected($estado === 'resueltas')>Resueltas</option>
            </select>
        </div>
        <button type="submit" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
            Filtrar
        </button>
        <a href="{{ route('lab.notificaciones.bandeja') }}" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
            Limpiar
        </a>
    </form>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left text-gray-700">
                <tr>
                    <th class="p-2">Tipo</th>
                    <th class="p-2">Título</th>
                    <th class="p-2">Fecha</th>
                    <th class="p-2 text-center">Ver</th>
                    <th class="p-2">Estado</th>
                    <th class="p-2 text-right">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notificaciones as $n)
                    @php
                        $estadoBadge = match (true) {
                            !is_null($n->resuelta_at) => ['Resuelta', 'bg-gray-300 text-gray-800'],
                            !is_null($n->read_at)     => ['Leída', 'bg-blue-200 text-blue-800'],
                            default                   => ['No leída', 'bg-yellow-300 text-yellow-900 font-semibold'],
                        };
                    @endphp
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="p-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-{{ $n->color }}-100 text-{{ $n->color }}-800">
                                <i class="{{ $n->icono }}"></i>
                                {{ $tipos[$n->tipo] ?? $n->tipo }}
                            </span>
                        </td>
                        <td class="p-2">{{ $n->titulo }}</td>
                        <td class="p-2 text-gray-600">{{ $n->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-2 text-center">
                            <button type="button"
                                onclick="verNotificacion({{ $n->id }}, '{{ addslashes($n->titulo) }}')"
                                class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded text-xs">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            <template id="notif-html-{{ $n->id }}">{!! $n->html_mensaje !!}</template>
                        </td>
                        <td class="p-2">
                            <span class="px-2 py-0.5 rounded text-xs {{ $estadoBadge[1] }}">
                                {{ $estadoBadge[0] }}
                            </span>
                        </td>
                        <td class="p-2 text-right">
                            @if ($n->url_accion)
                                <a href="{{ $n->url_accion }}" class="text-indigo-600 hover:underline text-xs">
                                    Ver detalle
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">
                            No tiene notificaciones que coincidan con los filtros.
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
