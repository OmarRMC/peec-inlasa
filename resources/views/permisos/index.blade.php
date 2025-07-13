<x-app-layout>
    <div class="px-4 py-6 max-w-5xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-gray-800">Lista de Permisos</h1>
            <a href="{{ route('permiso.create') }}"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 transition shadow-md text-sm">
                <i class="fas fa-plus-circle"></i> Nuevo Permiso
            </a>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($permisos as $permiso)
                        <tr>
                            <td class="px-4 py-2">{{ $permiso->id }}</td>
                            <td class="px-4 py-2">{{ $permiso->nombre_permiso }}</td>
                            <td class="px-4 py-2">{{ $permiso->descripcion ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @if ($permiso->status)
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">Activo</span>
                                @else
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-red-500 text-white">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 flex gap-2">
                                <a href="{{ route('permiso.edit', $permiso->id) }}"
                                    class="text-indigo-600 hover:text-indigo-800 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('permiso.destroy', $permiso->id) }}" method="POST"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este permiso?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No hay permisos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
