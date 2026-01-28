const api_url = app_config.api_url;
const token = app_config.token;
let idFaseSeleccionada = 0;
let idGrupoSeleccionado = 0;
let dataNominas = { local: [], visitante: [] };
let matchData = null;

document.addEventListener('DOMContentLoaded', () => {
    fntLoadTorneos();

    document.getElementById('selectTorneoCal').addEventListener('change', (e) => {
        const id = e.target.value;
        if (id) fntLoadEstructura(id);
    });

    // Formulario Fase
    document.getElementById('formFase').onsubmit = async (e) => {
        e.preventDefault();
        const data = {
            id_torneo: document.getElementById('selectTorneoCal').value,
            nombre: e.target.nombre.value,
            tipo: e.target.tipo.value,
            ida_vuelta: e.target.ida_vuelta.value
        };

        const response = await fetch(`${api_url}Competicion/setFase`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        if (res.status) {
            Swal.fire("Éxito", "Fase creada", "success");
            e.target.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalFase')).hide();
            fntLoadEstructura(data.id_torneo);
        }
    };

    // Formulario Grupo
    document.getElementById('formGrupo').onsubmit = async (e) => {
        e.preventDefault();
        const equiposSeleccionados = Array.from(document.querySelectorAll('.check-equipo:checked')).map(el => el.value);

        const data = {
            id_fase: document.getElementById('selectFaseGrupo').value,
            id_grupo: document.getElementById('id_grupo_edit').value,
            nombre: e.target.nombre.value,
            equipos: equiposSeleccionados
        };

        const response = await fetch(`${api_url}Competicion/setGrupo`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        if (res.status) {
            Swal.fire("Éxito", res.msg, "success");
            e.target.reset();
            bootstrap.Modal.getInstance(document.getElementById('modalGrupo')).hide();
            fntLoadEstructura(document.getElementById('selectTorneoCal').value);
        }
    };

    // Formulario Resultado
    document.getElementById('formResultado').onsubmit = async (e) => {
        e.preventDefault();

        // Recolectar eventos
        const eventos = [];
        const rows = document.querySelectorAll('#bodyEventosMatch tr:not(.empty-row)');
        rows.forEach(row => {
            const id_equipo = row.querySelector('.sel-equipo-ev').value;
            const id_jugador = row.querySelector('.sel-jugador-ev').value;
            const minuto = row.querySelector('.inp-minuto-ev').value;
            const tipo = row.querySelector('.sel-tipo-ev').value;

            if (id_jugador > 0) {
                eventos.push({ id_equipo, id_jugador, minuto, tipo });
            }
        });

        const data = {
            id_partido: e.target.id_partido.value,
            goles_local: e.target.goles_local.value,
            goles_visitante: e.target.goles_visitante.value,
            estado: e.target.estado.value,
            eventos: eventos
        };

        const response = await fetch(`${api_url}Competicion/setResultado`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        if (res.status) {
            Swal.fire("Guardado", res.msg, "success");
            bootstrap.Modal.getInstance(document.getElementById('modalResultado')).hide();
            fntLoadPartidos(idFaseSeleccionada, idGrupoSeleccionado);
        }
    };

    // Formulario Programar
    document.getElementById('formProgramar').onsubmit = async (e) => {
        e.preventDefault();
        try {
            const rows = document.querySelectorAll('.terna-row');
            const terna = [];
            rows.forEach(row => {
                const selA = row.querySelector('.select-arbitro');
                const selR = row.querySelector('.select-rol');
                if (selA && selR) {
                    const idA = selA.value;
                    const idR = selR.value;
                    if (idA && idR) terna.push({ id_arbitro: idA, id_rol: idR });
                }
            });

            const idPartido = document.getElementById('id_partido_prog').value;
            const fechaPartido = document.getElementById('fechaProg').value;

            if (!idPartido || !fechaPartido) {
                return Swal.fire("Aviso", "Por favor complete la fecha del partido", "warning");
            }

            const data = {
                id_partido: idPartido,
                fecha_partido: fechaPartido,
                terna: terna
            };

            const response = await fetch(`${api_url}Competicion/setProgramacion`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify(data)
            });
            const res = await response.json();
            if (res.status) {
                Swal.fire("Éxito", res.msg, "success");
                bootstrap.Modal.getInstance(document.getElementById('modalProgramar')).hide();
                fntLoadPartidos(idFaseSeleccionada, idGrupoSeleccionado);
            } else {
                Swal.fire("Error", res.msg || "Error al actualizar", "error");
            }
        } catch (error) {
            console.error("Error en submit programacion:", error);
            Swal.fire("Error", "Ocurrió un error inesperado al procesar el formulario", "error");
        }
    };
});

async function fntLoadTorneos() {
    const response = await fetch(`${api_url}Torneos/getTorneos`, {
        headers: { "Authorization": "Bearer " + token }
    });
    const result = await response.json();
    let html = '<option value="">Seleccione Torneo</option>';
    if (result.status && Array.isArray(result.data)) {
        result.data.forEach(t => {
            html += `<option value="${t.id_torneo}">${t.nombre}</option>`;
        });
    }
    document.getElementById('selectTorneoCal').innerHTML = html;
}

async function fntLoadEstructura(idTorneo) {
    document.getElementById('treeFases').innerHTML = `<div class="p-3 text-center"><div class="spinner-border spinner-border-sm text-primary"></div></div>`;

    const response = await fetch(`${api_url}Competicion/getEstructura/${idTorneo}`, {
        headers: { "Authorization": "Bearer " + token }
    });
    const res = await response.json();

    if (res.status && res.data.length > 0) {
        let html = "";
        res.data.forEach(f => {
            let gruposHtml = "";
            f.grupos.forEach(g => {
                gruposHtml += `<div class="grupo-chip border p-2 mb-2 d-flex justify-content-between align-items-center" onclick="fntSelectGrupo(${f.id_fase}, ${g.id_grupo}, '${g.nombre}')">
                    <span class="small fw-bold">${g.nombre}</span>
                    <div class="actions">
                        <button class="btn btn-sm btn-link text-primary p-0 me-2" onclick="event.stopPropagation(); openModalGrupo(${f.id_fase}, ${g.id_grupo}, '${g.nombre}')">
                            <i class="fa-solid fa-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-link text-danger p-0" onclick="event.stopPropagation(); fntDelGrupo(${g.id_grupo}, '${g.nombre}')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>`;
            });

            html += `
                <div class="fase-item border rounded-3 p-3 mb-3 bg-white shadow-sm position-relative">
                    <div class="position-absolute" style="top:10px; right:10px;">
                        <button class="btn btn-sm btn-light text-success me-1 shadow-sm" style="border-radius: 8px;" onclick="openModalGrupo(${f.id_fase})" title="Agregar Grupo">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger shadow-sm" style="border-radius: 8px;" onclick="fntDelFase(${f.id_fase}, '${f.nombre}')" title="Eliminar Fase">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pe-5">
                        <h6 class="fw-bold m-0 text-primary">${f.nombre}</h6>
                        <span class="badge bg-light text-dark border">${f.tipo}</span>
                    </div>
                    <div class="ps-2 border-start border-2">
                        ${gruposHtml || '<p class="text-muted small mb-0">No hay grupos creados</p>'}
                    </div>
                </div>
            `;
        });
        document.getElementById('treeFases').innerHTML = html;
    } else {
        document.getElementById('treeFases').innerHTML = `
            <div class="text-center p-3">
                <p class="small text-muted">Aún no hay fases definidas para este torneo.</p>
                <button class="btn btn-sm btn-outline-primary" onclick="openModalFase()">Definir Fase</button>
            </div>
        `;
    }
}

async function fntDelGrupo(id, nombre) {
    const result = await Swal.fire({
        title: `¿Eliminar el grupo "${nombre}"?`,
        text: "Se perderán los equipos y partidos asociados a este grupo.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar grupo',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        const response = await fetch(`${api_url}Competicion/delGrupo`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify({ id_grupo: id })
        });
        const res = await response.json();
        if (res.status) {
            Swal.fire("Eliminado", res.msg, "success");
            fntLoadEstructura(document.getElementById('selectTorneoCal').value);
        } else {
            Swal.fire("Error", res.msg, "error");
        }
    }
}

async function fntDelFase(id, nombre) {
    const result = await Swal.fire({
        title: `¿Eliminar la fase "${nombre}"?`,
        text: "Se perderán todos los grupos y partidos asociados a esta fase. Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar fase',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        const response = await fetch(`${api_url}Competicion/delFase`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify({ id_fase: id })
        });
        const res = await response.json();
        if (res.status) {
            Swal.fire("Eliminada", res.msg, "success");
            fntLoadEstructura(document.getElementById('selectTorneoCal').value);
            if (idFaseSeleccionada == id) {
                document.getElementById('containerCal').classList.add('d-none');
                document.getElementById('placeholderCal').classList.remove('d-none');
                idFaseSeleccionada = 0;
            }
        } else {
            Swal.fire("Error", res.msg, "error");
        }
    }
}

