import 'bootstrap/dist/js/bootstrap.bundle.js';
import 'admin-lte/dist/js/adminlte.js';

import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import Sortable from 'sortablejs';

window.Sortable = Sortable;

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

// --- Indicador global de "procesando" (barra superior + botón deshabilitado) ---

const barraCarga = document.createElement('div');
barraCarga.className = 'barra-carga-global';
document.body.prepend(barraCarga);

let peticionesActivas = 0;

function mostrarCarga() {
    peticionesActivas++;
    barraCarga.classList.remove('completa');
    requestAnimationFrame(() => barraCarga.classList.add('activa'));
}

function ocultarCarga() {
    peticionesActivas = Math.max(0, peticionesActivas - 1);

    if (peticionesActivas === 0) {
        barraCarga.classList.add('completa');
        setTimeout(() => barraCarga.classList.remove('activa', 'completa'), 300);
    }

    reactivarBotonesPendientes();
}

function reactivarBotonesPendientes() {
    if (peticionesActivas > 0) {
        return;
    }

    document.querySelectorAll('[data-cargando="1"]').forEach(restaurarBoton);
}

function restaurarBoton(boton) {
    clearTimeout(Number(boton.dataset.timeoutId));
    boton.disabled = false;
    boton.innerHTML = boton.dataset.textoOriginal ?? boton.innerHTML;
    delete boton.dataset.cargando;
    delete boton.dataset.textoOriginal;
    delete boton.dataset.timeoutId;
}

const fetchOriginal = window.fetch;
window.fetch = function (...args) {
    mostrarCarga();
    return fetchOriginal.apply(this, args).finally(ocultarCarga);
};

document.addEventListener('submit', (event) => {
    if (event.defaultPrevented) {
        return;
    }

    const boton = event.submitter;
    if (!boton || boton.disabled) {
        return;
    }

    boton.dataset.cargando = '1';
    boton.dataset.textoOriginal = boton.innerHTML;
    boton.disabled = true;
    boton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
    boton.dataset.timeoutId = String(setTimeout(() => restaurarBoton(boton), 15000));
});
