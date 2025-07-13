<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md">

        <h1 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-user-circle text-primary"></i> Detalle del Usuario
        </h1>

        {{-- Información Personal --}}
        <section class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-primary"></i> Información Personal
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div><span class="font-medium">Username:</span> {{ $usuario->username }}</div>
                <div><span class="font-medium">Nombre:</span> {{ $usuario->nombre }}</div>
                <div><span class="font-medium">Apellido Paterno:</span> {{ $usuario->ap_paterno }}</div>
                <div><span class="font-medium">Apellido Materno:</span> {{ $usuario->ap_materno }}</div>
                <div><span class="font-medium">CI:</span> {{ $usuario->ci }}</div>
                <div><span class="font-medium">Teléfono:</span> {{ $usuario->talefono }}</div>
                <div class="md:col-span-2"><span class="font-medium">Email:</span> {{ $usuario->email }}</div>
            </div>
        </section>

        {{-- Cargo y Estado --}}
        <section class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-briefcase text-primary"></i> Cargo y Estado
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div><span class="font-medium">Cargo:</span> {{ $usuario->cargo->nombre_cargo ?? 'No asignado' }}</div>
                <div>
                    <span class="font-medium">Estado:</span>
                    @if ($usuario->status)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </div>
            </div>
        </section>

        {{-- Permisos --}}
        <section>
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-shield-alt text-primary"></i> Permisos
            </h2>

            @if ($usuario->permisos->isEmpty())
                <p class="text-gray-600">No tiene permisos asignados.</p>
            @else
                <ul class="list-disc list-inside text-gray-700">
                    @foreach ($usuario->permisos as $permiso)
                        <li>{{ $permiso->nombre_permiso }}</li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Botón volver --}}
        <div class="mt-8">
            <a href="{{ route('usuario.index') }}" class="btn btn-primary inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>
</x-app-layout>
