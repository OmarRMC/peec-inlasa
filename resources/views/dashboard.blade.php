@php 
use App\Models\Permiso;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
            <i class="fas fa-home text-indigo-500"></i> Escritorio
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto">
    @if ($tipo === 'laboratorio')

        {{-- Banner de bienvenida --}}
        <div class="bg-indigo-600 text-white rounded-lg px-6 py-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-widest text-indigo-200 mb-0.5">Laboratorio registrado</p>
                <h2 class="text-lg font-bold leading-tight">{{ $lab->nombre_lab }}</h2>
                <p class="text-indigo-200 text-sm mt-0.5">
                    Código: <strong class="text-white">{{ $lab->cod_lab }}</strong>
                    &nbsp;·&nbsp; Gestión: <strong class="text-white">{{ $gestion }}</strong>
                </p>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2">
                    @if ($lab->wapp_lab)
                        <span class="flex items-center gap-1.5 text-xs text-indigo-100">
                            <i class="fab fa-whatsapp text-green-300"></i>
                            {{ $lab->wapp_lab }}
                        </span>
                    @endif
                    @if ($lab->mail_lab)
                        <span class="flex items-center gap-1.5 text-xs text-indigo-100">
                            <i class="fas fa-envelope text-indigo-300"></i>
                            {{ $lab->mail_lab }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                @if ($periodoInscripcion)
                    <span class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-medium px-3 py-1 rounded-full">
                        <i class="fas fa-circle text-[6px]"></i> Período de inscripción abierto
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 bg-indigo-500 text-white text-xs font-medium px-3 py-1 rounded-full">
                        <i class="fas fa-lock text-[10px]"></i> Inscripciones cerradas
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

            {{-- Inscripción de la gestión actual --}}
            <div class="bg-white rounded-lg shadow-sm border p-5">
                <h3 class="text-sm font-semibold text-gray-600 mb-3 flex items-center gap-2">
                    <i class="fas fa-file-signature text-indigo-500"></i> Inscripción {{ $gestion }}
                </h3>
                @if ($inscripcionActual)
                    @php
                        $si = $inscripcionActual->status_inscripcion;
                        $colorInsc = match($si) {
                            \App\Models\Inscripcion::STATUS_APROBADO => 'bg-green-100 text-green-800',
                            \App\Models\Inscripcion::STATUS_EN_REVISION  => 'bg-yellow-100 text-yellow-800',
                            \App\Models\Inscripcion::STATUS_EN_OBSERVACION => 'bg-red-100 text-red-800',
                            \App\Models\Inscripcion::STATUS_VENCIDO => 'bg-orange-100 text-orange-800',
                            \App\Models\Inscripcion::STATUS_ANULADO => 'bg-gray-200 text-gray-600',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorInsc }}">
                            {{ \App\Models\Inscripcion::STATUS_INSCRIPCION[$si] ?? '—' }}
                        </span>
                    </div>
                    @if ($inscripcionActual->detalleInscripciones->isNotEmpty())
                        <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Paquetes inscritos</p>
                        <ul class="space-y-1 mb-3">
                            @foreach ($inscripcionActual->detalleInscripciones as $detalle)
                                <li class="text-sm text-gray-700 flex items-start gap-2">
                                    <i class="fas fa-box-open text-indigo-400 mt-0.5 text-xs"></i>
                                    {{ $detalle->descripcion_paquete }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <p class="text-xs text-gray-400">Fecha: {{ $inscripcionActual->fecha_inscripcion }}</p>
                @else
                    <div class="flex flex-col items-center py-4 text-center text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p class="text-sm">Sin inscripción para la gestión <strong>{{ $gestion }}</strong></p>
                        @if ($periodoInscripcion)
                            <a href="{{ route('lab.ins.index') }}"
                               class="mt-3 text-xs text-indigo-600 hover:underline font-medium">
                                Ir a inscribirme &rarr;
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Estado de cuenta --}}
            <div class="bg-white rounded-lg shadow-sm border p-5">
                <h3 class="text-sm font-semibold text-gray-600 mb-3 flex items-center gap-2">
                    <i class="fas fa-wallet text-indigo-500"></i> Estado de cuenta
                </h3>
                @if ($inscripcionActual)
                    @php
                        $pagado = $inscripcionActual->status_cuenta === \App\Models\Inscripcion::STATUS_PAGADO;
                    @endphp
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $pagado ? 'bg-green-100' : 'bg-red-100' }}">
                            <i class="fas {{ $pagado ? 'fa-check text-green-600' : 'fa-exclamation text-red-600' }}"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ $pagado ? 'text-green-700' : 'text-red-700' }}">
                                {{ $pagado ? 'Pago registrado' : 'Pago pendiente' }}
                            </p>
                            <p class="text-xs text-gray-400">Gestión {{ $gestion }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700">
                        Costo total:
                        <span class="font-bold text-gray-900">Bs. {{ number_format($inscripcionActual->costo_total, 2) }}</span>
                    </p>
                    @if (!$pagado)
                        <p class="text-xs text-red-500 mt-2">
                            <i class="fas fa-info-circle"></i>
                            Regulariza tu pago para mantener tu inscripción activa.
                        </p>
                    @endif
                @else
                    <div class="flex flex-col items-center py-4 text-center text-gray-400">
                        <i class="fas fa-receipt text-3xl mb-2"></i>
                        <p class="text-sm">No hay cuenta activa para {{ $gestion }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div class="bg-white rounded-lg shadow-sm border p-5">
            <h3 class="text-sm font-semibold text-gray-600 mb-4 flex items-center gap-2">
                <i class="fas fa-bolt text-indigo-500"></i> Accesos rápidos
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('lab.profile') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-id-card-alt text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Perfil del laboratorio</span>
                </a>
                <a href="{{ route('lab.ins.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-file-alt text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Mis inscripciones</span>
                </a>
                <a href="{{ route('lab.certificados.disponibles.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-medal text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Mis certificados</span>
                </a>
                @if ($lab->tieneIscripcionGestionActual())
                    <a href="{{ route('formulario_contrato') }}" target="_blank"
                       class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                        <i class="fas fa-file-contract text-indigo-500 text-xl"></i>
                        <span class="text-xs text-gray-600 font-medium">Ver contrato</span>
                    </a>
                @endif
            </div>
        </div>
    @elseif ($tipo === 'responsable')

        {{-- Banner --}}
        <div class="bg-indigo-600 text-white rounded-lg px-6 py-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-widest text-indigo-200 mb-0.5">Responsable de EA</p>
                <h2 class="text-lg font-bold">{{ Auth::user()->nombre }} {{ Auth::user()->ap_paterno }}</h2>
                <p class="text-indigo-200 text-sm mt-0.5">Gestión: <strong class="text-white">{{ $gestion }}</strong></p>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2">
                    @if (Auth::user()->email)
                        <span class="flex items-center gap-1.5 text-xs text-indigo-100">
                            <i class="fas fa-envelope text-indigo-300"></i>
                            {{ Auth::user()->email }}
                        </span>
                    @endif
                    @if (Auth::user()->telefono)
                        <span class="flex items-center gap-1.5 text-xs text-indigo-100">
                            <i class="fas fa-phone text-indigo-300"></i>
                            {{ Auth::user()->telefono }}
                        </span>
                    @endif
                </div>
            </div>
            @if ($certificadosHabilitados)
                <span class="inline-flex items-center gap-1 bg-green-500 text-white text-xs font-medium px-3 py-1 rounded-full self-start sm:self-auto">
                    <i class="fas fa-circle text-[6px]"></i> Registro de evaluaciones abierto
                </span>
            @endif
        </div>

        {{-- Ensayos de aptitud asignados --}}
        <div class="bg-white rounded-lg shadow-sm border p-5">
            <h3 class="text-sm font-semibold text-gray-600 mb-4 flex items-center gap-2">
                <i class="fas fa-flask text-indigo-500"></i>
                Ensayos de aptitud asignados
                <span class="ml-auto bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $ensayoAps->count() }}
                </span>
            </h3>

            @if ($ensayoAps->isEmpty())
                <div class="text-center py-6 text-gray-400">
                    <i class="fas fa-flask text-3xl mb-2"></i>
                    <p class="text-sm">No tienes ensayos de aptitud asignados para la gestión {{ $gestion }}.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($ensayoAps as $ea)
                        @php
                            $paquete = $ea->paquete;
                            $mismoNombre = mb_strtolower(trim($ea->descripcion)) === mb_strtolower(trim($paquete->descripcion ?? ''));
                        @endphp
                        <div class="border rounded-lg p-4 hover:border-indigo-300 hover:bg-indigo-50 transition">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-vial text-indigo-600 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if (!$mismoNombre)
                                        <p class="text-xs text-gray-400 uppercase tracking-wide truncate">{{ $paquete->descripcion }}</p>
                                    @endif
                                    <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $ea->descripcion }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-3 flex-wrap">
                                <a href="{{ route('ea.lab.inscritos', $ea->id) }}"
                                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                                    <i class="fas fa-users"></i> Ver laboratorios inscritos
                                </a>
                                @if ($certificadosHabilitados)
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('ea.lab.certificados', $ea->id) }}"
                                       class="text-xs text-green-600 hover:text-green-800 font-medium flex items-center gap-1">
                                        <i class="fas fa-clipboard-check"></i> Registrar evaluación
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    @else
        {{-- Banner --}}
        <div class="bg-indigo-600 text-white rounded-lg px-6 py-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-widest text-indigo-200 mb-0.5">
                    {{ Auth::user()->cargo?->nombre_cargo ?? 'Personal PEEC' }}
                </p>
                <h2 class="text-lg font-bold">{{ Auth::user()->nombre }} {{ Auth::user()->ap_paterno }}</h2>
                <p class="text-indigo-200 text-sm mt-0.5">Gestión activa: <strong class="text-white">{{ $gestion }}</strong></p>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2">
                    @if (Auth::user()->email)
                        <span class="flex items-center gap-1.5 text-xs text-indigo-100">
                            <i class="fas fa-envelope text-indigo-300"></i>
                            {{ Auth::user()->email }}
                        </span>
                    @endif
                    @if (Auth::user()->telefono)
                        <span class="flex items-center gap-1.5 text-xs text-indigo-100">
                            <i class="fas fa-phone text-indigo-300"></i>
                            {{ Auth::user()->telefono }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex flex-col gap-1.5 text-xs self-start sm:self-auto">
                @if ($periodoInscripcion)
                    <span class="inline-flex items-center gap-1 bg-green-500 text-white font-medium px-3 py-1 rounded-full">
                        <i class="fas fa-circle text-[6px]"></i> Inscripciones abiertas
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 bg-indigo-500 text-white font-medium px-3 py-1 rounded-full">
                        <i class="fas fa-lock text-[10px]"></i> Inscripciones cerradas
                    </span>
                @endif
                @if ($certificadosHabilitados)
                    <span class="inline-flex items-center gap-1 bg-yellow-400 text-yellow-900 font-medium px-3 py-1 rounded-full">
                        <i class="fas fa-certificate text-[10px]"></i> Período de certificados abierto
                    </span>
                @endif
            </div>
        </div>

        {{-- Estadísticas --}}
        @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES, Permiso::GESTION_LABORATORIO]))
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">

            <div class="bg-white rounded-lg shadow-sm border p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-building text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalLabs }}</p>
                    <p class="text-xs text-gray-500">Laboratorios activos</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-file-signature text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalInscripciones }}</p>
                    <p class="text-xs text-gray-500">Inscripciones {{ $gestion }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-hourglass-half text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $enRevision }}</p>
                    <p class="text-xs text-gray-500">En revisión</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $aprobadas }}</p>
                    <p class="text-xs text-gray-500">Aprobadas</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $enObservacion }}</p>
                    <p class="text-xs text-gray-500">En observación</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-ban text-gray-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $anuladas }}</p>
                    <p class="text-xs text-gray-500">Anuladas</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-wallet text-orange-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $deudores }}</p>
                    <p class="text-xs text-gray-500">Con pago pendiente</p>
                </div>
            </div>

        </div>
        @endif

        {{-- Accesos rápidos --}}
        <div class="bg-white rounded-lg shadow-sm border p-5">
            <h3 class="text-sm font-semibold text-gray-600 mb-4 flex items-center gap-2">
                <i class="fas fa-bolt text-indigo-500"></i> Accesos rápidos
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO, Permiso::GESTION_INSCRIPCIONES]))
                <a href="{{ route('laboratorio.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-flask text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Laboratorios</span>
                </a>
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
                <a href="{{ route('inscripcion_paquete.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-file-signature text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Inscripciones</span>
                </a>
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES, Permiso::GESTION_LABORATORIO]))
                <a href="{{ route('reportes.inscripciones.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-chart-bar text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Reportes</span>
                </a>
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_CERTIFICADOS]))
                <a href="{{ route('admin.certificado.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-certificate text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Certificados</span>
                </a>
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_USUARIO]))
                <a href="{{ route('usuario.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-users text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Usuarios</span>
                </a>
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::CONFIGURACION]))
                <a href="{{ route('configuracion.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-cogs text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Configuración</span>
                </a>
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::JEFE_PEEC]))
                <a href="{{ route('certificado-desempeno.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-lg border hover:border-indigo-400 hover:bg-indigo-50 transition text-center">
                    <i class="fas fa-tasks text-indigo-500 text-xl"></i>
                    <span class="text-xs text-gray-600 font-medium">Administrar Ensayos</span>
                </a>
                @endif
            </div>
        </div>

    @endif

    </div>{{-- /max-w-5xl --}}

</x-app-layout>
