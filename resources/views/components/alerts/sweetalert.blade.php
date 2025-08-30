@if (session('success'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
@endif

@if (session('info'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('info') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
@endif



@if (session('warning'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: '{{ session('warning') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
@endif

@if (session('confirmation'))
    <script>
        Swal.fire({
            title: '¿Estás seguro?',
            text: '{{ session('confirmation') ?? '' }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, aceptar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'swal2-sm',
                title: 'text-base',
                htmlContainer: 'text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Confirmado',
                    'La acción se realizó correctamente.',
                    'success'
                )
            }
        });
    </script>
@endif

@if (session('notice'))
    <script>
        Swal.fire({
            title: "{!! session('notice.title') ?? 'Nota' !!}",
            html: {!! json_encode(session('notice.message') ?? '') !!},
            icon: "{{ session('notice.type') ?? 'info' }}",
            showConfirmButton: true,
            confirmButtonText: 'Entendido',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'swal2-sm',
                title: 'text-base',
                htmlContainer: 'text-sm'
            }
        });
    </script>
@endif
