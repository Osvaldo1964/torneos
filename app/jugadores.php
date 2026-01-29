<?php
$data = [
    "page_title" => "Gestión de Jugadores",
    "page_name" => "jugadores",
    "page_js" => "jugadores.js"
];
require_once("template/header.php");
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="deportiva.php" class="text-decoration-none">Gestión Deportiva</a></li>
        <li class="breadcrumb-item active">Jugadores</li>
    </ol>
</nav>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
        <h2 class="fw-bold m-0 text-dark">Base de Jugadores</h2>
        <div class="d-flex gap-2 flex-wrap">
            <select class="form-select form-select-sm shadow-sm border-0 d-none" id="filterLiga"
                style="min-width: 140px;">
                <option value="">Ligas...</option>
            </select>
            <select class="form-select form-select-sm shadow-sm border-0 d-none" id="filterTorneo"
                style="min-width: 140px;">
                <option value="">Torneos...</option>
            </select>
            <select class="form-select form-select-sm shadow-sm border-0 d-none" id="filterEquipo"
                style="min-width: 140px;">
                <option value="">Equipos...</option>
            </select>
        </div>
    </div>
    <button class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 12px;" onclick="openModal()">
        <i class="fa-solid fa-user-plus me-2"></i> Nuevo Jugador
    </button>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table id="tableJugadores" class="table table-hover align-middle border-0" style="width:100%">
            <thead>
                <tr>
                    <th class="border-0 text-muted small text-uppercase">Foto</th>
                    <th class="border-0 text-muted small text-uppercase">Nombre Completos</th>
                    <th class="border-0 text-muted small text-uppercase">Identificación</th>
                    <th class="border-0 text-muted small text-uppercase">Posición</th>
                    <th class="border-0 text-muted small text-uppercase">Estado</th>
                    <th class="border-0 text-muted small text-uppercase text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Jugador -->
<div class="modal fade" id="modalJugador" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Registro de Jugador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formJugador">
                <div class="modal-body p-4">
                    <input type="hidden" id="id_jugador" name="id_jugador" value="0">
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-3 text-center">
                            <img src="assets/images/default_user.png" id="imgFoto"
                                class="img-fluid rounded-circle border shadow-sm"
                                style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label text-muted small fw-bold">Fotografía del Jugador</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*"
                                style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Número de Identificación (DNI)</label>
                            <input type="text" class="form-control" id="identificacion" name="identificacion" required
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email"
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
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono"
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                style="border-radius: 10px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small fw-bold">Posición de Juego</label>
                            <select class="form-select" id="posicion" name="posicion" style="border-radius: 10px;">
                                <option value="Portero">Portero</option>
                                <option value="Defensa">Defensa</option>
                                <option value="Mediocampista">Mediocampista</option>
                                <option value="Delantero">Delantero</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-0">
                            <label class="form-label text-muted small fw-bold">Estado del Jugador</label>
                            <select class="form-select" id="estado" name="estado" style="border-radius: 10px;">
                                <option value="1">Activo</option>
                                <option value="2">Inactivo / Sancionado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Guardar
                        Jugador</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once("template/footer.php"); ?>