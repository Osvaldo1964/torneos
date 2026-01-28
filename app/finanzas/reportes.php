<?php
$data = [
    "page_title" => "Reportes y Balances",
    "page_name" => "reportes",
    "page_js" => "functions_reportes.js",
    "base_path" => "../"
];
require_once("../template/header.php");
?>

<!-- Biblioteca Chart.js para gráficas -->
<script src="<?= $base_path ?>assets/js/chart.min.js"></script>

<div class="container-fluid">
    <!-- Encabezado de Impresión (Solo PDF) -->
    <div id="headerReporte" class="d-none print-only-header">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h1 class="fw-bold m-0" style="color: #0f172a; font-size: 28px;">Global<span
                        style="color: #3b82f6;">Cup</span></h1>
                <p class="text-muted mb-0 small">Plataforma de Gestión Deportiva Profesional</p>
            </div>
            <div class="text-end">
                <h3 class="fw-bold mb-0 text-uppercase" id="printNombreTorneo">REPORTE FINANCIERO</h3>
                <p class="mb-1 text-primary fw-bold" id="printPeriodo">Análisis General</p>
                <small class="text-muted">Generado el: <?= date('d/m/Y H:i') ?></small>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../finanzas.php" class="text-decoration-none">Finanzas</a></li>
                    <li class="breadcrumb-item active">Reportes y Balances</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filtros Superiores -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4 bg-primary text-white" style="border-radius: 20px;">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="fw-bold mb-1">📊 Inteligencia Financiera</h2>
                        <p class="mb-0 opacity-75">Análisis de rendimiento, utilidades y proyecciones del torneo.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="small opacity-75 d-block mb-1">Periodo Inicial</label>
                                <input type="date" id="fechaInicio"
                                    class="form-control form-control-sm border-0 bg-white bg-opacity-25 text-white"
                                    style="color-scheme: dark;">
                            </div>
                            <div class="col-md-5">
                                <label class="small opacity-75 d-block mb-1">Periodo Final</label>
                                <input type="date" id="fechaFin"
                                    class="form-control form-control-sm border-0 bg-white bg-opacity-25 text-white"
                                    style="color-scheme: dark;">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-warning btn-sm w-100 fw-bold" onclick="cargarReportes()">
                                    <i class="fa-solid fa-sync"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selección de Torneo -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body">
                    <label class="form-label small fw-bold text-muted text-uppercase">Seleccionar Torneo</label>
                    <select class="form-select border-0 bg-light shadow-none" id="selectTorneo"
                        style="border-radius: 10px; height: 45px;">
                        <option value="">Seleccione un torneo para analizar...</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                <button class="btn btn-white bg-white border-0 px-3" onclick="exportar('pdf')">
                    <i class="fa-solid fa-file-pdf text-danger me-2"></i>PDF
                </button>
                <button class="btn btn-white bg-white border-0 px-3" onclick="exportar('excel')">
                    <i class="fa-solid fa-file-excel text-success me-2"></i>Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Indicadores Clave (KPIs) -->
    <div id="kpiContainer" class="row g-4 mb-4 d-none">
        <div class="col-md-3 col-6 print-col-3">
            <div class="card border-0 shadow-sm hover-card" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fa-solid fa-arrow-trend-up text-success fs-3"></i>
                    </div>
                    <p class="text-muted small mb-1 fw-bold">TOTAL INGRESOS</p>
                    <h3 class="fw-bold mb-0 text-success" id="kpiIngresos">$ 0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 print-col-3">
            <div class="card border-0 shadow-sm hover-card" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fa-solid fa-arrow-trend-down text-danger fs-3"></i>
                    </div>
                    <p class="text-muted small mb-1 fw-bold">TOTAL EGRESOS</p>
                    <h3 class="fw-bold mb-0 text-danger" id="kpiEgresos">$ 0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 print-col-3">
            <div class="card border-0 shadow-sm hover-card" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="fa-solid fa-wallet text-primary fs-3"></i>
                    </div>
                    <p class="text-muted small mb-1 fw-bold">BALANCE FINAL</p>
                    <h3 class="fw-bold mb-0 text-primary" id="kpiBalance">$ 0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 print-col-3">
            <div class="card border-0 shadow-sm hover-card" style="border-radius: 15px;">
                <div class="card-body text-center p-4" id="kpiResultadoCard">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block mb-3" id="kpiIconBg">
                        <i class="fa-solid fa-chart-line text-info fs-3" id="kpiIcon"></i>
                    </div>
                    <p class="text-muted small mb-1 fw-bold">ESTADO FINANCIERO</p>
                    <h3 class="fw-bold mb-0" id="kpiEstado">-</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas Principales -->
    <div id="chartContainer" class="row g-4 d-none">
        <!-- Título de Sección para Impresión -->
        <div class="col-12 d-none print-only-header">
            <h5 class="fw-bold text-muted border-bottom pb-2 mb-3">01. Análisis de Tendencias y Distribución</h5>
        </div>
        <!-- Evolución Mensual -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between">
                    <h5 class="fw-bold mb-0">📈 Evolución Mensual</h5>
                    <div class="badge bg-light text-dark border p-2">Flujo de Caja
                        <?= date('Y') ?>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="chartEvolucion" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribución de Egresos -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">🍕 Distribución de Egresos</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <canvas id="chartEgresos" style="max-height: 300px;"></canvas>
                    <div id="egresosEmpty" class="d-none py-5">
                        <i class="fa-solid fa-circle-exclamation text-muted opacity-25 fa-3x mb-3"></i>
                        <p class="text-muted small">Sin datos de egresos para este periodo</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle de Ingresos -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">💰 Composición de Ingresos</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">Cuotas Jugadores</span>
                            <span class="small text-muted" id="pctCuotas">0%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div id="barCuotas" class="progress-bar bg-success" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">Sanciones Económicas</span>
                            <span class="small text-muted" id="pctSanciones">0%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div id="barSanciones" class="progress-bar bg-warning" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">Otros Ingresos</span>
                            <span class="small text-muted" id="pctOtros">0%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div id="barOtros" class="progress-bar bg-info" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Resumen por Mes -->
        <div class="col-md-8">
            <!-- Título de Sección para Impresión -->
            <div class="d-none print-only-header mt-4">
                <h5 class="fw-bold text-muted border-bottom pb-2 mb-3">02. Consolidado de Movimientos por Mes</h5>
            </div>
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">📅 Tabla de Resumen Mensual</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaMensual">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Mes</th>
                                    <th class="text-end">Ingresos</th>
                                    <th class="text-end">Egresos</th>
                                    <th class="text-end">Balance</th>
                                    <th class="text-center">Tendencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje Inicial -->
    <div id="mensajeInicial" class="text-center py-5">
        <div class="opacity-25 mb-4">
            <i class="fa-solid fa-chart-pie fa-5x"></i>
        </div>
        <h4 class="text-muted fw-bold">Panel de Control de Activos</h4>
        <p class="text-muted">Seleccione un torneo para procesar las métricas de rendimiento financiero.</p>
    </div>
