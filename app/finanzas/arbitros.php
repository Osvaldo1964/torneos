<?php
$data = [
    "page_title" => "Gestión de Árbitros",
    "page_name" => "arbitros",
    "page_js" => "functions_arbitros.js",
    "base_path" => "../"
];
require_once("../template/header.php");
?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../finanzas.php" class="text-decoration-none">Finanzas</a></li>
                    <li class="breadcrumb-item active">Gestión de Árbitros</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4 bg-primary text-white" style="border-radius: 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="fw-bold mb-1">👨‍⚖️ Módulo de Árbitros</h2>
                        <p class="mb-0 opacity-75">Configuración de honorarios y gestión de pagos por partido.</p>
                    </div>
                    <div>
                        <button class="btn btn-light fw-bold" onclick="abrirModalArbitro()">
                            <i class="fa-solid fa-plus me-2"></i>Nuevo Árbitro
                        </button>
                        <button class="btn btn-warning fw-bold ms-2" onclick="abrirModalConfig()">
                            <i class="fa-solid fa-gear me-2"></i>Configurar Tarifas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Selector de Torneo -->
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Seleccionar Torneo</label>
                        <select class="form-select border-0 bg-light fw-bold" id="selectTorneo"
                            style="height: 50px; border-radius: 12px;">
                            <option value="">Elija un torneo...</option>
                        </select>
                    </div>
                    <div class="col-md-8 text-end">
                        <div id="rolesBadges" class="d-flex justify-content-end gap-2 overflow-auto py-1">
                            <!-- Dinámico: Badges de roles y sus montos -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mensajeInicial" class="text-center py-5">
        <div class="mb-4 opacity-25">
            <i class="fa-solid fa-user-tie fa-6x text-muted"></i>
        </div>
        <h4 class="text-muted">Seleccione un torneo para ver los honorarios</h4>
        <p class="text-muted small">Podrá gestionar los cargos, árbitros y registrar pagos realizados.</p>
    </div>

    <div id="seccionArbitros" class="d-none">
        <ul class="nav nav-pills nav-custom mb-4" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="pills-pendientes-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-pendientes" type="button" role="tab">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i> Pagos Pendientes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="pills-historial-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-historial" type="button" role="tab" onclick="cargarHistorialPagos()">
                    <i class="fa-solid fa-file-invoice-dollar me-2"></i> Historial de Pagos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="pills-lista-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-lista" type="button" role="tab" onclick="cargarListaArbitros()">
                    <i class="fa-solid fa-address-book me-2"></i> Lista de Árbitros
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- Pagos Pendientes -->
            <div class="tab-pane fade show active" id="pills-pendientes" role="tabpanel">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaPagosPendientes">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Partido/Encuentro</th>
                                    <th>Fecha</th>
                                    <th>Árbitro / Rol</th>
                                    <th class="text-end">Monto</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data se carga vía JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Historial de Pagos -->
            <div class="tab-pane fade" id="pills-historial" role="tabpanel">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaHistorial">
                            <thead class="bg-light">
                                <tr>
                                    <th>Fecha Pago</th>
                                    <th>Partido</th>
                                    <th>Árbitro</th>
                                    <th class="text-end">Monto</th>
                                    <th>Forma Pago</th>
                                    <th>Comprobante</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Lista de Árbitros -->
            <div class="tab-pane fade" id="pills-lista" role="tabpanel">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaArbitros">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Identificación</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Configuración -->
    <div class="modal fade" id="modalConfig" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">⚙️ Configurar Cargos y Tarifas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert bg-soft-info text-info border-0 small mb-4">
                        <i class="fa-solid fa-circle-info me-2"></i> Defina los roles (Ej: Central, Línea, Veedor) y la
                        tarifa que recibirá cada uno por partido en este torneo.
                    </div>

                    <form id="formRol" class="row g-2 mb-4 align-items-end">
                        <input type="hidden" id="id_rol_edit" value="0">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">Nombre del Cargo</label>
                            <input type="text" id="rol_nombre" class="form-control border-0 bg-light"
                                placeholder="Ej: Árbitro Central" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Tarifa ($)</label>
                            <input type="number" id="rol_monto" class="form-control border-0 bg-light"
                                placeholder="0.00" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="fa-solid fa-plus me-1"></i> Guardar
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle" id="tablaRoles">
                            <thead class="bg-light">
                                <tr>
                                    <th>Cargo / Papel</th>
                                    <th class="text-end">Tarifa</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="bodyRoles">
                                <!-- Dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo/Editar Árbitro -->
    <div class="modal fade" id="modalArbitro" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title fw-bold" id="tituloModalArbitro">👤 Nuevo Árbitro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <form id="formArbitro">
                        <input type="hidden" id="id_arbitro">
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold">Nombre Completo</label>
                            <input type="text" id="nombre_completo" class="form-control border-0 bg-light" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold">Identificación</label>
                            <input type="text" id="identificacion" class="form-control border-0 bg-light">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Teléfono</label>
                                <input type="text" id="telefono" class="form-control border-0 bg-light">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Email</label>
                                <input type="email" id="email" class="form-control border-0 bg-light">
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary fw-bold p-3 rounded-3">Guardar Árbitro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Registrar Pago -->
    <div class="modal fade" id="modalRegistrarPago" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title fw-bold">💸 Registrar Pago de Honorarios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="alert bg-soft-primary text-primary border-0 mb-4" id="infoPago">
                        <!-- Dinámico -->
                    </div>
                    <form id="formRegistrarPago">
                        <input type="hidden" id="payIdPago">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Fecha de Pago</label>
                                <input type="date" id="payFecha" class="form-control border-0 bg-light" required
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">Forma de Pago</label>
                                <select id="payForma" class="form-select border-0 bg-light" required>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TRANSFERENCIA">Transferencia</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold">Nro. Comprobante / Ref</label>
                            <input type="text" id="payRef" class="form-control border-0 bg-light"
                                placeholder="Ej: Transf #12345">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold">Observaciones</label>
                            <textarea id="payObs" class="form-control border-0 bg-light" rows="2"></textarea>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-success fw-bold p-3 rounded-3">Confirmar Pago</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-custom .nav-link {
            color: #64748b;
            background: transparent;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .nav-custom .nav-link.active {
            background: #0f172a !important;
            color: white !important;
        }

        .bg-soft-success {
            background: #d1e7dd;
        }

        .bg-soft-primary {
            background: #cfe2ff;
        }

        .bg-soft-info {
            background: #cff4fc;
        }
    </style>

    </div><!-- End container-fluid -->
    <?php require_once("../template/footer.php"); ?>