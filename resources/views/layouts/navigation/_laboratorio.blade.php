@php
    use App\Models\Configuracion;
    use App\Models\NotificacionLab;
    use App\Models\RecursoLaboratorio;

    $user = Auth::user();
    $lab = $user->laboratorio;
    $notifNoLeidas = NotificacionLab::query()
        ->paraLaboratorio($lab?->id ?? 0)
        ->pendientes()
        ->count();
    $hayRecursos = RecursoLaboratorio::active()->exists();
    $enPeriodoInscripcion = Configuracion::esPeriodoInscripcion();
    $tieneInscripcionGestionActual = $lab?->tieneIscripcionGestionActual() ?? false;
@endphp

{{-- ============ PRINCIPAL ============ --}}
<div class="space-y-1">
    <x-nav.link href="{{ route('dashboard') }}" icon="fa-home" label="Escritorio" route="dashboard" />
    <x-nav.link href="{{ route('lab.notificaciones.bandeja') }}" icon="fa-bell"
        label="Mis notificaciones" :badge="$notifNoLeidas" route="lab.notificaciones.*" />
</div>

{{-- ============ MI LABORATORIO ============ --}}
<div class="space-y-1 mt-4">
    <x-nav.dropdown icon="fa-building" label="Mi laboratorio"
        :routes="['lab.profile', 'lab.profile.edit']">
        <x-nav.sublink href="{{ route('lab.profile') }}" icon="fa-id-card-alt"
            label="Perfil de laboratorio" route="lab.profile" />
        @if ($enPeriodoInscripcion)
            <x-nav.sublink href="{{ route('lab.profile.edit') }}" icon="fa-user-edit"
                label="Actualizar tu información" route="lab.profile.edit" />
        @endif
    </x-nav.dropdown>

    <x-nav.dropdown icon="fa-file-signature" label="Inscripciones"
        :routes="['lab.ins.*', 'formulario_contrato']">
        <x-nav.sublink href="{{ route('lab.ins.index') }}" icon="fa-file-alt"
            label="Mis inscripciones" route="lab.ins.*" />
        @if ($tieneInscripcionGestionActual)
            <x-nav.sublink href="{{ route('formulario_contrato') }}" icon="fa-file-contract"
                label="Contrato" target="_blank" />
        @endif
    </x-nav.dropdown>

    <x-nav.dropdown icon="fa-flask" label="Resultados"
        :routes="['lab.inscritos-ensayos.*', 'lab.ensayos-informes.*']">
        <x-nav.sublink href="{{ route('lab.inscritos-ensayos.index') }}" icon="fa-check-circle"
            label="Registrar resultados" route="lab.inscritos-ensayos.*" />
        <x-nav.sublink href="{{ route('lab.ensayos-informes.index') }}" icon="fa-file-alt"
            label="Ver informes" route="lab.ensayos-informes.*" />
    </x-nav.dropdown>
</div>

{{-- ============ CERTIFICADOS Y RECURSOS ============ --}}
<div class="space-y-1 mt-4">
    <x-nav.link href="{{ route('lab.certificados.disponibles.index') }}" icon="fa-medal"
        label="Mis certificados" route="lab.certificados.*" />
    @if ($hayRecursos)
        <x-nav.link href="{{ route('lab.recursos.index') }}" icon="fa-folder-open"
            label="Recursos" route="lab.recursos.*" />
    @endif
</div>
