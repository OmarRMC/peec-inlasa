<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow text-xs">
        <h2 class="text-sm font-semibold mb-4">
            Grupos de Selectores <span class="text-gray-500">{{ $ensayo->descripcion }}</span>
        </h2>

        {{-- Mensajes de éxito --}}
        @if (session('success'))
            <div class="p-2 mb-4 bg-green-100 text-green-800 rounded text-xs">
                {{ session('success') }}
            </div>
        @endif

        {{-- Crear nuevo grupo --}}
        <form method="POST" action="{{ route('admin.grupos.store', $ensayo->id) }}" class="mb-4 flex gap-2 text-xs">
            @csrf
            <input type="text" name="nombre" placeholder="Nombre del grupo"
                class="border rounded px-2 py-1 w-1/3 text-xs">
            <input type="text" name="descripcion" placeholder="Descripción"
                class="border rounded px-2 py-1 w-1/3 text-xs">
            <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm text-xs">
                <i class="fas fa-plus"></i>
            </button>
        </form>

        {{-- Listado de grupos --}}
        @foreach ($ensayo->gruposSelectores as $grupo)
            <div class="mb-6 border rounded p-3 shadow-sm text-xs">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-medium text-sm">{{ $grupo->nombre }}</h3>
                    <div class="flex gap-2 items-center text-xs">
                        {{-- Editar grupo --}}
                        <form method="POST" action="{{ route('admin.grupos.update', $grupo->id) }}"
                            class="flex gap-1 items-center text-xs">
                            @csrf @method('PUT')
                            <input type="text" name="nombre" value="{{ $grupo->nombre }}"
                                class="border px-2 py-1 rounded text-xs w-24">
                            <input type="text" name="descripcion" value="{{ $grupo->descripcion }}"
                                class="border px-2 py-1 rounded text-xs w-32">
                            <button type="submit" data-tippy-content="Actualizar"
                                class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded shadow-sm text-xs">
                                <i class="fas fa-edit"></i>
                            </button>
                        </form>

                        {{-- Eliminar grupo --}}
                        <form method="POST" action="{{ route('admin.grupos.destroy', $grupo->id) }}"
                            onsubmit="return confirm('¿Eliminar este grupo?')">
                            @csrf @method('DELETE')
                            <button type="submit" data-tippy-content="Eliminar"
                                class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm text-xs">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Opciones del grupo --}}
                <table class="w-full text-xs border mt-2">
                    <thead class="bg-gray-100 text-xs">
                        <tr>
                            <th class="border px-2 py-1 text-left">Valor</th>
                            <th class="border px-2 py-1 text-left">Grupo Hijo</th>
                            <th class="border px-2 py-1 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($grupo->opciones as $opcion)
                            <tr>
                                <form method="POST" action="{{ route('admin.opciones.update', $opcion->id) }}" class="contents">
                                    @csrf @method('PUT')
                                    <td class="border px-2 py-1">
                                        <input type="text" name="valor" value="{{ $opcion->valor }}"
                                            class="border px-2 py-1 rounded w-full text-xs">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <select name="id_grupo_hijo" class="border px-2 py-1 rounded w-full text-xs">
                                            <option value="">Ningún</option>
                                            @foreach ($ensayo->gruposSelectores as $grupoHijo)
                                                @if ($grupoHijo->id !== $grupo->id)
                                                    <option value="{{ $grupoHijo->id }}" @selected($opcion->id_grupo_hijo == $grupoHijo->id)>
                                                        {{ $grupoHijo->nombre }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border px-2 py-1 flex gap-2">
                                        <button type="submit" data-tippy-content="Actualizar"
                                            class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded shadow-sm text-xs">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                </form>
                                <form method="POST" action="{{ route('admin.opciones.destroy', $opcion->id) }}"
                                    onsubmit="return confirm('¿Eliminar esta opción?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-tippy-content="Eliminar"
                                        class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm text-xs">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                    </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-gray-400 text-center py-2">Sin opciones</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Agregar nueva opción --}}
                <form method="POST" action="{{ route('admin.opciones.store', $grupo->id) }}"
                    class="mt-2 flex gap-2 items-center justify-center text-xs">
                    @csrf
                    <input type="text" name="valor" placeholder="Valor"
                        class="border rounded px-2 py-1 text-xs w-32">
                    <select name="id_grupo_hijo" class="border px-2 py-1 rounded text-xs">
                        <option value="">Ningún</option>
                        @foreach ($ensayo->gruposSelectores as $grupoHijo)
                            @if ($grupoHijo->id !== $grupo->id)
                                <option value="{{ $grupoHijo->id }}">{{ $grupoHijo->nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit" data-tippy-content="Agregar Opción"
                        class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm text-xs">
                        <i class="fas fa-plus"></i>
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</x-app-layout>
