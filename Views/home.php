<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Cup | Gestiona tu Pasión</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary: #0f172a;
            /* Azul Navy Profundo (Fondo) */
            --accent: #3b82f6;
            /* Azul Eléctrico (Acción) */
            --success: #10b981;
            /* Verde Esmeralda (Éxito/Campo) */
            --success-light: #d1fae5;
            /* Verde Menta (Suave) */
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: #020617;
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Premium Navigation */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 10%;
            position: absolute;
            width: 100%;
            z-index: 100;
        }

        .logo {
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -1px;
        }

        .logo span {
            color: var(--accent);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        /* Hero Section with Glassmorphism */
        .hero {
            height: 100vh;
            background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.15), transparent),
                linear-gradient(to bottom, #020617, #0f172a);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 10%;
        }

        .hero h1 {
            font-size: 4.5rem;
            line-height: 1;
            margin-bottom: 1.5rem;
            font-weight: 800;
        }

        .hero p {
            font-size: 1.2rem;
            color: #94a3b8;
            max-width: 600px;
            margin-bottom: 2.5rem;
        }

        .btn-main {
            background: var(--accent);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
            transition: 0.4s;
        }

        .btn-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(59, 130, 246, 0.4);
        }

        /* Multi-tenant Cards */
        .features {
            padding: 100px 10%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 20px;
            transition: 0.3s;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-5px);
        }

        .feature-card h3 {
            margin-bottom: 1rem;
            color: var(--accent);
        }

        /* Contact/PQR Floating Buttons */
        .floating-action {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .float-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
        }

        .btn-pqr {
            background: #ef4444;
        }

        .btn-contact {
            background: #22c55e;
        }
    </style>
</head>

<body>

    <nav>
        <div class="logo">GLOBAL<span>CUP</span></div>
        <ul class="nav-links">
            <li><a href="#eventos">Eventos</a></li>
            <li><a href="#funcionalidades">Funcionalidades</a></li>
            <li><a href="#finanzas">Gestión Financiera</a></li>
            <li><a href="<?= base_url() ?>login"
                    style="background: rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 20px;">Acceso Admin</a>
            </li>
        </ul>
    </nav>

    <section class="hero">
        <h1>Organizar es Ganar.</h1>
        <p>La plataforma integral para ligas de fútbol que automatiza tus finanzas, estadísticas y torneos en un solo
            lugar.</p>
        <div style="display: flex; gap: 1rem;">
            <a href="<?= base_url() ?>registro" class="btn-main" style="background: var(--success);">Registrar mi
                Liga</a>
            <a href="#funcionalidades" class="btn-main">Saber más</a>
        </div>
    </section>

    <section id="funcionalidades" class="features">
        <div class="feature-card">
            <h3>🔒 Ligas Independientes</h3>
            <p>Aislamiento total de datos. Tu liga, tus reglas, tus jugadores. Nadie más puede ver tu información.</p>
        </div>
        <div class="feature-card">
            <h3>💰 Control Financiero</h3>
            <p>Generación automática de facturas mensuales, cobro de multas por tarjetas y recibos digitales.</p>
        </div>
        <div class="feature-card">
            <h3>📊 Estadísticas Pro</h3>
            <p>Tablas de posiciones, goleadores y valla menos vencida en tiempo real.</p>
        </div>
    </section>

    <!-- Floating UI for PQR and Contact -->
    <div class="floating-action">
        <a href="<?= base_url() ?>pqr" class="float-btn btn-pqr" title="Enviar PQR">PQR</a>
        <a href="<?= base_url() ?>contacto" class="float-btn btn-contact" title="Contacto">C</a>
    </div>

</body>

</html>