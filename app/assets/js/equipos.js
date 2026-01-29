const api_url = app_config.api_url;
const token = app_config.token;
let tableEquipos;

document.addEventListener('DOMContentLoaded', () => {
    tableEquipos = $('#tableEquipos').DataTable({
        "ajax": {
            "url": api_url + "Equipos/getEquipos",
            "headers": { "Authorization": "Bearer " + token },
            "data": function (d) {
                d.id_liga = document.getElementById('filterLiga') ? document.getElementById('filterLiga').value : '';
                d.id_torneo = document.getElementById('filterTorneo') ? document.getElementById('filterTorneo').value : '';
            },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_equipo" },
            {
                "data": "escudo",
                "render": (data) => `<img src="assets/images/equipos/${data}" class="rounded border shadow-sm" style="width: 35px; height: 35px; object-fit: cover;">`
            },
            { "data": "nombre", "className": "fw-bold" },
            {
                "data": null,
                "render": (data) => data.delegado_nombre ? `${data.delegado_nombre} ${data.delegado_apellido}` : '<span class="text-muted">Sin asignar</span>'
            },
            {
                "data": "estado",
                "render": function (data) {
                    return data == 1
                        ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2" style="border-radius: 8px;">Activo</span>'
                        : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2" style="border-radius: 8px;">Inactivo</span>';
                }
            },
            {
                "data": null,
                "className": "text-center",
                "render": function (data) {
                    return `
                        <button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntEdit(${data.id_equipo})"><i class="fa-solid fa-pencil text-primary"></i></button>
                        <button class="btn btn-light btn-sm shadow-sm" style="border-radius: 8px;" onclick="fntDel(${data.id_equipo})"><i class="fa-solid fa-trash text-danger"></i></button>
                    `;
                }
            }
        ],
        "language": app_config.datatables_lang
    });

    // Lógica de Filtros
    const userRole = app_config.user.id_rol;
    const filterLiga = document.getElementById('filterLiga');
    const filterTorneo = document.getElementById('filterTorneo');

    if (userRole == 1) { // Super Admin
        filterLiga.classList.remove('d-none');
        filterTorneo.classList.remove('d-none');
        loadLigasGeneric('filterLiga');

        filterLiga.addEventListener('change', function () {
            loadTorneosGeneric('filterTorneo', this.value);
            tableEquipos.ajax.reload();
        });
    } else if (userRole == 2) { // Liga Admin
        filterTorneo.classList.remove('d-none');
        loadTorneosGeneric('filterTorneo');
    }

    filterTorneo.addEventListener('change', function () {
        tableEquipos.ajax.reload();
    });

    fntLoadDelegados();

    const escudoInput = document.getElementById('escudo');
    if (escudoInput) {
        escudoInput.addEventListener('change', function (e) {
            if (e.target.files[0]) {
                let reader = new FileReader();
                reader.onload = function (ev) {
                    document.getElementById('imgEscudo').src = ev.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    const formEquipo = document.getElementById('formEquipo');
    formEquipo.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(formEquipo);

        try {
            const response = await fetch(api_url + "Equipos/setEquipo", {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            const result = await response.json();

            if (result.status) {
                Swal.fire("Éxito", result.msg, "success");
                bootstrap.Modal.getInstance(document.getElementById('modalEquipo')).hide();
                tableEquipos.ajax.reload();
            } else {
                Swal.fire("Error", result.msg, "error");
            }
        } catch (error) {
            Swal.fire("Error", "No se pudo procesar la solicitud", "error");
        }
    };
});

async function fntLoadDelegados() {
    try {
        const response = await fetch(api_url + "Equipos/getDelegados", {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        let html = '<option value="">Seleccione Delegado</option>';
        if (result.status) {
            result.data.forEach(d => {
                html += `<option value="${d.id_persona}">${d.nombres} ${d.apellidos}</option>`;
            });
        }
        document.getElementById('id_delegado').innerHTML = html;
    } catch (error) { }
}

function openModal() {
    document.getElementById('formEquipo').reset();
    document.getElementById('idEquipo').value = 0;
    document.getElementById('imgEscudo').src = "assets/images/equipos/default_shield.png";
    document.getElementById('modalTitle').innerText = "Nuevo Equipo";

    // Mostrar/Ocultar selects según rol y contexto
    const role = app_config.user.id_rol;
    const modalLigaC = document.getElementById('modalLigaContainer');
    const modalTorneoC = document.getElementById('modalTorneoContainer');
    const modalTorneo = document.getElementById('modalIdTorneo');

    if (modalTorneoC) modalTorneoC.style.display = 'block';
    if (modalTorneo) modalTorneo.required = true;

    if (role == 1) {
        if (modalLigaC) modalLigaC.style.display = 'block';
        loadLigasGeneric('modalIdLiga');
        if (modalTorneo) modalTorneo.innerHTML = '<option value="">Seleccione Liga primero...</option>';

        const ml = document.getElementById('modalIdLiga');
        if (ml) {
            ml.onchange = function () {
                loadTorneosGeneric('modalIdTorneo', this.value);
            };
        }
    } else {
        if (modalLigaC) modalLigaC.style.display = 'none';
        if (role == 2) loadTorneosGeneric('modalIdTorneo');
    }

    new bootstrap.Modal(document.getElementById('modalEquipo')).show();
}

async function fntEdit(id) {
    try {
        const response = await fetch(api_url + "Equipos/getEquipo/" + id, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        if (result.status) {
            const d = result.data;
            document.getElementById('idEquipo').value = d.id_equipo;
            document.getElementById('nombre').value = d.nombre;
            document.getElementById('id_delegado').value = d.id_delegado;
            document.getElementById('estado').value = d.estado;
            document.getElementById('imgEscudo').src = "assets/images/equipos/" + d.escudo;

            // Al editar, ocultamos la asignación de liga/torneo
            if (document.getElementById('modalLigaContainer')) document.getElementById('modalLigaContainer').style.display = 'none';
            if (document.getElementById('modalTorneoContainer')) document.getElementById('modalTorneoContainer').style.display = 'none';
            if (document.getElementById('modalIdTorneo')) document.getElementById('modalIdTorneo').required = false;

            document.getElementById('modalTitle').innerText = "Editar Equipo";
            new bootstrap.Modal(document.getElementById('modalEquipo')).show();
        }
    } catch (error) { }
}

function fntDel(id) {
    Swal.fire({
        title: "¿Eliminar Equipo?",
        text: "Los jugadores asociados a este equipo quedarán libres.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(api_url + "Equipos/delEquipo", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ id_equipo: id })
                });
                const res = await response.json();
                if (res.status) {
                    Swal.fire("Eliminado", res.msg, "success");
                    tableEquipos.ajax.reload();
                } else {
                    Swal.fire("Error", res.msg, "error");
                }
            } catch (e) {
                Swal.fire("Error", "No se pudo eliminar", "error");
            }
        }
    });
}

// Funciones Genéricas de Carga
async function loadLigasGeneric(targetId, selectedId = null) {
    try {
        const response = await fetch(api_url + "Ligas/getLigas", { headers: { "Authorization": "Bearer " + token } });
        const result = await response.json();
        if (result.status) {
            let html = '<option value="">Seleccione Liga...</option>';
            result.data.forEach(l => {
                let sel = (selectedId == l.id_liga) ? 'selected' : '';
                html += `<option value="${l.id_liga}" ${sel}>${l.nombre}</option>`;
            });
            const selEl = document.getElementById(targetId);
            if (selEl) selEl.innerHTML = html;
        }
    } catch (e) { }
}

async function loadTorneosGeneric(targetId, idLiga = null, selectedId = null) {
    const sel = document.getElementById(targetId);
    if (!sel) return;

    sel.innerHTML = '<option value="">Cargando...</option>';

    let url = api_url + 'Torneos/getTorneos';
    if (idLiga) url += '?id_liga=' + idLiga;

    try {
        const response = await fetch(url, { headers: { "Authorization": "Bearer " + token } });
        const result = await response.json();

        let html = '<option value="">Seleccione Torneo...</option>';
        if (result.status) {
            result.data.forEach(t => {
                let s = (selectedId == t.id_torneo) ? 'selected' : '';
                html += `<option value="${t.id_torneo}" ${s}>${t.nombre}</option>`;
            });
        }
        sel.innerHTML = html;
    } catch (e) { sel.innerHTML = '<option value="">Sin torneos</option>'; }
}
