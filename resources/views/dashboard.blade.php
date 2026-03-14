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

        {{-- Inscripciones aprobadas última gestión --}}
        <div class="bg-white rounded-lg shadow-sm border p-5 mb-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-4 flex items-center gap-2">
                <i class="fas fa-file-signature text-indigo-500"></i> Inscripción aprobada
                @if ($ultimaGestionAprobada)
                    <span class="text-gray-400 font-normal">- Gestión {{ $ultimaGestionAprobada }}</span>
                @endif
            </h3>

            @if ($inscripcionesAprobadas->isEmpty())
                <div class="flex flex-col items-center py-6 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">No tienes inscripciones aprobadas.</p>
                    @if ($periodoInscripcion)
                        <a href="{{ route('lab.ins.index') }}"
                           class="mt-3 text-xs text-indigo-600 hover:underline font-medium">
                            Ir a inscribirme &rarr;
                        </a>
                    @endif
                </div>
            @else
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                        <i class="fas fa-check text-[10px]"></i> Aprobada
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900">
                            Bs. {{ number_format($totalAprobado, 2) }}
                        </span>
                        @if ($pagadoAprobado)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                <i class="fas fa-check text-[10px]"></i> Pagado
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                <i class="fas fa-exclamation text-[10px]"></i> Saldo: Bs. {{ number_format($saldoTotal, 2) }}
                            </span>
                        @endif
                    </div>
                </div>

                <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Paquetes inscritos</p>
                <ul class="space-y-1">
                    @foreach ($paquetesAprobados as $detalle)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <i class="fas fa-box-open text-indigo-400 mt-0.5 text-xs shrink-0"></i>
                            {{ $detalle->descripcion_paquete }}
                        </li>
                    @endforeach
                </ul>

                @if (!$pagadoAprobado)
                    <p class="text-xs text-red-500 mt-3 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        Tienes un saldo pendiente de <strong>Bs. {{ number_format($saldoTotal, 2) }}</strong>. Regulariza tu pago para mantener tu inscripción activa.
                    </p>
                @endif
            @endif
        </div>

        {{-- Pendientes de acción --}}
        @if ($pendientes->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border p-5 mb-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-3 flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-amber-500"></i> Pendientes de acción
                <span class="ml-auto bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $pendientes->where('completado', false)->count() }} pendiente(s)
                </span>
            </h3>
            <div class="space-y-2">
                @foreach ($pendientes as $item)
                    <div class="flex items-center justify-between rounded-lg px-3 py-2.5 border
                        {{ $item['completado'] ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0
                                {{ $item['completado'] ? 'bg-green-100' : 'bg-amber-100' }}">
                                @if ($item['completado'])
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                @elseif ($item['tipo'] === 'resultados')
                                    <i class="fas fa-chart-bar text-amber-600 text-xs"></i>
                                @else
                                    <i class="fas fa-file-alt text-amber-600 text-xs"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-semibold {{ $item['completado'] ? 'text-green-700' : 'text-gray-800' }}">
                                    {{ $item['tipo'] === 'resultados' ? 'Envío de resultados' : 'Informe técnico' }}
                                    @if ($item['completado'])
                                        <span class="font-normal text-green-600">- Completado</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $item['ciclo']->ensayoAptitud?->descripcion }} · {{ $item['ciclo']->nombre }}
                                    @if (!$item['completado'])
                                        · <span class="text-amber-600 font-medium">Vence {{ $item['vence'] }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if (!$item['completado'])
                            <a href="{{ $item['ruta'] }}"
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 shrink-0 ml-2">
                                Ir <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Ciclo activo o próximo por ensayo --}}
        @if ($ensayosConCiclo->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border p-5 mb-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-4 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-indigo-500"></i> Estado de ciclos - Gestión {{ $gestion }}
            </h3>
            <div class="space-y-3">
                @foreach ($ensayosConCiclo as $item)
                    @php
                        $ciclo        = $item['ciclo'];
                        $ea           = $item['ensayo'];
                        $estadoCiclo  = $item['estadoCiclo'];
                        $hoy          = \Carbon\Carbon::today();
                        $enMuestras   = $ciclo->fecha_inicio_envio_muestras && $ciclo->fecha_fin_envio_muestras
                                        && $hoy->between($ciclo->fecha_inicio_envio_muestras, $ciclo->fecha_fin_envio_muestras);
                        $enResultados = $ciclo->enPeriodoEnvioResultados();
                    @endphp
                    <div class="border rounded-lg p-3 {{ $estadoCiclo === 'activo' ? 'border-indigo-200 bg-indigo-50/30' : 'border-gray-200' }}">
                        <div class="flex items-start justify-between mb-2 gap-2">
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide leading-none mb-0.5">{{ $ea->descripcion }}</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $ciclo->nombre }}</p>
                            </div>
                            @if ($estadoCiclo === 'activo')
                                @if ($enMuestras)
                                    <span class="shrink-0 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                                        <i class="fas fa-circle text-[6px]"></i> En curso · Muestras
                                    </span>
                                @elseif ($enResultados)
                                    <span class="shrink-0 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        <i class="fas fa-circle text-[6px]"></i> En curso · Resultados
                                    </span>
                                @endif
                            @else
                                <span class="shrink-0 px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">
                                    <i class="fas fa-clock text-[10px]"></i> Próximo
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="flex flex-col gap-0.5 {{ $enMuestras ? 'text-indigo-700 font-semibold' : 'text-gray-500' }}">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-vial {{ $enMuestras ? 'text-indigo-400' : 'text-gray-300' }} text-[10px]"></i> Envío de muestras
                                </span>
                                <span>{{ $ciclo->fecha_inicio_envio_muestras_show }}</span>
                                <span class="{{ $enMuestras ? 'text-indigo-400' : 'text-gray-400' }}">→ {{ $ciclo->fecha_fin_envio_muestras_show }}</span>
                            </div>
                            <div class="flex flex-col gap-0.5 {{ $enResultados ? 'text-blue-700 font-semibold' : 'text-gray-500' }}">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-chart-bar {{ $enResultados ? 'text-blue-400' : 'text-gray-300' }} text-[10px]"></i> Envío de resultados
                                </span>
                                <span>{{ $ciclo->fecha_inicio_envio_resultados_show }}</span>
                                <span class="{{ $enResultados ? 'text-blue-400' : 'text-gray-400' }}">→ {{ $ciclo->fecha_fin_envio_resultados_show }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

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
                                {{-- Ciclo activo o próximo --}}
                            @if ($ea->ciclo_dashboard)
                                @php
                                    $ciclo        = $ea->ciclo_dashboard;
                                    $hoy          = \Carbon\Carbon::today();
                                    $enMuestras   = $ciclo->fecha_inicio_envio_muestras && $ciclo->fecha_fin_envio_muestras
                                                    && $hoy->between($ciclo->fecha_inicio_envio_muestras, $ciclo->fecha_fin_envio_muestras);
                                    $enResultados = $ciclo->enPeriodoEnvioResultados();
                                    $enReporte    = $ciclo->fecha_inicio_envio_reporte && $ciclo->fecha_fin_envio_reporte
                                                    && $hoy->between($ciclo->fecha_inicio_envio_reporte, $ciclo->fecha_fin_envio_reporte);
                                    $total        = $ea->total_inscritos ?? 0;
                                    $recibidos    = $ciclo->resultados_recibidos ?? 0;
                                    $informes     = $ciclo->informes_recibidos ?? 0;
                                    $pct          = $total > 0 ? round(($recibidos / $total) * 100) : 0;
                                @endphp
                                <div class="mt-3 bg-gray-50 rounded-lg px-3 py-2.5 border border-gray-100">
                                    {{-- Nombre + estado --}}
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700">{{ $ciclo->nombre }}</span>
                                        @if ($ea->estado_ciclo === 'activo')
                                            @if ($enMuestras)
                                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px] font-medium">Muestras · hasta {{ $ciclo->fecha_fin_envio_muestras_show }}</span>
                                            @elseif ($enResultados)
                                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-medium">Resultados · hasta {{ $ciclo->fecha_fin_envio_resultados_show }}</span>
                                            @elseif ($enReporte)
                                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-medium">Reporte · hasta {{ $ciclo->fecha_fin_envio_reporte_show }}</span>
                                            @endif
                                        @else
                                            <span class="px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded text-[10px]">
                                                <i class="fas fa-clock text-[8px]"></i> Próximo · desde {{ $ciclo->fecha_inicio_envio_muestras_show }}
                                            </span>
                                        @endif
                                    </div>
                                    {{-- Fecha de reporte --}}
                                    @if ($ciclo->fecha_inicio_envio_reporte && $ciclo->fecha_fin_envio_reporte)
                                        <div class="flex items-center gap-1 text-[10px] text-gray-500 mb-2">
                                            <i class="fas fa-file-alt text-green-400"></i>
                                            Emisión de reporte: {{ $ciclo->fecha_inicio_envio_reporte_show }} → {{ $ciclo->fecha_fin_envio_reporte_show }}
                                        </div>
                                    @endif
                                    {{-- Barra de progreso resultados --}}
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-[10px] text-gray-500 shrink-0">{{ $recibidos }}/{{ $total }} resultados</span>
                                    </div>
                                </div>
                            @endif

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

        {{-- Actividad operativa --}}
        @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA]))
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">

            {{-- Ciclos --}}
            <div class="bg-white rounded-lg shadow-sm border p-5">
                <h3 class="text-sm font-semibold text-gray-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-indigo-500"></i> Ciclos - Gestión {{ $gestion }}
                </h3>
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $totalCiclos }}</p>
                        <p class="text-xs text-gray-500">Registrados</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $ciclosHabilitados }}</p>
                        <p class="text-xs text-gray-500">Habilitados</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $ciclosEnCurso }}</p>
                        <p class="text-xs text-gray-500">En curso hoy</p>
                    </div>
                </div>
                {{-- Lista ciclos activos hoy --}}
                @if ($ciclosActivosHoy->isNotEmpty())
                <div class="space-y-1.5 border-t pt-3">
                    @foreach ($ciclosActivosHoy as $ca)
                        @php
                            $hoyC = \Carbon\Carbon::today();
                            $enM  = $ca->fecha_inicio_envio_muestras && $ca->fecha_fin_envio_muestras
                                    && $hoyC->between($ca->fecha_inicio_envio_muestras, $ca->fecha_fin_envio_muestras);
                            $enR  = $ca->enPeriodoEnvioResultados();
                            $enRp = $ca->fecha_inicio_envio_reporte && $ca->fecha_fin_envio_reporte
                                    && $hoyC->between($ca->fecha_inicio_envio_reporte, $ca->fecha_fin_envio_reporte);
                        @endphp
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-700 truncate max-w-[55%]">
                                {{ $ca->ensayoAptitud?->descripcion }} · {{ $ca->nombre }}
                            </span>
                            @if ($enM)
                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-medium shrink-0">Muestras</span>
                            @elseif ($enR)
                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded font-medium shrink-0">Resultados</span>
                            @elseif ($enRp)
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded font-medium shrink-0">Reporte</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                @else
                    <p class="text-xs text-gray-400 italic">Ningún ciclo activo hoy.</p>
                @endif
                <div class="mt-3 pt-3 border-t">
                    <a href="{{ route('admin.formularios.ea') }}"
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                        <i class="fas fa-arrow-right text-[10px]"></i> Gestionar ensayos y ciclos
                    </a>
                </div>
            </div>

            {{-- Resultados e Informes --}}
            <div class="bg-white rounded-lg shadow-sm border p-5">
                <h3 class="text-sm font-semibold text-gray-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-inbox text-indigo-500"></i> Recepción - Gestión {{ $gestion }}
                    @if (Gate::check(Permiso::ADMIN) && !Gate::check(Permiso::JEFE_PEEC))
                        <span class="ml-auto text-[10px] text-gray-400 font-normal italic">solo lectura</span>
                    @endif
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-chart-bar text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-800">{{ $totalResultados }}</p>
                            <p class="text-xs text-gray-500">Resultados enviados por laboratorios</p>
                        </div>
                        @if (Gate::check(Permiso::JEFE_PEEC))
                            <a href="{{ route('reportes.resultados.ensayos') }}"
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium shrink-0">
                                Ver <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-file-alt text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-800">{{ $totalInformes }}</p>
                            <p class="text-xs text-gray-500">Reportes emitidos por responsables de EA</p>
                        </div>
                        @if (Gate::check(Permiso::JEFE_PEEC))
                            <a href="{{ route('informe.tecnico.ensayos') }}"
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium shrink-0">
                                Ver <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    </div>
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
