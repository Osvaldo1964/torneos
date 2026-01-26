<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $data['page_tag'] ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit';
        }

        body {
            background: #f1f5f9;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #0f172a;
            color: white;
            padding: 30px;
        }

        .main-content {
            flex: 1;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .card .value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
        }

        .liga-badge {
            background: #3b82f6;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <?php require_once("Views/Template/nav_admin.php"); ?>
    <div class="main-content">
        <div class="header">
            <div>
                <h1>Bienvenido,
                    <?= $_SESSION['userData']['nombres'] ?>
                </h1>
                <p style="color: #64748b;">Panel administrativo de tu liga.</p>
            </div>
            <div class="liga-badge">
                <?= $_SESSION['userData']['nombre_liga'] ?>
            </div>
        </div>

        <div class="card-grid">
            <div class="card">
                <h3>Torneos Activos</h3>
                <div class="value">0</div>
            </div>
            <div class="card">
                <h3>Total Jugadores</h3>
                <div class="value">0</div>
            </div>
            <div class="card">
                <h3>Recaudo Mensual</h3>
                <div class="value">$ 0.00</div>
            </div>
        </div>
    </div>
</body>

</html>