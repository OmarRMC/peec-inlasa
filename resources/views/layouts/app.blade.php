
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
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script> --}}

    {{-- 
       SwitAleart2 
    --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- TippyJS -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            /* position: absolute; */
            top: 0.8rem;
            left: 1.05rem;
            z-index: 50;
            /* background-color: #0891b2; */
            background: white;
            /* cyan-600 */
            /* color: white; */
            /* color: black; */
            border-radius: 5px;
            padding: 0.25rem 0.3rem;
            /* box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); */
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
    <div class="relative">
        {{-- <button class="sidebar-toggle-button md:hidden" @click="sidebarOpen = true" x-show="!sidebarOpen">
            <i class="fas fa-bars"></i>
        </button> --}}
        {{-- <button class="sidebar-toggle-button hidden md:block" @click="sidebarCollapsed = false"
            x-show="sidebarCollapsed">
            <i class="fas fa-bars"></i>
        </button> --}}
    </div>

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
                        {{-- <i class="fas fa-eye" x-show="sidebarCollapsed"></i> --}}
                    </button>
                </div>
            </div>
            @include('layouts.navigation')
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b px-4 sm:px-6 py-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 md:hidden">
                        <i class="fas fa-bars"></i>
                    </button>

                    {{-- <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hidden">
                        <i class="fas fa-bars"></i>
                    </button> --}}
                    <button class="sidebar-toggle-button hidden md:block text-gray-500"
                        @click="sidebarCollapsed = false" x-show="sidebarCollapsed">
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
                            <div class="text-indigo-600 font-semibold">
                                @if (Auth::user()->isLaboratorio())
                                    LABORATORIO REGISTRADO
                                @else 
                                    USUARIO REGISTRADO
                                @endif
                            </div>
                            <div>PEEC - INLASA</div>
                            <div class="font-bold">{{ Auth::user()->username ?? 'BOL1146' }}</div>
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
                <span class="float-right mr-4 text-gray-400">SigPEEC v2</span>
            </footer>
        </div>
    </div>
    @stack('scripts')
    <x-shared.notificacion-lab/>
</body>

</html>
