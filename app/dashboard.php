<?php
$data = [
    "page_title" => "Dashboard",
    "page_name" => "dashboard"
];
require_once("template/header.php");
?>

<div class="header-welcome mb-5">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fw-extrabold text-dark" style="font-size: 2.5rem;">Bienvenido, <span id="userName" class="text-primary text-capitalize">Usuario</span></h1>
            <p class="text-muted lead">Panel administrativo integral de Global Cup.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <h5 class="fw-bold mb-0" id="currentDate"><?= date('d M, Y') ?></h5>
            <p class="text-muted mb-0">Estado del Sistema: <span class="badge bg-success">En línea</span></p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Acceso a Gestión Deportiva -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card overflow-hidden">
            <div class="card-body p-0">
                <div class="p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fa-solid fa-futbol text-primary fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Gestión Deportiva</h4>
                            <p class="text-muted small mb-0 font-outfit">Ligas, Torneos y Competencias</p>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Administre toda la estructura de competencia, desde la inscripción de jugadores hasta las tablas de posiciones y calendarios.</p>
                </div>
                <div class="bg-light p-3 border-top mt-auto">
                    <a href="deportiva.php" class="btn btn-primary w-100 fw-bold">
                        Entrar al Módulo Deportivo <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Acceso a Finanzas -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card overflow-hidden">
            <div class="card-body p-0">
                <div class="p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fa-solid fa-money-bill-trend-up text-success fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Módulo Financiero</h4>
                            <p class="text-muted small mb-0 font-outfit">Ingresos, Egresos y Balance</p>
                        </div>
                    </div>
                    <p class="text-muted mb-4">Controle los movimientos económicos de su organización, cuotas mensuales, pagos de arbitraje y reportes detallados.</p>
                </div>
                <div class="bg-light p-3 border-top mt-auto">
                    <a href="finanzas.php" class="btn btn-success w-100 fw-bold">
                        Entrar al Módulo Financiero <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Resumen de Actividad -->
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 bg-primary text-white" style="border-radius: 20px;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-2">Resumen de Actividad</h3>
                    <p class="mb-0 opacity-75">Visualice los indicadores clave de su liga en tiempo real.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-light fw-bold" onclick="location.reload()">
                        <i class="fa-solid fa-rotate me-2"></i>Actualizar Datos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        transition: all 0.3s ease;
        border-radius: 20px !important;
    }

    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .font-outfit {
        font-family: 'Outfit', sans-serif;
    }
</style>

<script>
    const userLocal = JSON.parse(localStorage.getItem('gc_user'));
    if (userLocal) {
        document.getElementById('userName').innerText = userLocal.nombre;
    }
</script>

<?php require_once("template/footer.php"); ?>