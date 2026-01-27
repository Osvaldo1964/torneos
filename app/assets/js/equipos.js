const api_url = app_config.api_url;
const token = app_config.token;
let tableEquipos;

document.addEventListener('DOMContentLoaded', () => {
    tableEquipos = $('#tableEquipos').DataTable({
        "ajax": {
            "url": api_url + "Equipos/getEquipos",
            "headers": { "Authorization": "Bearer " + token },
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

    fntLoadDelegados();

    const formEquipo = document.getElementById('formEquipo');
    formEquipo.onsubmit = async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('id_equipo', document.getElementById('idEquipo').value);
        formData.append('nombre', document.getElementById('nombre').value);
        formData.append('id_delegado', document.getElementById('id_delegado').value);
        formData.append('estado', document.getElementById('estado').value);

        const escudoFile = document.getElementById('escudo').files[0];
        if (escudoFile) {
            formData.append('escudo', escudoFile);
        }

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
        result.data.forEach(d => {
            html += `<option value="${d.id_persona}">${d.nombres} ${d.apellidos}</option>`;
        });
        document.getElementById('id_delegado').innerHTML = html;
    } catch (error) { }
}

function openModal() {
    document.getElementById('formEquipo').reset();
    document.getElementById('idEquipo').value = 0;
    document.getElementById('imgEscudo').src = "assets/images/equipos/default_shield.png";
    document.getElementById('modalTitle').innerText = "Nuevo Equipo";
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
            }
        }
    });
}
