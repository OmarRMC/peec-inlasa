<x-app-layout>
    <div class="px-4 max-w-6xl mx-auto" x-data="{ openModal: false, ciclo: null, isEdit: false }">
        <x-shared.btn-volver :url="route('admin.formularios.ea')" />
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-xl font-bold text-gray-800">
                Ciclos del Ensayo: {{ $ensayo->descripcion }}
            </h1>
            <button @click="isEdit = false; ciclo = null; openModal = true"
                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                <i class="fas fa-plus"></i> Nuevo Ciclo
            </button>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2">Nombre</th>
                        <th class="px-3 py-2">Número</th>
                        <th class="px-3 py-2">Muestras</th>
                        <th class="px-3 py-2">Resultados</th>
                        <th class="px-3 py-2">Reporte</th>
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ciclos as $c)
                        <tr class="border-b">
                            <td class="px-3 py-2 font-semibold">{{ $c->nombre }}</td>
                            <td class="px-3 py-2">{{ $c->numero }}</td>
                            <td class="px-3 py-2 text-xs">{{ $c->fecha_inicio_envio_muestras_show }} →
                                {{ $c->fecha_fin_envio_muestras_show }}</td>
                            <td class="px-3 py-2 text-xs">{{ $c->fecha_inicio_envio_resultados_show }} →
                                {{ $c->fecha_fin_envio_resultados_show }}</td>
                            <td class="px-3 py-2 text-xs">{{ $c->fecha_inicio_envio_reporte_show }} →
                                {{ $c->fecha_fin_envio_reporte_show }}</td>
                            <td class="px-3 py-2">
                                @if ($c->estado)
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Habilitado</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Deshabilitado</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center flex gap-2 justify-center">
                                <!-- Editar -->
                                <button @click="isEdit = true; ciclo = {{ $c->toJson() }}; openModal = true"
                                    data-tippy-content="Editar Ciclo"
                                    class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded shadow-sm">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form method="POST" action="{{ route('admin.ciclos.toggle', $c->id) }}">
                                    @csrf @method('PUT')
                                    <button type="submit" data-tippy-content="Cambiar Estado"
                                        class="px-2 py-1 rounded shadow-sm {{ $c->estado ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }}">
                                        <i class="fas {{ $c->estado ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.ciclos.destroy', $c->id) }}"
                                    onsubmit="return confirm('¿Seguro que quieres eliminar este ciclo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" data-tippy-content="Eliminar Ciclo"
                                        class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">No hay ciclos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <strong>Se encontraron errores:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Modal -->
        <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
            x-cloak>
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
                <h2 class="text-lg font-bold mb-4" x-text="isEdit ? 'Editar Ciclo' : 'Nuevo Ciclo'"></h2>
                <form
                    :action="isEdit ? '{{ route('admin.ciclos.update', ':id') }}'.replace(':id', ciclo.id) :
                        '{{ route('admin.ciclos.store') }}'"
                    method="POST">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="id_ensayo" value="{{ $ensayo->id }}">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="block text-sm">Nombre</label>
                        <input type="text" name="nombre" class="w-full border rounded px-2 py-1"
                            :value="isEdit ? ciclo.nombre : ''" required>
                    </div>

                    <!-- Número -->
                    <div class="mb-3">
                        <label class="block text-sm">Número</label>
                        <input type="number" name="numero" class="w-full border rounded px-2 py-1"
                            :value="isEdit ? ciclo.numero : ''" required>
                    </div>

                    <!-- Fechas -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm">Inicio Muestras</label>
                            <input type="date" name="fecha_inicio_envio_muestras"
                                class="w-full border rounded px-2 py-1"
                                :value="isEdit ? ciclo.fecha_inicio_envio_muestras : ''" required>
                        </div>
                        <div>
                            <label class="block text-sm">Fin Muestras</label>
                            <input type="date" name="fecha_fin_envio_muestras"
                                class="w-full border rounded px-2 py-1"
                                :value="isEdit ? ciclo.fecha_fin_envio_muestras : ''" required>
                        </div>
                        <div>
                            <label class="block text-sm">Inicio Resultados</label>
                            <input type="date" name="fecha_inicio_envio_resultados"
                                class="w-full border rounded px-2 py-1"
                                :value="isEdit ? ciclo.fecha_inicio_envio_resultados : ''" required>
                        </div>
                        <div>
                            <label class="block text-sm">Fin Resultados</label>
                            <input type="date" name="fecha_fin_envio_resultados"
                                class="w-full border rounded px-2 py-1"
                                :value="isEdit ? ciclo.fecha_fin_envio_resultados : ''" required>
                        </div>
                        <div>
                            <label class="block text-sm">Inicio Reporte</label>
                            <input type="date" name="fecha_inicio_envio_reporte"
                                class="w-full border rounded px-2 py-1"
                                :value="isEdit ? ciclo.fecha_inicio_envio_reporte : ''" required>
                        </div>
                        <div>
                            <label class="block text-sm">Fin Reporte</label>
                            <input type="date" name="fecha_fin_envio_reporte" class="w-full border rounded px-2 py-1"
                                :value="isEdit ? ciclo.fecha_fin_envio_reporte : ''" required>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="openModal = false"
                            class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
