<?php
$data = [
    "page_title" => "Gestión de Usuarios",
    "page_name" => "usuarios",
    "page_js" => "usuarios.js"
];
require_once("template/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold m-0 text-dark">Usuarios del Sistema</h2>
    <button class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 12px;" onclick="openModal()">
        <i class="fa-solid fa-plus me-2"></i> Nuevo Usuario
    </button>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table id="tableUsuarios" class="table table-hover align-middle border-0" style="width:100%">
            <thead>
                <tr>
                    <th class="border-0 text-muted small text-uppercase">ID</th>
                    <th class="border-0 text-muted small text-uppercase">Nombre</th>
                    <th class="border-0 text-muted small text-uppercase">Identificación</th>
                    <th class="border-0 text-muted small text-uppercase">Email</th>
                    <th class="border-0 text-muted small text-uppercase">Rol</th>
                    <th class="border-0 text-muted small text-uppercase">Estado</th>
                    <th class="border-0 text-muted small text-uppercase text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUsuario">
                <div class="modal-body p-4">
                    <input type="hidden" id="idUser" name="idUser" value="0">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Identificación (DNI)</label>
                            <input type="text" class="form-control" id="identificacion" name="identificacion" required
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Nombres</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Rol</label>
                            <select class="form-select" id="id_rol" name="id_rol" style="border-radius: 10px;" required>
                                <!-- Cargar Roles AJAX -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="divLiga" style="display:none;">
                            <label class="form-label text-muted small fw-bold">Liga</label>
                            <select class="form-select" id="id_liga" name="id_liga" style="border-radius: 10px;">
                                <!-- Cargar Ligas AJAX -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Contraseña <small
                                    class="text-primary">(Dejar blanco para no cambiar)</small></label>
                            <input type="password" class="form-control" id="password" name="password"
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-0">
                            <label class="form-label text-muted small fw-bold">Estado</label>
                            <select class="form-select" id="estado" name="estado" style="border-radius: 10px;">
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Cerrar</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Guardar
                        Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once("template/footer.php"); ?>