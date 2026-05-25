@php
    use App\Models\Permiso;
@endphp
<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow text-xs">
        @if (Gate::any(Permiso::RESPONSABLE))
            <x-shared.btn-volver :url="route('admin.formularios.show', $ensayo->id)" />
        @endif
        @if (Gate::any(Permiso::ADMIN, Permiso::GESTION_FORMULARIOS))
            <x-shared.btn-volver :url="route('admin.formularios.ea')" />
        @endif
        <div class="mb-4">
            <h2 class="text-sm font-semibold">Grupos de Selectores</h2>
            <div class="flex flex-col pt-3 text-sm">
                <span class="font-bold text-gray-800">{{ $ensayo->paquete->descripcion ?? '' }}</span>
                <span class="text-gray-500">{{ $ensayo->descripcion }}</span>
            </div>
        </div>

        {{-- Mensajes de éxito --}}
        @if (session('success'))
            <div class="p-2 mb-4 bg-green-100 text-green-800 rounded text-xs">
                {{ session('success') }}
            </div>
        @endif

        {{-- Crear nuevo grupo --}}
        <form method="POST" action="{{ route('admin.grupos.store', $ensayo->id) }}" class="mb-4 flex gap-2 text-xs">
            @csrf
            <input type="text" name="nombre" placeholder="Nombre del grupo" required
                class="border rounded px-2 py-1 flex-1 text-xs">
            <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm text-xs">
                <i class="fas fa-plus"></i>
            </button>
        </form>

        {{-- Nota explicativa de selectores dependientes --}}
        @if ($ensayo->gruposSelectores->isNotEmpty())
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800">
                <div class="font-semibold mb-1">
                    <i class="fas fa-info-circle mr-1"></i> Sobre el "Subgrupo a mostrar"
                </div>
                <p class="leading-relaxed">
                    Cada opción puede activar un <strong>selector dependiente</strong>: si el laboratorio elige
                    una opción que tenga un subgrupo asignado, aparecerá automáticamente un segundo selector
                    con las opciones de ese subgrupo. Útil para listas en cascada (ej: "Categoría" → muestra
                    opciones específicas de esa categoría).
                </p>
            </div>
        @endif

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
                            <input type="text" name="nombre" value="{{ $grupo->nombre }}" required
                                class="border px-2 py-1 rounded text-xs w-40">
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
                            <th class="border px-2 py-1 text-left"
                                data-tippy-content="Si esta opción es elegida por el lab, se despliega un segundo selector con las opciones del subgrupo seleccionado aquí.">
                                <i class="fas fa-arrow-turn-down text-gray-400 mr-1"></i>
                                Subgrupo a mostrar
                                <i class="fas fa-info-circle text-gray-400 ml-1 text-[10px]"></i>
                            </th>
                            <th class="border px-2 py-1 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($grupo->opciones as $opcion)
                            <tr>
                                <form method="POST" action="{{ route('admin.opciones.update', $opcion->id) }}"
                                    class="contents">
                                    @csrf @method('PUT')
                                    <td class="border px-2 py-1">
                                        <input type="text" name="valor" value="{{ $opcion->valor }}"
                                            class="border px-2 py-1 rounded w-full text-xs">
                                    </td>
                                    <td class="border px-2 py-1">
                                        <div class="flex items-center gap-1">
                                            @if ($opcion->id_subgrupo)
                                                <i class="fas fa-link text-indigo-500" data-tippy-content="Esta opción activa un subselector"></i>
                                            @endif
                                            <select name="id_subgrupo" class="border px-2 py-1 rounded w-full text-xs">
                                                <option value="">Sin subgrupo</option>
                                                @foreach ($ensayo->gruposSelectores as $subgrupo)
                                                    @if ($subgrupo->id !== $grupo->id)
                                                        <option value="{{ $subgrupo->id }}" @selected($opcion->id_subgrupo == $subgrupo->id)>
                                                            {{ $subgrupo->nombre }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
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
                    <input type="text" name="valor" placeholder="Valor de la opción" required
                        class="border rounded px-2 py-1 text-xs w-40">
                    <select name="id_subgrupo" class="border px-2 py-1 rounded text-xs"
                        data-tippy-content="Subgrupo que se desplegará si el lab elige esta opción (opcional)">
                        <option value="">Sin subgrupo</option>
                        @foreach ($ensayo->gruposSelectores as $subgrupo)
                            @if ($subgrupo->id !== $grupo->id)
                                <option value="{{ $subgrupo->id }}">↳ {{ $subgrupo->nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit" data-tippy-content="Agregar opción"
                        class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm text-xs">
                        <i class="fas fa-plus"></i>
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</x-app-layout>
