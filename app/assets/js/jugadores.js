const api_url = app_config.api_url;
const token = app_config.token;
let tableJugadores;

document.addEventListener('DOMContentLoaded', () => {
    tableJugadores = $('#tableJugadores').DataTable({
        "ajax": {
            "url": api_url + "Jugadores/getJugadores",
            "headers": { "Authorization": "Bearer " + token },
            "data": function (d) {
                d.id_liga = document.getElementById('filterLiga') ? document.getElementById('filterLiga').value : '';
                d.id_torneo = document.getElementById('filterTorneo') ? document.getElementById('filterTorneo').value : '';
                d.id_equipo = document.getElementById('filterEquipo') ? document.getElementById('filterEquipo').value : '';
            },
            "dataSrc": "data"
        },
        "columns": [
            {
                "data": "foto",
                "render": (data) => `<img src="assets/images/jugadores/${data}" class="rounded-circle border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">`
            },
            {
                "data": null,
                "render": (data) => `<div class="fw-bold text-dark">${data.nombres} ${data.apellidos}</div>`
            },
            { "data": "identificacion" },
            { "data": "posicion" },
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
                    // Si es jugador (rol 4), quizás ocultar botones de eliminar
                    let btns = `<button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntEdit(${data.id_jugador})"><i class="fa-solid fa-pencil text-primary"></i></button>`;
                    if (app_config.user.id_rol != 4) {
                        btns += `<button class="btn btn-light btn-sm shadow-sm" style="border-radius: 8px;" onclick="fntDel(${data.id_jugador})"><i class="fa-solid fa-trash text-danger"></i></button>`;
                    }
                    return btns;
                }
            }
        ],
        "language": app_config.datatables_lang
    });

    // Lógica de Roles y Filtros
    const role = app_config.user.id_rol;
    const fLiga = document.getElementById('filterLiga');
    const fTorneo = document.getElementById('filterTorneo');
    const fEquipo = document.getElementById('filterEquipo');

    if (role == 1) { // Super Admin
        fLiga.classList.remove('d-none');
        fTorneo.classList.remove('d-none');
        fEquipo.classList.remove('d-none');

        loadLigasGeneric('filterLiga');
        fLiga.onchange = function () {
            loadTorneosGeneric('filterTorneo', this.value);
            // Limpia equipo
            fEquipo.innerHTML = '<option value="">Todos los Equipos</option>';
            tableJugadores.ajax.reload();
        };
        fTorneo.onchange = function () {
            loadEquiposGeneric('filterEquipo', fLiga.value, this.value);
            tableJugadores.ajax.reload();
        };
        fEquipo.onchange = function () { tableJugadores.ajax.reload(); };

    } else if (role == 2) { // Liga Admin
        fTorneo.classList.remove('d-none');
        fEquipo.classList.remove('d-none');

        loadTorneosGeneric('filterTorneo'); // Su liga
        fTorneo.onchange = function () {
            loadEquiposGeneric('filterEquipo', app_config.user.id_liga, this.value);
            tableJugadores.ajax.reload();
        };
        fEquipo.onchange = function () { tableJugadores.ajax.reload(); };

    } else if (role == 3) { // Delegado
        fEquipo.classList.remove('d-none');
        loadEquiposGeneric('filterEquipo', app_config.user.id_liga); // API filtra por delegado
        fEquipo.onchange = function () { tableJugadores.ajax.reload(); };
    }

    // Listener Foto
    document.getElementById('foto').addEventListener('change', function (e) {
        if (e.target.files[0]) {
            let reader = new FileReader();
            reader.onload = function (ev) {
                document.getElementById('imgFoto').src = ev.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    const formJugador = document.getElementById('formJugador');
    formJugador.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(formJugador);

        try {
            const response = await fetch(api_url + "Jugadores/setJugador", {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            const result = await response.json();

            if (result.status) {
                Swal.fire("Éxito", result.msg, "success");
                bootstrap.Modal.getInstance(document.getElementById('modalJugador')).hide();
                tableJugadores.ajax.reload();
            } else {
                Swal.fire("Error", result.msg, "error");
            }
        } catch (error) {
            Swal.fire("Error", "No se pudo procesar la solicitud", "error");
        }
    };
});

function openModal() {
    document.getElementById('formJugador').reset();
    document.getElementById('id_jugador').value = 0;
    document.getElementById('imgFoto').src = "assets/images/default_user.png";
    document.getElementById('modalTitle').innerText = "Nuevo Jugador";
    new bootstrap.Modal(document.getElementById('modalJugador')).show();
}

async function fntEdit(id) {
    try {
        const response = await fetch(api_url + "Jugadores/getJugador/" + id, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        if (result.status) {
            const d = result.data;
            document.getElementById('id_jugador').value = d.id_jugador;
            document.getElementById('identificacion').value = d.identificacion;
            document.getElementById('nombres').value = d.nombres;
            document.getElementById('apellidos').value = d.apellidos;
            document.getElementById('email').value = d.email;
            document.getElementById('telefono').value = d.telefono;
            document.getElementById('fecha_nacimiento').value = d.fecha_nacimiento;
            document.getElementById('posicion').value = d.posicion;
            document.getElementById('estado').value = d.estado;
            document.getElementById('imgFoto').src = "assets/images/jugadores/" + d.foto;
            document.getElementById('modalTitle').innerText = "Editar Jugador";
            new bootstrap.Modal(document.getElementById('modalJugador')).show();
        }
    } catch (error) { }
}

function fntDel(id) {
    Swal.fire({
        title: "¿Eliminar Perfil?",
        text: "Se eliminará el perfil deportivo de esta liga. Si la persona es Admin o Delegado, su cuenta de acceso no se borrará.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar perfil"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(api_url + "Jugadores/delJugador", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ id_jugador: id })
                });
                const res = await response.json();
                if (res.status) {
                    Swal.fire("Eliminado", res.msg, "success");
                    tableJugadores.ajax.reload();
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
            const el = document.getElementById(targetId);
            if (el) el.innerHTML = html;
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

        let html = '<option value="">Todos los Torneos</option>';
        if (result.status) {
            result.data.forEach(t => {
                let s = (selectedId == t.id_torneo) ? 'selected' : '';
                html += `<option value="${t.id_torneo}" ${s}>${t.nombre}</option>`;
            });
        }
        sel.innerHTML = html;
    } catch (e) { sel.innerHTML = '<option value="">Todos los Torneos</option>'; }
}

async function loadEquiposGeneric(targetId, idLiga = null, idTorneo = null, selectedId = null) {
    const sel = document.getElementById(targetId);
    if (!sel) return;

    sel.innerHTML = '<option value="">Cargando...</option>';

    let url = api_url + 'Equipos/getEquipos';
    let params = [];
    if (idLiga) params.push('id_liga=' + idLiga);
    if (idTorneo) params.push('id_torneo=' + idTorneo);
    if (params.length > 0) url += '?' + params.join('&');

    try {
        const response = await fetch(url, { headers: { "Authorization": "Bearer " + token } });
        const result = await response.json();

        let html = '<option value="">Todos los Equipos</option>';
        if (result.status) {
            result.data.forEach(e => {
                let s = (selectedId == e.id_equipo) ? 'selected' : '';
                html += `<option value="${e.id_equipo}" ${s}>${e.nombre}</option>`;
            });
        }
        sel.innerHTML = html;
    } catch (e) { sel.innerHTML = '<option value="">Todos los Equipos</option>'; }
}
