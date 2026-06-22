@php
    use App\Models\Permiso;
    use App\Models\Configuracion;

    $user = Auth::user();
    $esResponsable = Gate::any([Permiso::RESPONSABLE]);
    $ensayosResponsable = $esResponsable
        ? $user->load(['responsablesEA', 'responsablesEA.paquete'])->responsablesEA
        : collect();
    $habilitadoCargarCertificado = Configuracion::estaHabilitadoCargarCertificado();

    $verNotificaciones = Gate::any([Permiso::ADMIN, Permiso::JEFE_PEEC, Permiso::GESTION_NOTIFICACIONES]);

    // Operación
    $verOperacion = Gate::any([
        Permiso::ADMIN, Permiso::JEFE_PEEC,
        Permiso::GESTION_INSCRIPCIONES, Permiso::GESTION_LABORATORIO,
        Permiso::GESTION_CERTIFICADOS,
    ]);
    $verCertificados = Gate::any([Permiso::ADMIN, Permiso::GESTION_CERTIFICADOS]);
    $verReportes = Gate::any([
        Permiso::ADMIN, Permiso::JEFE_PEEC, Permiso::RESPONSABLE,
        Permiso::GESTION_INSCRIPCIONES, Permiso::GESTION_PAGOS,
        Permiso::GESTION_LABORATORIO, Permiso::GESTION_CERTIFICADOS,
    ]);

    // Datos maestros (split)
    $verProgramasEa = Gate::any([Permiso::ADMIN, Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA]);
    $verTiposLab = Gate::any([Permiso::ADMIN]);
    $verUbicacion = Gate::any([Permiso::ADMIN, Permiso::GESTION_GEOGRAFICA]);

    // Configuración (split)
    $verUsuarios = Gate::any([Permiso::ADMIN, Permiso::GESTION_USUARIO]);
    $verConfiguracionGeneral = Gate::any([Permiso::ADMIN, Permiso::CONFIGURACION]);
    $verRecursos = Gate::any([Permiso::ADMIN]);
    $verAuditoria = Gate::allows(Permiso::VER_AUDITORIA);

    $nombreEnsayoConPaquete = function ($ea) {
        $paquete = $ea->paquete;
        if (!$paquete) return $ea->descripcion;
        $eaDesc = mb_strtolower(trim(\Normalizer::normalize($ea->descripcion, Normalizer::FORM_C)), 'UTF-8');
        $paqDesc = mb_strtolower(trim(\Normalizer::normalize($paquete->descripcion, Normalizer::FORM_C)), 'UTF-8');
        return $eaDesc === $paqDesc ? $ea->descripcion : "{$paquete->descripcion} - {$ea->descripcion}";
    };
@endphp

{{-- ============ PRINCIPAL ============ --}}
<div class="space-y-1">
    <x-nav.link href="{{ route('dashboard') }}" icon="fa-home" label="Escritorio" route="dashboard" />
    @if ($verNotificaciones)
        <x-nav.link href="{{ route('admin.notificaciones.index') }}" icon="fa-paper-plane"
            label="Notificaciones enviadas" route="admin.notificaciones.*" />
    @endif
</div>

{{-- ============ RESPONSABLE DE EA ============ --}}
@if ($esResponsable && $ensayosResponsable->isNotEmpty())
    <div class="space-y-1 mt-4">
        <x-nav.link href="{{ route('responsable.mis-ensayos') }}" icon="fa-vials"
            label="Mis ensayos asignados"
            :route="['responsable.mis-ensayos', 'ea.lab.inscritos', 'ea.lab.inscritos.*']" />

        @if ($habilitadoCargarCertificado)
            <x-nav.dropdown icon="fa-clipboard-check" label="Registrar evaluación"
                :routes="['ea.lab.certificados', 'ea.lab.desempeno.*', 'ea.lab.subir.ponderaciones']">
                @foreach ($ensayosResponsable as $ea)
                    <x-nav.sublink href="{{ route('ea.lab.certificados', $ea->id) }}" icon="fa-flask"
                        :label="$nombreEnsayoConPaquete($ea)" />
                @endforeach
            </x-nav.dropdown>
        @endif

        <x-nav.link href="{{ route('ea.formulario.index') }}" icon="fa-clipboard-list"
            label="Gestión de formularios" route="ea.formulario.*" />
    </div>
@endif

