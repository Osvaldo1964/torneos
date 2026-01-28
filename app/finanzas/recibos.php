<?php
$data = [
    'page_title' => 'Recibos de Ingreso',
    'page_name' => 'finanzas',
    'base_path' => '../',
    'page_js' => 'functions_recibos.js'
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
                    <li class="breadcrumb-item active">Recibos de Ingreso</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-file-invoice-dollar me-2 text-success"></i>Tesorería y Recibos</h3>
            <p class="text-muted mb-0">Gestión de cobros, emisión de recibos y control de caja</p>
        </div>
    </div>

    <!-- Selector de Torneo -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-2">Seleccionar Torneo</label>
                    <select class="form-select" id="selectTorneo">
                        <option value="">Seleccione un torneo para operar caja...</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div id="seccionCaja" style="display: none;">
        <!-- Tabs de Navegación -->
        <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 shadow-sm" id="pills-caja-tab" data-bs-toggle="pill" data-bs-target="#pills-caja" type="button" role="tab">
                    <i class="fa-solid fa-cash-register me-2"></i>Nueva Cobranza
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 shadow-sm" id="pills-historial-tab" data-bs-toggle="pill" data-bs-target="#pills-historial" type="button" role="tab" onclick="cargarHistorialRecibos()">
                    <i class="fa-solid fa-history me-2"></i>Historial de Recibos
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- PESTAÑA DE CAJA -->
            <div class="tab-pane fade show active" id="pills-caja" role="tabpanel">
                <div class="row g-4">
                    <!-- Columna Izquierda: Listado de Pendientes -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Deudas Pendientes</h5>
                                <div class="input-group input-group-sm w-50">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-search"></i></span>
                                    <input type="text" class="form-control bg-light border-0" id="searchPendientes" placeholder="Buscar por equipo o jugador...">
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0" id="tablaPendientesCaja">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th width="40" class="text-center">
                                                    <input type="checkbox" class="form-check-input" id="checkAllItems" onchange="toggleAllItems(this)">
                                                </th>
                                                <th>Concepto / Fecha</th>
                                                <th>Equipo / Jugador</th>
                                                <th class="text-end">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se carga dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="msgNoPendientes" class="text-center py-5" style="display: none;">
                                    <i class="fa-solid fa-check-circle text-success fs-1 mb-3"></i>
                                    <h5>¡No hay deudas pendientes!</h5>
                                    <p class="text-muted">Todos los pagos están al día para este torneo.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Detalle del Recibo -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                            <div class="card-header bg-success text-white py-3 border-0">
                                <h5 class="fw-bold mb-0"><i class="fa-solid fa-shopping-cart me-2"></i>Generar Recibo</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6 class="fw-bold small text-uppercase text-muted mb-3 border-bottom pb-2">Resumen de Cobro</h6>
                                    <div id="listaCheckout" class="mb-3">
                                        <p class="text-center text-muted small py-3">Seleccione deudas de la lista para cobrar</p>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-3">
                                        <h5 class="fw-bold">TOTAL A PAGAR:</h5>
                                        <h5 class="fw-bold text-success" id="totalCheckout">$ 0</h5>
                                    </div>
                                </div>

                                <form id="formRecibo">
                                    <h6 class="fw-bold small text-uppercase text-muted mb-3 border-bottom pb-2">Datos del Pago</h6>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Nombre del Pagador</label>
                                        <input type="text" class="form-control" id="recPagador" placeholder="Nombre de quien paga..." required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small fw-bold">Forma de Pago</label>
                                            <select class="form-select" id="recFormaPago" required>
                                                <option value="EFECTIVO">Efectivo</option>
                                                <option value="TRANSFERENCIA">Transferencia</option>
                                                <option value="TARJETA">Tarjeta</option>
                                                <option value="OTRO">Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small fw-bold">Referencia</label>
                                            <input type="text" class="form-control" id="recReferencia" placeholder="Opcional">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label small fw-bold">Observaciones</label>
                                        <textarea class="form-control" id="recObs" rows="2" placeholder="Nota adicional..."></textarea>
                                    </div>

                                    <button type="button" class="btn btn-success btn-lg w-100 shadow-sm" id="btnEmitirRecibo" onclick="procesarPago()" disabled>
                                        <i class="fa-solid fa-print me-2"></i>Emitir Recibo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA DE HISTORIAL -->
            <div class="tab-pane fade" id="pills-historial" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaHistorialRecibos">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th>Pagador</th>
                                        <th>Monto</th>
                                        <th>F. Pago</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje Inicial -->
    <div class="card border-0 bg-light" id="mensajeInicial">
        <div class="card-body text-center py-5">
            <i class="fa-solid fa-cash-register text-muted fs-1 mb-3"></i>
            <h5 class="fw-bold mb-2">Caja y Tesorería</h5>
            <p class="text-muted mb-0">Seleccione un torneo para cargar las deudas pendientes y gestionar cobros.</p>
        </div>
    </div>
</div>

<!-- Modal Detalle Recibo -->
<div class="modal fade" id="modalDetalleRecibo" tabindex="-1">
    <div class="modal-dialog modal-lg shadow-lg">
        <div class="modal-content border-0">
            <div class="modal-body p-0">
                <div id="printArea" class="p-5 bg-white">
                    <!-- Contenedor para el encabezado dinámico -->
                    <div id="headerReciboPrint"></div>

                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0 text-primary">RECIBO DE CAJA</h5>
                            <p class="text-muted mb-0 fw-bold small" id="detNumeroRecibo">#REC-00000</p>
                        </div>
                        <div class="text-end">
                            <p class="text-muted mb-0 small" id="detFecha">00/00/0000</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <p class="text-muted x-small text-uppercase fw-bold mb-0" style="font-size: 10px;">Recibido de:</p>
                            <h6 class="fw-bold" id="detPagador">-</h6>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted x-small text-uppercase fw-bold mb-0" style="font-size: 10px;">Total Pagado:</p>
                            <h4 class="fw-bold text-success" id="detTotal">$ 0</h4>
                        </div>
                    </div>

                    <div class="mb-4">
                        <table class="table table-bordered table-sm small">
                            <thead class="bg-light">
                                <tr>
                                    <th style="font-size: 11px;">Concepto</th>
                                    <th class="text-end" width="150" style="font-size: 11px;">Monto</th>
                                </tr>
                            </thead>
                            <tbody id="detListaConceptos"></tbody>
                        </table>
                    </div>

                    <div class="row align-items-end mt-4">
                        <div class="col-6">
                            <p class="mb-0 text-muted" style="font-size: 11px;">Forma de Pago: <span class="fw-bold text-dark" id="detFormaPago">-</span></p>
                            <p class="mb-0 text-muted" style="font-size: 11px;">Referencia: <span class="fw-bold text-dark" id="detReferencia">-</span></p>
                            <p class="mb-0 text-muted mt-2" style="font-size: 11px;">Registrado por: <span id="detUsuario">-</span></p>
                        </div>
                        <div class="col-6 text-center">
                            <div style="border-top: 1px solid #ddd;" class="pt-1 mt-4">
                                <p class="fw-bold mb-0" style="font-size: 11px;">Firma Autorizada / Sello</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill shadow" onclick="imprimirRecibo()">
                    <i class="fa-solid fa-print me-2"></i>Imprimir Recibo
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . '/../template/footer.php'); ?>