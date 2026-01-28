<?php
$data = [
    'page_title' => 'Finanzas',
    'page_name' => 'finanzas',
    'page_js' => 'functions_finanzas.js'
];
require_once('template/header.php');
?>

<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Finanzas</li>
        </ol>
    </nav>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">💰 Módulo Financiero</h3>
            <p class="text-muted mb-0">Gestión integral de ingresos y egresos del torneo</p>
        </div>
        <div>
            <select class="form-select" id="selectTorneo" style="min-width: 250px;">
                <option value="">Seleccione un torneo...</option>
            </select>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row g-4 mb-4" id="statsCards" style="display: none;">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Ingresos</p>
                            <h4 class="fw-bold mb-0" id="totalIngresos">$0</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-arrow-trend-up text-success fs-4"></i>
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
                            <p class="text-muted mb-1 small">Total Egresos</p>
                            <h4 class="fw-bold mb-0" id="totalEgresos">$0</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-arrow-trend-down text-danger fs-4"></i>
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
                            <p class="text-muted mb-1 small">Balance</p>
                            <h4 class="fw-bold mb-0" id="balance">$0</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-scale-balanced text-primary fs-4"></i>
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
                            <p class="text-muted mb-1 small">Estado</p>
                            <h5 class="fw-bold mb-0" id="estadoBalance">-</h5>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fa-solid fa-chart-pie text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Módulos del Sistema Financiero -->
    <div class="row g-4" id="modulosFinancieros">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-calendar-check text-primary fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Cuotas Mensuales</h5>
                            <small class="text-muted">Gestión de cuotas por jugador</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Configure y administre las cuotas mensuales de los jugadores
                        inscritos en el torneo.</p>
                    <a href="finanzas/cuotas.php" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-triangle-exclamation text-warning fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Sanciones</h5>
                            <small class="text-muted">Multas y sanciones económicas</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Registre y gestione sanciones económicas por tarjetas y otras
                        infracciones.</p>
                    <a href="finanzas/sanciones.php" class="btn btn-outline-warning btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-receipt text-success fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Recibos de Ingreso</h5>
                            <small class="text-muted">Registro de pagos</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Genere recibos numerados por todos los ingresos del torneo.</p>
                    <a href="finanzas/recibos.php" class="btn btn-outline-success btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-gavel text-info fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Árbitros</h5>
                            <small class="text-muted">Pagos a árbitros</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Administre el catálogo de árbitros y sus pagos por partido.</p>
                    <a href="finanzas/arbitros.php" class="btn btn-outline-info btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-money-bill-transfer text-danger fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Gastos</h5>
                            <small class="text-muted">Egresos del torneo</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Registre todos los gastos generales del torneo con comprobantes.
                    </p>
                    <a href="finanzas/gastos.php" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-secondary bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-chart-line text-secondary fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Reportes</h5>
                            <small class="text-muted">Balance y análisis</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Visualice reportes financieros, balances y gráficas del torneo.</p>
                    <a href="finanzas/reportes.php" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de Estado Inicial -->
    <div class="row mt-5" id="mensajeInicial">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-chart-pie text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-2">Resumen Financiero</h5>
                    <p class="text-muted mb-0">Seleccione un torneo del listado superior para visualizar el balance de
                        ingresos y egresos.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
    }
</style>



<?php require_once('template/footer.php'); ?>