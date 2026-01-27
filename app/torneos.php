<?php
$data = [
    "page_title" => "Gestión de Torneos",
    "page_name" => "torneos",
    "page_js" => "torneos.js"
];
require_once("template/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold m-0 text-dark">Torneos de la Liga</h2>
    <button class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 12px;" onclick="openModal()">
        <i class="fa-solid fa-plus me-2"></i> Nuevo Torneo
    </button>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table id="tableTorneos" class="table table-hover align-middle border-0" style="width:100%">
            <thead>
                <tr>
                    <th class="border-0 text-muted small text-uppercase">ID</th>
                    <th class="border-0 text-muted small text-uppercase">Nombre</th>
                    <th class="border-0 text-muted small text-uppercase">Categoría</th>
                    <th class="border-0 text-muted small text-uppercase">Cuota Jugador</th>
                    <th class="border-0 text-muted small text-uppercase">Arbitraje</th>
                    <th class="border-0 text-muted small text-uppercase">Estado</th>
                    <th class="border-0 text-muted small text-uppercase text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Torneo -->
<div class="modal fade" id="modalTorneo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Torneo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTorneo">
                <div class="modal-body p-4">
                    <input type="hidden" id="idTorneo" name="idTorneo" value="0">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center">
                            <img src="assets/images/torneos/default_torneo.png" id="imgLogo"
                                class="img-fluid rounded border p-1 shadow-sm"
                                style="max-height: 100px; width: 100px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label text-muted small fw-bold">Logo del Torneo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*"
                                style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label text-muted small fw-bold">Nombre del Torneo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                placeholder="Ej: Apertura 2026" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold">Categoría</label>
                            <input type="text" class="form-control" id="categoria" name="categoria"
                                placeholder="Ej: Sub-20, Senior" style="border-radius: 10px;">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Fecha Fin (Estimada)</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                                style="border-radius: 10px;">
                        </div>

                        <hr class="text-muted my-3">
                        <p class="small fw-bold text-primary mb-3"><i class="fa-solid fa-money-bill-wave me-1"></i>
                            Configuración Financiera del Torneo</p>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted small fw-bold">Cuota Jugador</label>
                            <input type="number" step="0.01" class="form-control" id="cuota_jugador"
                                name="cuota_jugador" value="0.00" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted small fw-bold">Multa Amarilla</label>
                            <input type="number" step="0.01" class="form-control" id="valor_amarilla"
                                name="valor_amarilla" value="0.00" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted small fw-bold">Multa Roja</label>
                            <input type="number" step="0.01" class="form-control" id="valor_roja" name="valor_roja"
                                value="0.00" style="border-radius: 10px;">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted small fw-bold">Arbitraje Base</label>
                            <input type="number" step="0.01" class="form-control" id="valor_arbitraje_base"
                                name="valor_arbitraje_base" value="0.00" style="border-radius: 10px;">
                        </div>

                        <div class="col-md-12 mb-0">
                            <label class="form-label text-muted small fw-bold">Estado del Torneo</label>
                            <select class="form-select" id="estado" name="estado" style="border-radius: 10px;">
                                <option value="PROGRAMADO">Programado</option>
                                <option value="EN CURSO">En Curso</option>
                                <option value="FINALIZADO">Finalizado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Guardar
                        Torneo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Inscripciones -->
<div class="modal fade" id="modalInscripciones" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-bold">Gestionar Equipos</h5>
                    <p class="text-muted small mb-0" id="torneoInscripcionNombre"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <label class="form-label text-muted small fw-bold mb-3">Equipos Disponibles</label>
                        <div id="listDisponibles" class="list-group list-group-flush overflow-auto"
                            style="max-height: 350px;">
                            <!-- AJAX -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold mb-3">Equipos Inscritos</label>
                        <div id="listInscritos" class="list-group list-group-flush overflow-auto"
                            style="max-height: 350px;">
                            <!-- AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once("template/footer.php"); ?>