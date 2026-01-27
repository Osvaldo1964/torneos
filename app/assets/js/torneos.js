const api_url = app_config.api_url;
const token = app_config.token;
let tableTorneos;

document.addEventListener('DOMContentLoaded', () => {
    tableTorneos = $('#tableTorneos').DataTable({
        "ajax": {
            "url": api_url + "Torneos/getTorneos",
            "headers": { "Authorization": "Bearer " + token },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_torneo" },
            {
                "data": null,
                "render": (data) => `
                    <div class="d-flex align-items-center">
                        <img src="assets/images/torneos/${data.logo}" class="rounded me-2 border shadow-sm" style="width: 35px; height: 35px; object-fit: cover;">
                        <span class="fw-bold">${data.nombre}</span>
                    </div>
                `
            },
            { "data": "categoria" },
            {
                "data": "cuota_jugador",
                "render": (data) => `$${parseFloat(data).toLocaleString()}`
            },
            {
                "data": "valor_arbitraje_base",
                "render": (data) => `$${parseFloat(data).toLocaleString()}`
            },
            {
                "data": "estado",
                "render": function (data) {
                    let badge = 'bg-secondary';
                    if (data == 'EN CURSO') badge = 'bg-success';
                    if (data == 'FINALIZADO') badge = 'bg-dark';
                    return `<span class="badge ${badge} px-3 py-2" style="border-radius: 8px;">${data}</span>`;
                }
            },
            {
                "data": null,
                "className": "text-center",
                "render": function (data) {
                    return `
                        <button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntInscribir(${data.id_torneo}, '${data.nombre}')" title="Inscribir Equipos"><i class="fa-solid fa-right-to-bracket text-success"></i></button>
                        <button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntEdit(${data.id_torneo})" title="Editar"><i class="fa-solid fa-pencil text-primary"></i></button>
                        <button class="btn btn-light btn-sm shadow-sm" style="border-radius: 8px;" onclick="fntDel(${data.id_torneo})" title="Eliminar"><i class="fa-solid fa-trash text-danger"></i></button>
                    `;
                }
            }
        ],
        "language": app_config.datatables_lang
    });

    const formTorneo = document.getElementById('formTorneo');
    formTorneo.onsubmit = async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('id_torneo', document.getElementById('idTorneo').value);
        formData.append('nombre', document.getElementById('nombre').value);
        formData.append('categoria', document.getElementById('categoria').value);
        formData.append('fecha_inicio', document.getElementById('fecha_inicio').value);
        formData.append('fecha_fin', document.getElementById('fecha_fin').value);
        formData.append('cuota_jugador', document.getElementById('cuota_jugador').value);
        formData.append('valor_amarilla', document.getElementById('valor_amarilla').value);
        formData.append('valor_roja', document.getElementById('valor_roja').value);
        formData.append('valor_arbitraje_base', document.getElementById('valor_arbitraje_base').value);
        formData.append('estado', document.getElementById('estado').value);

        const logoFile = document.getElementById('logo').files[0];
        if (logoFile) {
            formData.append('logo', logoFile);
        }

        try {
            const response = await fetch(api_url + "Torneos/setTorneo", {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                body: formData
            });
            const result = await response.json();

            if (result.status) {
                Swal.fire("Éxito", result.msg, "success");
                bootstrap.Modal.getInstance(document.getElementById('modalTorneo')).hide();
                tableTorneos.ajax.reload();
            } else {
                Swal.fire("Error", result.msg, "error");
            }
        } catch (error) {
            Swal.fire("Error", "No se pudo procesar la solicitud", "error");
        }
    };
});

function openModal() {
    document.getElementById('formTorneo').reset();
    document.getElementById('idTorneo').value = 0;
    document.getElementById('imgLogo').src = "assets/images/torneos/default_torneo.png";
    document.getElementById('modalTitle').innerText = "Nuevo Torneo";
    new bootstrap.Modal(document.getElementById('modalTorneo')).show();
}

