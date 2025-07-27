<x-app-layout>
    <div class="px-4 py-6 max-w-6xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalles del Usuario</h1>
            <x-shared.btn-volver :url="route('usuario.index')" />
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden divide-y divide-gray-200">

            <!-- Información Personal -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">👤 Información Personal</h2>
                <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>Username:</strong> {{ $usuario->username }}</div>
                    <div><strong>Nombre:</strong> {{ $usuario->nombre }}</div>
                    <div><strong>Apellido Paterno:</strong> {{ $usuario->ap_paterno }}</div>
                    <div><strong>Apellido Materno:</strong> {{ $usuario->ap_materno }}</div>
                    <div><strong>CI:</strong> {{ $usuario->ci }}</div>
                    <div><strong>Teléfono:</strong> {{ $usuario->telefono }}</div>
                    <div class="md:col-span-2"><strong>Email:</strong> {{ $usuario->email }}</div>
                </div>
            </section>

            <!-- Cargo y Estado -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">💼 Cargo y Estado</h2>
                <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>Cargo:</strong> {{ $usuario->cargo->nombre_cargo ?? 'No asignado' }}</div>
                    <div>
                        <strong>Estado:</strong>
                        @if ($usuario->status)
                            <span
                                class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Activo</span>
                        @else
                            <span class="inline-block px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Inactivo</span>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Ensayos de Aptitud -->
            @if ($usuario->responsablesEA->isNotEmpty())
                <section class="p-6">
                    <h2 class="text-lg font-semibold text-blue-700 mb-4">🧪 Ensayos de Aptitud a Cargo</h2>
                    <div class="space-y-2 text-sm text-gray-700">
                        @foreach ($usuario->responsablesEA as $ea)
                            <div class="border rounded px-4 py-2 bg-gray-50">
                                <strong>Nombre: </strong> {{ $ea->descripcion ?? 'Sin nombre' }}
                                {{-- Agrega más detalles si deseas como gestión, estado, etc. --}}
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Permisos -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">🔐 Permisos Asignados</h2>
                @if ($usuario->permisos->isEmpty())
                    <p class="text-sm text-gray-500">No tiene permisos asignados.</p>
                @else
                    <ul class="list-disc list-inside text-sm text-gray-700">
                        @foreach ($usuario->permisos as $permiso)
                            <li>{{ $permiso->nombre_permiso }}</li>
                        @endforeach
                    </ul>
                @endif
            </section>

        </div>
    </div>
</x-app-layout>
