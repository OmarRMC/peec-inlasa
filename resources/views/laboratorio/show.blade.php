<x-app-layout>
    <div class="px-4 py-6 max-w-5xl mx-auto">
        <!-- Título -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalles del Laboratorio</h1>
            @if (!isset($backTo) || $backTo != false)
                <x-shared.btn-volver :url="route('laboratorio.index')" />
            @endif
        </div>

        <!-- Contenedor principal -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden divide-y divide-gray-200">

            <!-- Sección: Datos Generales -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">🧪 Datos Generales</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>Nombre:</strong> {{ $laboratorio->nombre_lab }}</div>
                    <div><strong>Código:</strong> {{ $laboratorio->cod_lab }}</div>
                    <div><strong>Sigla:</strong> {{ $laboratorio->sigla_lab ?? '-' }}</div>
                    <div><strong>NIT:</strong> {{ $laboratorio->nit_lab }}</div>
                    <div><strong>Ant. Código PEEC:</strong> {{ $laboratorio->antcod_peec ?? '-' }}</div>
                    <div><strong>Sedes:</strong> {{ $laboratorio->numsedes_lab ?? '-' }}</div>
                    <div><strong>Categoría:</strong> {{ $laboratorio->categoria->descripcion ?? '-' }}</div>
                    <div><strong>Tipo:</strong> {{ $laboratorio->tipo->descripcion ?? '-' }}</div>
                    <div><strong>Nivel:</strong> {{ $laboratorio->nivel->descripcion_nivel ?? '-' }}</div>
                    <div><strong>Estado:</strong>
                        @if ($laboratorio->status)
                            <span
                                class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">Activo</span>
                        @else
                            <span
                                class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">Inactivo</span>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Sección: Responsables -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">👤 Responsables</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>Responsable:</strong> {{ $laboratorio->respo_lab }}</div>
                    <div><strong>CI Responsable:</strong> {{ $laboratorio->ci_respo_lab ?? '-' }}</div>
                    <div><strong>Representante Legal:</strong> {{ $laboratorio->repreleg_lab }}</div>
                    <div><strong>CI Representante Legal:</strong> {{ $laboratorio->ci_repreleg_lab ?? '-' }}</div>
                </div>
            </section>

            <!-- Sección: Ubicación -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">📍 Ubicación</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>País:</strong> {{ $laboratorio->pais->nombre ?? '-' }}</div>
                    <div><strong>Departamento:</strong> {{ $laboratorio->departamento->nombre_dep ?? '-' }}</div>
                    <div><strong>Provincia:</strong> {{ $laboratorio->provincia->nombre_prov ?? '-' }}</div>
                    <div><strong>Municipio:</strong> {{ $laboratorio->municipio->nombre_municipio ?? '-' }}</div>
                    <div><strong>Zona:</strong> {{ $laboratorio->zona_lab }}</div>
                    <div><strong>Dirección:</strong> {{ $laboratorio->direccion_lab }}</div>
                </div>
            </section>

            <!-- Sección: Contacto -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">📞 Contacto</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>WhatsApp:</strong> {{ $laboratorio->wapp_lab }}</div>
                    <div><strong>WhatsApp 2:</strong> {{ $laboratorio->wapp2_lab ?? '-' }}</div>
                    <div><strong>Email:</strong> {{ $laboratorio->mail_lab }}</div>
                    <div><strong>Email 2:</strong> {{ $laboratorio->mail2_lab ?? '-' }}</div>
                    <div><strong>Teléfono:</strong> {{ $laboratorio->usuario->telefono ?? '-' }}</div>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
