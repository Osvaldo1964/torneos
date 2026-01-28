<?php
$data = [
    "page_title" => "Gestión de Calendario y Fases",
    "page_name" => "calendario",
    "page_js" => "calendario.js"
];
require_once("template/header.php");
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="deportiva.php" class="text-decoration-none">Gestión Deportiva</a></li>
        <li class="breadcrumb-item active">Calendario</li>
    </ol>
</nav>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold m-0 text-dark">Estructura del Torneo</h2>
        <p class="text-muted mb-0">Define fases, grupos y genera los encuentros.</p>
    </div>
    <div class="dropdown">
        <button class="btn btn-primary px-4 fw-bold shadow-sm dropdown-toggle" type="button" id="btnAcciones"
            data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 12px;">
            <i class="fa-solid fa-list-check me-2"></i> Acciones
        </button>
        <ul class="dropdown-menu border-0 shadow-lg" style="border-radius: 12px;">
            <li><a class="dropdown-item" href="#" onclick="openModalFase()"><i
                        class="fa-solid fa-layer-group me-2 text-primary"></i>Nueva Fase</a></li>
            <li><a class="dropdown-item" href="#" onclick="openModalGrupo()"><i
                        class="fa-solid fa-users-rectangle me-2 text-success"></i>Nuevo Grupo</a></li>
        </ul>
    </div>
</div>

<div class="row">
    <!-- Columna de Fases y Grupos -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4 overflow-hidden">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-trophy me-2 text-warning"></i>Torneos Activos</h5>
            </div>
            <div class="card-body p-4">
                <select class="form-select mb-4 shadow-sm" id="selectTorneoCal"
                    style="border-radius: 10px; height: 50px;">
                    <option value="">Seleccione un Torneo</option>
                </select>

                <div id="treeFases" class="list-group list-group-flush">
                    <!-- Dinámico: Fases y Grupos -->
                    <div class="p-4 text-center text-muted border rounded-3 dashed">
                        Seleccione un torneo para ver su estructura
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Visualización y Generación -->
    <div class="col-lg-8">
        <div id="placeholderCal" class="card border-0 shadow-sm p-5 text-center" style="border-radius: 15px;">
            <div class="p-5">
                <i class="fa-solid fa-calendar-days fa-4x text-light mb-4"></i>
                <h4 class="text-muted">Gestión de Encuentros</h4>
                <p class="text-muted px-5">Seleccione una fase o grupo para gestionar los partidos, configurar cruces o
                    generar el calendario automáticamente.</p>
            </div>
        </div>

        <div id="containerCal" class="d-none">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                    <h5 class="fw-bold m-0" id="titleContexto">Configuración del Grupo</h5>
                    <button class="btn btn-sm btn-success fw-bold px-3" onclick="fntGenerarFixture()"
                        style="border-radius: 8px;">
                        <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Generar Calendario
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0" id="tablePreviewPartidos">
                            <thead>
                                <tr class="bg-light">
                                    <th class="border-0 rounded-start">Jor.</th>
                                    <th class="border-0 text-center">Encuentro</th>
                                    <th class="border-0">Estado</th>
                                    <th class="border-0 text-center rounded-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="bodyPreviewPartidos">
                                <!-- Dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Fase -->
<div class="modal fade" id="modalFase" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Nueva Fase de Competencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formFase">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nombre de la Fase</label>
                        <input type="text" class="form-control" name="nombre"
                            placeholder="Ej: Fase de Grupos, Octavos, etc." required style="border-radius: 10px;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Tipo de Juego</label>
                            <select class="form-select" name="tipo" style="border-radius: 10px;">
                                <option value="GRUPOS">Grupos / Liga</option>
                                <option value="ELIMINACION">Eliminación Directa</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Formato</label>
                            <select class="form-select" name="ida_vuelta" style="border-radius: 10px;">
                                <option value="0">Solo Ida</option>
                                <option value="1">Ida y Vuelta</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Crear Fase</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Grupo -->
