// ============================================
// MÓDULO DE CUOTAS MENSUALES
// ============================================

const API_URL = app_config.api_url;
const token = app_config.token;
let torneoActual = null;
let tablaCuotas = null;
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
            cargarCuotas();
            cargarEquipos();
            mostrarSeccion();
        } else {
            ocultarSeccion();
        }
    });

    // Filtros
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroEquipo').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroMes').addEventListener('change', aplicarFiltros);
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
        const response = await fetch(`${API_URL}Cuotas/configuracion/${torneoActual}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const result = await response.json();

        if (result.status && result.data) {
            configActual = result.data;
            document.getElementById('configMonto').textContent = formatMoney(result.data.monto_mensual);
            document.getElementById('configDia').textContent = result.data.dia_vencimiento;
            document.getElementById('configInfo').style.display = 'block';
        } else {
            configActual = null;
            document.getElementById('configInfo').style.display = 'none';
            Swal.fire({
                icon: 'warning',
                title: 'Sin Configuración',
                text: 'Este torneo no tiene configuración de cuotas. ¿Desea configurarlo ahora?',
                showCancelButton: true,
                confirmButtonText: 'Sí, configurar',
                cancelButtonText: 'Más tarde'
            }).then((result) => {
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
        const response = await fetch(`${API_URL}Cuotas/resumen/${torneoActual}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const result = await response.json();

        if (result.status) {
            let total = 0;
            let pendientes = 0;
            let pagadas = 0;
            let vencidas = 0;

            result.data.forEach(item => {
                total += parseInt(item.cantidad);
                if (item.estado === 'PENDIENTE') pendientes = parseInt(item.cantidad);
                if (item.estado === 'PAGADO') pagadas = parseInt(item.cantidad);
                if (item.estado === 'VENCIDO') vencidas = parseInt(item.cantidad);
            });

            document.getElementById('statTotal').textContent = total;
            document.getElementById('statPendientes').textContent = pendientes;
            document.getElementById('statPagadas').textContent = pagadas;
            document.getElementById('statVencidas').textContent = vencidas;
        }
    } catch (error) {
        console.error('Error al cargar resumen:', error);
    }
}

