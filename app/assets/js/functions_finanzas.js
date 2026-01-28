

document.addEventListener('DOMContentLoaded', () => {
    cargarTorneos();
});

// Cargar lista de torneos
async function cargarTorneos() {
    try {
        const result = await fetchAPI('Posiciones/torneos');



        if (result.status) {
            const select = document.getElementById('selectTorneo');

            if (result.data.is_super_admin) {
                // Super Admin ve todas las ligas
                result.data.ligas.forEach(liga => {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = liga.nombre;
                    // Aquí se cargarían los torneos de cada liga
                    select.appendChild(optgroup);
                });
            } else {
                // Usuario normal ve sus torneos
                result.data.torneos.forEach(torneo => {
                    const option = document.createElement('option');
                    option.value = torneo.id_torneo;
                    option.textContent = torneo.nombre;
                    select.appendChild(option);
                });
            }

            // Event listener para cambio de torneo
            select.addEventListener('change', (e) => {
                if (e.target.value) {
                    cargarBalance(e.target.value);
                } else {
                    document.getElementById('statsCards').style.display = 'none';
                    document.getElementById('mensajeInicial').style.display = 'block';
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar torneos:', error);
    }
}

// Cargar balance del torneo
async function cargarBalance(idTorneo) {
    try {
        const result = await fetchAPI(`Finanzas/balance/${idTorneo}`);

        if (result.status) {
            const balance = result.data;

            // Actualizar tarjetas
            document.getElementById('totalIngresos').textContent = formatMoney(balance.totales.ingresos);
            document.getElementById('totalEgresos').textContent = formatMoney(balance.totales.egresos);
            document.getElementById('balance').textContent = formatMoney(balance.totales.resultado);

            const estadoElement = document.getElementById('estadoBalance');
            estadoElement.textContent = balance.totales.tipo;

            // Colorear según el tipo
            estadoElement.className = 'fw-bold mb-0';
            if (balance.totales.tipo === 'UTILIDAD') {
                estadoElement.classList.add('text-success');
            } else if (balance.totales.tipo === 'PERDIDA') {
                estadoElement.classList.add('text-danger');
            } else {
                estadoElement.classList.add('text-secondary');
            }

            // Mostrar tarjetas y ocultar mensaje inicial
            document.getElementById('statsCards').style.display = 'flex';
            document.getElementById('mensajeInicial').style.display = 'none';
        }
    } catch (error) {
        console.error('Error al cargar balance:', error);
        swalError('No se pudo cargar el balance del torneo');
    }
}


