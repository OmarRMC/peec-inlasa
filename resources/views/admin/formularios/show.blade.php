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
                        <th class="px-3 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($formularios as $formulario)
                        <tr class="border-b">
                            <td class="px-3 py-2 font-semibold">{{ $formulario->nombre }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $formulario->codigo ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ Str::limit($formulario->nota, 50) }}</td>
                            <td class="px-3 py-2 text-center space-x-2">
                                <!-- Ver detalle -->
                                <a href="{{ route('admin.formularios.edit', $formulario->id) }}"
                                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm"
                                    data-tippy-content="Ver / Editar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <!-- Eliminar -->
                                {{-- <form action="{{ route('admin.formularios.destroy', $formulario->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('¿Seguro de eliminar este formulario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm"
                                        data-tippy-content="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">
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
</x-app-layout>
