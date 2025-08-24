@props(['updateUrl', 'desempeno'])

<div class="flex items-center space-x-1">
    <form action="{{ $updateUrl }}" method="POST" class="flex items-center space-x-1 update-form-desempeno">
        @csrf
        <input type="text" name="calificacion_certificado" value="{{ $desempeno }}"
            class="border rounded px-1 py-0.5 text-sm">

        <button type="submit"
            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded shadow-sm flex items-center justify-center"
            data-tippy-content="Actualizar">
            <i class="fas fa-sync-alt"></i>
        </button>
    </form>
</div>