</div>

<style>
    .chart-container {
        position: relative;
        margin: auto;
    }

    /* Estilos Estructurales para Impresión */
    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }

        .print-only-header {
            display: block !important;
        }

        .sidebar,
        .topbar,
        nav[aria-label="breadcrumb"],
        .card.bg-primary,
        .btn-group,
        #selectTorneo,
        .card-header .badge,
        #mensajeInicial,
        .form-label,
        .navbar {
            display: none !important;
        }

        .main-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
            display: block !important;
        }

        .main-content {
            padding: 0 !important;
            overflow: visible !important;
        }

        body {
            background-color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Grid de KPIs Horizontal en Impresión */
        #kpiContainer {
            display: flex !important;
            flex-wrap: nowrap !important;
            margin-bottom: 40px !important;
        }

        .print-col-3 {
            width: 25% !important;
            flex: 0 0 25% !important;
            max-width: 25% !important;
        }

        /* Layout de Gráficas en 2 Columnas para Impresión */
        #chartContainer {
            display: flex !important;
            flex-wrap: wrap !important;
        }

        .col-md-8,
        .col-md-4 {
            width: 50% !important;
            flex: 0 0 50% !important;
            max-width: 50% !important;
        }

        /* La tabla debe ocupar el 100% */
        #tablaMensual.parent,
        .col-md-8:has(#tablaMensual) {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }

        .card {
            border: 1px solid #e2e8f0 !important;
            padding: 5px !important;
            margin-bottom: 15px !important;
            break-inside: avoid;
        }

        .bg-opacity-10 {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }

        .text-success {
            color: #16a34a !important;
        }

        .text-danger {
            color: #dc2626 !important;
        }

        .text-primary {
            color: #2563eb !important;
        }

        canvas {
            max-width: 100% !important;
        }

        /* Quitar la generación de título anterior */
        .container-fluid::before {
            display: none !important;
        }

        /* Forzar visibilidad de iconos FontAwesome */
        .fa-solid {
            display: inline-block !important;
        }

        /* Ajuste de fuentes para evitar desbordes */
        .card h3 {
            font-size: 1.2rem !important;
        }

        .card p {
            font-size: 0.8rem !important;
        }

        .card i.fs-3 {
            font-size: 1.5rem !important;
        }
    }
</style>

<?php require_once("../template/footer.php"); ?>