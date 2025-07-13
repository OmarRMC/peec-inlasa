export function setupPagination(table, options = {}) {
    const infoSelector = options.infoSelector || '#custom-info';
    const paginationSelector = options.paginationSelector || '#custom-pagination';

    const info = table.page.info();
    const currentPage = info.page;
    const totalPages = info.pages;
    const start = info.start + 1;
    const end = info.end;
    const total = info.recordsTotal;

    // Mostrar el resumen de registros
    $(infoSelector).html(
        `<span class="text-gray-600">Mostrando del <span class="font-semibold">${start}</span> al <span class="font-semibold">${end}</span> de <span class="font-semibold">${total}</span> registros</span>`
    );

    // Paginación
    const $pagination = $(paginationSelector);
    $pagination.empty();

    const createBtn = (label, page, isCurrent, isDisabled = false) => {
        return `<button ${isDisabled ? 'disabled' : ''} data-page="${page}"
            class="px-3 py-1 rounded-md border text-sm transition
                ${isDisabled ? 'bg-gray-100 text-gray-400 border-gray-200 opacity-50 cursor-not-allowed' : ''}
                ${isCurrent ? 'bg-indigo-600 text-white border-indigo-600 shadow' : 'bg-white text-indigo-700 border-gray-300 hover:bg-indigo-50'}">
            ${label}
        </button>`;
    };

    // "Primero" y "Anterior"
    $pagination.append(createBtn('Primero', 0, false, currentPage === 0));
    $pagination.append(createBtn('‹', currentPage - 1, false, currentPage === 0));

    // Páginas visibles
    const visiblePages = 4;
    let startPage = Math.max(currentPage - Math.floor(visiblePages / 2), 0);
    let endPage = Math.min(startPage + visiblePages, totalPages);
    if (endPage - startPage < visiblePages)
        startPage = Math.max(endPage - visiblePages, 0);

    for (let i = startPage; i < endPage; i++) {
        $pagination.append(createBtn(i + 1, i, i === currentPage));
    }

    // "Siguiente" y "Último"
    $pagination.append(createBtn('›', currentPage + 1, false, currentPage >= totalPages - 1));
    $pagination.append(createBtn('Último', totalPages - 1, false, currentPage >= totalPages - 1));

    // Acción al hacer clic
    $(`${paginationSelector} button[data-page]`).on('click', function () {
        const page = $(this).data('page');
        table.page(page).draw('page');
    });
}
