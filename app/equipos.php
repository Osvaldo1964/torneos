<?php
$data = [
    "page_title" => "Gestión de Equipos",
    "page_name" => "equipos",
    "page_js" => "equipos.js"
];
require_once("template/header.php");
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="deportiva.php" class="text-decoration-none">Gestión Deportiva</a></li>
        <li class="breadcrumb-item active">Equipos</li>
    </ol>
</nav>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
        <h2 class="fw-bold m-0 text-dark">Equipos</h2>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm shadow-sm border-0 d-none" id="filterLiga"
                style="min-width: 150px;">
                <option value="">Todas las Ligas</option>
            </select>
            <select class="form-select form-select-sm shadow-sm border-0 d-none" id="filterTorneo"
                style="min-width: 150px;">
                <option value="">Todos los Torneos</option>
            </select>
        </div>
    </div>
    <div id="btnNewContainer">
        <button class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 12px;" onclick="openModal()">
            <i class="fa-solid fa-plus me-2"></i> Nuevo Equipo
        </button>
    </div>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table id="tableEquipos" class="table table-hover align-middle border-0" style="width:100%">
            <thead>
                <tr>
                    <th class="border-0 text-muted small text-uppercase">ID</th>
                    <th class="border-0 text-muted small text-uppercase">Escudo</th>
                    <th class="border-0 text-muted small text-uppercase">Nombre</th>
                    <th class="border-0 text-muted small text-uppercase">Delegado</th>
                    <th class="border-0 text-muted small text-uppercase">Estado</th>
                    <th class="border-0 text-muted small text-uppercase text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Equipo -->
<div class="modal fade" id="modalEquipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEquipo">
                <div class="modal-body p-4">
                    <input type="hidden" id="idEquipo" name="idEquipo" value="0">
                    <div class="text-center mb-4">
                        <img src="assets/images/equipos/default_shield.png" id="imgEscudo"
                            class="img-fluid rounded border p-1 shadow-sm mb-3"
                            style="max-height: 120px; width: 120px; object-fit: cover;">
                        <input type="file" class="form-control" id="escudo" name="escudo" accept="image/*"
                            style="border-radius: 10px;">
                        <label class="form-label text-muted small d-block mt-1">Escudo del Equipo</label>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3" id="modalLigaContainer" style="display:none;">
                            <label class="form-label text-muted small fw-bold">Liga</label>
                            <select class="form-select" id="modalIdLiga" name="id_liga" style="border-radius: 10px;">
                                <option value="">Seleccione Liga...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="modalTorneoContainer">
                            <label class="form-label text-muted small fw-bold">Torneo de Inscripción</label>
                            <select class="form-select" id="modalIdTorneo" name="id_torneo"
                                style="border-radius: 10px;">
                                <option value="">Seleccione Torneo...</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre del Equipo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required
                            placeholder="Ej: F.C. Barcelona" style="border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Delegado Responsable</label>
                        <select class="form-select" id="id_delegado" name="id_delegado" style="border-radius: 10px;"
                            required>
                            <!-- Cargar via AJAX -->
                        </select>
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
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Guardar
                        Equipo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once("template/footer.php"); ?>