@auth
    @if (Auth::user()->laboratorio)
<script>
    (function () {
        const ENDPOINT_LISTA = "{{ url('/lab/notificaciones/pendientes') }}";
        const ENDPOINT_READ_BASE = "{{ url('/lab/notificaciones') }}";
        const CSRF = "{{ csrf_token() }}";

        const COLOR_CLASES = {
            yellow: { border: 'border-yellow-400', bg: 'bg-yellow-50', text: 'text-yellow-700', btn: 'bg-yellow-500 hover:bg-yellow-600' },
            red:    { border: 'border-red-400',    bg: 'bg-red-50',    text: 'text-red-700',    btn: 'bg-red-600 hover:bg-red-700' },
            green:  { border: 'border-green-400',  bg: 'bg-green-50',  text: 'text-green-700',  btn: 'bg-green-600 hover:bg-green-700' },
            indigo: { border: 'border-indigo-400', bg: 'bg-indigo-50', text: 'text-indigo-700', btn: 'bg-indigo-600 hover:bg-indigo-700' },
        };

        // La memoria de "ya mostrada en esta sesión" vive en la sesión Laravel
        // (ver NotificacionController::getNotificacionesPendientes). El server
        // solo devuelve las notificaciones que NO se entregaron antes en esta
        // sesión; al re-loguearse, las persistentes reaparecen.
        document.addEventListener('DOMContentLoaded', () => {
            fetch(ENDPOINT_LISTA, { credentials: 'same-origin' })
                .then(res => res.ok ? res.json() : [])
                .then(lista => {
                    if (!Array.isArray(lista) || lista.length === 0) return;
                    mostrarCola(lista);
                })
                .catch(err => console.error('NotifLab error:', err));
        });

        function mostrarCola(cola) {
            if (cola.length === 0) return;
            const notif = cola.shift();
            mostrarUno(notif, () => mostrarCola(cola));
        }

        function mostrarUno(notif, onClose) {
            const c = COLOR_CLASES[notif.color] || COLOR_CLASES.indigo;

            const overlay = document.createElement('div');
            overlay.id = 'notif-lab-modal-' + notif.id;
            overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 sm:p-6';

            if (notif.adaptable) {
                // Layout adaptable: el modal se ajusta al contenido (útil para avisos
                // con imágenes o tamaños propios - como la notificación global de Configuracion).
                const descripcion = notif.descripcion
                    ? `<p class="mb-4 text-gray-600">${escapeHtml(notif.descripcion)}</p>`
                    : '';

                overlay.innerHTML = `
                    <div class="bg-white rounded-xl shadow-lg max-w-4xl w-full relative mx-auto overflow-y-auto"
                         style="max-height:90vh; width:fit-content">
                        <div class="p-6 sm:p-10">
                            <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold" data-cerrar>&times;</button>
                            <h2 class="text-2xl font-bold mb-3 pr-8">${escapeHtml(notif.titulo)}</h2>
                            ${descripcion}
                            <div class="mb-2 text-gray-800">${notif.html}</div>
                        </div>
                    </div>
                `;
            } else {
                const verDetalleBtn = notif.url_accion
                    ? `<a href="${notif.url_accion}" class="px-4 py-2 ${c.btn} text-white rounded text-sm">Ver detalle</a>`
                    : '';

                const footer = notif.sin_acciones ? '' : `
                    <div class="px-6 py-3 bg-gray-50 border-t flex justify-end gap-2">
                        ${verDetalleBtn}
                        <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded text-sm" data-entendido>
                            Entendido
                        </button>
                    </div>
                `;

                overlay.innerHTML = `
                    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-auto overflow-y-auto" style="max-height:90vh">
                        <div class="${c.bg} border-b ${c.border} px-6 py-4 flex items-center gap-3">
                            <i class="${notif.icono} ${c.text} text-2xl"></i>
                            <h2 class="text-lg font-bold ${c.text}">${escapeHtml(notif.titulo)}</h2>
                            <button type="button" class="ml-auto text-gray-500 hover:text-gray-700 text-2xl font-bold" data-cerrar>&times;</button>
                        </div>
                        <div class="px-6 py-5 text-gray-800">
                            ${notif.html}
                        </div>
                        ${footer}
                    </div>
                `;
            }

            document.body.appendChild(overlay);

            const cerrar = () => {
                marcarLeida(notif.id);
                overlay.remove();
                if (onClose) onClose();
            };

            overlay.querySelector('[data-cerrar]').onclick = cerrar;
            const entendido = overlay.querySelector('[data-entendido]');
            if (entendido) entendido.onclick = cerrar;
        }

        function marcarLeida(id) {
            fetch(`${ENDPOINT_READ_BASE}/${id}/read`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            }).catch(() => {});
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => (
                {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]
            ));
        }
    })();
</script>
    @endif
@endauth