async function cargarCuotas() {
    try {
        const response = await fetch(`${API_URL}Cuotas/listar/${torneoActual}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const result = await response.json();

        if (result.status) {
            renderizarTabla(result.data);
        }
    } catch (error) {
        console.error('Error al cargar cuotas:', error);
        mostrarError('Error al cargar las cuotas');
    }
}

async function cargarEquipos() {
    try {
        // Aquí deberías tener un endpoint para obtener equipos del torneo
        // Por ahora lo dejamos vacío
        const select = document.getElementById('filtroEquipo');
        select.innerHTML = '<option value="">Todos</option>';
    } catch (error) {
        console.error('Error al cargar equipos:', error);
    }
}

// ============================================
// RENDERIZADO
// ============================================

function renderizarTabla(cuotas) {
    if (tablaCuotas) {
        tablaCuotas.destroy();
    }

    const tbody = document.querySelector('#tablaCuotas tbody');
    tbody.innerHTML = '';

    cuotas.forEach(cuota => {
        const tr = document.createElement('tr');

        const estadoBadge = getEstadoBadge(cuota.estado);
        const nombreMes = getMesNombre(cuota.mes);

        tr.innerHTML = `
            <td>${cuota.apellidos}, ${cuota.nombres}</td>
            <td>${cuota.equipo}</td>
            <td>${nombreMes} ${cuota.anio}</td>
            <td class="fw-bold">${formatMoney(cuota.monto)}</td>
            <td>${formatDate(cuota.fecha_vencimiento)}</td>
            <td>${estadoBadge}</td>
            <td>${cuota.fecha_pago ? formatDate(cuota.fecha_pago) : '-'}</td>
            <td>${cuota.numero_recibo || '-'}</td>
        `;

        tbody.appendChild(tr);
    });

    // Inicializar DataTable
    tablaCuotas = $('#tablaCuotas').DataTable({
        language: {
            url: '../assets/js/datatables-es.json'
        },
        order: [[2, 'desc'], [0, 'asc']],
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
    fetch(`${API_URL}Cuotas/configuracion/${torneoActual}`, {
        headers: { 'Authorization': `Bearer ${token}` }
    })
        .then(res => res.json())
        .then(result => {
            if (result.status && result.data) {
                document.getElementById('configMontoMensual').value = result.data.monto_mensual;
                document.getElementById('configDiaVencimiento').value = result.data.dia_vencimiento;
            } else {
                document.getElementById('configMontoMensual').value = '';
                document.getElementById('configDiaVencimiento').value = '5';
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
        monto_mensual: parseFloat(document.getElementById('configMontoMensual').value),
        dia_vencimiento: parseInt(document.getElementById('configDiaVencimiento').value)
    };

    try {
        const response = await fetch(`${API_URL}Cuotas/guardarConfiguracion`, {
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
                title: '¡Éxito!',
                text: 'Configuración guardada correctamente',
                timer: 2000,
                showConfirmButton: false
            });

            bootstrap.Modal.getInstance(document.getElementById('modalConfiguracion')).hide();
            cargarConfiguracion();
        } else {
            mostrarError(result.msg || 'Error al guardar la configuración');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al guardar la configuración');
    }
}

// ============================================
// ACCIONES
// ============================================

async function generarCuotas() {
    if (!torneoActual) {
        mostrarError('Seleccione un torneo primero');
        return;
    }

    // Verificar si hay configuración
    if (!configActual) {
        mostrarError('Debe configurar el monto y día de vencimiento antes de generar las cuotas');
        abrirConfiguracion();
        return;
    }

    Swal.fire({
        title: '¿Generar cuotas mensuales?',
        text: 'Se generarán las cuotas para todos los jugadores inscritos en las nóminas de este torneo basado en las fechas de inicio y fin del mismo.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Sí, generar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745'
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                text: 'Generando cuotas para los jugadores...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(`${API_URL}Cuotas/generarMasivas/${torneoActual}`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await response.json();

                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Generación Completa!',
                        text: data.msg,
                        timer: 3000,
                        showConfirmButton: true
                    });

                    cargarResumen();
                    cargarCuotas();
                } else {
                    mostrarError(data.msg || 'Error al generar las cuotas');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarError('Error en el servidor al intentar generar las cuotas');
            }
        }
    });
}

async function marcarVencidas() {
    if (!torneoActual) {
        mostrarError('Seleccione un torneo primero');
        return;
    }

    Swal.fire({
        title: '¿Marcar cuotas vencidas?',
        text: 'Se actualizarán todas las cuotas pendientes con fecha de vencimiento pasada',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, marcar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`${API_URL}Cuotas/marcarVencidas/${torneoActual}`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await response.json();

                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'Cuotas vencidas actualizadas',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    cargarResumen();
                    cargarCuotas();
                } else {
                    mostrarError(data.msg || 'Error al marcar cuotas vencidas');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarError('Error al marcar cuotas vencidas');
            }
        }
    });
}

function aplicarFiltros() {
    if (!tablaCuotas) return;

    const estado = document.getElementById('filtroEstado').value;
    const equipo = document.getElementById('filtroEquipo').value;
    const mes = document.getElementById('filtroMes').value;

    // Aplicar filtros en DataTable
    tablaCuotas.column(5).search(estado).draw();
    tablaCuotas.column(1).search(equipo).draw();

    if (mes) {
        const nombreMes = getMesNombre(parseInt(mes));
        tablaCuotas.column(2).search(nombreMes).draw();
    } else {
        tablaCuotas.column(2).search('').draw();
    }
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
        'VENCIDO': '<span class="badge bg-danger">Vencido</span>'
    };
    return badges[estado] || estado;
}

function getMesNombre(mes) {
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    return meses[mes - 1] || mes;
}

function formatMoney(amount) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(amount || 0);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString + 'T00:00:00');
    return date.toLocaleDateString('es-CO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}
