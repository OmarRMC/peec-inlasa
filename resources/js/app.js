import './bootstrap';
import './swal-confirm';
import { confirmDelete, showToast, confirmDialog, showError, confirmAsyncHandle, mostrarAlertaConfirmacion } from './utils/alert';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import Swal from 'sweetalert2';
import Alpine from 'alpinejs';
import jquery from 'jquery';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import { setupPagination } from './components/datatable-pagination.js';

import * as pdfjsLib from 'pdfjs-dist/legacy/build/pdf';
pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/utils/pdf.worker.min.mjs';
window.pdfjsLib = pdfjsLib;
window.setupPagination = setupPagination;
// DataTables se inicializa usando jQuery
window.$ = window.jQuery = jquery;
import 'datatables.net';
// tippy('[data-mesaje-validacion]', {
//     content(reference) {
//         return reference.getAttribute('data-mesaje-validacion');
//     },
// });
window.Alpine = Alpine;
window.Swal = Swal;
window.confirmDelete = confirmDelete;
window.confirmAsyncHandle = confirmAsyncHandle;
window.mostrarAlertaConfirmacion = mostrarAlertaConfirmacion;
window.showToast = showToast;
window.showError = showError;

document.addEventListener('DOMContentLoaded', () => {
    tippy('[data-tippy-content]');
    tippy('[data-mensaje-validacion]', {
        content(reference) {
            return reference.getAttribute('data-mensaje-validacion');
        },
        placement: 'top',
        animation: 'shift-away-subtle',
        theme: 'validacion',
        arrow: true,
        trigger: 'focus',
    });
});
Alpine.start();
