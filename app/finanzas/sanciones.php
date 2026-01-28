<?php
$data = [
    'page_title' => 'Sanciones Económicas',
    'page_name' => 'finanzas',
    'base_path' => '../',
    'page_js' => 'functions_sanciones.js'
];
require_once(__DIR__ . '/../template/header.php');
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="../finanzas.php" class="text-decoration-none">Finanzas</a></li>
                    <li class="breadcrumb-item active">Sanciones Económicas</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-1">⚠️ Sanciones Económicas</h3>
            <p class="text-muted mb-0">Gestión de multas y sanciones por tarjetas e infracciones</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="abrirConfiguracion()">
                <i class="fa-solid fa-gear me-2"></i>Configuración
            </button>
            <button class="btn btn-warning" onclick="abrirNuevaSancion()" id="btnNuevaSancion">
                <i class="fa-solid fa-plus me-2"></i>Nueva Sanción
            </button>
        </div>
    </div>

    <!-- Selector de Torneo -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-2">Seleccionar Torneo</label>
                    <select class="form-select" id="selectTorneo">
                        <option value="">Seleccione un torneo...</option>
                    </select>
                </div>
                <div class="col-md-6" id="configInfo" style="display: none;">
                    <div class="alert alert-warning mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Configuración:</strong>
                                T. Amarilla: <span id="configAmarilla">-</span> |
                                T. Roja: <span id="configRoja">-</span>
                            </div>
                            <button class="btn btn-sm btn-warning" onclick="abrirConfiguracion()">
                                <i class="fa-solid fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row g-4 mb-4" id="statsCards" style="display: none;">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Sanciones</p>
                            <h4 class="fw-bold mb-0" id="statTotal">0</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-list text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pendientes de Pago</p>
                            <h4 class="fw-bold mb-0 text-warning" id="statPendientes">0</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Recaudado</p>
                            <h4 class="fw-bold mb-0 text-success" id="statPagadas">0</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-money-bill-wave text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4" id="filtrosCard" style="display: none;">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Estado</label>
                    <select class="form-select form-select-sm" id="filtroEstado">
                        <option value="">Todos</option>
                        <option value="PENDIENTE">Pendiente</option>
                        <option value="PAGADO">Pagado</option>
                        <option value="ANULADO">Anulado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Tipo</label>
                    <select class="form-select form-select-sm" id="filtroTipo">
                        <option value="">Todos</option>
                        <option value="AMARILLA">Amarilla</option>
                        <option value="ROJA">Roja</option>
                        <option value="COMPORTAMIENTO">Comportamiento</option>
                        <option value="NO_PRESENTACION">No Presentación</option>
                        <option value="OTRA">Otra</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Equipo</label>
                    <select class="form-select form-select-sm" id="filtroEquipo">
                        <option value="">Todos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">&nbsp;</label>
                    <button class="btn btn-primary btn-sm w-100" onclick="aplicarFiltros()">
                        <i class="fa-solid fa-filter me-2"></i>Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Sanciones -->
    <div class="card border-0 shadow-sm" id="tablaCard" style="display: none;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tablaSanciones">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Concepto</th>
                            <th>Involucrado</th>
                            <th>Equipo</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mensaje Inicial -->
    <div class="card border-0 bg-light" id="mensajeInicial">
        <div class="card-body text-center py-5">
            <i class="fa-solid fa-triangle-exclamation text-warning fs-1 mb-3"></i>
            <h5 class="fw-bold mb-2">Módulo de Sanciones Económicas</h5>
            <p class="text-muted mb-0">Seleccione un torneo para gestionar las sanciones</p>
        </div>
    </div>
</div>

<!-- Modal de Configuración -->
<div class="modal fade" id="modalConfiguracion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-gear me-2"></i>Configuración de Sanciones
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formConfiguracion">
                    <input type="hidden" id="configIdTorneo">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto Tarjeta Amarilla</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="configMontoAmarilla"
                                placeholder="0" min="0" step="500" required>
                        </div>
                        <small class="text-muted">Monto por cada tarjeta amarilla registrada</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto Tarjeta Roja</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="configMontoRoja"
                                placeholder="0" min="0" step="500" required>
                        </div>
                        <small class="text-muted">Monto por cada tarjeta roja registrada</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Estas tarifas se aplicarán automáticamente al registrar tarjetas en los partidos.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarConfiguracion()">
                    <i class="fa-solid fa-save me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Sanción -->
<div class="modal fade" id="modalSancion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="tituloModalSancion">
                    <i class="fa-solid fa-plus me-2"></i>Nueva Sanción Manual
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formSancion">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Tipo de Sancion</label>
                            <select class="form-select" id="tipoSancion" required>
                                <option value="COMPORTAMIENTO">Comportamiento</option>
                                <option value="NO_PRESENTACION">No Presentación</option>
                                <option value="OTRA">Otra</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Fecha</label>
                            <input type="date" class="form-control" id="fechaSancion" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Equipo</label>
                            <select class="form-select" id="selectEquipoSancion" onchange="cargarJugadoresEquipo(this.value)">
                                <option value="">Seleccione equipo...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Jugador (Opcional)</label>
                            <select class="form-select" id="selectJugadorSancion">
                                <option value="">Seleccione jugador...</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Concepto</label>
                        <input type="text" class="form-control" id="conceptoSancion" placeholder="Ej: Daños en camerino" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="montoSancion" required min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Observaciones</label>
                        <textarea class="form-control" id="obsSancion" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="guardarSancion()">
                    <i class="fa-solid fa-save me-2"></i>Guardar Sanción
                </button>
            </div>
        </div>
    </div>
</div>



<?php require_once(__DIR__ . '/../template/footer.php'); ?>