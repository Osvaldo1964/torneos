<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $data['page_title'] ?> - Global Cup
    </title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/plugins/datatables/dataTables.bootstrap5.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 280px;
            background: #0f172a;
            color: white;
            padding: 30px;
            flex-shrink: 0;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .sidebar h2 span {
            color: #3b82f6;
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

        /* Switch iOS Style */
        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            cursor: pointer;
        }
    </style>
    <script>
        if (!localStorage.getItem('gc_token')) window.location.href = "login.php";
    </script>
</head>

<body>
    <div class="sidebar d-flex flex-column">
        <h2 class="mb-5 fw-bold">Global<span>Cup</span></h2>
        <nav class="nav flex-column mb-auto">
            <a href="dashboard.php" class="nav-link <?= $data['page_name'] == 'dashboard' ? 'active fw-bold' : '' ?>"><i
                    class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a href="roles.php" class="nav-link <?= $data['page_name'] == 'roles' ? 'active fw-bold' : '' ?>"><i
                    class="fa-solid fa-key me-2"></i> Roles</a>
            <a href="ligas.php" class="nav-link <?= $data['page_name'] == 'ligas' ? 'active fw-bold' : '' ?>"><i
                    class="fa-solid fa-building-columns me-2"></i> Ligas</a>
        </nav>
        <hr>
        <button class="btn btn-outline-danger btn-sm border-0 text-start" onclick="logout()"><i
                class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</button>
    </div>
    <div class="main-content">