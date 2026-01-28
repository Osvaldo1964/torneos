<?php
$data = [
    "page_title" => "Gastos Generales",
    "page_name" => "gastos",
    "page_js" => "functions_gastos.js",
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
                    <li class="breadcrumb-item active">Gastos Generales</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4 bg-dark text-white" style="border-radius: 20px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="fw-bold mb-1">💸 Gastos y Egresos</h2>
                        <p class="mb-0 opacity-75">Control de gastos operativos, alquileres y suministros del torneo.
                        </p>
                    </div>
                    <div>
                        <button class="btn btn-primary fw-bold" onclick="abrirModalGasto()">
                            <i class="fa-solid fa-plus me-2"></i>Registrar Gasto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted text-uppercase">Seleccionar Torneo</label>
                            <select class="form-select border-0 bg-light shadow-none" id="selectTorneo"
                                style="border-radius: 10px; height: 45px;">
                                <option value="">Seleccione un torneo...</option>
                            </select>
                        </div>
                        <div class="col-md-7 text-end">
                            <div id="resumenGastos" class="d-none">
                                <span class="text-muted small me-2">TOTAL GASTOS:</span>
                                <span class="h4 fw-bold text-danger mb-0" id="totalGastos">$ 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="seccionGastos" class="d-none">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaGastos">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Fecha</th>
                                <th>Concepto / Categoría</th>
                                <th>Beneficiario</th>
                                <th class="text-end">Monto</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
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

    <!-- Mensaje Inicial -->
    <div id="mensajeInicial" class="text-center py-5">
        <div class="opacity-25 mb-3">
            <i class="fa-solid fa-receipt fa-4x"></i>
        </div>
        <h5 class="text-muted">Seleccione un torneo para ver el historial de gastos</h5>
    </div>
</div>

<!-- Modal Registrar Gasto -->
<div class="modal fade" id="modalGasto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">💸 Registrar Nuevo Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formGasto">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Categoría</label>
                            <select class="form-select" name="tipo_gasto" required>
                                <option value="OTRO">Otro / General</option>
                                <option value="ESCENARIO">Alquiler de Escenario</option>
                                <option value="PREMIO">Premios y Trofeos</option>
                                <option value="MATERIAL">Material Deportivo</option>
                                <option value="ADMINISTRATIVO">Gastos Administrativos</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Fecha</label>
                            <input type="date" class="form-control" name="fecha_pago" value="<?= date('Y-m-d') ?>"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Concepto</label>
                        <input type="text" class="form-control" name="concepto"
                            placeholder="Ej: Pago arbitraje jornada 5" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Beneficiario</label>
                        <input type="text" class="form-control" name="beneficiario"
                            placeholder="Nombre de la persona o entidad" required>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control fw-bold text-danger" name="monto"
                                    placeholder="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Forma de Pago</label>
                            <select class="form-select" name="forma_pago">
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="CHEQUE">Cheque</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">N° Comprobante</label>
                            <input type="text" class="form-control" name="numero_comprobante" placeholder="Opcional">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="2"
                            placeholder="Notas adicionales..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-soft-danger {
        background: #f8d7da;
        color: #842029;
    }

    .bg-soft-success {
        background: #d1e7dd;
        color: #0f5132;
    }

    .bg-soft-info {
        background: #cff4fc;
        color: #055160;
    }
</style>

<?php require_once("../template/footer.php"); ?>