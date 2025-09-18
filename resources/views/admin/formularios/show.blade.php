<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold text-gray-800">
                Formularios del Ensayo: {{ $ensayo->descripcion }}
            </h1>
            <button onclick="document.getElementById('modalCrear').classList.remove('hidden')"
                class="px-3 py-1 bg-green-600 text-white rounded shadow hover:bg-green-500 text-sm">
                <i class="fas fa-plus"></i> Nuevo Formulario
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2">Nombre</th>
                        <th class="px-3 py-2">Código</th>
                        <th class="px-3 py-2">Nota</th>
                        <th class="px-3 py-2">Color Primario</th>
                        <th class="px-3 py-2">Color Secundario</th>
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($formularios as $formulario)
                        <tr class="border-b">
                            <td class="px-3 py-2 font-semibold">{{ $formulario->nombre }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $formulario->codigo ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ Str::limit($formulario->nota, 50) }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-block w-6 h-6 rounded-full border"
                                    style="background-color: {{ $formulario->color_primario }}"></span>
                                {{ $formulario->color_primario }}
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-block w-6 h-6 rounded-full border"
                                    style="background-color: {{ $formulario->color_secundario }}"></span>
                                {{ $formulario->color_secundario }}
                            </td>
                            <td class="px-3 py-2">
                                @if ($formulario->estado)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Activo</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center space-x-2">

                                <a href="{{ route('admin.formularios.edit', $formulario->id) }}"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-1 py-1 rounded shadow-sm"
                                    data-tippy-content="Ver / Editar">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Editar: aquí pasamos todo el objeto como JSON en data-formulario -->
                                <button type="button"
                                    class="edit-btn bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded shadow-sm"
                                    data-formulario='@json($formulario)' data-tippy-content="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Eliminar -->
                                <form action="{{ route('admin.formularios.destroy', $formulario->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('¿Seguro de eliminar este formulario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                                No hay formularios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($errors->any())
        <div class="mb-3 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div id="modalCrear" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h2 class="text-lg font-bold mb-4">Nuevo Formulario</h2>
            <form action="{{ route('admin.formularios.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_ensayo" value="{{ $ensayo->id }}">

                <div class="mb-3">
                    <label class="block text-sm font-medium">Nombre</label>
                    <input type="text" name="nombre" class="w-full border-gray-300 rounded shadow-sm text-sm"
                        required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Código</label>
                    <input type="text" name="codigo" class="w-full border-gray-300 rounded shadow-sm text-sm">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Nota</label>
                    <textarea name="nota" class="w-full border-gray-300 rounded shadow-sm text-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium">Color Primario</label>
                        <input type="color" name="color_primario" value="#272AF5"
                            class="w-full border-gray-300 rounded shadow-sm h-10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Color Secundario</label>
                        <input type="color" name="color_secundario" value="#E9E9F2"
                            class="w-full border-gray-300 rounded shadow-sm h-10">
                    </div>
                </div>

                <div class="mb-3 flex items-center">
                    <input type="hidden" name="estado" value="0">
                    <input type="checkbox" name="estado" value="1" checked
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Activo</label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalCrear').classList.add('hidden')"
                        class="px-3 py-1 bg-gray-300 text-gray-800 rounded shadow hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded shadow hover:bg-blue-500">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit (único) --}}
    <div id="modalEdit" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h2 class="text-lg font-bold mb-4">Editar Formulario</h2>

            {{-- La action se actualiza por JS antes de abrir el modal --}}
            <form id="formEdit" action="#" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id_ensayo" value="{{ $ensayo->id }}">
                <div class="mb-3">
                    <label class="block text-sm font-medium">Nombre</label>
                    <input type="text" name="nombre" class="w-full border-gray-300 rounded shadow-sm text-sm"
                        required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Código</label>
                    <input type="text" name="codigo" class="w-full border-gray-300 rounded shadow-sm text-sm">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Nota</label>
                    <textarea name="nota" class="w-full border-gray-300 rounded shadow-sm text-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium">Color Primario</label>
                        <input type="color" name="color_primario"
                            class="w-full border-gray-300 rounded shadow-sm h-10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Color Secundario</label>
                        <input type="color" name="color_secundario"
                            class="w-full border-gray-300 rounded shadow-sm h-10">
                    </div>
                </div>

                <div class="mb-3 flex items-center">
                    <input type="hidden" name="estado" value="0">
                    <input type="checkbox" name="estado" value="1"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Activo</label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button"
                        class="close-modal px-3 py-1 bg-gray-300 text-gray-800 rounded shadow hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded shadow hover:bg-blue-500">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JS: abre modal de edición y llena inputs --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const editModal = document.getElementById('modalEdit');
            const editForm = document.getElementById('formEdit');
            const baseUpdateUrl = "{{ route('admin.formularios.update', ['id' => '__ID__']) }}";

            // abrir modal y llenar campos desde data-formulario (JSON)
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const data = JSON.parse(btn.dataset.formulario);
                    editForm.action = baseUpdateUrl.replace('__ID__', data.id);
                    editForm.querySelector('input[name="nombre"]').value = data.nombre ?? '';
                    editForm.querySelector('input[name="codigo"]').value = data.codigo ?? '';
                    editForm.querySelector('textarea[name="nota"]').value = data.nota ?? '';
                    editForm.querySelector('input[name="color_primario"]').value = data
                        .color_primario ?? '#272AF5';
                    editForm.querySelector('input[name="color_secundario"]').value = data
                        .color_secundario ?? '#E9E9F2';

                    // estado: puede venir booleano
                    const chk = editForm.querySelector('input[type="checkbox"][name="estado"]');
                    chk.checked = Boolean(data.estado);

                    // mostrar modal
                    editModal.classList.remove('hidden');
                });
            });

            // cerrar modal
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    editModal.classList.add('hidden');
                });
            });
            // cerrar si clickeas fuera del modal content
            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) editModal.classList.add('hidden');
            });
        });
    </script>
</x-app-layout>