{{-- ============ OPERACIÓN ============ --}}
@if ($verOperacion || $verCertificados)
    <div class="space-y-1 mt-4">
        @if ($verOperacion)
            <x-nav.dropdown icon="fa-cogs" label="Operación PEEC"
                :routes="['inscripcion_paquete.*', 'laboratorio.*', 'admin.formularios.*', 'certificado-desempeno.*', 'list.cert.participacion.desemp']">
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
                    <x-nav.sublink href="{{ route('inscripcion_paquete.index') }}" icon="fa-file-signature"
                        label="Inscripciones a paquetes" route="inscripcion_paquete.*" />
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO, Permiso::GESTION_INSCRIPCIONES]))
                    <x-nav.sublink href="{{ route('laboratorio.index') }}" icon="fa-flask"
                        label="Laboratorios registrados" route="laboratorio.*" />
                @endif
                @can(Permiso::ADMIN)
                    <x-nav.sublink href="{{ route('admin.formularios.ea') }}" icon="fa-clipboard-list"
                        label="Formularios de EA" route="admin.formularios.*" />
                @endcan
                @if (Gate::any([Permiso::ADMIN, Permiso::JEFE_PEEC]))
                    <x-nav.sublink href="{{ route('certificado-desempeno.index') }}" icon="fa-tasks"
                        label="Administrar ensayos" route="certificado-desempeno.*" />
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_CERTIFICADOS]))
                    <x-nav.sublink href="{{ route('list.cert.participacion.desemp') }}" icon="fa-bullhorn"
                        label="Publicar certificados" route="list.cert.participacion.desemp" />
                @endif
            </x-nav.dropdown>
        @endif

        @if ($verCertificados)
            <x-nav.dropdown icon="fa-certificate" label="Certificados"
                :routes="['admin.certificado.*', 'plantillas-certificados.*', 'configuracion.cerfificado']">
                <x-nav.sublink href="{{ route('admin.certificado.index') }}" icon="fa-file-alt"
                    label="Listado de certificados" route="admin.certificado.*" />
                <x-nav.sublink href="{{ route('plantillas-certificados.index') }}" icon="fa-award"
                    label="Plantillas" route="plantillas-certificados.*" />
                <x-nav.sublink href="{{ route('configuracion.cerfificado') }}" icon="fa-sliders-h"
                    label="Configuración" route="configuracion.cerfificado" />
            </x-nav.dropdown>
        @endif
    </div>
@endif

{{-- ============ REPORTES ============ --}}
@if ($verReportes)
    <div class="space-y-1 mt-4">
        <x-nav.dropdown icon="fa-chart-line" label="Reportes"
            :routes="['reportes.*', 'admin.reportes.*', 'admin.dashboard-peec', 'informe.tecnico.*']">
            @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO, Permiso::GESTION_INSCRIPCIONES]))
                <x-nav.sublink href="{{ route('reportes.inscripciones.index') }}" icon="fa-file-alt"
                    label="Inscripciones" route="reportes.inscripciones.*" />
            @endif
            @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_PAGOS, Permiso::GESTION_INSCRIPCIONES]))
                <x-nav.sublink href="{{ route('reportes.pagos.index') }}" icon="fa-money-bill-wave"
                    label="Pagos" route="reportes.pagos.*" />
            @endif
            @if (Gate::any([Permiso::RESPONSABLE, Permiso::JEFE_PEEC]))
                <x-nav.sublink href="{{ route('reportes.resultados.ensayos') }}" icon="fa-clipboard-list"
                    label="Resultados" route="reportes.resultados.*" />
            @endif
            @if (Gate::any([Permiso::ADMIN, Permiso::JEFE_PEEC, Permiso::GESTION_CERTIFICADOS, Permiso::RESPONSABLE]))
                <x-nav.sublink href="{{ route('admin.reportes.distribucion') }}" icon="fa-chart-bar"
                    label="Distribución por ensayo" route="admin.reportes.*" />
            @endif
            @if (Gate::any([Permiso::ADMIN, Permiso::JEFE_PEEC]))
                <x-nav.sublink href="{{ route('admin.dashboard-peec') }}" icon="fa-chart-pie"
                    label="Dashboard PEEC" route="admin.dashboard-peec" />
            @endif
            @can(Permiso::RESPONSABLE)
                <x-nav.sublink href="{{ route('informe.tecnico.ensayos') }}" icon="fa-stethoscope"
                    label="Informe técnico" route="informe.tecnico.*" />
            @endcan
        </x-nav.dropdown>
    </div>
@endif

