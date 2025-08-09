<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (sessionStorage.getItem('notificacionVista')) return;

        fetch("{{ url('/lab/notificacion/verify') }}")
            .then(res => {
                if (res.status === 204) return null;
                return res.json();
            })
            .then(data => {
                if (data) {
                    mostrarNotificacion(data);
                }
            })
            .catch(err => console.error("Error al obtener notificación:", err));

        function mostrarNotificacion(data) {
            const container = document.createElement('div');
            container.innerHTML = `
            <div id="notificacion-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6 relative">
                    <button id="btn-cerrar" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                    <h2 class="text-xl font-bold mb-3">${data.titulo}</h2>
                    <p class="mb-4 text-gray-600">${data.descripcion}</p>
                    <div class="mb-6 text-gray-800" id="mensaje-notificacion"></div>
                </div>
            </div>
        `;
            /*
            <div class="flex justify-end gap-4">
                             <button data-clave="${data.clave}" id="btn-aceptar" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Aceptar</button>
                             <button id="btn-cerrar2" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cerrar</button>
            </div>
            */
            document.body.appendChild(container);

            // Renderizar HTML del mensaje
            document.getElementById('mensaje-notificacion').innerHTML = data.mensaje;

            document.getElementById('btn-cerrar').onclick = cerrarModal;
            // document.getElementById('btn-cerrar2').onclick = cerrarModal;

            // document.getElementById('btn-aceptar').onclick = (e) => {
            //     cerrarModal();
            //     const clave = e.target.getAttribute('data-clave');
            //     fetch("{{ url('/lab/notificacion/read') }}", {
            //         method: 'POST',
            //         headers: {
            //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
            //             'Content-Type': 'application/json'
            //         },
            //         body: JSON.stringify({
            //             'clave': clave
            //         })
            //     });
            // };

            function cerrarModal() {
                const modal = document.getElementById('notificacion-modal');
                if (modal) modal.remove();
                sessionStorage.setItem('notificacionVista', '1');
            }
        }
    });
</script>

<style>
    #notificacion-modal {
        animation: fadeIn 0.3s ease forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>
