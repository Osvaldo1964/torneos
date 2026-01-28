<?php
$data = [
    'page_title' => 'Cuotas Mensuales',
    'page_name' => 'finanzas',
    'base_path' => '../',
    'page_js' => 'functions_cuotas.js'
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
                    <li class="breadcrumb-item active">Cuotas Mensuales</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-1">📅 Cuotas Mensuales</h3>
            <p class="text-muted mb-0">Gestión de cuotas mensuales por jugador</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="abrirConfiguracion()">
                <i class="fa-solid fa-gear me-2"></i>Configuración
            </button>
            <button class="btn btn-success" onclick="generarCuotas()" id="btnGenerarCuotas">
                <i class="fa-solid fa-magic me-2"></i>Generar Cuotas
            </button>
            <button class="btn btn-primary" onclick="marcarVencidas()" id="btnMarcarVencidas">
                <i class="fa-solid fa-clock me-2"></i>Marcar Vencidas
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
                    <div class="alert alert-info mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Configuración:</strong>
                                <span id="configMonto">-</span> |
                                Vence día <span id="configDia">-</span>
                            </div>
                            <button class="btn btn-sm btn-info" onclick="abrirConfiguracion()">
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
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Cuotas</p>
                            <h4 class="fw-bold mb-0" id="statTotal">0</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-list text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pendientes</p>
                            <h4 class="fw-bold mb-0 text-warning" id="statPendientes">0</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pagadas</p>
                            <h4 class="fw-bold mb-0 text-success" id="statPagadas">0</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Vencidas</p>
                            <h4 class="fw-bold mb-0 text-danger" id="statVencidas">0</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-exclamation-triangle text-danger fs-4"></i>
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
                        <option value="VENCIDO">Vencido</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Equipo</label>
                    <select class="form-select form-select-sm" id="filtroEquipo">
                        <option value="">Todos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Mes</label>
                    <select class="form-select form-select-sm" id="filtroMes">
                        <option value="">Todos</option>
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
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

    <!-- Tabla de Cuotas -->
    <div class="card border-0 shadow-sm" id="tablaCard" style="display: none;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tablaCuotas">
                    <thead>
                        <tr>
                            <th>Jugador</th>
                            <th>Equipo</th>
                            <th>Periodo</th>
                            <th>Monto</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Fecha Pago</th>
                            <th>Recibo</th>
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
            <i class="fa-solid fa-calendar-check text-primary fs-1 mb-3"></i>
            <h5 class="fw-bold mb-2">Módulo de Cuotas Mensuales</h5>
            <p class="text-muted mb-0">Seleccione un torneo para gestionar las cuotas mensuales</p>
        </div>
    </div>
</div>

<!-- Modal de Configuración -->
<div class="modal fade" id="modalConfiguracion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-gear me-2"></i>Configuración de Cuotas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formConfiguracion">
                    <input type="hidden" id="configIdTorneo">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto Mensual</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="configMontoMensual"
                                placeholder="0" min="0" step="1000" required>
                        </div>
                        <small class="text-muted">Monto que se cobrará mensualmente a cada jugador</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Día de Vencimiento</label>
                        <select class="form-select" id="configDiaVencimiento" required>
                            <option value="">Seleccione...</option>
                            <?php for ($i = 1; $i <= 28; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <small class="text-muted">Día del mes en que vence la cuota</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Esta configuración se aplicará a todas las cuotas generadas para este torneo.
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



<?php require_once(__DIR__ . '/../template/footer.php'); ?>