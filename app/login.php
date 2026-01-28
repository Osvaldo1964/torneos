<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Global Cup Admin</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="assets/js/sweetalert2.min.js"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card h2 {
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 30px;
        }

        .login-card h2 span {
            color: #3b82f6;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .btn-primary {
            width: 100%;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            background-color: #3b82f6;
            border: none;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h2>Global<span>Cup</span> Admin</h2>
        <form id="formLogin">
            <input type="email" id="txtEmail" class="form-control" placeholder="Correo Electrónico" required>
            <input type="password" id="txtPassword" class="form-control" placeholder="Contraseña" required>
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </form>
    </div>

    <script>
        const base_url = window.location.origin + window.location.pathname.replace("app/login.php", "api/");

        document.getElementById('formLogin').onsubmit = async (e) => {
            e.preventDefault();
            const email = document.getElementById('txtEmail').value;
            const password = document.getElementById('txtPassword').value;

            try {
                const response = await fetch(base_url + "Login/login", {
                    method: 'POST',
                    body: JSON.stringify({
                        email,
                        password
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                const result = await response.json();

                if (result.status) {
                    localStorage.setItem('gc_token', result.data.token);
                    localStorage.setItem('gc_user', JSON.stringify(result.data.user));
                    window.location.href = "dashboard.php";
                } else {
                    Swal.fire("Error", result.msg, "error");
                }
            } catch (error) {
                console.error(error);
                Swal.fire("Error", "No se pudo conectar con el servidor", "error");
            }
        };
    </script>
</body>

</html>