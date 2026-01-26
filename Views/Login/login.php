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
        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background: #020617;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 350px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .login-box h2 {
            margin-bottom: 30px;
            font-weight: 600;
            color: #3b82f6;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
            color: white;
            outline: none;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: #3b82f6;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: 0.4s;
        }

        .btn-login:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .footer-link {
            margin-top: 20px;
            display: block;
            font-size: 0.8rem;
            color: #64748b;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Admin Portal</h2>
        <form id="formLogin" action="">
            <div class="input-group">
                <label>Correo Electrónico</label>
                <input type="email" id="txtEmail" name="txtEmail" placeholder="correo@ejemplo.com" required>
            </div>
            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" id="txtPassword" name="txtPassword" placeholder="********" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        <a href="<?= base_url() ?>" class="footer-link">← Volver a la Landing</a>
    </div>

    <script>
        document.getElementById('formLogin').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('<?= base_url() ?>login/loginUser', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status) {
                window.location.href = '<?= base_url() ?>dashboard';
            } else {
                alert(result.msg);
            }
        }
    </script>
</body>

</html>