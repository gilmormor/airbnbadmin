import 'bootstrap/dist/js/bootstrap.bundle.js';
import 'admin-lte/dist/js/adminlte.js';

import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

DataTable.defaults.language = {
    search: 'Buscar:',
    lengthMenu: 'Mostrar _MENU_ registros',
    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
    infoEmpty: 'Sin registros',
    infoFiltered: '(filtrado de _MAX_ registros totales)',
    zeroRecords: 'Sin resultados encontrados',
    emptyTable: 'No hay datos disponibles',
    paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' },
};

window.DataTable = DataTable;
