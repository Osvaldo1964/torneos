// ============================================
// MÓDULO DE SANCIONES ECONÓMICAS
// ============================================


let torneoActual = null;
let tablaSanciones = null;
let configActual = null;

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
        if (torneoActual) {
            cargarConfiguracion();
            cargarResumen();
            cargarSanciones();
            cargarEquipos();
            mostrarSeccion();
        } else {
            ocultarSeccion();
        }
    });

    // Filtros
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroTipo').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroEquipo').addEventListener('change', aplicarFiltros);
}

// ============================================
// CARGA DE DATOS
// ============================================

async function cargarTorneos() {
    try {
        const result = await fetchAPI('Posiciones/torneos');

        if (result.status) {
            const select = document.getElementById('selectTorneo');

            if (result.data.is_super_admin) {
                result.data.ligas.forEach(liga => {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = liga.nombre;
                    select.appendChild(optgroup);
                });
            } else {
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
        mostrarError('Error al cargar la lista de torneos');
    }
}

async function cargarConfiguracion() {
    try {
        const result = await fetchAPI(`Sanciones/configuracion/${torneoActual}`);

        if (result.status && result.data) {
            configActual = result.data;
            document.getElementById('configAmarilla').textContent = formatMoney(result.data.monto_amarilla);
            document.getElementById('configRoja').textContent = formatMoney(result.data.monto_roja);
            document.getElementById('configInfo').style.display = 'block';
        } else {
            configActual = null;
            document.getElementById('configInfo').style.display = 'none';
            configActual = null;
            document.getElementById('configInfo').style.display = 'none';
            swalConfirm(
                'Sin Configuración',
                'Este torneo no tiene configuración de sanciones económicas. ¿Desea configurarlo ahora?',
                'Sí, configurar'
            ).then((result) => {
                if (result.isConfirmed) {
                    abrirConfiguracion();
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar configuración:', error);
    }
}

async function cargarResumen() {
    try {
        const result = await fetchAPI(`Sanciones/resumen/${torneoActual}`);

        if (result.status) {
            let total = 0;
            let pendientes = 0;
            let pagadas = 0;

            result.data.forEach(item => {
                total += parseInt(item.cantidad);
                if (item.estado === 'PENDIENTE') pendientes = parseInt(item.cantidad);
                if (item.estado === 'PAGADO') pagadas = parseInt(item.total);
            });

            document.getElementById('statTotal').textContent = total;
            document.getElementById('statPendientes').textContent = pendientes;
            document.getElementById('statPagadas').textContent = formatMoney(pagadas);
        }
    } catch (error) {
        console.error('Error al cargar resumen:', error);
    }
}

async function cargarSanciones() {
    try {
        const result = await fetchAPI(`Sanciones/listar/${torneoActual}`);

        if (result.status) {
            renderizarTabla(result.data);
        }
    } catch (error) {
        console.error('Error al cargar sanciones:', error);
        mostrarError('Error al cargar las sanciones');
    }
}

async function cargarEquipos() {
    try {
        const result = await fetchAPI(`Equipos/listarTorneo/${torneoActual}`);

        if (result.status) {
            const selectFiltro = document.getElementById('filtroEquipo');
            const selectModal = document.getElementById('selectEquipoSancion');

            selectFiltro.innerHTML = '<option value="">Todos</option>';
            selectModal.innerHTML = '<option value="">Seleccione equipo...</option>';

            result.data.forEach(equipo => {
                const opt = `<option value="${equipo.id_equipo}">${equipo.nombre}</option>`;
                selectFiltro.innerHTML += opt;
                selectModal.innerHTML += opt;
            });
        }
    } catch (error) {
        console.error('Error al cargar equipos:', error);
    }
}

async function cargarJugadoresEquipo(idEquipo) {
    if (!idEquipo) {
        document.getElementById('selectJugadorSancion').innerHTML = '<option value="">Seleccione jugador...</option>';
        return;
    }

    try {
        const result = await fetchAPI(`Jugadores/listarEquipo/${idEquipo}`);

        if (result.status) {
            const select = document.getElementById('selectJugadorSancion');
            select.innerHTML = '<option value="">Seleccione jugador...</option>';

            result.data.forEach(jugador => {
                const option = document.createElement('option');
                option.value = jugador.id_jugador;
                option.textContent = `${jugador.apellidos} ${jugador.nombres}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar jugadores:', error);
    }
}

// ============================================
// RENDERIZADO
// ============================================

function renderizarTabla(sanciones) {
    if (tablaSanciones) {
        tablaSanciones.destroy();
    }

    const tbody = document.querySelector('#tablaSanciones tbody');
    tbody.innerHTML = '';

    sanciones.forEach(sancion => {
        const tr = document.createElement('tr');

        const estadoBadge = getEstadoBadge(sancion.estado);
        const tipoBadge = getTipoBadge(sancion.tipo_sancion);
        const involucrado = sancion.nombres ? `${sancion.apellidos}, ${sancion.nombres}` : 'General / Equipo';

        tr.innerHTML = `
            <td>${formatDate(sancion.fecha_sancion)}</td>
            <td>${tipoBadge}</td>
            <td>${sancion.concepto}</td>
            <td>${involucrado}</td>
            <td>${sancion.equipo || '-'}</td>
            <td class="fw-bold">${formatMoney(sancion.monto)}</td>
            <td>${estadoBadge}</td>
            <td>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-info" onclick="verDetalle(${sancion.id_sancion})" title="Ver detalle">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    ${sancion.estado === 'PENDIENTE' ? `
                        <button class="btn btn-sm btn-danger" onclick="anularSancion(${sancion.id_sancion})" title="Anular">
                            <i class="fa-solid fa-ban"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        `;

        tbody.appendChild(tr);
    });

    // Inicializar DataTable
    tablaSanciones = $('#tablaSanciones').DataTable({
        language: {
            url: '../assets/js/datatables-es.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });
}

// ============================================
// CONFIGURACIÓN
// ============================================

function abrirConfiguracion() {
    document.getElementById('configIdTorneo').value = torneoActual;

    // Cargar configuración actual si existe
    fetch(`${API_URL}Sanciones/configuracion/${torneoActual}`, {
        headers: { 'Authorization': `Bearer ${token}` }
    })
        .then(res => res.json())
        .then(result => {
            if (result.status && result.data) {
                document.getElementById('configMontoAmarilla').value = result.data.monto_amarilla;
                document.getElementById('configMontoRoja').value = result.data.monto_roja;
            } else {
                document.getElementById('configMontoAmarilla').value = '';
                document.getElementById('configMontoRoja').value = '';
            }
        });

    const modal = new bootstrap.Modal(document.getElementById('modalConfiguracion'));
    modal.show();
}

async function guardarConfiguracion() {
    const form = document.getElementById('formConfiguracion');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const data = {
        id_torneo: parseInt(document.getElementById('configIdTorneo').value),
        monto_amarilla: parseFloat(document.getElementById('configMontoAmarilla').value),
        monto_roja: parseFloat(document.getElementById('configMontoRoja').value)
    };

    try {
        const result = await fetchAPI('Sanciones/guardarConfiguracion', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (result.status) {
            swalSuccess('Configuración guardada correctamente', '¡Éxito!');

            bootstrap.Modal.getInstance(document.getElementById('modalConfiguracion')).hide();
            cargarConfiguracion();
        } else {
            swalError(result.msg || 'Error al guardar la configuración');
        }
    } catch (error) {
        console.error('Error:', error);
        swalError('Error al guardar la configuración');
    }
}

// ============================================
// ACCIONES
// ============================================

function abrirNuevaSancion() {
    if (!torneoActual) {
        mostrarError('Seleccione un torneo primero');
        return;
    }

    document.getElementById('formSancion').reset();
    document.getElementById('fechaSancion').value = new Date().toISOString().split('T')[0];

    const modal = new bootstrap.Modal(document.getElementById('modalSancion'));
    modal.show();
}

async function guardarSancion() {
    const form = document.getElementById('formSancion');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const data = {
        id_torneo: torneoActual,
        tipo_sancion: document.getElementById('tipoSancion').value,
        id_equipo: document.getElementById('selectEquipoSancion').value || null,
        id_jugador: document.getElementById('selectJugadorSancion').value || null,
        concepto: document.getElementById('conceptoSancion').value,
        monto: parseFloat(document.getElementById('montoSancion').value),
        fecha_sancion: document.getElementById('fechaSancion').value,
        observaciones: document.getElementById('obsSancion').value
    };

    try {
        const result = await fetchAPI('Sanciones/crear', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (result.status) {
            swalSuccess('Sanción registrada correctamente', '¡Éxito!');

            bootstrap.Modal.getInstance(document.getElementById('modalSancion')).hide();
            cargarResumen();
            cargarSanciones();
        } else {
            swalError(result.msg || 'Error al guardar la sanción');
        }
    } catch (error) {
        console.error('Error:', error);
        swalError('Error al registrar la sanción');
    }
}

function anularSancion(id) {
    Swal.fire({
        title: '¿Anular sanción?',
        text: 'Escriba el motivo de la anulación:',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Anular',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        showLoaderOnConfirm: true,
        preConfirm: (motivo) => {
            if (!motivo) {
                Swal.showValidationMessage('Debe ingresar un motivo');
                return false;
            }
            // Usamos fetchAPI pero debemos adaptarlo para Swal.preConfirm que espera promesas
            // Como fetchAPI ya devuelve response.json(), lo usamos directo
            return fetchAPI(`Sanciones/anular/${id}`, {
                method: 'PUT',
                body: JSON.stringify({ observaciones: motivo })
            })
                .then(data => {
                    if (!data.status) throw new Error(data.msg);
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Error: ${error.message}`);
                });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            swalSuccess('La sanción ha sido anulada correctamente', 'Anulada');
            cargarResumen();
            cargarSanciones();
        }
    });
}

function aplicarFiltros() {
    if (!tablaSanciones) return;

    const estado = document.getElementById('filtroEstado').value;
    const tipo = document.getElementById('filtroTipo').value;
    const equipo = document.getElementById('filtroEquipo').value;

    tablaSanciones.column(6).search(estado).draw();
    tablaSanciones.column(1).search(tipo).draw();
    tablaSanciones.column(4).search(equipo).draw();
}

// ============================================
// UTILIDADES
// ============================================

function mostrarSeccion() {
    document.getElementById('mensajeInicial').style.display = 'none';
    document.getElementById('statsCards').style.display = 'flex';
    document.getElementById('filtrosCard').style.display = 'block';
    document.getElementById('tablaCard').style.display = 'block';
}

function ocultarSeccion() {
    document.getElementById('mensajeInicial').style.display = 'block';
    document.getElementById('statsCards').style.display = 'none';
    document.getElementById('filtrosCard').style.display = 'none';
    document.getElementById('tablaCard').style.display = 'none';
}

function getEstadoBadge(estado) {
    const badges = {
        'PENDIENTE': '<span class="badge bg-warning">Pendiente</span>',
        'PAGADO': '<span class="badge bg-success">Pagado</span>',
        'ANULADO': '<span class="badge bg-danger">Anulado</span>'
    };
    return badges[estado] || estado;
}

function getTipoBadge(tipo) {
    const badges = {
        'AMARILLA': '<span class="badge bg-warning text-dark border"><i class="fa-solid fa-square me-1"></i>Amarilla</span>',
        'ROJA': '<span class="badge bg-danger border"><i class="fa-solid fa-square me-1"></i>Roja</span>',
        'COMPORTAMIENTO': '<span class="badge bg-info text-dark">Comportamiento</span>',
        'NO_PRESENTACION': '<span class="badge bg-secondary">No Presentación</span>',
        'OTRA': '<span class="badge bg-light text-dark border">Otra</span>'
    };
    return badges[tipo] || `<span class="badge bg-light text-dark border">${tipo}</span>`;
}


