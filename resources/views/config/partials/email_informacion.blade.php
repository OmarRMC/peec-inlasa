{{-- Editor de la sección "INFORMACIÓN" del correo de aprobación / observación. Requiere $tipo --}}
@php
    use App\Models\Configuracion;
    $clave = Configuracion::EMAIL_TIPOS[$tipo];
    $guardado = Configuracion::find($clave)?->valor;
    $original = Configuracion::informacionEmailPorDefecto($tipo);
    $valor = old(str_replace('.', '_', $clave), filled($guardado) ? $guardado : Configuracion::informacionEmailInicial($tipo));
@endphp

<form action="{{ route('configuracion.update', $clave) }}" method="POST" class="space-y-3" data-editor-email-form
    data-tipo="{{ $tipo }}" data-upload-url="{{ route('configuracion.email.imagen') }}">
    @csrf @method('PUT')

    <div class="flex flex-wrap items-center gap-2 text-xs">
        <span class="font-medium text-gray-600">Comodines:</span>
        @foreach (Configuracion::EMAIL_COMODINES as $comodin => $descripcion)
            <button type="button" data-comodin="{{ $comodin }}" title="{{ $descripcion }}"
                class="px-2 py-1 rounded border border-gray-300 bg-gray-50 hover:bg-gray-100 font-mono">{{ $comodin }}</button>
        @endforeach
    </div>

    <textarea name="{{ $clave }}" data-editor-email>{{ $valor }}</textarea>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end">
        <div class="relative">
            <label for="preview-lab-buscar-{{ $tipo }}" class="block text-sm font-medium">Laboratorio para la vista previa</label>
            <input type="text" id="preview-lab-buscar-{{ $tipo }}" class="input-standard" autocomplete="off"
                placeholder="Buscar por código (vacío = primer laboratorio)" data-buscar-lab
                data-url="{{ route('configuracion.email.laboratorios') }}">
            <input type="hidden" id="preview-lab-{{ $tipo }}" value="">
            <ul class="mt-1 w-full max-h-56 overflow-auto bg-white border border-gray-200 rounded-lg shadow hidden"
                data-lista-lab></ul>
        </div>
        <div class="flex flex-wrap gap-2 md:justify-end">
            <button type="button" class="btn-secondary" data-previsualizar>
                <i class="fas fa-eye mr-1"></i>Previsualizar correo completo
            </button>
        </div>
    </div>

    <div class="flex flex-wrap justify-between gap-2 pt-2">
        <button type="button" class="btn-secondary" data-restaurar>
            <i class="fas fa-undo mr-1"></i>Restaurar contenido original
        </button>
        <button type="submit" class="btn-primary"><i class="fas fa-save mr-1"></i>Guardar</button>
    </div>
</form>

{{-- Vista previa con el contenido actual del editor (pestaña nueva, no guarda) --}}
<form id="form-preview-{{ $tipo }}" action="{{ route('configuracion.email.preview', $tipo) }}" method="POST"
    target="_blank" class="hidden">
    @csrf
    <input type="hidden" name="html">
    <input type="hidden" name="laboratorio_id">
</form>

<template id="email-original-{{ $tipo }}">{!! $original !!}</template>