async function fntEdit(id) {
    try {
        const response = await fetch(api_url + "Torneos/getTorneo/" + id, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        if (result.status) {
            const d = result.data;
            document.getElementById('idTorneo').value = d.id_torneo;
            document.getElementById('nombre').value = d.nombre;
            document.getElementById('imgLogo').src = "assets/images/torneos/" + d.logo;
            document.getElementById('categoria').value = d.categoria;
            document.getElementById('fecha_inicio').value = d.fecha_inicio;
            document.getElementById('fecha_fin').value = d.fecha_fin;
            document.getElementById('cuota_jugador').value = d.cuota_jugador;
            document.getElementById('valor_amarilla').value = d.valor_amarilla;
            document.getElementById('valor_roja').value = d.valor_roja;
            document.getElementById('valor_arbitraje_base').value = d.valor_arbitraje_base;
            document.getElementById('estado').value = d.estado;
            document.getElementById('modalTitle').innerText = "Editar Torneo";
            new bootstrap.Modal(document.getElementById('modalTorneo')).show();
        }
    } catch (error) { }
}

function fntDel(id) {
    Swal.fire({
        title: "¿Eliminar Torneo?",
        text: "Se ocultará de la lista pero los datos históricos se mantendrán.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar"
    }).then(async (result) => {
        if (result.isConfirmed) {
            const response = await fetch(api_url + "Torneos/delTorneo", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({ id_torneo: id })
            });
            const res = await response.json();
            if (res.status) {
                Swal.fire("Eliminado", res.msg, "success");
                tableTorneos.ajax.reload();
            } else {
                Swal.fire("Error", res.msg, "error");
            }
        }
    });
}

function fntInscribir(id, nombre) {
    document.getElementById('torneoInscripcionNombre').innerText = nombre;
    loadEquiposInscripcion(id);
    new bootstrap.Modal(document.getElementById('modalInscripciones')).show();
}

async function loadEquiposInscripcion(idTorneo) {
    try {
        // Cargar Disponibles
        const resDisp = await fetch(`${api_url}Torneos/getDisponibles/${idTorneo}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const disp = await resDisp.json();

        // Cargar Inscritos
        const resInsc = await fetch(`${api_url}Torneos/getInscritos/${idTorneo}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const insc = await resInsc.json();

        let htmlDisp = "";
        disp.data.forEach(e => {
            htmlDisp += `
                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <div class="d-flex align-items-center">
                        <img src="assets/images/equipos/${e.escudo}" class="rounded me-2" style="width:30px; height:30px; object-fit:cover;">
                        <span>${e.nombre}</span>
                    </div>
                    <button class="btn btn-sm btn-outline-primary border-0" onclick="inscribirEquipo(${idTorneo}, ${e.id_equipo})">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            `;
        });
        document.getElementById('listDisponibles').innerHTML = htmlDisp || '<p class="text-muted small">No hay más equipos disponibles.</p>';

        let htmlInsc = "";
        insc.data.forEach(e => {
            htmlInsc += `
                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <div class="d-flex align-items-center">
                        <img src="assets/images/equipos/${e.escudo}" class="rounded me-2" style="width:30px; height:30px; object-fit:cover;">
                        <span>${e.nombre}</span>
                    </div>
                    <button class="btn btn-sm btn-outline-danger border-0" onclick="retirarEquipo(${idTorneo}, ${e.id_equipo})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            `;
        });
        document.getElementById('listInscritos').innerHTML = htmlInsc || '<p class="text-muted small">No hay equipos inscritos aún.</p>';

    } catch (error) {
        console.error(error);
    }
}

async function inscribirEquipo(idTorneo, idEquipo) {
    try {
        const response = await fetch(`${api_url}Torneos/setInscripcion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ id_torneo: idTorneo, id_equipo: idEquipo })
        });
        const result = await response.json();
        if (result.status) loadEquiposInscripcion(idTorneo);
    } catch (error) { }
}

async function retirarEquipo(idTorneo, idEquipo) {
    try {
        const response = await fetch(`${api_url}Torneos/delInscripcion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ id_torneo: idTorneo, id_equipo: idEquipo })
        });
        const result = await response.json();
        if (result.status) loadEquiposInscripcion(idTorneo);
    } catch (error) { }
}
