const API_URL = app_config.api_url;
const token = app_config.token;
let torneoActual = null;

// Instancias de Gráficas
let chartEvolucion = null;
let chartEgresos = null;

document.addEventListener('DOMContentLoaded', () => {
    cargarTorneos();
    inicializarEventos();
});

function inicializarEventos() {
    document.getElementById('selectTorneo').addEventListener('change', (e) => {
        torneoActual = e.target.value;
        if (torneoActual) {
            document.getElementById('mensajeInicial').classList.add('d-none');
            document.getElementById('kpiContainer').classList.remove('d-none');
            document.getElementById('chartContainer').classList.remove('d-none');
            cargarReportes();
        } else {
            document.getElementById('mensajeInicial').classList.remove('d-none');
            document.getElementById('kpiContainer').classList.add('d-none');
            document.getElementById('chartContainer').classList.add('d-none');
        }
    });

    // Limitar fechas por defecto (mes actual)
    const hoy = new Date();
    const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().split('T')[0];
    const ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0).toISOString().split('T')[0];

    // Si quieres mostrar todo el año por defecto quita estas líneas o ajusta
    // document.getElementById('fechaInicio').value = hoy.getFullYear() + '-01-01';
    // document.getElementById('fechaFin').value = hoy.getFullYear() + '-12-31';
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

async function cargarReportes() {
    if (!torneoActual) return;

    const fi = document.getElementById('fechaInicio').value;
    const ff = document.getElementById('fechaFin').value;
    const params = (fi && ff) ? `?fechaInicio=${fi}&fechaFin=${ff}` : '';

    // Actualizar encabezado de impresión
    // Actualizar encabezado de impresión
    if (torneoActual) {
        const select = document.getElementById('selectTorneo');
        const nombreTorneo = select.options[select.selectedIndex].text;
        document.getElementById('printNombreTorneo').innerText = nombreTorneo;

        const fi = document.getElementById('fechaInicio').value;
        const ff = document.getElementById('fechaFin').value;
        document.getElementById('printPeriodo').innerText = (fi && ff) ? `Periodo: ${fi} a ${ff}` : 'Análisis Histórico Total';
    }

    try {
        // 1. Cargar Balance y KPIs
        await cargarKPIs(params);

        // 2. Cargar Gráfica de Evolución (Anual)
        await cargarGraficaEvolucion();

    } catch (error) {
        console.error('Error en cargarReportes:', error);
    }
}

async function cargarKPIs(params) {
    const { status, data } = await fetchAPI(`Finanzas/balance/${torneoActual}${params}`);

    if (status) {
        // Actualizar Números
        document.getElementById('kpiIngresos').textContent = formatMoney(data.totales.ingresos);
        document.getElementById('kpiEgresos').textContent = formatMoney(data.totales.egresos);
        document.getElementById('kpiBalance').textContent = formatMoney(data.totales.resultado);

        const res = data.totales.resultado;
        const kpiEstado = document.getElementById('kpiEstado');
        const card = document.getElementById('kpiResultadoCard');
        const iconBg = document.getElementById('kpiIconBg');
        const icon = document.getElementById('kpiIcon');

        kpiEstado.textContent = data.totales.tipo;

        // Reset classes
        card.className = 'card border-0 shadow-sm hover-card';
        iconBg.className = 'p-3 rounded-circle d-inline-block mb-3';

        if (res > 0) {
            kpiEstado.className = 'fw-bold mb-0 text-success';
            iconBg.classList.add('bg-success', 'bg-opacity-10');
            icon.className = 'fa-solid fa-face-smile text-success fs-3';
        } else if (res < 0) {
            kpiEstado.className = 'fw-bold mb-0 text-danger';
            iconBg.classList.add('bg-danger', 'bg-opacity-10');
            icon.className = 'fa-solid fa-face-frown text-danger fs-3';
        } else {
            kpiEstado.className = 'fw-bold mb-0 text-info';
            iconBg.classList.add('bg-info', 'bg-opacity-10');
            icon.className = 'fa-solid fa-equals text-info fs-3';
        }

        // Distribución de Ingresos
        const totalIng = data.totales.ingresos || 1;
        const pctCuotas = (data.ingresos.cuotas.total / totalIng) * 100;
        const pctSanciones = (data.ingresos.sanciones.total / totalIng) * 100;
        const pctOtros = (data.ingresos.otros.total / totalIng) * 100;

        document.getElementById('pctCuotas').textContent = pctCuotas.toFixed(1) + '%';
        document.getElementById('pctSanciones').textContent = pctSanciones.toFixed(1) + '%';
        document.getElementById('pctOtros').textContent = pctOtros.toFixed(1) + '%';

        document.getElementById('barCuotas').style.width = pctCuotas + '%';
        document.getElementById('barSanciones').style.width = pctSanciones + '%';
        document.getElementById('barOtros').style.width = pctOtros + '%';

        // Gráfica de Egresos (Pie Chart)
        actualizarGraficaEgresos(data.egresos);
    }
}