function openModalFase() {
    document.getElementById('formFase').reset();
    const idTorneo = document.getElementById('selectTorneoCal').value;
    if (!idTorneo) return Swal.fire("Aviso", "Primero seleccione un torneo", "warning");
    new bootstrap.Modal(document.getElementById('modalFase')).show();
}

async function openModalGrupo(idFasePreset = 0, idGrupoEdit = 0, nombreEdit = '') {
    const idTorneo = document.getElementById('selectTorneoCal').value;
    if (!idTorneo) return Swal.fire("Aviso", "Primero seleccione un torneo", "warning");

    document.getElementById('formGrupo').reset();
    document.getElementById('id_grupo_edit').value = idGrupoEdit;

    if (idGrupoEdit > 0) {
        document.querySelector('#formGrupo [name="nombre"]').value = nombreEdit;
    }

    // Obtener equipos vinculados si estamos editando
    let equiposVinculados = [];
    if (idGrupoEdit > 0) {
        const resDetalle = await fetch(`${api_url}Competicion/getDetalleGrupo/${idGrupoEdit}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const det = await resDetalle.json();
        if (det.status) equiposVinculados = det.data.teams;
    }

    // Cargamos los equipos inscritos en este torneo
    const resEquipos = await fetch(`${api_url}Torneos/getInscritos/${idTorneo}`, {
        headers: { "Authorization": "Bearer " + token }
    });
    const equipos = await resEquipos.json();

    let htmlEq = "";
    if (equipos.status && Array.isArray(equipos.data)) {
        equipos.data.forEach(e => {
            const isChecked = equiposVinculados.includes(String(e.id_equipo)) || equiposVinculados.includes(parseInt(e.id_equipo)) ? 'checked' : '';
            htmlEq += `
                <div class="col">
                    <div class="form-check p-2 border rounded-3 bg-light-subtle">
                        <input class="form-check-input check-equipo ms-1" type="checkbox" value="${e.id_equipo}" id="eq_${e.id_equipo}" ${isChecked}>
                        <label class="form-check-label ms-2 fw-bold" for="eq_${e.id_equipo}">
                            ${e.nombre}
                        </label>
                    </div>
                </div>
            `;
        });
    }
    document.getElementById('listEquiposParaGrupo').innerHTML = htmlEq || '<div class="col-12 text-center text-muted p-3">No hay equipos inscritos en este torneo. Ve a la sección de Torneos para inscribirlos.</div>';

    // Cargamos fases para el select
    const resFases = await fetch(`${api_url}Competicion/getEstructura/${idTorneo}`, {
        headers: { "Authorization": "Bearer " + token }
    });
    const fases = await resFases.json();
    let htmlF = '<option value="">Seleccione Fase Destino</option>';
    if (fases.status && Array.isArray(fases.data)) {
        fases.data.forEach(f => {
            let selected = (idFasePreset == f.id_fase) ? 'selected' : '';
            htmlF += `<option value="${f.id_fase}" ${selected}>${f.nombre}</option>`;
        });
    }
    document.getElementById('selectFaseGrupo').innerHTML = htmlF;

    new bootstrap.Modal(document.getElementById('modalGrupo')).show();
}

function fntSelectGrupo(idFase, idGrupo, nombre) {
    idFaseSeleccionada = idFase;
    idGrupoSeleccionado = idGrupo;
    document.getElementById('placeholderCal').classList.add('d-none');
    document.getElementById('containerCal').classList.remove('d-none');
    document.getElementById('titleContexto').innerText = `Gestión: ${nombre}`;

    fntLoadPartidos(idFase, idGrupo);
}

async function fntLoadPartidos(idFase, idGrupo) {
    const response = await fetch(`${api_url}Competicion/getPartidos/grupo,${idGrupo}`, {
        headers: { "Authorization": "Bearer " + token }
    });
    const res = await response.json();

    let html = "";
    if (res.status && res.data.length > 0) {
        res.data.forEach(p => {
            const logoL = p.logo_local ? `assets/images/equipos/${p.logo_local}` : 'assets/images/default_shield.png';
            const logoV = p.logo_visitante ? `assets/images/equipos/${p.logo_visitante}` : 'assets/images/default_shield.png';

            html += `
                <tr>
                    <td class="fw-bold text-primary">J${p.nro_jornada}</td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="text-end me-3 d-none d-md-block" style="width: 120px;">
                                <span class="small fw-bold">${p.local}</span>
                            </div>
                            <img src="${logoL}" class="rounded-circle border p-1 bg-white" style="width: 35px; height: 35px; object-fit: contain;">
                            <div class="mx-3 px-3 py-1 bg-light rounded-pill fw-bold border" style="min-width: 60px;">
                                ${p.goles_local ?? '-'} : ${p.goles_visitante ?? '-'}
                            </div>
                            <img src="${logoV}" class="rounded-circle border p-1 bg-white" style="width: 35px; height: 35px; object-fit: contain;">
                            <div class="text-start ms-3 d-none d-md-block" style="width: 120px;">
                                <span class="small fw-bold">${p.visitante}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${p.estado == 'PENDIENTE' ? 'bg-warning-subtle text-warning' : (p.estado == 'CANCELADO' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success')} border px-2 py-1" style="font-size: 0.7rem;">
                            ${p.estado}
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light border shadow-sm me-1" style="border-radius: 8px;" onclick="openModalProgramar(${JSON.stringify(p).replace(/"/g, '&quot;')})" title="Programar Fecha y Árbitro">
                            <i class="fa-solid fa-clock text-warning"></i>
                        </button>
                        <button class="btn btn-sm btn-light border shadow-sm" style="border-radius: 8px;" onclick="fntEditPartido(${p.id_partido})" title="Editar Resultado">
                            <i class="fa-solid fa-pen-to-square text-primary"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        document.getElementById('bodyPreviewPartidos').innerHTML = html;
    } else {
        document.getElementById('bodyPreviewPartidos').innerHTML = '<tr><td colspan="4" class="text-center p-5 text-muted"><i class="fa-solid fa-calendar-xmark fa-2x mb-3 d-block opacity-25"></i>No hay encuentros generados. Pulsa el botón "Generar Calendario".</td></tr>';
    }
}

async function fntEditPartido(id) {
    // 1. Obtener datos del partido
    const response = await fetch(`${api_url}Competicion/getPartido/${id}`, {
        headers: { "Authorization": "Bearer " + token }
    });
    const res = await response.json();
    if (res.status) {
        matchData = res.data;
        document.getElementById('id_partido_res').value = matchData.id_partido;
        document.getElementById('nameLocalRes').innerText = matchData.local;
        document.getElementById('nameVisitanteRes').innerText = matchData.visitante;
        document.getElementById('logoLocalRes').src = matchData.logo_local ? `assets/images/equipos/${matchData.logo_local}` : 'assets/images/default_shield.png';
        document.getElementById('logoVisitanteRes').src = matchData.logo_visitante ? `assets/images/equipos/${matchData.logo_visitante}` : 'assets/images/default_shield.png';
        document.getElementById('golesLocalRes').value = matchData.goles_local || 0;
        document.getElementById('golesVisitanteRes').value = matchData.goles_visitante || 0;
        document.getElementById('estadoRes').value = matchData.estado;

        // 2. Cargar Nóminas
        const resNom = await fetch(`${api_url}Competicion/getNominasMatch/${id}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const nom = await resNom.json();
        if (nom.status) dataNominas = nom.data;

        // 3. Cargar Eventos Previos
        const resEv = await fetch(`${api_url}Competicion/getEventosMatch/${id}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const ev = await resEv.json();
        document.getElementById('bodyEventosMatch').innerHTML = "";
        if (ev.status && ev.data.length > 0) {
            ev.data.forEach(e => addRowEvento(e));
        }

        new bootstrap.Modal(document.getElementById('modalResultado')).show();
    }
}

function addRowEvento(data = null) {
    const tbody = document.getElementById('bodyEventosMatch');
    const tr = document.createElement('tr');

    // Opciones de equipos
    let optEquipos = `
        <option value="${matchData.id_local}" ${data && data.id_equipo == matchData.id_local ? 'selected' : ''}>${matchData.local}</option>
        <option value="${matchData.id_visitante}" ${data && data.id_equipo == matchData.id_visitante ? 'selected' : ''}>${matchData.visitante}</option>
    `;

    // Opciones de jugadores del equipo seleccionado
    const idEq = data ? data.id_equipo : matchData.id_local;
    const players = (idEq == matchData.id_local) ? dataNominas.local : dataNominas.visitante;
    let optPlayers = '<option value="0">Seleccionar Jugador</option>';
    players.forEach(p => {
        optPlayers += `<option value="${p.id_jugador}" ${data && data.id_jugador == p.id_jugador ? 'selected' : ''}>(${p.dorsal}) ${p.nombres} ${p.apellidos}</option>`;
    });

    tr.innerHTML = `
        <td><select class="form-select form-select-sm sel-equipo-ev" onchange="updatePlayerSelect(this)">${optEquipos}</select></td>
        <td><select class="form-select form-select-sm sel-jugador-ev">${optPlayers}</select></td>
        <td><input type="number" class="form-control form-control-sm inp-minuto-ev" value="${data ? data.minuto : 0}" min="0" max="150"></td>
        <td>
            <select class="form-select form-select-sm sel-tipo-ev">
                <option value="GOL" ${data && data.tipo_evento == 'GOL' ? 'selected' : ''}>GOL</option>
                <option value="AMARILLA" ${data && data.tipo_evento == 'AMARILLA' ? 'selected' : ''}>🟨 Amarilla</option>
                <option value="ROJA" ${data && data.tipo_evento == 'ROJA' ? 'selected' : ''}>🟥 Roja</option>
                <option value="AUTOGOL" ${data && data.tipo_evento == 'AUTOGOL' ? 'selected' : ''}>Autogol</option>
            </select>
        </td>
        <td class="text-end"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function updatePlayerSelect(el) {
    const idEq = el.value;
    const tr = el.closest('tr');
    const selPlayer = tr.querySelector('.sel-jugador-ev');
    const players = (idEq == matchData.id_local) ? dataNominas.local : dataNominas.visitante;

    let html = '<option value="0">Seleccionar Jugador</option>';
    players.forEach(p => {
        html += `<option value="${p.id_jugador}">(${p.dorsal}) ${p.nombres} ${p.apellidos}</option>`;
    });
    selPlayer.innerHTML = html;
}

async function fntGenerarFixture() {
    if (!idFaseSeleccionada) return;

    const result = await Swal.fire({
        title: '¿Generar Encuentros?',
        text: "Se borrarán los partidos previos de esta fase y se crearán los nuevos automáticamente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, generar calendario',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        Swal.fire({ title: 'Generando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        const response = await fetch(`${api_url}Competicion/generarFixture/${idFaseSeleccionada}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const res = await response.json();

        if (res.status) {
            Swal.fire("Éxito", res.msg, "success");
            fntLoadEstructura(document.getElementById('selectTorneoCal').value);
        } else {
            Swal.fire("Error", res.msg, "error");
        }
    }
}
async function openModalProgramar(p) {
    document.getElementById('formProgramar').reset();
    document.getElementById('id_partido_prog').value = p.id_partido;
    document.getElementById('infoMatchProg').innerText = `${p.local} vs ${p.visitante}`;

    if (p.fecha_partido) {
        // Convertir YYYY-MM-DD HH:MM:SS a YYYY-MM-DDTHH:MM
        const fecha = p.fecha_partido.replace(' ', 'T').substring(0, 16);
        document.getElementById('fechaProg').value = fecha;
    }

    // Cargar Catálogos y Terna
    await fntInitTerna(p);

    new bootstrap.Modal(document.getElementById('modalProgramar')).show();
}

let catArbitros = [];
let catRoles = [];

async function fntInitTerna(p) {
    const container = document.getElementById('ternaContainer');
    container.innerHTML = '';

    const idTorneo = document.getElementById('selectTorneoCal').value;
    if (!idTorneo) return;

    try {
        const [resA, resR] = await Promise.all([
            fetch(`${api_url}Arbitros/listar`, { headers: { "Authorization": "Bearer " + token } }).then(r => r.json()).catch(e => ({ status: false })),
            fetch(`${api_url}Arbitros/roles/${idTorneo}`, { headers: { "Authorization": "Bearer " + token } }).then(r => r.json()).catch(e => ({ status: false }))
        ]);

        catArbitros = resA.status ? resA.data : [];
        catRoles = resR.status ? resR.data : [];
    } catch (e) {
        console.error("Error cargando catálogos de arbitraje:", e);
    }

    // Si el partido ya tiene terna (en p.terna que viene del API selectPartido actualizado)
    if (p.terna && p.terna.length > 0) {
        p.terna.forEach(asig => agregarArbitroRow(asig));
    } else {
        // Por defecto una fila vacía para facilitar
        agregarArbitroRow();
    }
}

function agregarArbitroRow(data = null) {
    const container = document.getElementById('ternaContainer');
    const div = document.createElement('div');
    div.className = "row g-2 mb-2 terna-row animate__animated animate__fadeIn";

    let optA = '<option value="">-- Árbitro --</option>';
    catArbitros.forEach(a => {
        const sel = (data && data.id_arbitro == a.id_arbitro) ? 'selected' : '';
        optA += `<option value="${a.id_arbitro}" ${sel}>${a.nombre_completo}</option>`;
    });

    let optR = '<option value="">-- Rol --</option>';
    catRoles.forEach(r => {
        const sel = (data && data.id_rol == r.id_rol) ? 'selected' : '';
        optR += `<option value="${r.id_rol}" ${sel}>${r.nombre}</option>`;
    });

    div.innerHTML = `
        <div class="col-6">
            <select class="form-select select-arbitro" style="border-radius: 10px;">${optA}</select>
        </div>
        <div class="col-4">
            <select class="form-select select-rol" style="border-radius: 10px;">${optR}</select>
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-outline-danger w-100" style="border-radius: 10px;" onclick="this.parentElement.parentElement.remove()">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
}


