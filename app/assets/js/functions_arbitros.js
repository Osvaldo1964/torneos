const API_URL = app_config.api_url;
const token = app_config.token;
let torneoActual = null;
let tablaPagos = null;
let tablaHistorial = null;
let tablaArbitros = null;

document.addEventListener('DOMContentLoaded', () => {
    cargarTorneos();
    inicializarEventos();
});

function inicializarEventos() {
    // Cambio de torneo
    document.getElementById('selectTorneo').addEventListener('change', (e) => {
        torneoActual = e.target.value;
        if (torneoActual) {
            document.getElementById('mensajeInicial').classList.add('d-none');
            document.getElementById('seccionArbitros').classList.remove('d-none');

            cargarRoles();
            cargarPagosPendientes();
        } else {
            document.getElementById('mensajeInicial').classList.remove('d-none');
            document.getElementById('seccionArbitros').classList.add('d-none');
        }
    });

    // Forms
    if (document.getElementById('formRol')) document.getElementById('formRol').onsubmit = (e) => guardarRol(e);
    document.getElementById('formArbitro').onsubmit = (e) => guardarArbitro(e);
    document.getElementById('formRegistrarPago').onsubmit = (e) => procesarPago(e);
}

// --- CARGA DE TORNEOS ---
async function cargarTorneos() {
    try {
        const response = await fetch(`${API_URL}Posiciones/torneos`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const result = await response.json();
        if (result.status) {
            const select = document.getElementById('selectTorneo');
            result.data.torneos.forEach(torneo => {
                const option = document.createElement('option');
                option.value = torneo.id_torneo;
                option.textContent = torneo.nombre;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar torneos:', error);
    }
}

// --- GESTIÓN DE ROLES Y TARIFAS ---
async function cargarRoles() {
    if (!torneoActual) return;
    try {
        const response = await fetch(`${API_URL}Arbitros/roles/${torneoActual}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const res = await response.json();

        const container = document.getElementById('rolesBadges');
        const tbody = document.getElementById('bodyRoles');
        if (container) container.innerHTML = '';
        if (tbody) tbody.innerHTML = '';

        if (res.status && res.data.length > 0) {
            res.data.forEach(r => {
                // Dashboard Badges
                if (container) {
                    const span = document.createElement('span');
                    span.className = "badge bg-soft-primary text-primary p-2 px-3 rounded-pill me-2";
                    span.innerHTML = `${r.nombre}: <strong>${formatMoney(r.monto)}</strong>`;
                    container.appendChild(span);
                }

                // Modal Table
                if (tbody) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="fw-bold">${r.nombre}</td>
                        <td class="text-end">${formatMoney(r.monto)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light" onclick="editarRol(${r.id_rol}, '${r.nombre}', ${r.monto})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-danger" onclick="eliminarRol(${r.id_rol})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
            });
        } else {
            if (container) container.innerHTML = '<small class="text-muted">Sin tarifas configuradas</small>';
            if (tbody) tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-muted">No hay roles definidos</td></tr>';
        }
    } catch (error) {
        console.error("Error al cargar roles:", error);
    }
}

async function guardarRol(e) {
    e.preventDefault();
    const data = {
        id_torneo: torneoActual,
        id_rol: document.getElementById('id_rol_edit').value,
        nombre: document.getElementById('rol_nombre').value,
        monto: document.getElementById('rol_monto').value
    };

    try {
        const response = await fetch(`${API_URL}Arbitros/guardarRol`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        if (res.status) {
            document.getElementById('formRol').reset();
            document.getElementById('id_rol_edit').value = "0";
            cargarRoles();
        }
    } catch (error) {
        console.error(error);
    }
}

function editarRol(id, nombre, monto) {
    document.getElementById('id_rol_edit').value = id;
    document.getElementById('rol_nombre').value = nombre;
    document.getElementById('rol_monto').value = monto;
}

async function eliminarRol(id) {
    const result = await Swal.fire({
        title: '¿Eliminar Cargo?',
        text: "Se desactivará este rol para futuras programaciones",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`${API_URL}Arbitros/eliminarRol/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const res = await response.json();
            if (res.status) {
                cargarRoles();
            }
        } catch (error) {
            console.error(error);
        }
    }
}

function abrirModalConfig() {
    if (!torneoActual) {
        return Swal.fire('Atención', 'Seleccione primero un torneo', 'warning');
    }
    cargarRoles();
    new bootstrap.Modal(document.getElementById('modalConfig')).show();
}

// --- GESTIÓN DE ÁRBITROS ---
function abrirModalArbitro(data = null) {
    const form = document.getElementById('formArbitro');
    form.reset();
    document.getElementById('id_arbitro').value = '';
    document.getElementById('tituloModalArbitro').textContent = '👤 Nuevo Árbitro';

    if (data) {
        document.getElementById('id_arbitro').value = data.id_arbitro;
        document.getElementById('nombre_completo').value = data.nombre_completo;
        document.getElementById('identificacion').value = data.identificacion;
        document.getElementById('telefono').value = data.telefono;
        document.getElementById('email').value = data.email;
        document.getElementById('tituloModalArbitro').textContent = '👤 Editar Árbitro';
    }

    new bootstrap.Modal(document.getElementById('modalArbitro')).show();
}

async function guardarArbitro(e) {
    e.preventDefault();
    const id = document.getElementById('id_arbitro').value;
    const data = {
        nombre_completo: document.getElementById('nombre_completo').value,
        identificacion: document.getElementById('identificacion').value,
        telefono: document.getElementById('telefono').value,
        email: document.getElementById('email').value
    };

    const url = id ? `${API_URL}Arbitros/actualizar/${id}` : `${API_URL}Arbitros/crear`;
    const method = id ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.status) {
            Swal.fire('Éxito', result.msg, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalArbitro')).hide();
            cargarListaArbitros();
        } else {
            Swal.fire('Error', result.msg, 'error');
        }
    } catch (error) {
        console.error(error);
    }
}

async function cargarListaArbitros() {
    try {
        const response = await fetch(`${API_URL}Arbitros/listar`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const result = await response.json();

        if (tablaArbitros) tablaArbitros.destroy();
        const tbody = document.querySelector('#tablaArbitros tbody');
        tbody.innerHTML = '';

        if (result.status) {
            result.data.forEach(a => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-bold">${a.nombre_completo}</td>
                    <td>${a.identificacion || '-'}</td>
                    <td>${a.telefono || '-'}</td>
                    <td>${a.email || '-'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info text-white" onclick='editarArbitro(${JSON.stringify(a)})'>
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        tablaArbitros = $('#tablaArbitros').DataTable({
            language: { url: '../assets/js/datatables-es.json' },
            pageLength: 5
        });

    } catch (error) {
        console.error(error);
    }
}

function editarArbitro(data) {
    abrirModalArbitro(data);
}

// --- PAGOS PENDIENTES ---
async function cargarPagosPendientes() {
    if (!torneoActual) return;

    try {
        const response = await fetch(`${API_URL}Arbitros/pagos/${torneoActual}?estado=PENDIENTE`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const result = await response.json();

        const tbody = document.querySelector('#tablaPagosPendientes tbody');
        tbody.innerHTML = '';

        if (result.status && result.data.length > 0) {
            result.data.forEach(p => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-4">
                        <div class="fw-bold">${p.equipo_local} vs ${p.equipo_visitante}</div>
                        <small class="text-muted">Partido ID: #${p.id_partido}</small>
                    </td>
                    <td>${formatDate(p.fecha_partido)}</td>
                    <td>
                        <div class="fw-bold text-dark">${p.arbitro}</div>
                        <span class="badge bg-light text-dark border small" style="font-size: 10px;">${p.rol || 'CENTRAL'}</span>
                    </td>
                    <td class="text-end fw-bold text-primary">${formatMoney(p.monto)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-success" onclick="abrirModalPago(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Pagar
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No existen pagos pendientes para este torneo.</td></tr>';
        }
    } catch (error) {
        console.error(error);
    }
}

function abrirModalPago(data) {
    document.getElementById('formRegistrarPago').reset();
    document.getElementById('payIdPago').value = data.id_pago;
    document.getElementById('infoPago').innerHTML = `
        <div class="d-flex justify-content-between">
            <span><strong>Para:</strong> ${data.arbitro} (${data.rol || 'CENTRAL'})</span>
            <span><strong>Monto:</strong> <span class="fw-bold">${formatMoney(data.monto)}</span></span>
        </div>
        <div class="mt-1 small"><strong>Partido:</strong> ${data.equipo_local} vs ${data.equipo_visitante}</div>
    `;
    new bootstrap.Modal(document.getElementById('modalRegistrarPago')).show();
}

async function procesarPago(e) {
    e.preventDefault();
    const id = document.getElementById('payIdPago').value;
    const data = {
        fecha_pago: document.getElementById('payFecha').value,
        forma_pago: document.getElementById('payForma').value,
        numero_comprobante: document.getElementById('payRef').value,
        observaciones: document.getElementById('payObs').value
    };

    try {
        const response = await fetch(`${API_URL}Arbitros/registrarPago/${id}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.status) {
            Swal.fire('Éxito', result.msg, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalRegistrarPago')).hide();
            cargarPagosPendientes();
        } else {
            Swal.fire('Error', result.msg, 'error');
        }
    } catch (error) {
        console.error(error);
    }
}

// --- HISTORIAL ---
async function cargarHistorialPagos() {
    if (!torneoActual) return;

    try {
        const response = await fetch(`${API_URL}Arbitros/pagos/${torneoActual}?estado=PAGADO`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const result = await response.json();

        if (tablaHistorial) tablaHistorial.destroy();
        const tbody = document.querySelector('#tablaHistorial tbody');
        tbody.innerHTML = '';

        if (result.status) {
            result.data.forEach(p => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${formatDate(p.fecha_pago)}</td>
                    <td>
                        <div class="small fw-bold">${p.equipo_local} vs ${p.equipo_visitante}</div>
                        <div class="text-muted" style="font-size: 10px;">En fecha: ${formatDate(p.fecha_partido)}</div>
                    </td>
                    <td>
                        <div>${p.arbitro}</div>
                        <small class="text-muted">${p.rol || 'CENTRAL'}</small>
                    </td>
                    <td class="text-end fw-bold">${formatMoney(p.monto)}</td>
                    <td><span class="badge bg-light text-dark border">${p.forma_pago}</span></td>
                    <td class="small">${p.numero_comprobante || '-'}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        tablaHistorial = $('#tablaHistorial').DataTable({
            language: { url: '../assets/js/datatables-es.json' },
            order: [[0, 'desc']]
        });
    } catch (error) {
        console.error(error);
    }
}