function actualizarGraficaEgresos(egresos) {
    const ctx = document.getElementById('chartEgresos').getContext('2d');
    const labels = [];
    const values = [];
    const colors = ['#dc3545', '#ffc107', '#0dcaf0', '#6610f2', '#fd7e14', '#198754'];

    // Extraer categorías
    Object.keys(egresos).forEach(key => {
        if (key !== 'total' && egresos[key].total > 0) {
            labels.push(key.toUpperCase());
            values.push(egresos[key].total);
        }
    });

    if (chartEgresos) chartEgresos.destroy();

    if (values.length === 0) {
        document.getElementById('chartEgresos').classList.add('d-none');
        document.getElementById('egresosEmpty').classList.remove('d-none');
        return;
    }

    document.getElementById('chartEgresos').classList.remove('d-none');
    document.getElementById('egresosEmpty').classList.add('d-none');

    chartEgresos = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
            },
            cutout: '70%'
        }
    });
}

async function cargarGraficaEvolucion() {
    const anio = new Date().getFullYear();
    const { status, data } = await fetchAPI(`Finanzas/evolucion/${torneoActual}/${anio}`);

    if (status) {
        const ctx = document.getElementById('chartEvolucion').getContext('2d');
        const labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const ingresos = data.map(m => m.ingresos);
        const egresos = data.map(m => m.egresos);

        // Tabla Mensual
        const tbody = document.querySelector('#tablaMensual tbody');
        tbody.innerHTML = '';
        data.forEach((m, i) => {
            if (m.ingresos > 0 || m.egresos > 0) {
                const tr = document.createElement('tr');
                const diff = m.ingresos - m.egresos;
                const icon = diff >= 0 ? '<i class="fa-solid fa-caret-up text-success"></i>' : '<i class="fa-solid fa-caret-down text-danger"></i>';

                tr.innerHTML = `
                    <td class="ps-4 fw-bold">${labels[i]}</td>
                    <td class="text-end text-success">${formatMoney(m.ingresos)}</td>
                    <td class="text-end text-danger">${formatMoney(m.egresos)}</td>
                    <td class="text-end fw-bold">${formatMoney(diff)}</td>
                    <td class="text-center">${icon}</td>
                `;
                tbody.appendChild(tr);
            }
        });

        if (chartEvolucion) chartEvolucion.destroy();

        chartEvolucion = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ingresos',
                        data: ingresos,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Egresos',
                        data: egresos,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', align: 'end' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (v) => '$' + v.toLocaleString() } }
                }
            }
        });
    }
}

async function exportar(formato) {
    if (!torneoActual) return swalError('Seleccione un torneo primero', 'Atención');

    swalLoading('Generando Reporte...', 'Estamos procesando los datos, por favor espere.');

    try {
        const fi = document.getElementById('fechaInicio').value || '';
        const ff = document.getElementById('fechaFin').value || '';


        const url = `Finanzas/exportar/balance/${torneoActual}?formato=${formato}&fechaInicio=${fi}&fechaFin=${ff}`;

        // Como la exportación real aún no está implementada en el backend (solo retorna datos),
        // simulamos que lo descargamos o informamos al usuario.
        const result = await fetchAPI(url);

        Swal.close();

        if (result.status) {
            swalConfirm(
                'Reporte Preparado',
                'Los datos están listos para la impresión. ¿Deseas imprimir el balance actual?',
                'Sí, Imprimir'
            ).then((res) => {
                if (res.isConfirmed) window.print();
            });
        }
    } catch (error) {
        Swal.close();
        console.error(error);
    }
}
