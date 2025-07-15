<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false, sidebarCollapsed: false, openMenu: null }" x-cloak>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
    <!-- ToastifyJS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- TippyJS -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <style>
        [x-cloak] {
            display: none !important;
        }

        .sidebar-collapsed aside {
            width: 0 !important;
            overflow: hidden;
        }

        .sidebar-collapsed .sidebar-toggle-button {
            display: block;
        }

        .sidebar-toggle-button {
            display: none;
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 50;
            background-color: #0891b2;
            /* cyan-600 */
            color: white;
            border-radius: 9999px;
            padding: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            aside {
                position: fixed;
                z-index: 40;
                height: 100%;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar-open aside {
                transform: translateX(0);
            }

            .sidebar-toggle-button {
                left: 1rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-800"
    :class="{ 'sidebar-open': sidebarOpen, 'sidebar-collapsed': sidebarCollapsed }">

    <x-alerts.sweetalert />

    <!-- Sidebar Toggle Buttons -->
    <button class="sidebar-toggle-button md:hidden" @click="sidebarOpen = true" x-show="!sidebarOpen">
        <i class="fas fa-bars"></i>
    </button>
    <button class="sidebar-toggle-button hidden md:block" @click="sidebarCollapsed = false" x-show="sidebarCollapsed">
        <i class="fas fa-bars"></i>
    </button>

    <div class="flex h-screen transition-all duration-300 ease-in-out">
        <!-- Sidebar -->
        <aside class="bg-white border-r shadow-sm w-64 shrink-0 flex flex-col transition-all duration-300 ease-in-out">
            <div class="flex items-center justify-between bg-indigo-600 text-white px-4 py-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-microscope text-lg"></i>
                    <span class="font-semibold text-sm">SigPEEC</span>
                </div>
                <div class="flex gap-2">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-white">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:inline text-white">
                        <i class="fas fa-bars" x-show="!sidebarCollapsed"></i>
                        <i class="fas fa-eye" x-show="sidebarCollapsed"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-2 py-4 space-y-1 text-sm overflow-y-auto">
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
                    <i class="fas fa-home w-5 text-indigo-500"></i>
                    <span>Escritorio</span>
                </a>

                <!-- Gestión de Inscripciones -->
                <div>
                    <button @click="openMenu !== 1 ? openMenu = 1 : openMenu = null"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 text-left">
                        <i class="fas fa-file-signature w-5 text-indigo-500"></i>
                        <span>Inscripciones</span>
                        <i class="fas ml-auto" :class="openMenu === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openMenu === 1" x-collapse.duration.200ms class="ml-8 mt-1 space-y-1">
                        <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-list"></i> Ver Inscripciones</a>
                        <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-edit"></i> Formularios</a>
                        <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-file-alt"></i> Documentos</a>
                    </div>
                </div>

                <!-- Certificados -->
                <div>
                    <button @click="openMenu !== 2 ? openMenu = 2 : openMenu = null"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 text-left">
                        <i class="fas fa-certificate w-5 text-indigo-500"></i>
                        <span>Certificados</span>
                        <i class="fas ml-auto" :class="openMenu === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openMenu === 2" x-collapse.duration.200ms class="ml-8 mt-1 space-y-1">
                        <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-certificate"></i> Participación</a>
                        <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-medal"></i> Desempeño</a>
                    </div>
                </div>

                <!-- Recursos Laboratorio -->
                <div>
                    <button @click="openMenu !== 3 ? openMenu = 3 : openMenu = null"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 text-left">
                        <i class="fas fa-vials w-5 text-indigo-500"></i>
                        <span>Recursos Lab.</span>
                        <i class="fas ml-auto" :class="openMenu === 3 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openMenu === 3" x-collapse.duration.200ms class="ml-8 mt-1 space-y-1">
                        <a href="#" class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-file-contract"></i> Contrato 2025</a>
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

                <div>
                    <button @click="openMenu !== 7 ? openMenu = 7 : openMenu = null"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 text-left">
                        <i class="fas fa-flask w-5 text-indigo-500"></i>
                        <span>Gestión de Laboratorio</span>
                        <i class="fas ml-auto" :class="openMenu === 7 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openMenu === 7" x-collapse.duration.200ms class="ml-8 mt-1 space-y-1">

                        <a href="{{ route('nivel_laboratorio.index') }}"
                            class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                            <i class="fas fa-layer-group"></i> Nivel de Laboratorio
                        </a>

                        <a href="{{ route('tipo_laboratorio.index') }}"
                            class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                            <i class="fas fa-vial"></i> Tipo de Laboratorio
                        </a>

                        <a href="{{ route('categoria_laboratorio.index') }}"
                            class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                            <i class="fas fa-tags"></i> Categoría
                        </a>

                        <a href="{{ route('laboratorio.index') }}"
                            class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                            <i class="fas fa-file-signature"></i> Inscripciones
                        </a>

                        <a href="{{ route('laboratorio.index') }}"
                            class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded flex items-center gap-2">
                            <i class="fas fa-flask"></i> Laboratorios Inscritos
                        </a>

                    </div>
                </div>

                <!--  Programas , Area , Paquetes y Ensayo Aptutud -->
                <div>
                    <button @click="openMenu !== 4 ? openMenu = 4 : openMenu = null"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 text-left">
                        <i class="fas fa-boxes w-5 text-indigo-500"></i>
                        <span>Programas</span>
                        <i class="fas ml-auto" :class="openMenu === 4 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openMenu === 4" x-collapse.duration.200ms class="ml-8 mt-1 space-y-1">

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

                <!-- Ubicación Geográfica -->
                <div>
                    <button @click="openMenu !== 6 ? openMenu = 6 : openMenu = null"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 text-left">
                        <i class="fas fa-globe-americas w-5 text-indigo-500"></i>
                        <span>Ubicación</span>
                        <i class="fas ml-auto" :class="openMenu === 6 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openMenu === 6" x-collapse.duration.200ms class="ml-8 mt-1 space-y-1">
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
                <!-- Usuarios y Roles -->
                <div>
                    <button @click="openMenu !== 5 ? openMenu = 5 : openMenu = null"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50 text-left">
                        <i class="fas fa-users w-5 text-indigo-500"></i>
                        <span>Usuarios</span>
                        <i class="fas ml-auto" :class="openMenu === 5 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openMenu === 5" x-collapse.duration.200ms class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('usuario.index') }}"
                            class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-user"></i> Usuarios</a>
                        <a href="{{ route('cargos.index') }}"
                            class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-user-tag"></i> Cargos</a>
                        <a href="{{ route('permiso.index') }}"
                            class="block px-3 py-1 text-sm text-gray-600 hover:bg-indigo-100 rounded"><i
                                class="fas fa-key"></i> Permisos</a>
                    </div>
                </div>

                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
                    <i class="fas fa-database w-5 text-indigo-500"></i>
                    <span>Datos Laboratorio</span>
                </a>

                <a href="#" class="flex items-center gap-3 text-red-600 px-3 py-2 rounded hover:bg-red-50">
                    <i class="fas fa-book w-5"></i>
                    <span>Manual Usuario</span>
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-indigo-50">
                    <i class="fas fa-info-circle w-5 text-indigo-500"></i>
                    <span>Acerca de</span>
                </a>
            </nav>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b px-4 sm:px-6 py-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 md:hidden">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-sm font-semibold text-indigo-600 flex items-center gap-2">
                        <i class="fas fa-flask text-indigo-500"></i> CODIGO DISPONIBLE
                    </h1>
                </div>
                <div class="relative}">
                    <button @click="document.getElementById('userMenu').classList.toggle('hidden')"
                        class="flex items-center gap-2 text-sm text-gray-700 hover:text-indigo-600">
                        <i class="fas fa-user-circle text-lg"></i>
                        <span class="hidden sm:inline">{{ Auth::user()->nombre ?? 'Usuario' }}</span>
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div id="userMenu"
                        class="absolute right-0 mt-2 bg-white border rounded shadow-md w-56 hidden z-50">
                        <div class="px-4 py-3 border-b text-center text-sm">
                            <div class="text-indigo-600 font-semibold">LABORATORIO REGISTRADO</div>
                            <div>PEEC - INLASA</div>
                            <div class="font-bold">{{ Auth::user()->codigo ?? 'BOL1146' }}</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="text-sm">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 hover:bg-indigo-50 flex items-center gap-2">
                                <i class="fas fa-sign-out-alt text-gray-500"></i> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content -->
            @isset($header)
                <div class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <main class="flex-1 p-6 bg-gray-50 overflow-y-auto">
                {{ $slot }}
            </main>

            <footer class="bg-white text-center py-3 border-t text-sm text-gray-600">
                © 2025 | <strong>INLASA</strong> Instituto Nacional de Laboratorios de Salud
                <span class="float-right mr-4 text-gray-400">SigPEEC v1.5</span>
            </footer>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
