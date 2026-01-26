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
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --success: #10b981;
            --bg: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit';
        }

        body {
            background: var(--bg);
            display: flex;
            height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .team-banner {
            background: var(--primary);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .team-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 15px;
            padding: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: 2fr 1.2fr;
            gap: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .player-row {
            display: flex;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            gap: 15px;
        }

        .player-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .btn-fichar {
            background: var(--accent);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .btn-fichar:hover {
            background: #2563eb;
        }
    </style>
</head>

<body>
    <?php require_once("Views/Template/nav_admin.php"); ?>

    <div class="main-content">
        <div class="team-banner">
            <img class="team-logo" src="<?= base_url() ?>Assets/images/uploads/<?= $data['equipo']['escudo'] ?>"
                alt="Logo">
            <div>
                <p
                    style="color: var(--accent); font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;">
                    Gestión de Jugadores</p>
                <h1 style="font-size: 2.22rem;">
                    <?= $data['equipo']['nombre'] ?>
                </h1>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h3 style="margin-bottom: 20px;">🏃‍♂️ Plantilla Actual (Nómina)</h3>
                <div id="listaPlantilla">
                    <!-- Jugadores cargados por JS -->
                </div>
            </div>

            <div class="card">
                <h3 style="margin-bottom: 20px;">🔍 Fichar Jugadores</h3>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 15px;">Jugadores registrados en la liga
                    disponibles para inscribir:</p>
                <div id="busquedaJugadores" style="max-height: 500px; overflow-y: auto;">
                    <!-- Lista de jugadores de la liga -->
                </div>
            </div>
        </div>
    </div>

    <script>
        const idEquipo = <?= $data['equipo']['id_equipo'] ?>;

        async function loadPlantilla() {
            const response = await fetch('<?= base_url() ?>equipos/getPlantilla/' + idEquipo);
            const data = await response.json();
            const container = document.getElementById('listaPlantilla');
            container.innerHTML = data.length === 0 ? '<p style="color:#94a3b8; text-align:center; padding:20px;">No hay jugadores inscritos aún.</p>' : "";

            data.forEach(p => {
                container.innerHTML += `
                    <div class="player-row">
                        <img src="<?= base_url() ?>Assets/images/uploads/${p.foto}" class="player-img">
                        <div style="flex:1">
                            <p style="font-weight:600;">${p.nombres} ${p.apellidos}</p>
                            <p style="font-size:0.75rem; color:#64748b;">ID: ${p.identificacion} | ${p.posicion}</p>
                        </div>
                        <span style="color:var(--success); font-weight:700;">✓ Inscripto</span>
                    </div>
                `;
            });
        }

        async function loadBusqueda() {
            const response = await fetch('<?= base_url() ?>equipos/getJugadoresLiga');
            const data = await response.json();
            const container = document.getElementById('busquedaJugadores');
            container.innerHTML = "";

            data.forEach(p => {
                container.innerHTML += `
                    <div class="player-row">
                        <img src="<?= base_url() ?>Assets/images/uploads/${p.foto}" class="player-img">
                        <div style="flex:1">
                            <p style="font-weight:600; font-size:0.9rem;">${p.nombres}</p>
                            <p style="font-size:0.7rem; color:#64748b;">${p.identificacion}</p>
                        </div>
                        <button class="btn-fichar" onclick="fntFichar(${p.id_persona})">Fichar +</button>
                    </div>
                `;
            });
        }

        async function fntFichar(idPersona) {
            const formData = new FormData();
            formData.append('idEquipo', idEquipo);
            formData.append('idPersona', idPersona);

            const response = await fetch('<?= base_url() ?>equipos/setFichaje', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status) {
                loadPlantilla();
            } else {
                alert(result.msg);
            }
        }

        loadPlantilla();
        loadBusqueda();
    </script>
</body>

</html>