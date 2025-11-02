// resources/js/utils/alert.js
import Swal from 'sweetalert2';

/**
 * Muestra una notificación toast en la esquina superior.
 */
export function showToast({ icon = 'success', title = '', timer = 5000 }) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon,
        title,
        showConfirmButton: false,
        timer,
        timerProgressBar: true
    });
}

/**
 * Muestra una alerta de confirmación con botones personalizados.
 * Devuelve una Promise que resuelve si se confirma.
 */
export function confirmDialog({
    title,
    text = '',
    confirmText = 'Confirmar',
    cancelText = 'Cancelar',
    html = '',
    icon = 'warning'
}) {
    return Swal.fire({
        title,
        text,
        html,
        icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        customClass: {
            popup: 'swal2-mini-popup',
            title: 'swal2-mini-title',
            htmlContainer: 'swal2-mini-html',
            confirmButton: 'swal2-mini-btn swal2-confirm',
            cancelButton: 'swal2-mini-btn swal2-cancel'
        },
        buttonsStyling: false
    });
}
/**
 * Muestra una alerta de error genérica.
 */
export function showError(message = 'Ocurrió un error inesperado.') {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message
    });
}


export function confirmDelete({ csrfToken, table }) {
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function () {
            const nombre = this.dataset.nombre;
            const url = this.dataset.url;
            confirmDialog({
                title: `¿Eliminar "${nombre}"?`,
                text: 'Esta acción no se puede deshacer.'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    })
                        .then(res => res.json())
                        .then(response => {
                            console.log(response);
                            if (response.success) {
                                console.log('data1');

                                showToast({
                                    icon: 'success',
                                    title: response.message
                                });
                                console.log('data');
                                table.ajax.reload();
                            } else {
                                showError(response.message);
                            }
                        })
                        .catch(() => showError());
                }
            });
        });
    });
}


export function confirmAsyncHandle({ handle, html, title, text, icon = 'warning' }) {
    confirmDialog({
        html: html || '',
        title: title || 'Confirmar acción',
        icon: icon,
        title: title || 'Confirmar acción',
        text: text || 'Esta acción no se puede deshacer.',
        confirmText: 'Confirmar',
        cancelText: 'Cancelar'

    }).then(async result => {
        if (result.isConfirmed) {
            try {
                await handle();
            } catch (error) {
                showError()
            }
        }
    });
}

export function mostrarAlertaConfirmacion(titulo, texto, icono, textoConfirmacion, callback) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: icono,
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6c757d',
        confirmButtonText: textoConfirmacion,
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'swal2-sm',
            title: 'text-base',
            htmlContainer: 'text-sm'
        }
    }).then(result => {
        if (result.isConfirmed && typeof callback === "function") {
            callback();
        }
    });
}