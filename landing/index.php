<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Cup - Torneos de Fútbol</title>
    <link href="../app/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: white;
        }

        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80');
            background-size: cover;
        }

        .hero h1 {
            font-size: 5rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .hero h1 span {
            color: #3b82f6;
        }

        .btn-primary {
            background-color: #3b82f6;
            border: none;
            padding: 15px 40px;
            font-weight: 600;
            font-size: 1.2rem;
            border-radius: 50px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: scale(1.05);
        }

        .btn-outline-light {
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
            margin-left: 15px;
        }
    </style>
</head>

<body>
    <div class="hero">
        <div class="container">
            <h1>Global<span>Cup</span></h1>
            <p class="lead mb-5">La plataforma definitiva para la gestión de ligas y torneos de fútbol.</p>
            <div class="d-flex justify-content-center">
                <a href="../app/login.php" class="btn btn-primary">Acceso Sistema</a>
                <a href="../app-movil/" class="btn btn-outline-light">Versión Móvil</a>
            </div>
        </div>
    </div>
</body>

</html>