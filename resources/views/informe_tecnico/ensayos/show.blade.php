<x-app-layout>
    <div class="px-4 max-w-6xl mx-auto space-y-2 text-sm">

        <!-- Botones de navegación -->
        <div class="flex gap-2">
            <button id="btn-tab-upload" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                📤 Subir archivo
            </button>
            <button id="btn-tab-informes" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                Ver informes
            </button>
            <button id="btn-tab-download" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
                onclick="window.location='{{ route('formato.zip.descargar') }}'">
                <i class="fas fa-download mr-1"></i> Descargar Formato .zip
            </button>
            <form method="GET" id="filtrosForm" class="bg-white rounded-lg p-2 shadow">
                <div class="flex">
                    <div class="flex flex-wrap gap-3 items-end text-sm items-center flex-col">
                        <label class="block text-sm whitespace-nowrap">Gestión</label>
                        <select name="gestion" id="filter-gestion"
                            class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px]">
                            @foreach ($gestiones ?? [now()->year] as $g)
                                <option value="{{ $g }}"
                                    {{ (request('gestion') ?? now()->year) == $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-wrap gap-3 items-end text-sm items-center flex-col">
                        <label class="block text-sm whitespace-nowrap">Ciclo</label>
                        <select name="ciclo" id="filter-ciclo"
                            class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px]">
                            @foreach ($ciclos as $ciclo)
                                <option value="{{ $ciclo->id }}"
                                    {{ request('ciclo') == $ciclo->id ? 'selected' : '' }}>
                                    {{ $ciclo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </form>
        </div>

        <div id="section-upload" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Subir los documentos de informes tecnicos de gestión
                {{ $gestion }} - Ensayo A. {!! $descripcion !!}</h2>
            <form action="{{ route('informe.tecnico.subir', ['id' => $idEA] + request()->all()) }}" method="POST"
                enctype="multipart/form-data"
                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition"
                id="drop-area">
                @csrf
                <input type="file" name="archivo" id="file-input" accept=".zip" hidden>
                <input type="hidden" name="id_ciclo" value="{{ $selectedCiclo }}">
                <input type="hidden" name="id_ensayo" value="{{ $idEA }}">
                <i class="far fa-file-archive text-4xl text-blue-500 mb-3"></i>
                <p class="text-gray-600">Arrastra y suelta tu archivo .zip aquí o</p>
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
                            <th class="px-4 py-2 text-left">Fecha de Act.</th>
                            <th class="px-4 py-2 text-left">Código</th>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Correo</th>
                            <th class="px-4 py-2 text-left">WhatsApp</th>
                            <th class="px-4 py-2 text-left">Desempeño</th>
                            <th class="px-4 py-2 text-left">Acciones</th>
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
                <input type="hidden" name="gestion" value="{{ $gestion }}">
                <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">
                    Finalizar revisión de datos
                </button>
            </form>

        </div>

        <!-- SECCIÓN 3: Ver Ponderaciones -->
        <div id="section-ponderaciones-view" class="bg-white rounded-lg shadow p-6 hidden">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Informes de los laboratorios</h2>
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
                <table id="labs-informes" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-center">Informe</th>
                            <th class="px-4 py-2 text-center">Fecha de Reg.</th>
                            <th class="px-4 py-2 text-center">Fecha de Act.</th>
                            <th class="px-4 py-2 text-left">Código</th>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-between items-center flex-wrap mt-6 gap-2">
                <div id="custom-info-informe"></div>
                <div id="custom-pagination-informe" class="flex justify-center"></div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let table2 = $('#labs-informes').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('informe.tecnico.listados', ['idCiclo' => $selectedCiclo] + request()->all()) }}",
                    },
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'informe',
                            name: 'informe',
                            orderable: false,
                            className: 'text-center'
                        }, {
                            data: 'created_at',
                            name: 'created_at',
                            className: 'text-center'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at',
                            className: 'text-center'
                        },
                        {
                            data: 'cod_lab',
                            name: 'laboratorio.cod_lab',
                        },
                        {
                            data: 'nombre_lab',
                            name: 'laboratorio.nombre_lab',
                        },
                        {
                            data: 'acciones',
                            name: 'acciones',
                            orderable: false,
                        }
                    ],
                    language: {
                        url: "{{ asset('translation/es.json') }}"
                    },
                    dom: 'rt',
                    lengthChange: false,
                    drawCallback: function() {
                        tippy('[data-tippy-content]');
                        setupPagination(table2, {
                            infoSelector: '#custom-info-informe',
                            paginationSelector: '#custom-pagination-informe'
                        });
                        $(document).on('click', '.btn-eliminar-informe', function() {
                            const id = $(this).data('id');
                            mostrarAlertaConfirmacion(
                                '¿Seguro que deseas eliminar este informe técnico?',
                                'Esta acción no se puede deshacer.',
                                'error',
                                'Sí, eliminar',
                                () => {
                                    $.ajax({
                                        url: "{{ route('informe.tecnico.eliminar', ':id') }}"
                                            .replace(':id', id),
                                        type: 'DELETE',
                                        data: {
                                            _token: "{{ csrf_token() }}"
                                        },
                                        success: function(res) {
                                            table2.ajax.reload(null, false);
                                        },
                                        error: function(err) {
                                            alert(err.responseJSON?.message ||
                                                'Error al eliminar.');
                                        }
                                    });
                                }
                            );
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
            // Botones
            const btnTabUpload = document.getElementById('btn-tab-upload');
            // const btnTabView = document.getElementById('btn-tab-view');
            const btnTabInformes = document.getElementById('btn-tab-informes');
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
                // btnTabView.classList.remove('bg-blue-600', 'text-white');
                // btnTabView.classList.add('bg-gray-200', 'text-gray-800');
                btnTabInformes.classList.remove('bg-blue-600', 'text-white');
                btnTabInformes.classList.add('bg-gray-200', 'text-gray-800');
            });

            btnTabInformes.addEventListener('click', () => {
                sectionUpload.classList.add('hidden');
                sectionView.classList.add('hidden');
                sectionPonderacionesView.classList.remove('hidden');
                btnTabInformes.classList.add('bg-blue-600', 'text-white');
                btnTabInformes.classList.remove('bg-gray-200', 'text-gray-800');
                btnTabUpload.classList.remove('bg-blue-600', 'text-white');
                btnTabUpload.classList.add('bg-gray-200', 'text-gray-800');
                // btnTabView.classList.remove('bg-blue-600', 'text-white');
                // btnTabView.classList.add('bg-gray-200', 'text-gray-800');
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
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('filtrosForm');
                const inputsAutoSubmit = [
                    document.getElementById('filter-gestion'),
                    document.getElementById('filter-ciclo'),
                ];
                inputsAutoSubmit.forEach(input => {
                    if (input) {
                        input.addEventListener('change', () => {
                            form.submit();
                        });
                    }
                });
            });

            btnTabInformes.click();
        </script>
    @endpush
</x-app-layout>
