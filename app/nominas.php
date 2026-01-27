<?php
$data = [
    "page_title" => "Gestión de Nóminas",
    "page_name" => "nominas",
    "page_js" => "nominas.js"
];
require_once("template/header.php");
?>

<div class="row align-items-center mb-4">
    <div class="col-md-4">
        <h2 class="fw-bold m-0 text-dark">Nóminas por Torneo</h2>
    </div>
    <div class="col-md-4">
        <select class="form-select border-0 shadow-sm" id="selectTorneoNomina"
            style="border-radius: 12px; padding: 12px;">
            <option value="">Seleccione Torneo</option>
            <!-- AJAX -->
        </select>
    </div>
    <div class="col-md-4">
        <select class="form-select border-0 shadow-sm" id="selectEquipoNomina"
            style="border-radius: 12px; padding: 12px;" disabled>
            <option value="">Seleccione Equipo</option>
            <!-- AJAX -->
        </select>
    </div>
</div>

<div id="containerNomina" class="d-none">
    <div class="row">
        <!-- Lista de Jugadores Disponibles -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 fw-bold text-muted small"><i class="fa-solid fa-users me-2"></i> JUGADORES
                        DISPONIBLES EN LA LIGA</h6>
                </div>
                <div class="card-body p-0">
                    <div id="listDisponiblesNomina" class="list-group list-group-flush overflow-auto"
                        style="max-height: 500px;">
                        <!-- AJAX -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Transferencia / Acciones -->
        <div class="col-md-2 d-flex flex-column justify-content-center align-items-center">
            <i class="fa-solid fa-arrow-right-arrow-left fa-2x text-muted opacity-25 mb-4"></i>
            <p class="text-center text-muted small px-2">Selecciona un jugador para vincularlo a la nómina oficial del
                torneo.</p>
        </div>

        <!-- Nómina Oficial del Equipo -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-primary py-3">
                    <h6 class="m-0 fw-bold text-white small"><i class="fa-solid fa-id-card me-2"></i> NÓMINA OFICIAL DEL
                        EQUIPO</h6>
                </div>
                <div class="card-body p-0">
                    <div id="listOficialNomina" class="list-group list-group-flush overflow-auto"
                        style="max-height: 500px;">
                        <!-- AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="placeholderNomina" class="text-center py-5">
    <i class="fa-solid fa-clipboard-list fa-4x text-muted opacity-25 mb-3"></i>
    <h5 class="text-muted">Seleccione un torneo y un equipo para gestionar su nómina</h5>
</div>

<?php require_once("template/footer.php"); ?>