<div class="modal fade" id="modalGrupo" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Configuración de Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formGrupo">
                <input type="hidden" name="id_grupo" id="id_grupo_edit" value="0">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Fase Destino</label>
                            <select class="form-select" id="selectFaseGrupo" name="id_fase" required
                                style="border-radius: 10px;"></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Nombre del Grupo</label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej: Grupo A" required
                                style="border-radius: 10px;">
                        </div>
                    </div>
                    <hr class="my-3 opacity-10">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-shield-halved me-2 text-primary"></i>Seleccionar
                        Equipos para el Grupo</h6>
                    <div id="listEquiposParaGrupo" class="row row-cols-1 row-cols-md-2 g-3 overflow-auto"
                        style="max-height: 250px;">
                        <!-- Dinámico: Equipos inscritos en el torneo -->
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar Grupo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Resultado -->
<div class="modal fade" id="modalResultado" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Planilla de Juego</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formResultado">
                <input type="hidden" name="id_partido" id="id_partido_res">
                <div class="modal-body p-4 text-center">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="text-center" style="width: 40%;">
                            <img src="" id="logoLocalRes" class="rounded-circle border mb-2 bg-white"
                                style="width: 60px; height: 60px; object-fit: contain;">
                            <h6 class="fw-bold m-0" id="nameLocalRes">Local</h6>
                        </div>
                        <div class="fw-bold fs-4 text-muted">VS</div>
                        <div class="text-center" style="width: 40%;">
                            <img src="" id="logoVisitanteRes" class="rounded-circle border mb-2 bg-white"
                                style="width: 60px; height: 60px; object-fit: contain;">
                            <h6 class="fw-bold m-0" id="nameVisitanteRes">Visitante</h6>
                        </div>
                    </div>

                    <div class="row g-3 justify-content-center align-items-center">
                        <div class="col-4">
                            <input type="number" class="form-control form-control-lg text-center fw-bold"
                                name="goles_local" id="golesLocalRes" min="0" value="0"
                                style="border-radius: 15px; border: 2px solid #eee;">
                        </div>
                        <div class="col-1 fw-bold fs-3">:</div>
                        <div class="col-4">
                            <input type="number" class="form-control form-control-lg text-center fw-bold"
                                name="goles_visitante" id="golesVisitanteRes" min="0" value="0"
                                style="border-radius: 15px; border: 2px solid #eee;">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0"><i class="fa-solid fa-list-check me-2 text-primary"></i>Eventos del
                                Encuentro</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRowEvento()">
                                <i class="fa-solid fa-plus me-1"></i> Agregar Evento
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-borderless align-middle" id="tableEventosMatch">
                                <thead class="bg-light">
                                    <tr style="font-size: 0.75rem;" class="text-muted">
                                        <th style="width: 25%;">Equipo</th>
                                        <th style="width: 40%;">Jugador</th>
                                        <th style="width: 15%;">Min.</th>
                                        <th style="width: 15%;">Tipo</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="bodyEventosMatch">
                                    <!-- Dinámico -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <label class="form-label small fw-bold text-muted d-block text-start mb-2">Estado del
                            Encuentro</label>
                        <select class="form-select shadow-sm" name="estado" id="estadoRes" style="border-radius: 10px;">
                            <option value="PENDIENTE">Pendiente / En curso</option>
                            <option value="JUGADO">Finalizado</option>
                            <option value="CANCELADO">Cancelado / No presentado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Planilla</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .dashed {
        border-style: dashed !important;
        border-width: 2px !important;
    }

    .fase-item {
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 10px !important;
        margin-bottom: 5px;
    }

    .fase-item:hover {
        background-color: #f8f9fa;
    }

    .grupo-chip {
        font-size: 0.85rem;
        padding: 5px 12px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .grupo-chip:hover {
        border-color: var(--bs-primary) !important;
        color: var(--bs-primary);
    }
</style>

<?php require_once("template/footer.php"); ?>