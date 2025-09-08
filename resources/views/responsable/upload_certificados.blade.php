<x-app-layout>
    <div class="px-4 max-w-6xl mx-auto space-y-2 text-sm">

        <!-- Botones de navegación -->
        <div class="flex gap-2">
            <button id="btn-tab-upload" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                📤 Subir documento
            </button>
            <button id="btn-tab-view" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                En Revision
            </button>

            <button id="btn-tab-ponderaciones" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                Ver Ponderaciones
            </button>
        </div>

        <div id="section-upload" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Subir los datos para la certificación de gestión
                {{ $gestion }} - Ensayo A. {!! $descripcion !!}</h2>
            <form action="{{ route('ea.lab.subir.ponderaciones', ['id' => $idEA]) }}" method="POST"
                enctype="multipart/form-data"
                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition"
                id="drop-area">
                @csrf
                <input type="file" name="archivo" id="file-input" accept=".csv" hidden>
                <i class="fas fa-file-csv text-4xl text-blue-500 mb-3"></i>
                <p class="text-gray-600">Arrastra y suelta tu archivo CSV aquí o</p>
                <button type="button" id="btn-select"
                    class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">Seleccionar archivo</button>

                <p id="file-name" class="mt-2 text-sm text-gray-500"></p>
                <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 hidden"
                    id="btn-upload">Subir Archivo</button>
            </form>
        </div>

        <!-- SECCIÓN 2: Ver Calificaciones -->
        <div id="section-view" class="bg-white rounded-lg shadow p-6 hidden">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Datos en revision</h2>
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-2 flex-wrap">
                    <label for="custom-length-tem" class="whitespace-nowrap">Mostrar</label>
                    <select id="custom-length-tem"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[80px]">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="whitespace-nowrap">registros</span>
                </div>
                <!-- Buscador -->
                <div class="flex justify-between items-center flex-wrap gap-4">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" id="custom-search-tem"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Buscar laboratorio...">
                    </div>
                    <button id="btn-search-tem"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table id="labs-certificados-tem" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Fecha Reg.</th>
                            <th class="px-4 py-2 text-left">Código</th>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Correo</th>
                            <th class="px-4 py-2 text-left">WhatsApp</th>
                            <th class="px-4 py-2 text-left">Desempeño</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-between items-center flex-wrap mt-6 gap-2">
                <div id="custom-info-certificado-tem"></div>
                <div id="custom-pagination-certificado-tem" class="flex justify-center"></div>
            </div>

            <form action="{{ route('confirmar.datos.certificados', ['id' => $idEA]) }}" method="POST">
                @csrf
                <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">
                    Confirmar los datos
                </button>
            </form>

        </div>

        <!-- SECCIÓN 3: Ver Ponderaciones -->
        <div id="section-ponderaciones-view" class="bg-white rounded-lg shadow p-6 hidden">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Datos de las ponderaciones a los laboratorios</h2>
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-2 flex-wrap">
                    <label for="custom-length-ok" class="whitespace-nowrap">Mostrar</label>
                    <select id="custom-length-ok"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[80px]">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="whitespace-nowrap">registros</span>
                </div>
                <!-- Buscador -->
                <div class="flex justify-between items-center flex-wrap gap-4">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" id="custom-search-ok"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Buscar laboratorio...">
                    </div>
                    <button id="btn-search-ok"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table id="labs-certificados-ok" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Fecha Reg.</th>
                            <th class="px-4 py-2 text-left">Código</th>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Correo</th>
                            <th class="px-4 py-2 text-left">WhatsApp</th>
                            <th class="px-4 py-2 text-left">Desempeño</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-between items-center flex-wrap mt-6 gap-2">
                <div id="custom-info-certificado-ok"></div>
                <div id="custom-pagination-certificado-ok" class="flex justify-center"></div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                let table = $('#labs-certificados-tem').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('ea.lab.desempeno.temporal', ['id' => $idEA]) }}",
                    },
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'created_at',
                            name: 'certificado.detalles.created_at'
                        },
                        {
                            data: 'cod_lab',
                            name: 'laboratorio.cod_lab'
                        },
                        {
                            data: 'nombre_lab',
                            name: 'laboratorio.nombre_lab'
                        },
                        {
                            data: 'mail_lab',
                            name: 'laboratorio.mail_lab',
                        },
                        {
                            data: 'wapp_lab',
                            name: 'laboratorio.wapp_lab'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },

                    ],
                    language: {
                        url:  "{{ asset('translation/es.json') }}"
                    },
                    dom: 'rt',
                    lengthChange: false,
                    drawCallback: function() {
                        tippy('[data-tippy-content]');
                        setupPagination(table, {
                            infoSelector: '#custom-info-certificado-tem',
                            paginationSelector: '#custom-pagination-certificado-tem'
                        });
                        $('.update-form-desempeno').on('submit', function(e) {
                            e.preventDefault();
                            const form = $(this);
                            const url = form.attr('action');
                            const data = {
                                _token: '{{ csrf_token() }}',
                                calificacion_certificado: form.find(
                                    'input[name="calificacion_certificado"]').val()
                            };

                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: data,
                                success: function(response) {
                                    table.draw();
                                    tippy.hideAll();
                                    showToast({
                                        title: 'Desempeño actualizado correctamente.'
                                    });
                                },
                                error: function(xhr) {
                                    showError('Error al actualizar el desempeño: ' + xhr
                                        .responseJSON.message);
                                }
                            });
                        });
                    }
                });

                $('#btn-search-tem').on('click', () => table.search($('#custom-search-tem').val()).draw());
                $('#custom-search-tem').on('keypress', e => {
                    if (e.which === 13) table.search($('#custom-search-tem').val()).draw();
                });
                $('#custom-length-tem').on('change', function() {
                    table.page.len(this.value).draw();
                });

                let table2 = $('#labs-certificados-ok').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('ea.lab.desempeno.confirmado', ['id' => $idEA]) }}",
                    },
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'created_at',
                            name: 'certificado.detalles.created_at'
                        },
                        {
                            data: 'cod_lab',
                            name: 'laboratorio.cod_lab'
                        },
                        {
                            data: 'nombre_lab',
                            name: 'laboratorio.nombre_lab'
                        },
                        {
                            data: 'mail_lab',
                            name: 'laboratorio.mail_lab',
                        },
                        {
                            data: 'wapp_lab',
                            name: 'laboratorio.wapp_lab'
                        },
                        {
                            data: 'desempeno',
                            name: 'certificado.detalles.calificacion_certificado'
                        }
                    ],
                    language: {
                        url:  "{{ asset('translation/es.json') }}"
                    },
                    dom: 'rt',
                    lengthChange: false,
                    drawCallback: function() {
                        // tippy('[data-tippy-content]');
                        setupPagination(table2, {
                            infoSelector: '#custom-info-certificado-ok',
                            paginationSelector: '#custom-pagination-certificado-ok'
                        });
                    }
                });
                $('#btn-search-ok').on('click', () => table2.search($('#custom-search-ok').val()).draw());
                $('#custom-search-ok').on('keypress', e => {
                    if (e.which === 13) table2.search($('#custom-search-ok').val()).draw();
                });
                $('#custom-length-ok').on('change', function() {
                    table2.page.len(this.value).draw();
                });
            });
        </script>
    @endpush
    <!-- Script -->
    <script>
        // Botones
        const btnTabUpload = document.getElementById('btn-tab-upload');
        const btnTabView = document.getElementById('btn-tab-view');
        const btnTabPonderaciones = document.getElementById('btn-tab-ponderaciones');
        // Secciones
        const sectionUpload = document.getElementById('section-upload');
        const sectionView = document.getElementById('section-view');
        const sectionPonderacionesView = document.getElementById('section-ponderaciones-view');

        // Cambiar entre secciones
        btnTabUpload.addEventListener('click', () => {
            sectionUpload.classList.remove('hidden');
            sectionView.classList.add('hidden');
            sectionPonderacionesView.classList.add('hidden');
            btnTabUpload.classList.add('bg-blue-600', 'text-white');
            btnTabUpload.classList.remove('bg-gray-200', 'text-gray-800');
            btnTabView.classList.remove('bg-blue-600', 'text-white');
            btnTabView.classList.add('bg-gray-200', 'text-gray-800');
            btnTabPonderaciones.classList.remove('bg-blue-600', 'text-white');
            btnTabPonderaciones.classList.add('bg-gray-200', 'text-gray-800');
        });

        btnTabView.addEventListener('click', () => {
            sectionUpload.classList.add('hidden');
            sectionView.classList.remove('hidden');
            sectionPonderacionesView.classList.add('hidden');
            btnTabView.classList.add('bg-blue-600', 'text-white');
            btnTabView.classList.remove('bg-gray-200', 'text-gray-800');
            btnTabUpload.classList.remove('bg-blue-600', 'text-white');
            btnTabUpload.classList.add('bg-gray-200', 'text-gray-800');
            btnTabPonderaciones.classList.remove('bg-blue-600', 'text-white');
            btnTabPonderaciones.classList.add('bg-gray-200', 'text-gray-800');
        });

        btnTabPonderaciones.addEventListener('click', () => {
            sectionUpload.classList.add('hidden');
            sectionView.classList.add('hidden');
            sectionPonderacionesView.classList.remove('hidden');
            btnTabPonderaciones.classList.add('bg-blue-600', 'text-white');
            btnTabPonderaciones.classList.remove('bg-gray-200', 'text-gray-800');
            btnTabUpload.classList.remove('bg-blue-600', 'text-white');
            btnTabUpload.classList.add('bg-gray-200', 'text-gray-800');
            btnTabView.classList.remove('bg-blue-600', 'text-white');
            btnTabView.classList.add('bg-gray-200', 'text-gray-800');
        });

        // Drag & Drop
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('file-input');
        const btnSelect = document.getElementById('btn-select');
        const fileName = document.getElementById('file-name');
        const btnUpload = document.getElementById('btn-upload');

        btnSelect.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileName.textContent = `Archivo seleccionado: ${fileInput.files[0].name}`;
                btnUpload.classList.remove('hidden');
            }
        });

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('border-blue-400', 'bg-blue-50');
        });
        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('border-blue-400', 'bg-blue-50');
        });
        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('border-blue-400', 'bg-blue-50');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileName.textContent = `Archivo seleccionado: ${fileInput.files[0].name}`;
                btnUpload.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
