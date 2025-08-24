@php
    use App\Models\Permiso;
    use App\Models\Configuracion;
@endphp
<!-- Navigation -->
<nav class="flex-1 px-2 py-4 space-y-1 text-sm overflow-y-auto">
    @if (true || Gate::any([Permiso::ADMIN, Permiso::VER_ESCRITORIO, Permiso::LABORATORIO]))
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
            <i class="fas fa-home w-5 text-indigo-500"></i>
            <span>Escritorio</span>
        </a>
    @endif
    <!-- Gestión de Inscripciones -->
    @if (Gate::any([Permiso::ADMIN]))
        <div>
            <button @click="openMenu !== 1 ? openMenu = 1 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-file-signature w-5 text-indigo-500"></i>
                <span>Inscripciones</span>
                <i class="fas ml-auto" :class="openMenu === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 1" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                {{-- <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-list"></i> Ver Inscripciones</a> --}}
                <a href="{{ route('formularios.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded">
                    <i class="fas fa-file-alt"></i> Formularios
                </a>
                {{-- <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-file-alt"></i> Documentos</a> --}}
            </div>
        </div>
    @endif

    @if (Gate::any([Permiso::LABORATORIO]))
        <div>
            <button @click="openMenu !== 102 ? openMenu = 102 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-vials w-5 text-indigo-500"></i>
                <span>Laboratorio</span>
                <i class="fas ml-auto" :class="openMenu === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 102" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                <a href="{{ route('lab.profile') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded">
                    <i class="fas fa-id-card-alt w-4 mr-1 text-indigo-500"></i> Perfil de laboratorio
                </a>
                @if (Configuracion::esPeriodoInscripcion())
                    <a href="{{ route('lab.profile.edit') }}"
                        class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded">
                        <i class="fas fa-user-edit w-4 mr-1 text-indigo-500"></i> Actualizar tu información
                    </a>
                @endif
            </div>
        </div>
    @endif

    @if (Gate::any([Permiso::LABORATORIO]))
        <div>
            <button @click="openMenu !== 103 ? openMenu = 103 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-vials w-5 text-indigo-500"></i>
                <span>Gestion de inscripciones</span>
                <i class="fas ml-auto" :class="openMenu === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 103" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                <a href="{{ route('lab.ins.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded">
                    <i class="fas fa-file-alt w-4 mr-1 text-indigo-500"></i>
                    Listado de inscripciones
                </a>
                {{-- href="{{ route('formulario_contrato_lab.pdf') }}" --}}
                @if (Auth::user()->laboratorio->tieneIscripcionGestionActual())
                    <a href="{{ route('formulario_contrato') }}" target="_blank"
                        class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded">
                        <i class="fas fa-file-alt w-4 mr-1 text-indigo-500"></i> Contrato
                    </a>
                @endif
            </div>
        </div>
    @endif
    @if (Gate::any([Permiso::RESPONSABLE]))
        @php
            $user = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete']);
            $ensayoAps = $user->responsablesEA;
        @endphp
        <div>
            <button @click="openMenu !== 20 ? openMenu = 20 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-vials w-5 text-indigo-500"></i>
                <span>Gestión de Ensayos de Aptitud</span>
                <i class="fas ml-auto" :class="openMenu === 20 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 20" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                @foreach ($ensayoAps as $ea)
                    @php
                        $paquete = $ea->paquete;
                        $eaDesc = mb_strtolower(
                            trim(\Normalizer::normalize($ea->descripcion, Normalizer::FORM_C)),
                            'UTF-8',
                        );
                        $paqueteDesc = mb_strtolower(
                            trim(\Normalizer::normalize($paquete->descripcion, Normalizer::FORM_C)),
                            'UTF-8',
                        );
                    @endphp
                    <div>
                        <div class="font-semibold text-indigo-700 px-1 py-1">
                            {{-- <i class="fas fa-vial"></i> EA: {{ $ea->descripcion }} --}}
                            <a href="{{ route('ea.lab.inscritos', $ea->id) }}"
                                class="block px-1 py-1 text-[10px] text-gray-600 hover:bg-indigo-100 rounded">
                                <i class="fas fa-flask w-4 text-indigo-500"></i>
                                <p class="inline">
                                    @if ($eaDesc == $paqueteDesc)
                                        <span class="text-[12px]">
                                            {{ $ea->descripcion }}
                                        </span>
                                    @else
                                        {{ $paquete->descripcion }} <br>
                                        <span class="text-[12px]">
                                            {{ $ea->descripcion }}
                                        </span>
                                    @endif
                                </p>
                            </a>
                        </div>
                        {{-- @foreach ($ea->inscripciones as $inscripcion)
                            <a href="{{ route('ruta.lab.resultados', $inscripcion->laboratorio->id) }}"
                                class="block px-5 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded">
                                <i class="fas fa-flask w-4 mr-1 text-indigo-500"></i>
                                {{ $inscripcion->laboratorio->nombre }}
                            </a>
                        @endforeach --}}
                    </div>
                @endforeach
            </div>
            @if (Configuracion::estaHabilitadoCargarCertificado())
                <button @click="openMenu !== 21 ? openMenu = 21 : openMenu = null"
                    class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                    <i class="fas fa-vials w-5 text-indigo-500"></i>
                    <span>Certificados</span>
                    <i class="fas ml-auto" :class="openMenu === 20 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div x-show="openMenu === 21" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                    @php
                        $user = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete']);
                        $ensayoAps = $user->responsablesEA;
                    @endphp
                    @foreach ($ensayoAps as $ea)
                        @php
                            $paquete = $ea->paquete;
                            $eaDesc = mb_strtolower(
                                trim(\Normalizer::normalize($ea->descripcion, Normalizer::FORM_C)),
                                'UTF-8',
                            );
                            $paqueteDesc = mb_strtolower(
                                trim(\Normalizer::normalize($paquete->descripcion, Normalizer::FORM_C)),
                                'UTF-8',
                            );
                        @endphp
                        <div>
                            <div class="font-semibold text-indigo-700 px-3 py-1">
                                {{-- <i class="fas fa-vial"></i> EA: {{ $ea->descripcion }} --}}
                                <a href="{{ route('ea.lab.certificados', $ea->id) }}"
                                    class="block px-1 py-1 text-[10px] text-gray-600 hover:bg-indigo-100 rounded">
                                    <i class="fas fa-flask w-4 mr-1 text-indigo-500"></i>
                                    <p class="inline">
                                        @if ($eaDesc == $paqueteDesc)
                                            <span class="text-[12px]">
                                                {{ $ea->descripcion }}
                                            </span>
                                        @else
                                            {{ $paquete->descripcion }} <br>
                                            <span class="text-[12px]">
                                                {{ $ea->descripcion }}
                                            </span>
                                        @endif
                                    </p>
                                </a>
                            </div>
                            {{-- @foreach ($ea->inscripciones as $inscripcion)
                            <a href="{{ route('ruta.lab.resultados', $inscripcion->laboratorio->id) }}"
                                class="block px-5 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded">
                                <i class="fas fa-flask w-4 mr-1 text-indigo-500"></i>
                                {{ $inscripcion->laboratorio->nombre }}
                            </a>
                        @endforeach --}}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif


    <!-- Certificados -->
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_CERTIFICADOS]))
        <div>
            <button @click="openMenu !== 2 ? openMenu = 2 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-certificate w-5 text-indigo-500"></i>
                <span>Certificados</span>
                <i class="fas ml-auto" :class="openMenu === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 2" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                <a href="{{ route('configuracion.cerfificado') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded">
                    <i class="fas fa-cog"></i> Configuración
                </a>
                <a href="{{ route('list.cert.participacion.desemp') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-certificate"></i> Participación y Desempeño
                </a>
                <a href="{{ route('certificado-desempeno.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-medal"></i> Modificar el Desempeño
                </a>
            </div>
        </div>
    @endif
    @if (Gate::any([Permiso::LABORATORIO]))
        <div>
            <button @click="openMenu !== 300 ? openMenu = 300 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-certificate w-5 text-indigo-500"></i>
                <span>Certificados</span>
                <i class="fas ml-auto" :class="openMenu === 300 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 300" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                <a href="{{ route('lab.certificados.disponibles.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-medal"></i> Participación y Desempeño
                </a>
            </div>
        </div>
    @endif
    @if (Gate::any([Permiso::ADMIN, Permiso::CONFIGURACION]))
        <div>
            <a href="{{ route('configuracion.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
                <i class="fas fa-cogs w-5 text-indigo-500"></i>
                <span>Configuración</span>
            </a>
        </div>
    @endif
    @if (Gate::any([Permiso::ADMIN, Permiso::LABORATORIO]))
        <!-- Recursos Laboratorio -->
        <div>
            <button @click="openMenu !== 3 ? openMenu = 3 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-vials w-5 text-indigo-500"></i>
                <span>Recursos Lab.</span>
                <i class="fas ml-auto" :class="openMenu === 3 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 3" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                {{-- <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-file-contract"></i> Contrato 2025</a> --}}
                <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-bullhorn"></i> Convocatoria</a>
                <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-gavel"></i> Resolución</a>
                <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-file-alt"></i> Protocolos</a>
                <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-exclamation-triangle"></i> Quejas</a>
                <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-clipboard"></i> Formularios de Queja</a>
            </div>
        </div>
    @endif
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES, Permiso::GESTION_LABORATORIO]))
        <div>
            <button @click="openMenu !== 7 ? openMenu = 7 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-flask w-5 text-indigo-500"></i>
                <span>Gestión de Laboratorio</span>
                <i class="fas ml-auto" :class="openMenu === 7 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 7" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">

                @if (Gate::any([Permiso::ADMIN]))
                    <a href="{{ route('nivel_laboratorio.index') }}"
                        class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                        <i class="fas fa-layer-group"></i> Nivel de Laboratorio
                    </a>
                @endif
                @if (Gate::any([Permiso::ADMIN]))
                    <a href="{{ route('tipo_laboratorio.index') }}"
                        class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                        <i class="fas fa-vial"></i> Tipo de Laboratorio
                    </a>
                @endif
                @if (Gate::any([Permiso::ADMIN]))
                    <a href="{{ route('categoria_laboratorio.index') }}"
                        class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                        <i class="fas fa-tags"></i> Categoría
                    </a>
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO, Permiso::GESTION_INSCRIPCIONES]))
                    <a href="{{ route('laboratorio.index') }}"
                        class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                        <i class="fas fa-flask"></i> Laboratorios registrados
                    </a>
                @endif
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
                    <a href="{{ route('inscripcion_paquete.index') }}"
                        class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                        <i class="fas fa-file-signature"></i> Inscripciones a paquetes
                    </a>
                @endif
            </div>
        </div>
    @endif
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA]))
        <!--  Programas , Area , Paquetes y Ensayo Aptutud -->
        <div>
            <button @click="openMenu !== 4 ? openMenu = 4 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-boxes w-5 text-indigo-500"></i>
                <span>Programas</span>
                <i class="fas ml-auto" :class="openMenu === 4 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 4" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">

                <a href="{{ route('programa.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                    <i class="fas fa-clipboard-list"></i> <!-- icono para “Programas” -->
                    Programas
                </a>

                <a href="{{ route('area.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                    <i class="fas fa-layer-group"></i> <!-- icono para “Area” -->
                    Área
                </a>

                <a href="{{ route('paquete.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                    <i class="fas fa-box-open"></i> <!-- icono para “Paquetes” -->
                    Paquetes
                </a>

                <a href="{{ route('ensayo_aptitud.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                    <i class="fas fa-vials"></i> <!-- icono para “Ensayo de Aptitud” -->
                    Ensayo de Aptitud
                </a>

            </div>

        </div>
    @endif
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_GEOGRAFICA]))
        <!-- Ubicación Geográfica -->
        <div>
            <button @click="openMenu !== 6 ? openMenu = 6 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-globe-americas w-5 text-indigo-500"></i>
                <span>Ubicación</span>
                <i class="fas ml-auto" :class="openMenu === 6 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 6" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                <a href="{{ route('pais.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                    <i class="fas fa-flag"></i> País
                </a>
                <a href="{{ route('departamento.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                    <i class="fas fa-map"></i> Departamento
                </a>
                <a href="{{ route('provincia.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                    <i class="fas fa-map-marked-alt"></i> Provincia
                </a>
                <a href="{{ route('municipio.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                    <i class="fas fa-city"></i> Municipio
                </a>
            </div>
        </div>
    @endif
    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_USUARIO]))
        <!-- Usuarios y Roles -->
        <div>
            <button @click="openMenu !== 5 ? openMenu = 5 : openMenu = null"
                class="w-full flex items-center gap-3 px-2 py-2 rounded hover:bg-indigo-50 text-left">
                <i class="fas fa-users w-5 text-indigo-500"></i>
                <span>Usuarios</span>
                <i class="fas ml-auto" :class="openMenu === 5 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openMenu === 5" x-collapse.duration.200ms class="ml-4 mt-1 space-y-1">
                <a href="{{ route('usuario.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-user"></i>
                    Usuarios</a>
                <a href="{{ route('cargos.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-user-tag"></i> Cargos</a>
                <a href="{{ route('permiso.index') }}"
                    class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                        class="fas fa-key"></i>
                    Permisos</a>
            </div>
        </div>
    @endif
    @if (Gate::any([Permiso::ADMIN]))
        {{-- <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
                    <i class="fas fa-database w-5 text-indigo-500"></i>
                    <span>Datos Laboratorio</span>
                </a> --}}

        <a href="#" class="flex items-center gap-3 text-red-600 px-3 py-2 rounded hover:bg-red-50">
            <i class="fas fa-book w-5"></i>
            <span>Manual Usuario</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
            <i class="fas fa-info-circle w-5 text-indigo-500"></i>
            <span>Acerca de</span>
        </a>
    @endif
</nav>
