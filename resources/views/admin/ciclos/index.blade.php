@php
    // Datos que el navegador recupera tras un error de validación del servidor.
    $oldCiclo = [
        'hasErrors' => $errors->any(),
        'ciclo_id' => old('ciclo_id'),
        'nombre' => old('nombre'),
        'fecha_inicio_envio_muestras' => old('fecha_inicio_envio_muestras'),
        'fecha_fin_envio_muestras' => old('fecha_fin_envio_muestras'),
        'fecha_inicio_envio_resultados' => old('fecha_inicio_envio_resultados'),
        'fecha_fin_envio_resultados' => old('fecha_fin_envio_resultados'),
        'fecha_inicio_envio_reporte' => old('fecha_inicio_envio_reporte'),
        'fecha_fin_envio_reporte' => old('fecha_fin_envio_reporte'),
    ];
@endphp

<x-app-layout>
    <div class="px-4 max-w-6xl mx-auto" x-data="ciclosApp(@js($oldCiclo))" x-init="init()">
        <x-shared.btn-volver :url="route('admin.formularios.ea')" />

        <div class="flex justify-between items-center mb-4 mt-2">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Ciclos del Ensayo</h1>
                <div class="flex flex-col pt-3 text-sm">
                    <span class="font-bold text-gray-800">{{ $ensayo->paquete->descripcion ?? '' }}</span>
                    <span class="text-gray-500">{{ $ensayo->descripcion }}</span>
                </div>
            </div>
            <button @click="openCreate()"
                class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs flex items-center gap-1">
                <i class="fas fa-plus"></i> Nuevo Ciclo
            </button>
        </div>

        {{-- Selector de Gestión --}}
        <div class="flex items-center gap-2 mb-4">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Gestión:</span>
            <div class="flex gap-1 flex-wrap">
                @forelse ($gestiones as $g)
                    <a href="{{ route('admin.ciclos.index', ['idEa' => $ensayo->id, 'gestion' => $g]) }}"
                        class="px-3 py-1 rounded-full text-xs font-semibold border transition
                            {{ $g == $gestionActual
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white text-gray-600 border-gray-300 hover:border-blue-400 hover:text-blue-600' }}">
                        {{ $g }}
                    </a>
                @empty
                    <span class="text-xs text-gray-400 italic">Sin gestiones registradas</span>
                @endforelse
                {{-- Botón para nueva gestión --}}
                <button @click="openCreate()"
                    class="px-3 py-1 rounded-full text-xs font-semibold border border-dashed border-gray-400 text-gray-500 hover:border-blue-400 hover:text-blue-600 transition">
                    + {{ now()->year }}
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                <strong>Se encontraron errores:</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tabla de ciclos --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-2.5 bg-gray-50 border-b flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700">
                    Gestión {{ $gestionActual }}
                    <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-normal">
                        {{ $ciclos->count() }} {{ $ciclos->count() == 1 ? 'ciclo' : 'ciclos' }}
                    </span>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-800">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Nombre</th>
                            <th class="px-3 py-2 text-left">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-vial text-indigo-400"></i> Muestras
                                </span>
                            </th>
                            <th class="px-3 py-2 text-left">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-chart-bar text-blue-400"></i> Resultados
                                </span>
                            </th>
                            <th class="px-3 py-2 text-left">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-file-alt text-green-400"></i> Reporte
                                </span>
                            </th>
                            <th class="px-3 py-2 text-left">Estado</th>
                            <th class="px-3 py-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ciclos as $c)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-3 py-2.5 font-semibold text-gray-800">
                                    {{ $c->nombre }}
                                    @if ($c->enPeriodoEnvioResultados())
                                        <span class="ml-1 px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-normal">En curso</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-xs text-gray-600">
                                    <span class="block">{{ $c->fecha_inicio_envio_muestras_show }}</span>
                                    <span class="text-gray-400">→ {{ $c->fecha_fin_envio_muestras_show }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-xs text-gray-600">
                                    <span class="block">{{ $c->fecha_inicio_envio_resultados_show }}</span>
                                    <span class="text-gray-400">→ {{ $c->fecha_fin_envio_resultados_show }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-xs text-gray-600">
                                    <span class="block">{{ $c->fecha_inicio_envio_reporte_show }}</span>
                                    <span class="text-gray-400">→ {{ $c->fecha_fin_envio_reporte_show }}</span>
                                </td>
                                <td class="px-3 py-2.5">
                                    @if ($c->estado)
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Habilitado</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">Deshabilitado</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex gap-1.5 justify-center">
                                        <button @click="openEdit({{ $c->toJson() }})"
                                            data-tippy-content="Editar"
                                            class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded shadow-sm text-xs">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form method="POST" action="{{ route('admin.ciclos.toggle', $c->id) }}">
                                            @csrf @method('PUT')
                                            <button type="submit"
                                                data-tippy-content="{{ $c->estado ? 'Deshabilitar' : 'Habilitar' }}"
                                                class="px-2 py-1 rounded shadow-sm text-xs {{ $c->estado ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }}">
                                                <i class="fas {{ $c->estado ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.ciclos.destroy', $c->id) }}"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar este ciclo?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                data-tippy-content="Eliminar"
                                                class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded shadow-sm text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-calendar-times text-2xl mb-2 block"></i>
                                    No hay ciclos registrados para la gestión {{ $gestionActual }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal --}}
        <div x-show="openModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
            x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 mx-4 max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Editar Ciclo' : 'Nuevo Ciclo'"></h2>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="isEdit
                        ? '{{ route('admin.ciclos.update', ':id') }}'.replace(':id', ciclo.id)
                        : '{{ route('admin.ciclos.store') }}'"
                    method="POST">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="id_ensayo" value="{{ $ensayo->id }}">
                    {{-- Permite recuperar el modo edición si el servidor devuelve errores --}}
                    <input type="hidden" name="ciclo_id" :value="ciclo?.id || ''">

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre del Ciclo</label>
                        <input type="text" name="nombre" x-model="form.nombre"
                            class="w-full border rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 focus:outline-none"
                            placeholder="Ej. Ciclo 1 - Química" required>
                    </div>

                    <div class="mb-4 bg-blue-50 border border-blue-100 text-blue-700 text-xs rounded px-3 py-2 flex items-start gap-2">
                        <i class="fas fa-magic mt-0.5"></i>
                        <span>Elige la <strong>primera fecha</strong> y las siguientes se completan solas en orden.
                            Luego solo ajusta las que necesites: el calendario no te dejará elegir una fecha anterior a la fase previa.</span>
                    </div>

                    {{-- Fase 1: Muestras --}}
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-vial text-indigo-500 text-xs"></i>
                            <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Fase 1 - Envío de Muestras</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pl-4 border-l-2 border-indigo-200">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio_envio_muestras"
                                    x-model="form.fecha_inicio_envio_muestras" @change="cascade('fecha_inicio_envio_muestras')"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-indigo-400 focus:outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Fecha Fin</label>
                                <input type="date" name="fecha_fin_envio_muestras"
                                    x-model="form.fecha_fin_envio_muestras" @change="cascade('fecha_fin_envio_muestras')"
                                    :min="form.fecha_inicio_envio_muestras"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-indigo-400 focus:outline-none"
                                    required>
                            </div>
                        </div>
                    </div>

                    {{-- Fase 2: Resultados --}}
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-chart-bar text-blue-500 text-xs"></i>
                            <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Fase 2 - Envío de Resultados</span>
                            <span class="text-xs text-gray-400 italic">(define la gestión/año)</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pl-4 border-l-2 border-blue-200">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio_envio_resultados"
                                    x-model="form.fecha_inicio_envio_resultados" @change="cascade('fecha_inicio_envio_resultados')"
                                    :min="form.fecha_fin_envio_muestras"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 focus:outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Fecha Fin</label>
                                <input type="date" name="fecha_fin_envio_resultados"
                                    x-model="form.fecha_fin_envio_resultados" @change="cascade('fecha_fin_envio_resultados')"
                                    :min="form.fecha_inicio_envio_resultados"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 focus:outline-none"
                                    required>
                            </div>
                        </div>
                    </div>

                    {{-- Fase 3: Reporte --}}
                    <div class="mb-5">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-file-alt text-green-500 text-xs"></i>
                            <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Fase 3 - Envío de Reporte</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pl-4 border-l-2 border-green-200">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio_envio_reporte"
                                    x-model="form.fecha_inicio_envio_reporte" @change="cascade('fecha_inicio_envio_reporte')"
                                    :min="form.fecha_fin_envio_resultados"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-green-400 focus:outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Fecha Fin</label>
                                <input type="date" name="fecha_fin_envio_reporte"
                                    x-model="form.fecha_fin_envio_reporte" @change="cascade('fecha_fin_envio_reporte')"
                                    :min="form.fecha_inicio_envio_reporte"
                                    class="w-full border rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-green-400 focus:outline-none"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-2 border-t">
                        <p class="text-xs text-amber-600 flex items-center gap-1" x-show="!valid" x-cloak>
                            <i class="fas fa-info-circle"></i>
                            Completa el nombre y las 6 fechas en orden cronológico.
                        </p>
                        <span x-show="valid" class="text-xs text-green-600 flex items-center gap-1" x-cloak>
                            <i class="fas fa-check-circle"></i> Listo para guardar
                        </span>
                        <div class="flex gap-2 ml-auto">
                            <button type="button" @click="openModal = false"
                                class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="!valid"
                                :class="valid ? 'bg-blue-600 hover:bg-blue-500' : 'bg-gray-300 cursor-not-allowed'"
                                class="px-4 py-1.5 text-white rounded text-sm transition">
                                Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function ciclosApp(old) {
            // Orden cronológico estricto de las 6 fechas
            const FIELDS = [
                'fecha_inicio_envio_muestras',
                'fecha_fin_envio_muestras',
                'fecha_inicio_envio_resultados',
                'fecha_fin_envio_resultados',
                'fecha_inicio_envio_reporte',
                'fecha_fin_envio_reporte',
            ];
            const emptyForm = () => ({
                nombre: '',
                fecha_inicio_envio_muestras: '',
                fecha_fin_envio_muestras: '',
                fecha_inicio_envio_resultados: '',
                fecha_fin_envio_resultados: '',
                fecha_inicio_envio_reporte: '',
                fecha_fin_envio_reporte: '',
            });

            return {
                openModal: false,
                isEdit: false,
                ciclo: null,
                form: emptyForm(),

                init() {
                    // El servidor devolvió errores: recuperamos lo que el usuario ya había escrito
                    if (old && old.hasErrors) {
                        const { hasErrors, ciclo_id, ...values } = old;
                        this.form = Object.assign(emptyForm(), values);
                        if (ciclo_id) {
                            this.isEdit = true;
                            this.ciclo = { id: ciclo_id };
                        }
                        this.openModal = true;
                    }
                },

                openCreate() {
                    this.isEdit = false;
                    this.ciclo = null;
                    this.form = emptyForm();
                    this.openModal = true;
                },

                openEdit(ciclo) {
                    this.isEdit = true;
                    this.ciclo = ciclo;
                    this.form = {
                        nombre: ciclo.nombre ?? '',
                        fecha_inicio_envio_muestras: ciclo.fecha_inicio_envio_muestras ?? '',
                        fecha_fin_envio_muestras: ciclo.fecha_fin_envio_muestras ?? '',
                        fecha_inicio_envio_resultados: ciclo.fecha_inicio_envio_resultados ?? '',
                        fecha_fin_envio_resultados: ciclo.fecha_fin_envio_resultados ?? '',
                        fecha_inicio_envio_reporte: ciclo.fecha_inicio_envio_reporte ?? '',
                        fecha_fin_envio_reporte: ciclo.fecha_fin_envio_reporte ?? '',
                    };
                    this.openModal = true;
                },

                // Al cambiar una fecha, completa/empuja las siguientes para mantener el orden cronológico
                cascade(field) {
                    const i = FIELDS.indexOf(field);
                    let prev = this.form[field];
                    if (i === -1 || !prev) return;
                    for (let j = i + 1; j < FIELDS.length; j++) {
                        if (!this.form[FIELDS[j]] || this.form[FIELDS[j]] < prev) {
                            this.form[FIELDS[j]] = prev;
                        }
                        prev = this.form[FIELDS[j]];
                    }
                },

                // Validación en vivo: nombre + 6 fechas completas y en orden
                get valid() {
                    if (!this.form.nombre || !this.form.nombre.trim()) return false;
                    for (const f of FIELDS) {
                        if (!this.form[f]) return false;
                    }
                    for (let i = 1; i < FIELDS.length; i++) {
                        if (this.form[FIELDS[i]] < this.form[FIELDS[i - 1]]) return false;
                    }
                    return true;
                },
            }
        }
    </script>
</x-app-layout>
