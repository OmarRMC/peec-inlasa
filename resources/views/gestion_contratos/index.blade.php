@php
    use App\Models\Permiso;
@endphp

<x-app-layout>
    <div class="container py-6 max-w-4xl">
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Lista de Contratos</h1>
            <button onclick="openContratoModal()" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Contrato
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Firmante</th>
                        <th>Institución</th>
                        <th>Cargo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contratos as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->firmante_nombre }}</td>
                            <td>{{ $c->firmante_institucion }}</td>
                            <td>{{ $c->firmante_cargo }}</td>

                            <td>
                                @if ($c->estado)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>

                            <td>
                                <div class="flex space-x-1">

                                    <a href="{{ route('contratos.show', $c->id) }}" data-tippy-content="Ver Detalle"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded shadow-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button onclick="editContrato({{ $c }})" data-tippy-content="Editar"
                                        class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    {{-- PREVISUALIZAR CONTRATO --}}
                                    {{-- <a href="{{ route('contratos.preview', $c->id) }}"
                                        data-tippy-content="Previsualizar Contrato"
                                        class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded shadow-sm">
                                        <i class="fas fa-file-pdf"></i>
                                    </a> --}}

                                    <form method="POST" action="{{ route('contratos.destroy', $c->id) }}"
                                        class="delete-form inline" data-nombre="Contrato #{{ $c->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" data-tippy-content="Eliminar"
                                            class="delete-button bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay contratos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalContrato" class="fixed inset-0 bg-black bg-opacity-30 hidden items-center justify-center">

        <div class="bg-white w-full max-w-lg rounded-lg shadow p-6 relative">

            <!-- Cerrar -->
            <button onclick="closeContratoModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>

            <h2 id="modalTitle" class="text-lg font-bold mb-4 text-primary">Nuevo Contrato</h2>

            <form id="formContrato" method="POST">
                @csrf
                <input type="hidden" id="methodField">

                {{-- NOMBRE --}}
                <div class="mb-4">
                    <label class="label">Nombre del Firmante</label>
                    <input type="text" id="firmante_nombre" name="firmante_nombre" class="input-standard" required>
                </div>

                {{-- CARGO --}}
                <div class="mb-4">
                    <label class="label">Cargo</label>
                    <input type="text" id="firmante_cargo" name="firmante_cargo" class="input-standard" required>
                </div>

                <div class="mb-4">
                    <label class="label">Institución</label>
                    <input type="text" id="firmante_institucion" name="firmante_institucion" class="input-standard"
                        required>
                </div>

                <div class="mb-4">
                    <label class="label">Detalle bajo la firma</label>
                    <textarea name="firmante_detalle" id="firmante_detalle" class="input-standard"></textarea>
                </div>

                <div class="mb-4">
                    <label class="label">Estado</label>
                    <select id="estado" name="estado" class="input-standard">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeContratoModal()" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>

            </form>

        </div>
    </div>


    @push('scripts')
        <script>
            // CREATE
            function openContratoModal() {
                resetForm();

                document.getElementById('modalTitle').innerText = "Nuevo Contrato";

                let form = document.getElementById("formContrato");
                form.action = "{{ route('contratos.store') }}";

                document.getElementById("methodField").outerHTML = ""; // sin PUT

                showModal();
            }

            // EDIT
            function editContrato(data) {
                resetForm();

                document.getElementById('modalTitle').innerText = "Editar Contrato";
                let updateUrl = "{{ route('contratos.update', ':id') }}";
                let form = document.getElementById("formContrato");
                form.action = updateUrl.replace(':id', data.id);

                document.getElementById("methodField").outerHTML =
                    `<input type="hidden" name="_method" value="PUT" id="methodField">`;

                document.getElementById("firmante_nombre").value = data.firmante_nombre ?? "";
                document.getElementById("firmante_cargo").value = data.firmante_cargo ?? "";
                document.getElementById("firmante_institucion").value = data.firmante_institucion ?? "";
                document.getElementById("firmante_detalle").value = data.firmante_detalle ?? "";
                document.getElementById("estado").value = data.estado ? 1 : 0;

                showModal();
            }

            // Mostrar modal
            function showModal() {
                const m = document.getElementById('modalContrato');
                m.classList.remove('hidden');
                m.classList.add('flex');
            }

            function closeContratoModal() {
                const m = document.getElementById('modalContrato');
                m.classList.add('hidden');
                m.classList.remove('flex');
            }

            function resetForm() {
                document.getElementById("formContrato").reset();
            }
        </script>
    @endpush

</x-app-layout>
