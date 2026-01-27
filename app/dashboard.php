<?php 
    $data = [
        "page_title" => "Dashboard",
        "page_name" => "dashboard"
    ];
    require_once("template/header.php");
?>

<div class="header-welcome mb-5">
    <h1 class="fw-extrabold text-dark" style="font-size: 2.5rem;">Bienvenido, <span id="userName" class="text-primary text-capitalize">Admin</span></h1>
    <p class="text-muted lead">Panel administrativo de tu liga de fútbol.</p>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 20px;">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                    <i class="fa-solid fa-trophy text-primary fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0 fw-bold">Torneos Activos</p>
                    <h2 class="fw-bold mb-0">0</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const user = JSON.parse(localStorage.getItem('gc_user'));
    if(user) {
        document.getElementById('userName').innerText = user.nombre;
    }
</script>

<?php require_once("template/footer.php"); ?>