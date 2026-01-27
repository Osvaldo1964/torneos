const api_url = app_config.api_url;
const token = app_config.token;
let tableJugadores;

document.addEventListener('DOMContentLoaded', () => {
    tableJugadores = $('#tableJugadores').DataTable({
        "ajax": {
            "url": api_url + "Jugadores/getJugadores",
            "headers": { "Authorization": "Bearer " + token },
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
                    return `
                        <button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntEdit(${data.id_jugador})"><i class="fa-solid fa-pencil text-primary"></i></button>
                        <button class="btn btn-light btn-sm shadow-sm" style="border-radius: 8px;" onclick="fntDel(${data.id_jugador})"><i class="fa-solid fa-trash text-danger"></i></button>
                    `;
                }
            }
        ],
        "language": app_config.datatables_lang
    });

    const formJugador = document.getElementById('formJugador');
    formJugador.onsubmit = async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append('id_jugador', document.getElementById('id_jugador').value);
        formData.append('identificacion', document.getElementById('identificacion').value);
        formData.append('nombres', document.getElementById('nombres').value);
        formData.append('apellidos', document.getElementById('apellidos').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('telefono', document.getElementById('telefono').value);
        formData.append('fecha_nacimiento', document.getElementById('fecha_nacimiento').value);
        formData.append('posicion', document.getElementById('posicion').value);
        formData.append('estado', document.getElementById('estado').value);

        const fotoFile = document.getElementById('foto').files[0];
        if (fotoFile) {
            formData.append('foto', fotoFile);
        }

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
            }
        }
    });
}