{{-- ============ DATOS MAESTROS ============ --}}
@if ($verProgramasEa || $verTiposLab || $verUbicacion)
    <div class="space-y-1 mt-4">
        @if ($verProgramasEa)
            <x-nav.dropdown icon="fa-boxes" label="Programas y EA"
                :routes="['programa.*', 'area.*', 'paquete.*', 'ensayo_aptitud.*']">
                <x-nav.sublink href="{{ route('programa.index') }}" icon="fa-clipboard-list"
                    label="Programas" route="programa.*" />
                <x-nav.sublink href="{{ route('area.index') }}" icon="fa-layer-group"
                    label="Áreas" route="area.*" />
                <x-nav.sublink href="{{ route('paquete.index') }}" icon="fa-box-open"
                    label="Paquetes" route="paquete.*" />
                <x-nav.sublink href="{{ route('ensayo_aptitud.index') }}" icon="fa-vials"
                    label="Ensayos de aptitud" route="ensayo_aptitud.*" />
            </x-nav.dropdown>
        @endif

        @if ($verTiposLab)
            <x-nav.dropdown icon="fa-tags" label="Tipos de laboratorio"
                :routes="['nivel_laboratorio.*', 'tipo_laboratorio.*', 'categoria_laboratorio.*']">
                <x-nav.sublink href="{{ route('nivel_laboratorio.index') }}" icon="fa-signal"
                    label="Niveles" route="nivel_laboratorio.*" />
                <x-nav.sublink href="{{ route('tipo_laboratorio.index') }}" icon="fa-vial"
                    label="Tipos" route="tipo_laboratorio.*" />
                <x-nav.sublink href="{{ route('categoria_laboratorio.index') }}" icon="fa-tag"
                    label="Categorías" route="categoria_laboratorio.*" />
            </x-nav.dropdown>
        @endif

        @if ($verUbicacion)
            <x-nav.dropdown icon="fa-globe-americas" label="Ubicación"
                :routes="['pais.*', 'departamento.*', 'provincia.*', 'municipio.*']">
                <x-nav.sublink href="{{ route('pais.index') }}" icon="fa-flag"
                    label="Países" route="pais.*" />
                <x-nav.sublink href="{{ route('departamento.index') }}" icon="fa-map"
                    label="Departamentos" route="departamento.*" />
                <x-nav.sublink href="{{ route('provincia.index') }}" icon="fa-map-marked-alt"
                    label="Provincias" route="provincia.*" />
                <x-nav.sublink href="{{ route('municipio.index') }}" icon="fa-city"
                    label="Municipios" route="municipio.*" />
            </x-nav.dropdown>
        @endif
    </div>
@endif

{{-- ============ CONFIGURACIÓN ============ --}}
@if ($verUsuarios || $verConfiguracionGeneral || $verRecursos || $verAuditoria)
    <div class="space-y-1 mt-4">
        @if ($verUsuarios)
            <x-nav.dropdown icon="fa-users" label="Usuarios"
                :routes="['usuario.*', 'cargos.*', 'permiso.*']">
                <x-nav.sublink href="{{ route('usuario.index') }}" icon="fa-user"
                    label="Usuarios" route="usuario.*" />
                <x-nav.sublink href="{{ route('cargos.index') }}" icon="fa-user-tag"
                    label="Cargos" route="cargos.*" />
                <x-nav.sublink href="{{ route('permiso.index') }}" icon="fa-key"
                    label="Permisos" route="permiso.*" />
            </x-nav.dropdown>
        @endif

        @if ($verConfiguracionGeneral)
            <x-nav.dropdown icon="fa-sliders-h" label="Configuración"
                :routes="['configuracion.index', 'configuracion.update', 'contratos.*', 'formularios.*']">
                <x-nav.sublink href="{{ route('configuracion.index') }}" icon="fa-cogs"
                    label="Configuración general" route="configuracion.index" />
                <x-nav.sublink href="{{ route('contratos.index') }}" icon="fa-file-contract"
                    label="Configuración de contrato" route="contratos.*" />
                <x-nav.sublink href="{{ route('formularios.index') }}" icon="fa-file-alt"
                    label="Boleta de inscripción" route="formularios.*" />
            </x-nav.dropdown>
        @endif

        @if ($verRecursos)
            <x-nav.dropdown icon="fa-folder-open" label="Recursos"
                :routes="['galeria.*', 'recursos_lab.*']">
                <x-nav.sublink href="{{ route('galeria.index') }}" icon="fa-images"
                    label="Galería de archivos" route="galeria.*" />
                <x-nav.sublink href="{{ route('recursos_lab.index') }}" icon="fa-folder-open"
                    label="Recursos de laboratorio" route="recursos_lab.*" />
            </x-nav.dropdown>
        @endif

        @if ($verAuditoria)
            <x-nav.link href="{{ route('admin.auditoria.index') }}" icon="fa-history"
                label="Historial de acciones" route="admin.auditoria.*" />
        @endif
    </div>
@endif
