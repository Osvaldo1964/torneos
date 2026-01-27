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
                <button class="btn btn-outline-light ms-3" onclick="openModalRegistro()">Crear Mi Liga</button>
            </div>
        </div>
    </div>

    <!-- Modal Registro -->
    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg text-dark" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Inscribir Nueva Liga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formRegistro">
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">Nombre de la Liga</label>
                                <input type="text" class="form-control" id="nombre_liga" required
                                    placeholder="Ej: Liga Elite de Fútbol" style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">Logo de la Liga</label>
                                <input type="file" class="form-control" id="logo" accept="image/*"
                                    style="border-radius: 10px;">
                            </div>
                            <hr class="text-muted">
                            <p class="small text-muted mb-3">Datos del Administrador de la Liga</p>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">Nombres</label>
                                <input type="text" class="form-control" id="nombres" required placeholder="Nombres"
                                    style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" required placeholder="Apellidos"
                                    style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">Identificación (DNI)</label>
                                <input type="text" class="form-control" id="identificacion" required
                                    placeholder="Número de cédula" style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" required
                                    placeholder="admin@ejemplo.com" style="border-radius: 10px;">
                            </div>
                            <div class="col-md-12 mb-0">
                                <label class="form-label small fw-bold text-muted">Contraseña</label>
                                <input type="password" class="form-control" id="password" required
                                    placeholder="********" style="border-radius: 10px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                            style="border-radius: 10px;">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Crear Liga e
                            Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts locales (Previamente descargados en app/assets) -->
    <script src="../app/assets/js/bootstrap.bundle.min.js"></script>
    <script src="../app/assets/js/sweetalert2.min.js"></script>
    <script>
        const api_url = "http://localhost/torneos/api/";
        const modalRegistro = new bootstrap.Modal(document.getElementById('modalRegistro'));

        function openModalRegistro() {
            modalRegistro.show();
        }

        document.getElementById('formRegistro').onsubmit = async (e) => {
            e.preventDefault();

            const formData = new FormData();
            formData.append('nombre_liga', document.getElementById('nombre_liga').value);
            formData.append('identificacion', document.getElementById('identificacion').value);
            formData.append('nombres', document.getElementById('nombres').value);
            formData.append('apellidos', document.getElementById('apellidos').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('password', document.getElementById('password').value);

            const logoFile = document.getElementById('logo').files[0];
            if (logoFile) {
                formData.append('logo', logoFile);
            }

            try {
                const response = await fetch(api_url + "Registro/createLiga", {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status) {
                    Swal.fire("¡Éxito!", result.msg, "success").then(() => {
                        window.location.href = "../app/login.php";
                    });
                } else {
                    Swal.fire("Error", result.msg, "error");
                }
            } catch (error) {
                Swal.fire("Error", "No se pudo conectar con el servidor", "error");
            }
        };
    </script>
</body>

</html>