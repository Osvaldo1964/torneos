<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $data['page_title'] ?> - Global Cup
    </title>
    <?php
    // Detectar nivel de profundidad para ajustar rutas
    $base_path = isset($data['base_path']) ? $data['base_path'] : '';
    ?>
    <link href="<?= $base_path ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_path ?>assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_path ?>assets/plugins/datatables/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --sidebar-width: 280px;
            --topbar-height: 70px;
            --primary: #0f172a;
            --accent: #3b82f6;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            overflow: hidden;
        }

        /* Layout Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary);
            color: white;
            padding: 30px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: 0.3s;
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .topbar {
            height: var(--topbar-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .sidebar h2 span {
            color: var(--accent);
        }

        .nav-link {
            color: #94a3b8;
            padding: 12px 0;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Icon Styles */
        .top-icon {
            font-size: 1.2rem;
            color: #64748b;
            margin-left: 20px;
            cursor: pointer;
            transition: 0.2s;
            position: relative;
        }

        .top-icon:hover {
            color: var(--accent);
        }

        .badge-notify {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 50px;
            border: 2px solid white;
        }

        /* Switch iOS Style */
        .form-check-input:checked {
            background-color: var(--accent);
            border-color: var(--accent);
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            cursor: pointer;
        }
    </style>
    <script>
        if (!localStorage.getItem('gc_token')) window.location.href = "<?= $base_path ?>login.php";
    </script>
</head>

<body>
    <div class="sidebar d-flex flex-column">
        <h2 class="mb-5 fw-bold text-nowrap">Global<span>Cup</span></h2>
        <nav class="nav flex-column mb-auto">
            <a href="<?= $base_path ?>dashboard.php" class="nav-link <?= $data['page_name'] == 'dashboard' ? 'active fw-bold' : '' ?>"><i
                    class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a href="<?= $base_path ?>roles.php" id="menuRoles"
                class="nav-link <?= $data['page_name'] == 'roles' ? 'active fw-bold' : '' ?>"><i
                    class="fa-solid fa-key me-2"></i> Roles</a>
            <a href="<?= $base_path ?>usuarios.php" id="menuUsuarios"
                class="nav-link <?= $data['page_name'] == 'usuarios' ? 'active fw-bold' : '' ?>"><i
                    class="fa-solid fa-users me-2"></i> Usuarios</a>
            <a href="<?= $base_path ?>deportiva.php"
                class="nav-link <?= $data['page_name'] == 'deportiva' ? 'active fw-bold' : '' ?>"><i
                    class="fa-solid fa-futbol me-2"></i> Gestión Deportiva</a>
            <a href="<?= $base_path ?>finanzas.php"
                class="nav-link <?= $data['page_name'] == 'finanzas' ? 'active fw-bold' : '' ?>"><i
                    class="fa-solid fa-money-bill-trend-up me-2"></i> Finanzas</a>
        </nav>
        <hr>
        <button class="btn btn-outline-danger btn-sm border-0 text-start" onclick="logout()"><i
                class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</button>
    </div>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-bars-staggered me-4 text-muted fs-5 cursor-pointer"></i>
                <h5 class="m-0 fw-bold text-dark fs-6">Panel de Administración</h5>
            </div>

            <div class="d-flex align-items-center">
                <div class="top-icon">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-notify">3</span>
                </div>
                <a href="<?= $base_path ?>../landing/index.php" class="top-icon text-decoration-none">
                    <i class="fa-solid fa-earth-americas"></i>
                </a>
                <div class="top-icon">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <div class="ms-4 ps-4 border-start d-flex align-items-center">
                    <div class="text-end me-3 d-none d-md-block">
                        <div class="fw-bold small text-dark" id="headerUserName">Admin</div>
                        <div class="text-muted" style="font-size: 11px;" id="headerUserRole">Super Admin</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0f172a&color=fff" class="rounded-circle"
                        style="width: 35px; height: 35px;" id="headerUserAvatar">
                </div>
            </div>
        </header>

        <main class="main-content">
            <script>
                // Base path para rutas dinámicas
                const BASE_PATH = '<?= $base_path ?>';

                document.addEventListener('DOMContentLoaded', () => {
                    const user = JSON.parse(localStorage.getItem('gc_user'));
                    if (user) {
                        if (document.getElementById('headerUserName')) document.getElementById('headerUserName').innerText =
                            user.nombre;

                        const roles = {
                            1: 'Super Admin',
                            2: 'Liga Admin',
                            3: 'Delegado',
                            4: 'Jugador'
                        };
                        if (document.getElementById('headerUserRole')) document.getElementById('headerUserRole').innerText =
                            roles[user.id_rol] || 'Usuario';

                        // Restringir módulo de Roles solo a Super Admin (1)
                        if (user.id_rol != 1 && document.getElementById('menuRoles')) {
                            document.getElementById('menuRoles').remove();
                        }

                        // Configuración del menú de Ligas
                        const menuLigas = document.getElementById('menuLigas');
                        if (menuLigas) {
                            if (user.id_rol == 1) {
                                // Super Admin: Ve todas
                                menuLigas.innerHTML = '<i class="fa-solid fa-building-columns me-2"></i> Ligas';
                            } else if (user.id_rol == 2) {
                                // Liga Admin: Solo ve la suya
                                menuLigas.innerHTML = '<i class="fa-solid fa-gear me-2"></i> Mi Liga';
                            } else {
                                // Otros: No ven configuración de liga
                                menuLigas.remove();
                            }
                        }

                        const avatarImg = document.getElementById('headerUserAvatar');
                        if (avatarImg) {
                            if (user.id_rol != 1 && user.liga_logo && user.liga_logo != "default_logo.png") {
                                avatarImg.src = `${BASE_PATH}assets/images/logos/${user.liga_logo}`;
                                avatarImg.classList.add('border', 'p-1', 'bg-light');
                            } else {
                                avatarImg.src = `https://ui-avatars.com/api/?name=${user.nombre}&background=0f172a&color=fff`;
                            }
                        }
                    }
                });
            </script>