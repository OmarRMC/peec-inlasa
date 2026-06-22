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
            <button id="btn-tab-manual" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                Registro manual
            </button>
            <button id="btn-tab-download" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
                onclick="window.location='{{ route('formato.csv.descargar') }}'">
                <i class="fas fa-file-csv"></i> Descargar Formato
            </button>
            <form method="GET" id="filtrosForm" class="bg-white rounded-lg p-2 shadow">
                <div class="flex flex-wrap gap-3 items-end text-sm items-center">
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
            </form>
        </div>

        <div id="section-upload" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Subir los datos para la certificación de gestión
                {{ $gestion }} - Ensayo A. {!! $descripcion !!}</h2>
            <form action="{{ route('ea.lab.subir.ponderaciones', ['id' => $idEA] + request()->all()) }}" method="POST"
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
                            <th class="px-4 py-2 text-left">Fecha de Act.</th>
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
                <input type="hidden" name="gestion" value="{{ $gestion }}">
                <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">
                    Finalizar revisión de datos
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
                            <th class="px-4 py-2 text-left">Fecha de Act.</th>
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

        <!-- SECCIÓN 4: Registro manual (tabla editable) -->
        <div id="section-manual" class="bg-white rounded-lg shadow p-6 hidden">
            <h2 class="text-lg font-bold text-gray-800 mb-1">Registro manual de desempeño - Gestión {{ $gestion }}</h2>
            <p class="text-xs text-gray-500 mb-4">Escriba el desempeño en cada laboratorio o use "Aplicar" para cargar el mismo valor en bloque. Las filas vacías se omiten.</p>

            <datalist id="desempeno-list">
                <option value="EXCELENTE"></option>
                <option value="SATISFACTORIO"></option>
                <option value="BUENO"></option>
                <option value="ACEPTABLE"></option>
                <option value="SUFICIENTE"></option>
                <option value="REGULAR"></option>
                <option value="CUESTIONABLE"></option>
                <option value="NO SATISFACTORIO"></option>
            </datalist>

            @if ($labsManual->isEmpty())
                <div class="p-4 bg-yellow-50 text-yellow-800 rounded text-sm">
                    No hay laboratorios inscritos (aprobados/vencidos) en esta gestión.
                </div>
            @else
                <!-- Barra aplicar masivo -->
                <div class="flex flex-wrap items-end gap-3 mb-3 bg-gray-50 p-3 rounded">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Valor a aplicar</label>
                        <input type="text" id="masivo-valor" list="desempeno-list" maxlength="100" autocomplete="off"
                            placeholder="Ej. SATISFACTORIO"
                            class="border-gray-300 rounded-md shadow-sm text-sm px-2 py-1 min-w-[200px]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Aplicar a</label>
                        <select id="masivo-destino" class="border-gray-300 rounded-md shadow-sm text-sm px-2 py-1">
                            <option value="seleccionados">Seleccionados</option>
                            <option value="vacios">Vacíos</option>
                        </select>
                    </div>
                    <button type="button" id="btn-aplicar-masivo"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 text-sm">
                        Aplicar
                    </button>
                    <div class="relative ml-auto">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" id="masivo-buscar" placeholder="Buscar laboratorio..."
                            class="pl-10 pr-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm w-64">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="tabla-desempeno-masivo" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left w-10">
                                    <input type="checkbox" id="check-todos">
                                </th>
                                <th class="px-3 py-2 text-left">Código</th>
                                <th class="px-3 py-2 text-left">Laboratorio</th>
                                <th class="px-3 py-2 text-left">Desempeño</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($labsManual as $l)
                                <tr class="fila-lab border-b" data-id="{{ $l['id_inscripcion'] }}">
                                    <td class="px-3 py-2">
                                        <input type="checkbox" class="row-check">
                                    </td>
                                    <td class="px-3 py-2 cod-lab">{{ $l['cod_lab'] }}</td>
                                    <td class="px-3 py-2 nombre-lab">{{ $l['nombre_lab'] }}</td>
                                    <td class="px-3 py-2">
                                        <input type="text" class="row-desempeno w-full border-gray-300 rounded-md shadow-sm text-sm px-2 py-1"
                                            list="desempeno-list" maxlength="100" autocomplete="off"
                                            value="{{ $l['desempeno'] }}" placeholder="Elija o escriba...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="button" id="btn-guardar-masivo"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">
                        Guardar todo
                    </button>
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
        <script>
            let table, table2;
            document.addEventListener('DOMContentLoaded', function() {

                const urlParams = new URLSearchParams(window.location.search);
                if (!urlParams.has('gestion')) {
                    const selectGestion = document.getElementById('filter-gestion');
                    const gestionDefault = selectGestion ? selectGestion.value : '{{ now()->year }}';
                    urlParams.set('gestion', gestionDefault);
                    window.location.search = urlParams.toString();
                    return;
                }
                table = $('#labs-certificados-tem').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('ea.lab.desempeno.temporal', ['id' => $idEA] + request()->all()) }}",
                        error: function(xhr, error, thrown) {
                            let mensaje = 'Error al cargar los datos en revisión.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                mensaje = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.info) {
                                mensaje = xhr.responseJSON.info;
                            }
                            showError(mensaje);
                            console.error('Error DataTable temporal:', error, thrown);
                        }
                    },
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'updated_at',
                            name: 'certificado.detalles.updated_at'
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
                        url: "{{ asset('translation/es.json') }}"
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
                                    let mensaje = 'Error al actualizar el desempeño.';
                                    if (xhr.responseJSON) {
                                        if (xhr.responseJSON.message) {
                                            mensaje = xhr.responseJSON.message;
                                        } else if (xhr.responseJSON.info) {
                                            mensaje = xhr.responseJSON.info;
                                        }
                                    }
                                    showError(mensaje);
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

                table2 = $('#labs-certificados-ok').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('ea.lab.desempeno.confirmado', ['id' => $idEA] + request()->all()) }}",
                        error: function(xhr, error, thrown) {
                            let mensaje = 'Error al cargar las ponderaciones.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                mensaje = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.info) {
                                mensaje = xhr.responseJSON.info;
                            }
                            showError(mensaje);
                            console.error('Error DataTable confirmado:', error, thrown);
                        }
                    },
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'updated_at',
                            name: 'certificado.detalles.updated_at'
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
                        url: "{{ asset('translation/es.json') }}"
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

                // --- Registro masivo de desempeño (tabla editable, confirmación directa) ---

                // Aplicar un valor en bloque (a seleccionados o a vacíos). Solo cliente.
                $('#btn-aplicar-masivo').on('click', function() {
                    const valor = $('#masivo-valor').val();
                    const destino = $('#masivo-destino').val();
                    $('#tabla-desempeno-masivo tbody tr.fila-lab').each(function() {
                        const $fila = $(this);
                        if ($fila.is(':hidden')) return; // respeta el filtro de búsqueda
                        const $input = $fila.find('.row-desempeno');
                        if (destino === 'seleccionados') {
                            if ($fila.find('.row-check').is(':checked')) $input.val(valor);
                        } else { // vacios
                            if (!$input.val().trim()) $input.val(valor);
                        }
                    });
                });

                // Seleccionar / deseleccionar todos (filas visibles).
                $('#check-todos').on('change', function() {
                    const marcado = this.checked;
                    $('#tabla-desempeno-masivo tbody tr.fila-lab:visible .row-check').prop('checked', marcado);
                });

                // Filtro por código o nombre.
                $('#masivo-buscar').on('input', function() {
                    const q = this.value.toLowerCase();
                    $('#tabla-desempeno-masivo tbody tr.fila-lab').each(function() {
                        const txt = ($(this).find('.cod-lab').text() + ' ' + $(this).find('.nombre-lab').text()).toLowerCase();
                        $(this).toggle(txt.indexOf(q) !== -1);
                    });
                });

                // Guardar todo: recolecta filas con desempeño no vacío y envía en un POST.
                $('#btn-guardar-masivo').on('click', function() {
                    const registros = [];
                    $('#tabla-desempeno-masivo tbody tr.fila-lab').each(function() {
                        const valor = $(this).find('.row-desempeno').val().trim();
                        if (valor !== '') {
                            registros.push({
                                id_inscripcion: $(this).data('id'),
                                calificacion_certificado: valor
                            });
                        }
                    });
                    if (registros.length === 0) {
                        showError('No hay desempeños para guardar. Complete al menos una fila.');
                        return;
                    }

                    const $btn = $(this).prop('disabled', true);
                    $.ajax({
                        url: "{{ route('ea.lab.desempeno.masivo', ['id' => $idEA]) }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            gestion: '{{ $gestion }}',
                            registros: registros
                        },
                        success: function(r) {
                            showToast({ title: r.message || 'Desempeños registrados correctamente.' });
                            if (typeof table2 !== 'undefined') table2.draw();
                            // Marca visual de filas guardadas (las que tenían valor).
                            $('#tabla-desempeno-masivo tbody tr.fila-lab').each(function() {
                                if ($(this).find('.row-desempeno').val().trim() !== '') {
                                    $(this).addClass('bg-green-50');
                                }
                            });
                        },
                        error: function(xhr) {
                            let m = 'Error al registrar los desempeños.';
                            if (xhr.responseJSON) {
                                m = xhr.responseJSON.message || xhr.responseJSON.info || m;
                                if (xhr.responseJSON.errors) {
                                    m = Object.values(xhr.responseJSON.errors).flat().join(' ');
                                }
                            }
                            showError(m);
                        },
                        complete: function() {
                            $btn.prop('disabled', false);
                        }
                    });
                });
            });
            // Botones
            const btnTabUpload = document.getElementById('btn-tab-upload');
            const btnTabView = document.getElementById('btn-tab-view');
            const btnTabPonderaciones = document.getElementById('btn-tab-ponderaciones');
            const btnTabManual = document.getElementById('btn-tab-manual');
            // Secciones
            const sectionUpload = document.getElementById('section-upload');
            const sectionView = document.getElementById('section-view');
            const sectionPonderacionesView = document.getElementById('section-ponderaciones-view');
            const sectionManual = document.getElementById('section-manual');

            const tabs = [
                { btn: btnTabUpload, section: sectionUpload },
                { btn: btnTabView, section: sectionView },
                { btn: btnTabPonderaciones, section: sectionPonderacionesView },
                { btn: btnTabManual, section: sectionManual },
            ];

            // Cambiar entre secciones
            function activarTab(activo) {
                tabs.forEach(({ btn, section }) => {
                    const esActivo = btn === activo;
                    section.classList.toggle('hidden', !esActivo);
                    btn.classList.toggle('bg-blue-600', esActivo);
                    btn.classList.toggle('text-white', esActivo);
                    btn.classList.toggle('bg-gray-200', !esActivo);
                    btn.classList.toggle('text-gray-800', !esActivo);
                });
            }

            tabs.forEach(({ btn }) => btn.addEventListener('click', () => activarTab(btn)));

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
                ];
                inputsAutoSubmit.forEach(input => {
                    if (input) {
                        input.addEventListener('change', () => {
                            form.submit();
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
