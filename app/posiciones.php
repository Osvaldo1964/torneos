<?php
$data = [
    "page_title" => "Tabla de Posiciones",
    "page_name" => "posiciones",
    "page_js" => "functions_posiciones.js"
];
require_once "template/header.php";
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-medal"></i> Tabla de Posiciones</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Posiciones</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Filtros de Selección -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="selectTorneoPosiciones">
                                        <i class="fas fa-trophy"></i> Torneo
                                    </label>
                                    <select class="form-control" id="selectTorneoPosiciones" onchange="cargarFasesPosiciones()">
                                        <option value="">-- Seleccione Torneo --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="selectFasePosiciones">
                                        <i class="fas fa-layer-group"></i> Fase
                                    </label>
                                    <select class="form-control" id="selectFasePosiciones" onchange="cargarGruposPosiciones()">
                                        <option value="">-- Seleccione Fase --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="selectGrupoPosiciones">
                                        <i class="fas fa-users"></i> Grupo
                                    </label>
                                    <select class="form-control" id="selectGrupoPosiciones">
                                        <option value="">-- Seleccione Grupo --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button class="btn btn-primary btn-block" onclick="cargarTablaPosiciones()">
                                        <i class="fas fa-search"></i> Consultar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Grupo Seleccionado -->
        <div class="row">
            <div class="col-12">
                <div id="infoGrupoSeleccionado"></div>
            </div>
        </div>

        <!-- Tabla de Posiciones -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table"></i> Clasificación
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-outline-danger" onclick="exportarPDF()" title="Exportar a PDF">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="exportarExcel()" title="Exportar a Excel">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="bg-gradient-primary">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>Equipo</th>
                                        <th class="text-center" title="Partidos Jugados">PJ</th>
                                        <th class="text-center" title="Partidos Ganados">PG</th>
                                        <th class="text-center" title="Partidos Empatados">PE</th>
                                        <th class="text-center" title="Partidos Perdidos">PP</th>
                                        <th class="text-center" title="Goles a Favor">GF</th>
                                        <th class="text-center" title="Goles en Contra">GC</th>
                                        <th class="text-center" title="Diferencia de Goles">DG</th>
                                        <th class="text-center" title="Ver Racha">Racha</th>
                                        <th class="text-center" title="Puntos">PTS</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaPosicionesBody">
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-5">
                                            <i class="fas fa-info-circle fa-3x mb-3 d-block"></i>
                                            Seleccione un grupo para ver la tabla de posiciones
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="fas fa-trophy text-warning"></i>
                                    <strong>Criterios de desempate:</strong>
                                </small>
                            </div>
                            <div class="col-md-8">
                                <small class="text-muted">
                                    1° Puntos | 2° Diferencia de Goles | 3° Goles a Favor
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Goleadores -->
            <div class="col-lg-4">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-futbol"></i> Goleadores
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px;">
                            <table class="table table-hover mb-0">
                                <thead class="bg-gradient-warning sticky-top">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>Jugador</th>
                                        <th class="text-center">Goles</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaGoleadoresBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="fas fa-futbol fa-2x mb-2 d-block"></i>
                                            No hay datos disponibles
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Leyenda -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Leyenda
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <span class="badge bg-success">1°</span>
                                <small class="ms-2">Primer Lugar</small>
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-info">2°</span>
                                <small class="ms-2">Segundo Lugar</small>
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-warning">3°</span>
                                <small class="ms-2">Tercer Lugar</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-chart-line text-primary"></i>
                                <small class="ms-2">Ver racha del equipo</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Adicionales (Opcional - Para futuras mejoras) -->
        <div class="row">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner text-center">
                        <h3 id="totalPartidosJugados">-</h3>
                        <p>Partidos Jugados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-futbol"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner text-center">
                        <h3 id="totalGoles">-</h3>
                        <p>Goles Totales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner text-center">
                        <h3 id="promedioGoles">-</h3>
                        <p>Promedio Goles/Partido</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner text-center">
                        <h3 id="equipoLider">-</h3>
                        <p>Equipo Líder</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php
require_once "template/footer.php";
?>