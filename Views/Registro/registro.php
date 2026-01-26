<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $data['page_tag'] ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --success: #10b981;
        }

        body {
            background: #020617;
            color: white;
            font-family: 'Outfit';
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .reg-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 40px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 600px;
            backdrop-filter: blur(10px);
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--accent);
            text-align: center;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.3);
            color: white;
            outline: none;
        }

        input:focus {
            border-color: var(--accent);
        }

        .btn-reg {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: none;
            background: var(--accent);
            color: white;
            font-weight: 800;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn-reg:hover {
            transform: scale(1.02);
            background: #2563eb;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="reg-card">
        <h1>Crea tu Liga 🏆</h1>
        <form id="formRegistro">
            <div class="form-group">
                <label>Nombre de tu Liga</label>
                <input type="text" name="txtNombre" required placeholder="Ej: Liga Central de Fútbol">
            </div>

            <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 30px 0;"></div>
            <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 15px;">DATOS DEL ADMINISTRADOR</p>

            <div class="row">
                <div class="form-group">
                    <label>Identificación (DNI)</label>
                    <input type="text" name="txtIdentificacion" required>
                </div>
                <div class="form-group">
                    <label>Email de Acceso</label>
                    <input type="email" name="txtEmail" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>Nombres</label>
                    <input type="text" name="txtNombres" required>
                </div>
                <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" name="txtApellidos" required>
                </div>
            </div>
            <div class="form-group">
                <label>Define tu Contraseña</label>
                <input type="password" name="txtPassword" required>
            </div>

            <button type="submit" class="btn-reg">REGISTRAR MI LIGA</button>
            <a href="<?= base_url() ?>" class="back">← Volver al inicio</a>
        </form>
    </div>

    <script>
        document.getElementById('formRegistro').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('<?= base_url() ?>registro/createLiga', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status) {
                alert(result.msg);
                window.location.href = '<?= base_url() ?>login';
            } else {
                alert(result.msg);
            }
        }
    </script>
</body>

</html>