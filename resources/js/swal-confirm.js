document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const nombre = this.querySelector('.delete-button')?.dataset?.nombre || this.getAttribute('data-nombre') || 'este registro';

            Swal.fire({
                title: `¿Eliminar "${nombre}"?`,
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) this.submit();
            });
        });
    });
});
