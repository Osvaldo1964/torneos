// ========================================
// FUNCIONES PARA MÓDULO DE POSICIONES
// ========================================

// Configuración de URLs
const API_URL = app_config.api_url;
const BASE_URL = app_config.base_url;

let currentIdGrupo = null;

document.addEventListener('DOMContentLoaded', function () {
    if (typeof cargarTorneosPosiciones === 'function') {
        cargarTorneosPosiciones();
    }
});

/**
 * Carga los torneos disponibles para el usuario
 */
async function cargarTorneosPosiciones() {
    try {
        const response = await fetch(`${API_URL}Posiciones/torneos`, {
            headers: {
                'Authorization': `Bearer ${getToken()}`
            }
        });

        const result = await response.json();

        if (result.status) {
            const selectTorneo = document.getElementById('selectTorneoPosiciones');
            selectTorneo.innerHTML = '<option value="">-- Seleccione Torneo --</option>';

            if (result.data.is_super_admin) {
                // Super Admin: mostrar ligas primero
                result.data.ligas.forEach(liga => {
                    const option = document.createElement('option');
                    option.value = `liga_${liga.id_liga}`;
                    option.textContent = liga.nombre;
                    selectTorneo.appendChild(option);
                });
            } else {
                // Usuario normal: mostrar torneos directamente
                result.data.torneos.forEach(torneo => {
                    const option = document.createElement('option');
                    option.value = torneo.id_torneo;
                    option.textContent = `${torneo.nombre} - ${torneo.categoria}`;
                    selectTorneo.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error al cargar torneos:', error);
        mostrarAlerta('Error al cargar torneos', 'error');
    }
}

/**
 * Carga las fases de un torneo seleccionado
 */
async function cargarFasesPosiciones() {
    const selectTorneo = document.getElementById('selectTorneoPosiciones');
    const selectFase = document.getElementById('selectFasePosiciones');
    const selectGrupo = document.getElementById('selectGrupoPosiciones');

    // Limpiar selects dependientes
    selectFase.innerHTML = '<option value="">-- Seleccione Fase --</option>';
    selectGrupo.innerHTML = '<option value="">-- Seleccione Grupo --</option>';
    limpiarTablaPosiciones();

    const idTorneo = selectTorneo.value;

    if (!idTorneo || idTorneo.startsWith('liga_')) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}Posiciones/fases/${idTorneo}`, {
            headers: {
                'Authorization': `Bearer ${getToken()}`
            }
        });

        const result = await response.json();

        if (result.status && result.data.length > 0) {
            result.data.forEach(fase => {
                const option = document.createElement('option');
                option.value = fase.id_fase;
                option.textContent = `${fase.nombre} (${fase.tipo})`;
                selectFase.appendChild(option);
            });
        } else {
            mostrarAlerta('Este torneo no tiene fases configuradas', 'warning');
        }
    } catch (error) {
        console.error('Error al cargar fases:', error);
        mostrarAlerta('Error al cargar fases', 'error');
    }
}

/**
 * Carga los grupos de una fase seleccionada
 */
async function cargarGruposPosiciones() {
    const selectFase = document.getElementById('selectFasePosiciones');
    const selectGrupo = document.getElementById('selectGrupoPosiciones');

    selectGrupo.innerHTML = '<option value="">-- Seleccione Grupo --</option>';
    limpiarTablaPosiciones();

    const idFase = selectFase.value;

    if (!idFase) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}Posiciones/grupos/${idFase}`, {
            headers: {
                'Authorization': `Bearer ${getToken()}`
            }
        });

        const result = await response.json();

        if (result.status && result.data.length > 0) {
            result.data.forEach(grupo => {
                const option = document.createElement('option');
                option.value = grupo.id_grupo;
                option.textContent = grupo.nombre;
                selectGrupo.appendChild(option);
            });
        } else {
            mostrarAlerta('Esta fase no tiene grupos configurados', 'warning');
        }
    } catch (error) {
        console.error('Error al cargar grupos:', error);
        mostrarAlerta('Error al cargar grupos', 'error');
    }
}

/**
 * Carga y muestra la tabla de posiciones
 */
async function cargarTablaPosiciones() {
    const selectGrupo = document.getElementById('selectGrupoPosiciones');
    const idGrupo = selectGrupo.value;

    if (!idGrupo) {
        mostrarAlerta('Por favor seleccione un grupo', 'warning');
        return;
    }

    currentIdGrupo = idGrupo;

    try {
        const response = await fetch(`${API_URL}Posiciones/tabla/${idGrupo}`, {
            headers: {
                'Authorization': `Bearer ${getToken()}`
            }
        });

        const result = await response.json();

        if (result.status) {
            mostrarInfoGrupo(result.data.info);
            renderizarTablaPosiciones(result.data.tabla);
            cargarGoleadores(idGrupo);
        } else {
            mostrarAlerta(result.msg || 'Error al cargar tabla de posiciones', 'error');
        }
    } catch (error) {
        console.error('Error al cargar tabla:', error);
        mostrarAlerta('Error al cargar tabla de posiciones', 'error');
    }
}

/**
 * Muestra la información del grupo seleccionado
 */
function mostrarInfoGrupo(info) {
    const infoDiv = document.getElementById('infoGrupoSeleccionado');
    infoDiv.innerHTML = `
        <div class="alert alert-info">
            <h5 class="mb-1"><i class="fas fa-trophy"></i> ${info.nombre_torneo}</h5>
            <p class="mb-0">
                <strong>Liga:</strong> ${info.nombre_liga} | 
                <strong>Fase:</strong> ${info.nombre_fase} | 
                <strong>Grupo:</strong> ${info.nombre_grupo}
            </p>
        </div>
    `;
}

/**
 * Renderiza la tabla de posiciones
 */
function renderizarTablaPosiciones(tabla) {
    const tbody = document.getElementById('tablaPosicionesBody');

    if (!tabla || tabla.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="11" class="text-center text-muted">
                    <i class="fas fa-info-circle"></i> No hay datos disponibles. 
                    Los equipos aparecerán cuando se registren partidos jugados.
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = '';

    tabla.forEach((equipo, index) => {
        const tr = document.createElement('tr');

        // Destacar los primeros 3 lugares
        if (index === 0) tr.classList.add('table-success');
        else if (index === 1) tr.classList.add('table-info');
        else if (index === 2) tr.classList.add('table-warning');

        tr.innerHTML = `
            <td class="text-center fw-bold">${equipo.posicion}</td>
            <td>
                <div class="d-flex align-items-center">
                    <img src="${BASE_URL}app/assets/images/equipos/${equipo.escudo}" 
                         alt="${equipo.equipo}" 
                         class="rounded-circle me-2" 
                         style="width: 30px; height: 30px; object-fit: cover;"
                         onerror="this.onerror=null; this.src='${BASE_URL}app/assets/images/equipos/default_shield.png'">
                    <span>${equipo.equipo}</span>
                </div>
            </td>
            <td class="text-center">${equipo.pj}</td>
            <td class="text-center text-success fw-bold">${equipo.pg}</td>
            <td class="text-center text-warning">${equipo.pe}</td>
            <td class="text-center text-danger">${equipo.pp}</td>
            <td class="text-center">${equipo.gf}</td>
            <td class="text-center">${equipo.gc}</td>
            <td class="text-center ${equipo.dg > 0 ? 'text-success' : equipo.dg < 0 ? 'text-danger' : ''} fw-bold">
                ${equipo.dg > 0 ? '+' : ''}${equipo.dg}
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-primary" onclick="verRachaEquipo(${equipo.id_equipo}, '${equipo.equipo}')">
                    <i class="fas fa-chart-line"></i>
                </button>
            </td>
            <td class="text-center fw-bold fs-5 text-primary">${equipo.pts}</td>
        `;

        tbody.appendChild(tr);
    });

    // Calcular y mostrar estadísticas adicionales
    calcularEstadisticasAdicionales(tabla);
}

/**
 * Calcula y muestra las estadísticas adicionales del grupo
 */
function calcularEstadisticasAdicionales(tabla) {
    if (!tabla || tabla.length === 0) {
        document.getElementById('totalPartidosJugados').textContent = '-';
        document.getElementById('totalGoles').textContent = '-';
        document.getElementById('promedioGoles').textContent = '-';
        document.getElementById('equipoLider').textContent = '-';
        return;
    }

    // Total de partidos jugados (suma de PJ de todos los equipos / 2, porque cada partido cuenta para 2 equipos)
    const totalPartidos = tabla.reduce((sum, equipo) => sum + equipo.pj, 0) / 2;

    // Total de goles (suma de GF de todos los equipos)
    const totalGoles = tabla.reduce((sum, equipo) => sum + equipo.gf, 0);

    // Promedio de goles por partido
    const promedioGoles = totalPartidos > 0 ? (totalGoles / totalPartidos).toFixed(2) : 0;

    // Equipo líder (el primero de la tabla)
    const equipoLider = tabla[0].equipo;

    // Actualizar los elementos HTML
    document.getElementById('totalPartidosJugados').textContent = Math.round(totalPartidos);
    document.getElementById('totalGoles').textContent = totalGoles;
    document.getElementById('promedioGoles').textContent = promedioGoles;
    document.getElementById('equipoLider').textContent = equipoLider;
}

/**
 * Carga los goleadores del grupo
 */
async function cargarGoleadores(idGrupo) {
    try {
        const response = await fetch(`${API_URL}Posiciones/goleadores/${idGrupo}`, {
            headers: {
                'Authorization': `Bearer ${getToken()}`
            }
        });

        const result = await response.json();

        if (result.status) {
            renderizarGoleadores(result.data);
        }
    } catch (error) {
        console.error('Error al cargar goleadores:', error);
    }
}

/**
 * Renderiza la tabla de goleadores
 */
function renderizarGoleadores(goleadores) {
    const tbody = document.getElementById('tablaGoleadoresBody');

    if (!goleadores || goleadores.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    <i class="fas fa-info-circle"></i> No hay goleadores registrados
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = '';

    goleadores.forEach((jugador, index) => {
        const tr = document.createElement('tr');

        if (index === 0) tr.classList.add('table-warning'); // Destacar líder

        tr.innerHTML = `
            <td class="text-center fw-bold">${index + 1}</td>
            <td>
                <div class="d-flex align-items-center">
                    <img src="${BASE_URL}app/assets/images/jugadores/${jugador.foto}" 
                         alt="${jugador.nombres}" 
                         class="rounded-circle me-2" 
                         style="width: 30px; height: 30px; object-fit: cover;"
                         onerror="this.onerror=null; this.src='${BASE_URL}app/assets/images/default_user.png'">
                    <div>
                        <div>${jugador.nombres} ${jugador.apellidos}</div>
                        <small class="text-muted">
                            <img src="${BASE_URL}app/assets/images/equipos/${jugador.escudo}" 
                                 alt="${jugador.equipo}" 
                                 style="width: 16px; height: 16px; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='${BASE_URL}app/assets/images/equipos/default_shield.png'">
                            ${jugador.equipo}
                        </small>
                    </div>
                </div>
            </td>
            <td class="text-center">
                <span class="badge bg-success fs-6">
                    <i class="fas fa-futbol"></i> ${jugador.goles}
                </span>
            </td>
        `;

        tbody.appendChild(tr);
    });
}

/**
 * Muestra la racha de un equipo en un modal
 */
async function verRachaEquipo(idEquipo, nombreEquipo) {
    if (!currentIdGrupo) {
        mostrarAlerta('Error: Grupo no seleccionado', 'error');
        return;
    }

    try {
        const response = await fetch(`${API_URL}Posiciones/racha/${idEquipo}/${currentIdGrupo}`, {
            headers: {
                'Authorization': `Bearer ${getToken()}`
            }
        });

        const result = await response.json();

        if (result.status) {
            mostrarModalRacha(nombreEquipo, result.data);
        }
    } catch (error) {
        console.error('Error al cargar racha:', error);
        mostrarAlerta('Error al cargar racha del equipo', 'error');
    }
}

/**
 * Muestra el modal con la racha del equipo
 */
function mostrarModalRacha(nombreEquipo, racha) {
    const rachaHTML = racha.length > 0
        ? racha.map(r => {
            let badgeClass = '';
            let icon = '';

            if (r.resultado === 'V') {
                badgeClass = 'bg-success';
                icon = 'fa-check-circle';
            } else if (r.resultado === 'E') {
                badgeClass = 'bg-warning';
                icon = 'fa-minus-circle';
            } else {
                badgeClass = 'bg-danger';
                icon = 'fa-times-circle';
            }

            return `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                    <span class="badge ${badgeClass}">
                        <i class="fas ${icon}"></i> ${r.resultado}
                    </span>
                    <span>${r.goles_equipo} - ${r.goles_rival}</span>
                    <small class="text-muted">vs ${r.rival}</small>
                </div>
            `;
        }).join('')
        : '<p class="text-muted text-center">No hay partidos jugados</p>';

    Swal.fire({
        title: `Racha de ${nombreEquipo}`,
        html: `
            <div class="text-start">
                <p class="text-muted mb-3">Últimos 5 partidos:</p>
                ${rachaHTML}
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-check-circle text-success"></i> Victoria | 
                        <i class="fas fa-minus-circle text-warning"></i> Empate | 
                        <i class="fas fa-times-circle text-danger"></i> Derrota
                    </small>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Cerrar',
        width: '500px'
    });
}

/**
 * Limpia la tabla de posiciones
 */
function limpiarTablaPosiciones() {
    document.getElementById('infoGrupoSeleccionado').innerHTML = '';
    document.getElementById('tablaPosicionesBody').innerHTML = '';
    document.getElementById('tablaGoleadoresBody').innerHTML = '';

    // Limpiar estadísticas adicionales
    document.getElementById('totalPartidosJugados').textContent = '-';
    document.getElementById('totalGoles').textContent = '-';
    document.getElementById('promedioGoles').textContent = '-';
    document.getElementById('equipoLider').textContent = '-';

    currentIdGrupo = null;
}

/**
 * Exporta la tabla de posiciones a PDF
 */
function exportarPDF() {
    if (!currentIdGrupo) {
        mostrarAlerta('Primero debe cargar una tabla de posiciones', 'warning');
        return;
    }

    // Implementación con jsPDF (requiere incluir la librería)
    mostrarAlerta('Función de exportación a PDF en desarrollo', 'info');
}

/**
 * Exporta la tabla de posiciones a Excel
 */
function exportarExcel() {
    if (!currentIdGrupo) {
        mostrarAlerta('Primero debe cargar una tabla de posiciones', 'warning');
        return;
    }

    // Implementación con SheetJS (requiere incluir la librería)
    mostrarAlerta('Función de exportación a Excel en desarrollo', 'info');
}

/**
 * Muestra alertas con SweetAlert2
 */
function mostrarAlerta(mensaje, tipo = 'info') {
    Swal.fire({
        icon: tipo,
        title: mensaje,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

/**
 * Obtiene el token JWT del localStorage
 */
function getToken() {
    return localStorage.getItem('gc_token') || '';
}
