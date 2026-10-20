<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-chart-column text-indigo-500 mr-1"></i>
                Distribución de calificaciones por ensayo
            </h2>
            <p class="text-xs text-gray-500 mt-1">
                Cantidad de laboratorios por calificación final del certificado, agrupado por ensayo.
                @if ($esResponsablePuro)
                    <span class="text-amber-600 font-medium">Solo se muestran los ensayos que tiene asignados.</span>
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-end gap-3 mb-4 text-sm bg-white p-3 rounded-lg shadow-sm">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Gestión <span class="text-red-500">*</span></label>
                <select id="filter-gestion"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px] font-semibold">
                    @forelse ($gestiones as $gestion)
                        <option value="{{ $gestion }}" @selected((string)$gestion === (string)$gestionDefault)>{{ $gestion }}</option>
                    @empty
                        <option value="{{ $gestionDefault }}" selected>{{ $gestionDefault }}</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Área</label>
                <select id="filter-area"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[180px]">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->descripcion }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Paquete</label>
                <select id="filter-paquete"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[180px]">
                    <option value="">Todos los paquetes</option>
                    @foreach ($paquetes as $paquete)
                        <option value="{{ $paquete->id }}" data-area="{{ $paquete->id_area }}">
                            {{ $paquete->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Estado</label>
                <select id="filter-estado"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[180px]">
                    <option value="todos">Todos</option>
                    <option value="publicados" selected>Publicados</option>
                    <option value="no_publicados">No publicados</option>
                    <option value="pendientes_responsable">Pendientes (Responsable EA)</option>
                </select>
            </div>
            <div class="ml-auto flex gap-2">
                <button id="btn-export"
                    class="px-3 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-500 transition shadow text-xs">
                    <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                <h3 class="text-sm font-semibold text-gray-700">
                    Distribución
                    <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-600 text-white text-xs font-bold" id="chart-gestion-badge">
                        <i class="fas fa-calendar-alt"></i> Gestión <span id="chart-gestion-value"></span>
                    </span>
                </h3>
                <div id="chart-active-filters" class="flex flex-wrap gap-1"></div>
            </div>
            <div class="text-xs text-gray-500 mb-2" id="chart-summary"></div>
            <div style="height: 480px; position: relative;">
                <canvas id="chart-distribucion"></canvas>
            </div>
            <div id="chart-empty" class="hidden text-center text-gray-500 text-sm py-12">
                <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                <p>No hay datos para los filtros seleccionados.</p>
            </div>
        </div>

        <div id="drill-panel" class="hidden">
            <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                <h3 id="drill-title" class="text-base font-bold text-gray-800"></h3>
                <button id="drill-close"
                    class="flex items-center gap-1 px-3 py-1 border rounded-md text-xs bg-white hover:bg-gray-50 shadow-sm">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>

            <div class="flex justify-between items-center flex-wrap gap-3 mb-4 text-sm">
                <div class="flex items-center gap-2 flex-wrap">
                    <label for="drill-length" class="whitespace-nowrap">Mostrar</label>
                    <select id="drill-length"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[80px]">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="whitespace-nowrap">registros</span>
                </div>
                <div class="flex items-center gap-2 justify-end w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" id="drill-search"
                            class="w-full pl-10 pr-4 py-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs"
                            placeholder="Buscar laboratorio...">
                    </div>
                    <button id="drill-btn-search"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table id="drill-table" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Código</th>
                            <th class="px-4 py-2 text-left">Laboratorio</th>
                            <th class="px-4 py-2 text-left">Departamento</th>
                            <th class="px-4 py-2 text-left">Calificación</th>
                            <th class="px-4 py-2 text-left">Gestión</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-left">Publicado</th>
                            <th class="px-4 py-2 text-left">Actualizado</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="flex justify-between items-center flex-wrap mt-6 gap-2">
                <div id="drill-info"></div>
                <div id="drill-pagination" class="flex justify-center"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                const URL_DATA = "{{ route('admin.reportes.distribucion.data') }}";
                const URL_DRILL = "{{ route('admin.reportes.distribucion.drill') }}";
                const URL_EXPORT = "{{ route('admin.reportes.distribucion.export') }}";

                let chart = null;
                let idEnsayosActuales = [];
                let drillTable = null;

                function leerFiltros() {
                    return {
                        gestion: document.getElementById('filter-gestion').value,
                        id_area: document.getElementById('filter-area').value,
                        id_paquete: document.getElementById('filter-paquete').value,
                        estado: document.getElementById('filter-estado').value,
                    };
                }

                function queryString(obj) {
                    const params = new URLSearchParams();
                    Object.entries(obj).forEach(([k, v]) => {
                        if (v !== null && v !== undefined && v !== '') params.append(k, v);
                    });
                    return params.toString();
                }

                function filtrarPaquetesPorArea() {
                    const areaId = document.getElementById('filter-area').value;
                    const select = document.getElementById('filter-paquete');
                    let mantieneValor = false;
                    Array.from(select.options).forEach(opt => {
                        if (!opt.value) {
                            opt.hidden = false;
                            return;
                        }
                        const ok = !areaId || opt.dataset.area === areaId;
                        opt.hidden = !ok;
                        if (opt.selected && ok) mantieneValor = true;
                    });
                    if (!mantieneValor) select.value = '';
                }

                async function cargarChart() {
                    const filtros = leerFiltros();
                    const url = URL_DATA + '?' + queryString(filtros);
                    const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!resp.ok) {
                        console.error('Error cargando datos', resp.status);
                        return;
                    }
                    const json = await resp.json();
                    idEnsayosActuales = json.idEnsayos || [];

                    const empty = document.getElementById('chart-empty');
                    const canvas = document.getElementById('chart-distribucion');
                    actualizarFiltrosVisibles();
                    document.getElementById('chart-summary').textContent =
                        `${json.totalRegistros || 0} registros · ${json.labels.length} ensayos`;

                    if (!json.labels.length) {
                        empty.classList.remove('hidden');
                        canvas.style.display = 'none';
                        if (chart) { chart.destroy(); chart = null; }
                        return;
                    }
                    empty.classList.add('hidden');
                    canvas.style.display = '';

                    const config = {
                        type: 'bar',
                        data: {
                            labels: json.labels,
                            datasets: json.datasets.map(d => ({
                                label: d.label,
                                data: d.data,
                                backgroundColor: d.backgroundColor,
                                categoria: d.categoria,
                            })),
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    callbacks: {
                                        afterLabel: () => 'Click para ver detalle de laboratorios',
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    ticks: { maxRotation: 60, minRotation: 30, autoSkip: false, font: { size: 10 } },
                                },
                                y: { beginAtZero: true, ticks: { precision: 0 } },
                            },
                            onClick: (evt, elements) => {
                                if (!elements.length) return;
                                const { datasetIndex, index } = elements[0];
                                const ds = chart.data.datasets[datasetIndex];
                                const idEa = idEnsayosActuales[index];
                                const ensayoNombre = chart.data.labels[index];
                                abrirDrill(idEa, ds.categoria, ds.label, ensayoNombre);
                            },
                        },
                    };

                    if (chart) chart.destroy();
                    chart = new Chart(canvas.getContext('2d'), config);
                }

                function abrirDrill(idEa, categoria, calificacionLabel, ensayoNombre) {
                    const panel = document.getElementById('drill-panel');
                    panel.classList.remove('hidden');
                    document.getElementById('drill-title').textContent =
                        `${ensayoNombre} - ${calificacionLabel}`;
                    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });

                    const filtros = leerFiltros();
                    const ajaxUrl = URL_DRILL + '?' + queryString({ ...filtros, id_ea: idEa, categoria });

                    if (drillTable) {
                        drillTable.destroy();
                        $('#drill-table tbody').remove();
                    }
                    drillTable = $('#drill-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: ajaxUrl,
                        columns: [
                            { data: 'cod_lab', name: 'lab.cod_lab' },
                            { data: 'nombre_lab', name: 'lab.nombre_lab' },
                            { data: 'departamento', name: 'dep.nombre_dep' },
                            { data: 'calificacion_certificado', name: 'dc.calificacion_certificado' },
                            { data: 'gestion_certificado', name: 'c.gestion_certificado' },
                            { data: 'temporal', name: 'dc.temporal', orderable: false },
                            { data: 'publicado', name: 'c.publicado', orderable: false },
                            { data: 'updated_at', name: 'dc.updated_at' },
                        ],
                        language: { url: "{{ asset('translation/es.json') }}" },
                        pageLength: parseInt(document.getElementById('drill-length').value, 10) || 10,
                        order: [[1, 'asc']],
                        dom: 'rt',
                        lengthChange: false,
                        drawCallback: function () {
                            if (window.tippy) tippy('[data-tippy-content]');
                            if (window.setupPagination) {
                                window.setupPagination(drillTable, {
                                    infoSelector: '#drill-info',
                                    paginationSelector: '#drill-pagination',
                                });
                            }
                        },
                    });
                }

                function actualizarFiltrosVisibles() {
                    const container = document.getElementById('chart-active-filters');
                    container.innerHTML = '';
                    const gestionSel = document.getElementById('filter-gestion');
                    const areaSel = document.getElementById('filter-area');
                    const paqueteSel = document.getElementById('filter-paquete');
                    const estadoSel = document.getElementById('filter-estado');

                    document.getElementById('chart-gestion-value').textContent = gestionSel.value || '—';

                    const items = [
                        { label: 'Estado', value: estadoSel.options[estadoSel.selectedIndex].text, color: 'bg-slate-100 text-slate-700' },
                    ];
                    if (areaSel.value) {
                        items.push({ label: 'Área', value: areaSel.options[areaSel.selectedIndex].text, color: 'bg-emerald-100 text-emerald-700' });
                    }
                    if (paqueteSel.value) {
                        items.push({ label: 'Paquete', value: paqueteSel.options[paqueteSel.selectedIndex].text, color: 'bg-amber-100 text-amber-700' });
                    }
                    items.forEach(it => {
                        const span = document.createElement('span');
                        span.className = `inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs ${it.color}`;
                        span.innerHTML = `<span class="font-semibold">${it.label}:</span> ${it.value}`;
                        container.appendChild(span);
                    });
                }

                document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('filter-area').addEventListener('change', () => {
                        filtrarPaquetesPorArea();
                        cargarChart();
                    });
                    document.getElementById('filter-paquete').addEventListener('change', cargarChart);
                    document.getElementById('filter-gestion').addEventListener('change', cargarChart);
                    document.getElementById('filter-estado').addEventListener('change', cargarChart);

                    document.getElementById('btn-export').addEventListener('click', () => {
                        const url = URL_EXPORT + '?' + queryString(leerFiltros());
                        window.location.href = url;
                    });

                    document.getElementById('drill-close').addEventListener('click', () => {
                        document.getElementById('drill-panel').classList.add('hidden');
                    });

                    $('#drill-length').on('change', function () {
                        if (drillTable) drillTable.page.len(parseInt(this.value, 10) || 10).draw();
                    });
                    $('#drill-btn-search').on('click', () => {
                        if (drillTable) drillTable.search($('#drill-search').val()).draw();
                    });
                    $('#drill-search').on('keypress', e => {
                        if (e.which === 13 && drillTable) drillTable.search($('#drill-search').val()).draw();
                    });

                    filtrarPaquetesPorArea();
                    cargarChart();
                });
            })();
        </script>
    @endpush
</x-app-layout>
