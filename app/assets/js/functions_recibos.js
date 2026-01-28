// ============================================
// MÓDULO DE RECIBOS Y TESORERÍA
// ============================================

const API_URL = app_config.api_url;
const token = app_config.token;
let torneoActual = null;
let torneoSeleccionado = null; // Nuevo: Objeto completo del torneo
let torneosData = []; // Nuevo: Todos los torneos cargados
let tablaHistorial = null;
let itemsSeleccionados = [];

// ============================================
// INICIALIZACIÓN
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    cargarTorneos();
    inicializarEventos();
});

function inicializarEventos() {
    // Cambio de torneo
    document.getElementById('selectTorneo').addEventListener('change', (e) => {
        torneoActual = e.target.value;
        torneoSeleccionado = torneosData.find(t => t.id_torneo == torneoActual);

        if (torneoActual) {
            cargarPendientes();
            const tabHistorial = document.getElementById('pills-historial-tab');
            if (tabHistorial.classList.contains('active')) {
                cargarHistorialRecibos();
            }
            mostrarSeccionCaja();
        } else {
            ocultarSeccionCaja();
        }
    });

    // Buscador de pendientes
    document.getElementById('searchPendientes').addEventListener('keyup', function () {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('#tablaPendientesCaja tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

// ============================================
// CARGA DE DATOS
// ============================================

async function cargarTorneos() {
    try {
        const response = await fetch(`${API_URL}Posiciones/torneos`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const result = await response.json();
        if (result.status) {
            const select = document.getElementById('selectTorneo');
            if (result.data.is_super_admin) {
                // Aquí podrías manejar la carga de torneos por liga seleccionada si es superadmin
                result.data.ligas.forEach(liga => {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = liga.nombre;
                    select.appendChild(optgroup);
                });
            } else {
                torneosData = result.data.torneos; // Guardamos la data
                result.data.torneos.forEach(torneo => {
                    const option = document.createElement('option');
                    option.value = torneo.id_torneo;
                    option.textContent = torneo.nombre;
                    select.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error al cargar torneos:', error);
    }
}

async function cargarPendientes() {
    try {
        const response = await fetch(`${API_URL}Recibos/pendientes/${torneoActual}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const result = await response.json();

        const tbody = document.querySelector('#tablaPendientesCaja tbody');
        tbody.innerHTML = '';
        itemsSeleccionados = [];
        actualizarCheckout();

        if (result.status && result.data.length > 0) {
            document.getElementById('msgNoPendientes').style.display = 'none';
            document.getElementById('tablaPendientesCaja').style.display = 'table';

            result.data.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input check-deuda" 
                            data-tipo="${item.tipo_item}" 
                            data-id="${item.id_item}" 
                            data-concepto="${item.concepto}" 
                            data-monto="${item.monto}" 
                            data-email="${item.email || ''}" 
                            onchange="toggleItem(this)">
                    </td>
                    <td>
                        <div class="fw-bold">${item.concepto}</div>
                        <small class="text-muted">${formatDate(item.fecha)}</small>
                    </td>
                    <td>
                        <div class="fw-bold text-primary">${item.equipo}</div>
                        <small class="text-muted">${item.apellidos}, ${item.nombres}</small>
                    </td>
                    <td class="text-end fw-bold text-dark">${formatMoney(item.monto)}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            document.getElementById('msgNoPendientes').style.display = 'block';
            document.getElementById('tablaPendientesCaja').style.display = 'none';
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// ============================================
// LÓGICA DE COBRO (CHECKOUT)
// ============================================

function toggleItem(checkbox) {
    const data = checkbox.dataset;
    const item = {
        tipo: data.tipo,
        id: data.id,
        concepto: data.concepto,
        monto: parseFloat(data.monto),
        monto_max: parseFloat(data.monto), // Para control de pagos parciales
        email: data.email
    };

    if (checkbox.checked) {
        itemsSeleccionados.push(item);
    } else {
        itemsSeleccionados = itemsSeleccionados.filter(i => !(i.id == item.id && i.tipo == item.tipo));
    }
    actualizarCheckout();
}

function toggleAllItems(checkbox) {
    const checks = document.querySelectorAll('.check-deuda:not([style*="display: none"])');
    checks.forEach(check => {
        check.checked = checkbox.checked;
        const data = check.dataset;
        const item = {
            tipo: data.tipo,
            id: data.id,
            concepto: data.concepto,
            monto: parseFloat(data.monto),
            monto_max: parseFloat(data.monto)
        };

        if (checkbox.checked) {
            // Evitar duplicados
            if (!itemsSeleccionados.find(i => i.id == item.id && i.tipo == item.tipo)) {
                itemsSeleccionados.push(item);
            }
        } else {
            itemsSeleccionados = [];
        }
    });
    actualizarCheckout();
}

function actualizarCheckout() {
    const lista = document.getElementById('listaCheckout');
    const totalElement = document.getElementById('totalCheckout');
    const btnPago = document.getElementById('btnEmitirRecibo');

    if (itemsSeleccionados.length === 0) {
        lista.innerHTML = '<p class="text-center text-muted small py-3">Seleccione deudas de la lista para cobrar</p>';
        totalElement.textContent = formatMoney(0);
        btnPago.disabled = true;
        return;
    }

    let html = '<div class="list-group list-group-flush border rounded shadow-sm overflow-auto" style="max-height: 250px;">';
    let total = 0;

    itemsSeleccionados.forEach((item, index) => {
        total += item.monto;
        html += `
            <div class="list-group-item py-2 px-3 small">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-truncate fw-bold" style="max-width: 180px;">${item.concepto}</div>
                    <button class="btn btn-link btn-sm text-danger p-0" onclick="removerItem(${index})"><i class="fa-solid fa-times"></i></button>
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control form-control-sm text-end input-monto-recibo" 
                        value="${item.monto}" 
                        max="${item.monto_max}" 
                        min="1" 
                        onchange="actualizarMontoItem(${index}, this.value)">
                </div>
                <div class="text-end mt-1">
                    <small class="text-muted" style="font-size: 10px;">Saldo: ${formatMoney(item.monto_max)}</small>
                </div>
            </div>
        `;
    });
    html += '</div>';

    lista.innerHTML = html;
    totalElement.textContent = formatMoney(total);
    btnPago.disabled = false;
}

function actualizarMontoItem(index, valor) {
    const v = parseFloat(valor);
    const max = itemsSeleccionados[index].monto_max;

    if (v > max) {
        Swal.fire('Atención', `El monto no puede superar el saldo pendiente (${formatMoney(max)})`, 'warning');
        itemsSeleccionados[index].monto = max;
    } else if (v <= 0 || isNaN(v)) {
        itemsSeleccionados[index].monto = 1;
    } else {
        itemsSeleccionados[index].monto = v;
    }
    actualizarCheckout();
}

function removerItem(index) {
    const item = itemsSeleccionados[index];
    const checks = document.querySelectorAll('.check-deuda');
    checks.forEach(c => {
        if (c.dataset.id == item.id && c.dataset.tipo == item.tipo) {
            c.checked = false;
        }
    });
    itemsSeleccionados.splice(index, 1);
    actualizarCheckout();
}

async function procesarPago() {
    const pagador = document.getElementById('recPagador').value.trim();
    if (!pagador) {
        Swal.fire('Error', 'Debe ingresar el nombre del pagador', 'warning');
        return;
    }

    Swal.fire({
        title: '¿Confirmar Pago?',
        text: `Se generará un recibo por el total de ${document.getElementById('totalCheckout').textContent}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, generar recibo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#198754'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const data = {
                id_torneo: torneoActual,
                pagador: pagador,
                forma_pago: document.getElementById('recFormaPago').value,
                referencia: document.getElementById('recReferencia').value,
                observaciones: document.getElementById('recObs').value,
                items: itemsSeleccionados.map(item => ({
                    ...item,
                    email: item.email // El email ya viene en el objeto desde cargarPendientes
                })),
                enviar_email: document.getElementById('checkEnviarEmail').checked
            };

            try {
                const response = await fetch(`${API_URL}Recibos/crear`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.status) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Recibo Generado!',
                        text: 'El pago ha sido registrado correctamente.',
                        showCancelButton: true,
                        confirmButtonText: 'Ver/Imprimir Recibo',
                        cancelButtonText: 'Cerrar'
                    }).then((r) => {
                        if (r.isConfirmed) {
                            verDetalleRecibo(result.data.id_recibo);
                        }
                    });

                    // Limpiar formulario y recargar pendientes
                    document.getElementById('formRecibo').reset();
                    cargarPendientes();
                } else {
                    Swal.fire('Error', result.msg || 'No se pudo procesar el pago', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Ocurrió un error en el servidor', 'error');
            }
        }
    });
}

// ============================================
// HISTORIAL DE RECIBOS
// ============================================

async function cargarHistorialRecibos() {
    if (!torneoActual) return;

    try {
        const response = await fetch(`${API_URL}Recibos/listar/${torneoActual}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const result = await response.json();

        if (tablaHistorial) tablaHistorial.destroy();

        const tbody = document.querySelector('#tablaHistorialRecibos tbody');
        tbody.innerHTML = '';

        if (result.status) {
            result.data.forEach(r => {
                const tr = document.createElement('tr');
                const estadoClass = r.estado === 'ACTIVO' ? 'bg-success' : 'bg-danger';
                tr.innerHTML = `
                    <td class="fw-bold">${r.numero_recibo}</td>
                    <td>${formatDate(r.fecha_pago)}</td>
                    <td>${r.pagador}</td>
                    <td class="fw-bold">${formatMoney(r.total)}</td>
                    <td><span class="badge bg-light text-dark border">${r.forma_pago}</span></td>
                    <td><span class="badge ${estadoClass}">${r.estado}</span></td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="verDetalleRecibo(${r.id_recibo})">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        ${r.estado === 'ACTIVO' ? `
                            <button class="btn btn-sm btn-danger" onclick="anularRecibo(${r.id_recibo})">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        ` : ''}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        tablaHistorial = $('#tablaHistorialRecibos').DataTable({
            language: { url: '../assets/js/datatables-es.json' },
            order: [[0, 'desc']],
            pageLength: 10
        });
    } catch (error) {
        console.error('Error al cargar historial:', error);
    }
}

async function verDetalleRecibo(id) {
    try {
        const response = await fetch(`${API_URL}Recibos/detalle/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const result = await response.json();

        if (result.status) {
            const r = result.data;

            // Generar encabezado dinámico desde el helper
            const headerHtml = generarEncabezadoPrint({
                nombre_liga: torneoSeleccionado ? torneoSeleccionado.nombre_liga : "Global Cup",
                nombre_torneo: torneoSeleccionado ? torneoSeleccionado.nombre : "",
                logo: torneoSeleccionado ? torneoSeleccionado.logo : null,
                liga_logo: torneoSeleccionado ? torneoSeleccionado.liga_logo : null
            });
            document.getElementById('headerReciboPrint').innerHTML = headerHtml;

            document.getElementById('detNumeroRecibo').textContent = `#${r.numero_recibo}`;
            document.getElementById('detFecha').textContent = formatDate(r.fecha_pago);
            document.getElementById('detPagador').textContent = r.pagador;
            document.getElementById('detTotal').textContent = formatMoney(r.total);
            document.getElementById('detFormaPago').textContent = r.forma_pago;
            document.getElementById('detReferencia').textContent = r.referencia || '-';
            document.getElementById('detUsuario').textContent = `${r.usuario_nombre} ${r.usuario_apellido}`;

            const listBody = document.getElementById('detListaConceptos');
            listBody.innerHTML = '';
            r.detalle.forEach(d => {
                listBody.innerHTML += `
                    <tr>
                        <td>${d.concepto}</td>
                        <td class="text-end fw-bold">${formatMoney(d.monto)}</td>
                    </tr>
                `;
            });

            const modal = new bootstrap.Modal(document.getElementById('modalDetalleRecibo'));
            modal.show();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function anularRecibo(id) {
    Swal.fire({
        title: '¿Anular este recibo?',
        text: 'Se liberarán todas las deudas asociadas para que vuelvan a quedar pendientes. Ingrese el motivo:',
        input: 'text',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        inputValidator: (value) => {
            if (!value) return '¡El motivo es obligatorio!';
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`${API_URL}Recibos/anular/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ observaciones: result.value })
                });
                const res = await response.json();
                if (res.status) {
                    Swal.fire('Anulado', 'El recibo ha sido anulado y las deudas liberadas.', 'success');
                    cargarHistorialRecibos();
                    if (torneoActual) cargarPendientes();
                } else {
                    Swal.fire('Error', res.msg, 'error');
                }
            } catch (error) {
                console.error(error);
            }
        }
    });
}

// ============================================
// UTILIDADES
// ============================================

function imprimirRecibo() {
    const printContent = document.getElementById('printArea').innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload(); // Recargar para restaurar eventos
}

function mostrarSeccionCaja() {
    document.getElementById('mensajeInicial').style.display = 'none';
    document.getElementById('seccionCaja').style.display = 'block';
}

function ocultarSeccionCaja() {
    document.getElementById('mensajeInicial').style.display = 'block';
    document.getElementById('seccionCaja').style.display = 'none';
}

// Nota: formatMoney y formatDate ahora se usan desde helpers.js globalmente
