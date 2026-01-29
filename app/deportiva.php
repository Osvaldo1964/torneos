<?php
$data = [
    'page_title' => 'Gestión Deportiva',
    'page_name' => 'deportiva'
];
require_once('template/header.php');
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">⚽ Gestión Deportiva</h3>
            <p class="text-muted mb-0">Administración completa de ligas, torneos y competencias</p>
        </div>
    </div>

    <!-- Módulos del Sistema Deportivo -->
    <div class="row g-4">
        <div class="col-md-4" id="cardLigas">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-building-columns text-primary fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Ligas</h5>
                            <small class="text-muted">Organizaciones deportivas</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Gestione las ligas deportivas, configure parámetros y administre la
                        información institucional.</p>
                    <a href="ligas.php" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="cardTorneos">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-trophy text-warning fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Torneos</h5>
                            <small class="text-muted">Competencias y campeonatos</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Cree y administre torneos, configure fases, grupos y parámetros de
                        competencia.</p>
                    <a href="torneos.php" class="btn btn-outline-warning btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="cardEquipos">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-shield-halved text-success fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Equipos</h5>
                            <small class="text-muted">Clubes y delegaciones</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Registre equipos, gestione información institucional, logos y datos
                        de contacto.</p>
                    <a href="equipos.php" class="btn btn-outline-success btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="cardJugadores">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-user-graduate text-info fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Jugadores</h5>
                            <small class="text-muted">Deportistas registrados</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Administre la base de datos de jugadores, documentos, fotografías y
                        datos personales.</p>
                    <a href="jugadores.php" class="btn btn-outline-info btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="cardNominas">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-clipboard-list text-danger fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Nóminas</h5>
                            <small class="text-muted">Inscripciones por torneo</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Gestione las inscripciones de equipos y jugadores en cada torneo
                        activo.</p>
                    <a href="nominas.php" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="cardCalendario">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-secondary bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-calendar-days text-secondary fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Calendario</h5>
                            <small class="text-muted">Programación de partidos</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Programe fechas, horarios, sedes y administre el calendario
                        completo de partidos.</p>
                    <a href="calendario.php" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="cardPosiciones">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-dark bg-opacity-10 p-3 rounded me-3">
                            <i class="fa-solid fa-medal text-dark fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Posiciones</h5>
                            <small class="text-muted">Tablas y estadísticas</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Consulte tablas de posiciones, estadísticas de equipos y goleadores
                        del torneo.</p>
                    <a href="posiciones.php" class="btn btn-outline-dark btn-sm w-100">
                        <i class="fa-solid fa-arrow-right me-2"></i>Acceder
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const user = JSON.parse(localStorage.getItem('gc_user'));
            if (user) {
                // Rol 4: Jugador -> Solo ve Calendario y Posiciones
                if (user.id_rol == 4) {
                    if (document.getElementById('cardLigas')) document.getElementById('cardLigas').remove();
                    if (document.getElementById('cardTorneos')) document.getElementById('cardTorneos').remove();
                    if (document.getElementById('cardEquipos')) document.getElementById('cardEquipos').remove();
                    if (document.getElementById('cardJugadores')) document.getElementById('cardJugadores').remove();
                    if (document.getElementById('cardNominas')) document.getElementById('cardNominas').remove();
                }
                // Rol 3: Delegado -> No ve Ligas (y quizás Torneos solo lectura, pero dejémoslo)
                if (user.id_rol == 3) {
                    if (document.getElementById('cardLigas')) document.getElementById('cardLigas').remove();
                }
                // Rol 2: Liga Admin -> Ve todo menos Ligas (que se convierte en "Mi Liga" en el menu, pero aqui Ligas es para SuperAdmin)
                if (user.id_rol == 2) {
                    if (document.getElementById('cardLigas')) document.getElementById('cardLigas').remove();
                }
            }
        });
    </script>

    <!-- Información Adicional -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body text-center py-4">
                    <i class="fa-solid fa-futbol text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-2">Sistema Integral de Gestión Deportiva</h5>
                    <p class="text-muted mb-0">Administre todos los aspectos deportivos de su liga desde un solo lugar
                    </p>
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