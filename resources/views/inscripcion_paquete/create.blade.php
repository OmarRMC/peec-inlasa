<x-app-layout>
    <div class="container py-6 max-w-5xl" x-data="modalComponent">
        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-primary">Inscripción de Paquetes</h1>
            <button @click="open = true" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Inscribir Paquetes
            </button>
        </div>

        <!-- Modal -->
        <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Inscripción de Paquetes</h2>

                <!-- Selector de Programa -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programa</label>
                    <select x-model="programaId" @change="fetchPaquetes(programaId)"
                        class="form-select block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Seleccione un programa...</option>
                        @foreach ($programas as $programa)
                            <option value="{{ $programa->id }}">{{ $programa->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tabla de Paquetes -->
                <template x-if="paquetes.length">
                    <div class="overflow-x-auto mb-4">
                        <table class="table w-full text-sm">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Descripción</th>
                                    <th>Costo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="pkt in paquetes" :key="pkt.id">
                                    <tr>
                                        <td><input type="checkbox" :value="pkt.id" x-model="seleccionados" />
                                        </td>
                                        <td x-text="pkt.descripcion"></td>
                                        <td x-text="pkt.costo_paquete + ' Bs.'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>

                <!-- Tabla de Seleccionados -->
                <template x-if="seleccionados.length">
                    <div class="overflow-x-auto mb-4">
                        <h3 class="font-semibold text-gray-700 mb-2">Paquetes Seleccionados</h3>
                        <table class="table w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Costo</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(pkt, i) in resumen" :key="i">
                                    <tr>
                                        <td x-text="pkt.descripcion"></td>
                                        <td x-text="pkt.costo_paquete + ' Bs.'"></td>
                                        <td>
                                            <button @click="remove(i)"
                                                class="text-red-500 hover:underline">Eliminar</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="text-right font-bold mt-2">
                            Total: <span x-text="total + ' Bs.'"></span>
                        </div>
                    </div>
                </template>

                <!-- Botones de Acción -->
                <div class="flex justify-end gap-3">
                    <button @click="store()" class="btn-primary">Confirmar Inscripción</button>
                    <button @click="open = false" class="btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script AlpineJS -->
    <script>
        function modalComponent() {
            return {
                open: false,
                programaId: null,
                paquetes: [],
                seleccionados: [],
                get resumen() {
                    return this.seleccionados.map(id => this.paquetes.find(p => p.id === id));
                },
                get total() {
                    return this.resumen.reduce((sum, p) => sum + p.costo_paquete, 0);
                },
                fetchPaquetes(id) {
                    this.paquetes = [];
                    this.seleccionados = [];
                    if (!id) return;
                    fetch(`{{ route('paquetes.programa') }}?programa_id=${id}`)
                        .then(res => res.json())
                        .then(data => this.paquetes = data);
                },
                remove(index) {
                    this.seleccionados.splice(index, 1);
                },
                store() {
                    if (!this.seleccionados.length) return alert('Debe seleccionar al menos un paquete.');
                    const data = {
                        id_lab: {{ $laboratorio->id }},
                        gestion: new Date().getFullYear(),
                        paquetes: this.resumen.map(p => ({
                            id_paquete: p.id,
                            descripcion_paquete: p.descripcion,
                            costo_paquete: p.costo_paquete
                        }))
                    };
                    fetch(`{{ route('inscripcion-paquetes.store') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    }).then(response => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert('Error al registrar la inscripción');
                        }
                    });
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('modalComponent', modalComponent);
        });
    </script>
</x-app-layout>
