const api_url = app_config.api_url;
const token = app_config.token;
let tableRoles;

document.addEventListener('DOMContentLoaded', () => {
    tableRoles = $('#tableRoles').DataTable({
        "ajax": {
            "url": api_url + "Roles/getRoles",
            "headers": { "Authorization": "Bearer " + token },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_rol" },
            { "data": "nombre_rol" },
            { "data": "descripcion" },
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
                "render": function (data) {
                    return `
                        <div class="text-center">
                            <button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntEdit(${data.id_rol})"><i class="fa-solid fa-pencil text-primary"></i></button>
                            <button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntPermisos(${data.id_rol})"><i class="fa-solid fa-key text-warning"></i></button>
                            <button class="btn btn-light btn-sm shadow-sm" style="border-radius: 8px;" onclick="fntDel(${data.id_rol})"><i class="fa-solid fa-trash text-danger"></i></button>
                        </div>
                    `;
                }
            }
        ],
        "language": app_config.datatables_lang,
        "order": [[0, "desc"]]
    });

    const formRol = document.getElementById('formRol');
    formRol.onsubmit = async (e) => {
        e.preventDefault();
        const formData = {
            idRol: document.getElementById('idRol').value,
            nombre_rol: document.getElementById('nombre_rol').value,
            descripcion: document.getElementById('descripcion').value,
            estado: document.getElementById('estado').value
        };

        try {
            const response = await fetch(api_url + "Roles/setRol", {
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
                bootstrap.Modal.getInstance(document.getElementById('modalRol')).hide();
                tableRoles.ajax.reload();
            } else {
                Swal.fire("Error", result.msg, "error");
            }
        } catch (error) {
            Swal.fire("Error", "Ocurrió un error al procesar la solicitud", "error");
        }
    };
});

function openModal() {
    document.getElementById('formRol').reset();
    document.getElementById('idRol').value = 0;
    document.getElementById('modalTitle').innerText = "Nuevo Rol";
    new bootstrap.Modal(document.getElementById('modalRol')).show();
}

async function fntEdit(id) {
    try {
        const response = await fetch(api_url + "Roles/getRol/" + id, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        if (result.status) {
            document.getElementById('idRol').value = result.data.id_rol;
            document.getElementById('nombre_rol').value = result.data.nombre_rol;
            document.getElementById('descripcion').value = result.data.descripcion;
            document.getElementById('estado').value = result.data.estado;
            document.getElementById('modalTitle').innerText = "Editar Rol";
            new bootstrap.Modal(document.getElementById('modalRol')).show();
        }
    } catch (error) {
        Swal.fire("Error", "No se pudo obtener la información", "error");
    }
}

function fntDel(id) {
    Swal.fire({
        title: "¿Eliminar Rol?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(api_url + "Roles/delRol", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ id_rol: id })
                });
                const res = await response.json();
                if (res.status) {
                    Swal.fire("Eliminado", res.msg, "success");
                    tableRoles.ajax.reload();
                } else {
                    Swal.fire("Error", res.msg, "error");
                }
            } catch (error) {
                Swal.fire("Error", "No se pudo completar la acción", "error");
            }
        }
    });
}

async function fntPermisos(id) {
    document.getElementById('idRolPermisos').value = id;
    try {
        const response = await fetch(api_url + "Permisos/getPermisosRol/" + id, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        if (result.status) {
            let htmlModulos = "";
            result.data.modulos.forEach(mod => {
                htmlModulos += `
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">${mod.nombre}</div>
                            <small class="text-muted">${mod.descripcion}</small>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input check-r" type="checkbox" data-mod="${mod.id_modulo}" ${mod.permisos.r == 1 ? 'checked' : ''}>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input check-w" type="checkbox" data-mod="${mod.id_modulo}" ${mod.permisos.w == 1 ? 'checked' : ''}>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input check-u" type="checkbox" data-mod="${mod.id_modulo}" ${mod.permisos.u == 1 ? 'checked' : ''}>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input check-d" type="checkbox" data-mod="${mod.id_modulo}" ${mod.permisos.d == 1 ? 'checked' : ''}>
                            </div>
                        </td>
                    </tr>
                `;
            });
            document.getElementById('modulosPermisos').innerHTML = htmlModulos;
            new bootstrap.Modal(document.getElementById('modalPermisos')).show();
        }
    } catch (error) {
        Swal.fire("Error", "No se pudieron cargar los permisos", "error");
    }
}

document.getElementById('formPermisos').onsubmit = async (e) => {
    e.preventDefault();
    const idRol = document.getElementById('idRolPermisos').value;
    const modulos = [];

    document.querySelectorAll('#modulosPermisos tr').forEach(tr => {
        const idMod = tr.querySelector('.check-r').dataset.mod;
        modulos.push({
            id_modulo: idMod,
            r: tr.querySelector('.check-r').checked ? 1 : 0,
            w: tr.querySelector('.check-w').checked ? 1 : 0,
            u: tr.querySelector('.check-u').checked ? 1 : 0,
            d: tr.querySelector('.check-d').checked ? 1 : 0
        });
    });

    try {
        const response = await fetch(api_url + "Permisos/setPermisos", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ id_rol: idRol, modulos: modulos })
        });
        const res = await response.json();
        if (res.status) {
            Swal.fire("Éxito", res.msg, "success");
            bootstrap.Modal.getInstance(document.getElementById('modalPermisos')).hide();
        }
    } catch (error) {
        Swal.fire("Error", "No se pudieron guardar los permisos", "error");
    }
};
