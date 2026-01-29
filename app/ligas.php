<?php
$data = [
    "page_title" => "Gestión de Ligas",
    "page_name" => "ligas",
    "page_js" => "ligas.js"
];
require_once("template/header.php");
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="deportiva.php" class="text-decoration-none">Gestión Deportiva</a></li>
        <li class="breadcrumb-item active">Ligas</li>
    </ol>
</nav>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold m-0 text-dark">Ligas Registradas</h2>
    <div id="btnContainer">
        <!-- Button created dynamically for Super Admin -->
    </div>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table id="tableLigas" class="table table-hover align-middle border-0" style="width:100%">
            <thead>
                <tr>
                    <th class="border-0 text-muted small text-uppercase">ID</th>
                    <th class="border-0 text-muted small text-uppercase">Liga</th>
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

<!-- Modal Liga -->
<div class="modal fade" id="modalLiga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Detalles de la Liga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formLiga">
                <div class="modal-body p-4">
                    <input type="hidden" id="idLiga" name="idLiga" value="0">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted small fw-bold">Nombre de la Liga</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-12 mb-0">
                            <label class="form-label text-muted small fw-bold">Estado</label>
                            <select class="form-select" id="estado" name="estado" style="border-radius: 10px;">
                                <option value="1">Activa</option>
                                <option value="2">Inactiva / Suspendida</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Cerrar</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Guardar
                        Configuración</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once("template/footer.php"); ?>