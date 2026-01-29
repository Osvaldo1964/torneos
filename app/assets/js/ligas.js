const api_url = app_config.api_url;
const token = app_config.token;
const user = app_config.user;
let tableLigas;

document.addEventListener('DOMContentLoaded', () => {
    // Si es Super Admin, mostrar botón de "Nueva Liga"
    if (user.id_rol == 1) {
        document.getElementById('btnContainer').innerHTML = `
            <button class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 12px;" onclick="openModal()">
                <i class="fa-solid fa-plus me-2"></i> Crear Liga
            </button>
        `;
    } else if (user.id_rol == 2) {
        document.querySelector('h2.fw-bold').innerText = "Configuración de Mi Liga";
    }

    tableLigas = $('#tableLigas').DataTable({
        "ajax": {
            "url": api_url + "Ligas/getLigas",
            "headers": { "Authorization": "Bearer " + token },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_liga" },
            {
                "data": null,
                "render": function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=${data.nombre}&background=0D8ABC&color=fff" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                            <div>
                                <div class="fw-bold">${data.nombre}</div>
                                <small class="text-muted">Liga ID: ${data.id_liga}</small>
                            </div>
                        </div>
                    `;
                }
            },
            {
                "data": "estado",
                "render": function (data) {
                    return data == 1
                        ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2" style="border-radius: 8px;">Activa</span>'
                        : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2" style="border-radius: 8px;">Inactiva</span>';
                }
            },
            {
                "data": null,
                "className": "text-center",
                "render": function (data) {
                    return `
                        <div class="text-center">
                            <button class="btn btn-light btn-sm shadow-sm me-1" style="border-radius: 8px;" onclick="fntEdit(${data.id_liga})"><i class="fa-solid fa-pencil text-primary"></i></button>
                            ${user.id_rol == 1 ? `<button class="btn btn-light btn-sm shadow-sm" style="border-radius: 8px;" onclick="fntDel(${data.id_liga})"><i class="fa-solid fa-trash text-danger"></i></button>` : ''}
                        </div>
                    `;
                }
            }
        ],
        "language": app_config.datatables_lang,
        "order": [[0, "asc"]]
    });

    // Preview de imagen
    const inputFile = document.getElementById('logo');
    inputFile.addEventListener('change', function (e) {
        if (e.target.files[0]) {
            let reader = new FileReader();
            reader.onload = function (ev) {
                document.getElementById('imgPreview').src = ev.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    const formLiga = document.getElementById('formLiga');
    formLiga.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(formLiga);

        try {
            const response = await fetch(api_url + "Ligas/setLiga", {
                method: 'POST',
                headers: {
                    // 'Content-Type': 'multipart/form-data', // No poner esto manualmente con fetch+FormData
                    'Authorization': 'Bearer ' + token
                },
                body: formData
            });
            const result = await response.json();

            if (result.status) {
                Swal.fire("Éxito", result.msg, "success");
                bootstrap.Modal.getInstance(document.getElementById('modalLiga')).hide();
                tableLigas.ajax.reload();
            } else {
                Swal.fire("Error", result.msg, "error");
            }
        } catch (error) {
            Swal.fire("Error", "Ocurrió un error al procesar la solicitud", "error");
        }
    };
});

function openModal() {
    document.getElementById('formLiga').reset();
    document.getElementById('idLiga').value = 0;
    document.getElementById('imgPreview').src = "assets/images/logos/default_logo.png";
    document.getElementById('modalTitle').innerText = "Crear Nueva Liga";
    new bootstrap.Modal(document.getElementById('modalLiga')).show();
}

async function fntEdit(id) {
    try {
        const response = await fetch(api_url + "Ligas/getLiga/" + id, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        if (result.status) {
            const d = result.data;
            document.getElementById('idLiga').value = d.id_liga;
            document.getElementById('nombre').value = d.nombre;
            document.getElementById('estado').value = d.estado;

            // Cargar preview del logo si existe, o default
            let logoSrc = d.logo ? "assets/images/logos/" + d.logo : "assets/images/logos/default_logo.png";
            document.getElementById('imgPreview').src = logoSrc;

            document.getElementById('modalTitle').innerText = "Configurar Liga: " + d.nombre;
            new bootstrap.Modal(document.getElementById('modalLiga')).show();
        }
    } catch (error) {
        Swal.fire("Error", "No se pudo obtener la información", "error");
    }
}

function fntDel(id) {
    Swal.fire({
        title: "¿Eliminar Liga?",
        text: "Al eliminar la liga, todos sus datos quedarán suspendidos.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(api_url + "Ligas/delLiga", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ id_liga: id })
                });
                const res = await response.json();
                if (res.status) {
                    Swal.fire("Eliminado", res.msg, "success");
                    tableLigas.ajax.reload();
                } else {
                    Swal.fire("Error", res.msg, "error");
                }
            } catch (error) {
                Swal.fire("Error", "No se pudo completar la acción", "error");
            }
        }
    });
}
