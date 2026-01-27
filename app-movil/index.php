<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Global Cup Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f172a;
            color: white;
            margin-bottom: 70px;
        }

        .mobile-header {
            padding: 20px;
            border-bottom: 1px solid #1e293b;
            text-align: center;
        }

        .nav-bottom {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #1e293b;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            border-top: 1px solid #334155;
        }

        .nav-item {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.8rem;
            text-align: center;
        }

        .nav-item.active {
            color: #3b82f6;
        }
    </style>
</head>

<body>
    <div class="mobile-header">
        <h4 class="mb-0">GlobalCup Mobile</h4>
    </div>

    <div class="container mt-4">
        <div class="card bg-secondary text-white mb-3">
            <div class="card-body">
                <h6 class="card-title">Próximos Torneos</h6>
                <p class="card-text small">Cargando información...</p>
            </div>
        </div>
    </div>

    <div class="nav-bottom">
        <a href="#" class="nav-item active">Inicio</a>
        <a href="#" class="nav-item">Partidos</a>
        <a href="#" class="nav-item">Perfil</a>
    </div>
</body>

</html>