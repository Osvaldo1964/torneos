const api_url = app_config.api_url;
const token = app_config.token;
const user = app_config.user;
let tableUsuarios;

document.addEventListener('DOMContentLoaded', () => {
    tableUsuarios = $('#tableUsuarios').DataTable({
        "ajax": {
            "url": api_url + "Usuarios/getUsuarios",
            "headers": { "Authorization": "Bearer " + token },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_persona" },
            {
                "data": null,
                "render": function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=${data.nombres}&background=0D8ABC&color=fff" class="rounded-circle me-3" style="width: 35px; height: 35px;">
                            <div><div class="fw-bold">${data.nombres} ${data.apellidos}</div></div>
                        </div>
                    `;
                }
            },
            { "data": "identificacion" },
            { "data": "email" },
            { "data": "nombre_rol" },
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
                        <button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntEdit(${data.id_persona})"><i class="fa-solid fa-pencil text-primary"></i></button>
                        <button class="btn btn-light btn-sm shadow-sm" style="border-radius: 8px;" onclick="fntDel(${data.id_persona})"><i class="fa-solid fa-trash text-danger"></i></button>
                    `;
                }
            }
        ],
        "language": app_config.datatables_lang
    });

    fntLoadRoles();
    if (user.id_rol == 1) {
        document.getElementById('divLiga').style.display = "block";
        fntLoadLigas();
    }

    const formUsuario = document.getElementById('formUsuario');
    formUsuario.onsubmit = async (e) => {
        e.preventDefault();
        const formData = {
            id_user: document.getElementById('idUser').value,
            identificacion: document.getElementById('identificacion').value,
            nombres: document.getElementById('nombres').value,
            apellidos: document.getElementById('apellidos').value,
            email: document.getElementById('email').value,
            id_rol: document.getElementById('id_rol').value,
            id_liga: document.getElementById('id_liga')?.value || user.id_liga,
            estado: document.getElementById('estado').value,
            password: document.getElementById('password').value
        };

        try {
            const response = await fetch(api_url + "Usuarios/setUsuario", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(formData)
            });
            const result = await response.json();

            if (result.status) {
                Swal.fire("Éxito", result.msg, "success");
                bootstrap.Modal.getInstance(document.getElementById('modalUsuario')).hide();
                tableUsuarios.ajax.reload();
            } else {
                Swal.fire("Error", result.msg, "error");
            }
        } catch (error) {
            Swal.fire("Error", "No se pudo procesar la solicitud", "error");
        }
    };
});

async function fntLoadRoles() {
    try {
        const response = await fetch(api_url + "Roles/getRoles", {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        let html = '<option value="">Seleccione Rol</option>';
        result.data.forEach(r => {
            html += `<option value="${r.id_rol}">${r.nombre_rol}</option>`;
        });
        document.getElementById('id_rol').innerHTML = html;
    } catch (error) { }
}

async function fntLoadLigas() {
    try {
        const response = await fetch(api_url + "Ligas/getLigas", {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        let html = '<option value="">Seleccione Liga</option>';
        result.data.forEach(l => {
            html += `<option value="${l.id_liga}">${l.nombre}</option>`;
        });
        document.getElementById('id_liga').innerHTML = html;
    } catch (error) { }
}

function openModal() {
    document.getElementById('formUsuario').reset();
    document.getElementById('idUser').value = 0;
    document.getElementById('modalTitle').innerText = "Nuevo Usuario";
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}

async function fntEdit(id) {
    try {
        const response = await fetch(api_url + "Usuarios/getUsuario/" + id, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        if (result.status) {
            const d = result.data;
            document.getElementById('idUser').value = d.id_persona;
            document.getElementById('identificacion').value = d.identificacion;
            document.getElementById('nombres').value = d.nombres;
            document.getElementById('apellidos').value = d.apellidos;
            document.getElementById('email').value = d.email;
            document.getElementById('id_rol').value = d.id_rol;
            if (document.getElementById('id_liga')) document.getElementById('id_liga').value = d.id_liga;
            document.getElementById('estado').value = d.estado;
            document.getElementById('password').value = "";
            document.getElementById('modalTitle').innerText = "Editar Usuario";
            new bootstrap.Modal(document.getElementById('modalUsuario')).show();
        }
    } catch (error) { }
}

function fntDel(id) {
    Swal.fire({
        title: "¿Eliminar Usuario?",
        text: "El usuario ya no podrá ingresar al sistema.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar"
    }).then(async (result) => {
        if (result.isConfirmed) {
            const response = await fetch(api_url + "Usuarios/delUsuario", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({ id_user: id })
            });
            const res = await response.json();
            if (res.status) {
                Swal.fire("Eliminado", res.msg, "success");
                tableUsuarios.ajax.reload();
            } else {
                Swal.fire("Error", res.msg, "error");
            }
        }
    });
}
