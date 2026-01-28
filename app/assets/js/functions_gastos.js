
let torneoActual = null;
let tablaGastos = null;

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
            document.getElementById('seccionGastos').classList.remove('d-none');
            document.getElementById('resumenGastos').classList.remove('d-none');
            cargarGastos();
        } else {
            document.getElementById('mensajeInicial').classList.remove('d-none');
            document.getElementById('seccionGastos').classList.add('d-none');
            document.getElementById('resumenGastos').classList.add('d-none');
        }
    });

    // Formulario de Gasto
    document.getElementById('formGasto').onsubmit = (e) => guardarGasto(e);
}

async function cargarTorneos() {
    try {
        const result = await fetchAPI('Posiciones/torneos');
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

async function cargarGastos() {
    if (!torneoActual) return;

    try {
        const result = await fetchAPI(`Pagos/listar/${torneoActual}`);

        if (tablaGastos) tablaGastos.destroy();
        const tbody = document.querySelector('#tablaGastos tbody');
        tbody.innerHTML = '';

        let total = 0;

        if (result.status && Array.isArray(result.data)) {
            result.data.forEach(g => {
                if (g.estado === 'ACTIVO') total += parseFloat(g.monto);

                const tr = document.createElement('tr');
                const badgeClass = g.estado === 'ACTIVO' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger';

                tr.innerHTML = `
                    <td class="ps-4">${formatDate(g.fecha_pago)}</td>
                    <td>
                        <div class="fw-bold text-dark">${g.concepto}</div>
                        <span class="badge bg-light text-muted border small" style="font-size: 10px;">${g.tipo_gasto}</span>
                    </td>
                    <td>${g.beneficiario}</td>
                    <td class="text-end fw-bold ${g.estado === 'ACTIVO' ? 'text-danger' : 'text-muted text-decoration-line-through'}">
                        ${formatMoney(g.monto)}
                    </td>
                    <td class="text-center">
                        <span class="badge ${badgeClass} border px-2 py-1" style="font-size: 0.7rem;">${g.estado}</span>
                    </td>
                    <td class="text-center">
                        ${g.estado === 'ACTIVO' ? `
                            <button class="btn btn-sm btn-light text-danger" onclick="anularGasto(${g.id_pago}, '${g.concepto}')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        ` : '-'}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        document.getElementById('totalGastos').textContent = formatMoney(total);

        tablaGastos = $('#tablaGastos').DataTable({
            language: { url: '../assets/js/datatables-es.json' },
            order: [[0, 'desc']],
            pageLength: 10
        });

    } catch (error) {
        console.error(error);
    }
}

function abrirModalGasto() {
    if (!torneoActual) {
        return swalError('Seleccione primero un torneo', 'Atención');
    }
    document.getElementById('formGasto').reset();
    new bootstrap.Modal(document.getElementById('modalGasto')).show();
}

async function guardarGasto(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    data.id_torneo = torneoActual;

    try {
        const result = await fetchAPI('Pagos/crear', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (result.status) {
            swalSuccess(result.msg, 'Éxito');
            bootstrap.Modal.getInstance(document.getElementById('modalGasto')).hide();
            cargarGastos();
        } else {
            swalError(result.msg);
        }
    } catch (error) {
        console.error(error);
    }
}

async function anularGasto(id, concepto) {
    const result = await Swal.fire({
        title: '¿Anular Gasto?',
        text: `Se anulará el gasto: "${concepto}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cerrar',
        input: 'text',
        inputPlaceholder: 'Motivo de anulación...',
        inputValidator: (value) => {
            if (!value) return '¡El motivo es obligatorio!';
        }
    });

    if (result.isConfirmed) {
        try {
            const res = await fetchAPI(`Pagos/anular/${id}`, {
                method: 'POST',
                body: JSON.stringify({ motivo: result.value })
            });

            if (res.status) {
                swalSuccess(res.msg, 'Anulado');
                cargarGastos();
            } else {
                swalError(res.msg);
            }
        } catch (error) {
            console.error(error);
        }
    }
}
