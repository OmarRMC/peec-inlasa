import './bootstrap';
import './swal-confirm';
import { confirmDelete, showToast, confirmDialog, showError } from './utils/alert';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import Swal from 'sweetalert2';
import Alpine from 'alpinejs';
import jquery from 'jquery';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import { setupPagination } from './components/datatable-pagination.js';

window.setupPagination = setupPagination;
// DataTables se inicializa usando jQuery
window.$ = window.jQuery = jquery;
import 'datatables.net';

window.Alpine = Alpine;
window.Swal = Swal;
window.confirmDelete = confirmDelete;

document.addEventListener('DOMContentLoaded', () => {
    tippy('[data-tippy-content]');
});
Alpine.start();
