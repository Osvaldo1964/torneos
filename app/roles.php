<?php
$data = [
    "page_title" => "Gestión de Roles",
    "page_name" => "roles",
    "page_js" => "roles.js"
];
require_once("template/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold m-0 text-dark">Roles de Usuario</h2>
    <button class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 12px;" onclick="openModal()">
        <i class="fa-solid fa-plus me-2"></i> Nuevo Rol
    </button>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table id="tableRoles" class="table table-hover border-0" style="width:100%">
            <thead>
                <tr>
                    <th class="border-0 text-muted small text-uppercase">ID</th>
                    <th class="border-0 text-muted small text-uppercase">Nombre</th>
                    <th class="border-0 text-muted small text-uppercase">Descripción</th>
                    <th class="border-0 text-muted small text-uppercase">Estado</th>
                    <th class="border-0 text-muted small text-uppercase text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalRol" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRol">
                <div class="modal-body p-4">
                    <input type="hidden" id="idRol" name="idRol" value="0">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre del Rol</label>
                        <input type="text" class="form-control" id="nombre_rol" name="nombre_rol"
                            placeholder="Ej: Administrador" required style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                            placeholder="Define las funciones de este rol" required
                            style="border-radius: 10px;"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted small fw-bold">Estado</label>
                        <select class="form-select" id="estado" name="estado" style="border-radius: 10px;">
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px;">Guardar
                        Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Permisos -->
<div class="modal fade" id="modalPermisos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Asignar Permisos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formPermisos">
                    <input type="hidden" id="idRolPermisos" value="0">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Módulo</th>
                                    <th class="text-center">Leer</th>
                                    <th class="text-center">Escribir</th>
                                    <th class="text-center">Actualizar</th>
                                    <th class="text-center">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody id="modulosPermisos">
                                <!-- Modulos loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                            style="border-radius: 10px;">Cerrar</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Guardar
                            Permisos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once("template/footer.php"); ?>