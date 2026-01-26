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

        .sidebar {
            width: 260px;
            background: var(--primary);
            color: white;
            padding: 30px;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn-add {
            background: var(--success);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #f1f5f9;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .price-tag {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-azul {
            background: #dbeafe;
            color: #1e40af;
            padding: 4px 10px;
            border-radius: 8px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
        }

        .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: #94a3b8;
        }

        form .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-save {
            width: 100%;
            background: var(--accent);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php require_once("Views/Template/nav_admin.php"); ?>

    <div class="main-content">
        <div class="header-actions">
            <h1>
                <?= $data['page_title'] ?>
            </h1>
            <button class="btn-add" onclick="openModal()">+ Nueva Liga</button>
        </div>

        <div class="table-container">
            <table id="tableLigas">
                <thead>
                    <tr>
                        <th>Nombre de la Liga</th>
                        <th>Cuota Jugador</th>
                        <th>Vr. Amarilla</th>
                        <th>Vr. Roja</th>
                        <th>Arbitraje Base</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="listaLigas">
                    <!-- Datos cargados por JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nueva Liga -->
    <div id="modalLiga" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Registrar Nueva Liga</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="formLiga">
                <div class="form-group">
                    <label>Nombre de la Liga</label>
                    <input type="text" id="txtNombre" name="txtNombre" required placeholder="Ej: Liga Premier Ciudad">
                </div>
                <div class="row">
                    <div class="form-group">
                        <label>Cuota Mensual Jugador</label>
                        <input type="number" id="txtCuota" name="txtCuota" required value="0">
                    </div>
                    <div class="form-group">
                        <label>Vr. Arbitraje Base</label>
                        <input type="number" id="txtArbitraje" name="txtArbitraje" required value="0">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label>Multa Amarilla</label>
                        <input type="number" id="txtAmarilla" name="txtAmarilla" required value="0">
                    </div>
                    <div class="form-group">
                        <label>Multa Roja</label>
                        <input type="number" id="txtRoja" name="txtRoja" required value="0">
                    </div>
                </div>

                <h3 style="margin: 20px 0 10px; font-size: 1rem; color: var(--primary);">Datos del Administrador</h3>
                <div class="row">
                    <div class="form-group">
                        <label>Identificación</label>
                        <input type="text" id="txtIdentificacion" name="txtIdentificacion" required>
                    </div>
                    <div class="form-group">
                        <label>Email Access</label>
                        <input type="email" id="txtEmail" name="txtEmail" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label>Nombres</label>
                        <input type="text" id="txtNombres" name="txtNombres" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" id="txtApellidos" name="txtApellidos" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Contraseña Temporal</label>
                    <input type="password" id="txtPassword" name="txtPassword" required>
                </div>
                <button type="submit" class="btn-save">Guardar Liga y Admin</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalLiga').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalLiga').style.display = 'none';
        }

        async function loadLigas() {
            const response = await fetch('<?= base_url() ?>ligas/getLigas');
            const data = await response.json();
            const container = document.getElementById('listaLigas');
            container.innerHTML = "";

            data.forEach(liga => {
                container.innerHTML += `
                    <tr>
                        <td><strong>${liga.nombre}</strong></td>
                        <td><span class="price-tag">$${parseFloat(liga.cuota_mensual_jugador).toLocaleString()}</span></td>
                        <td>$${parseFloat(liga.valor_amarilla).toLocaleString()}</td>
                        <td>$${parseFloat(liga.valor_roja).toLocaleString()}</td>
                        <td>$${parseFloat(liga.valor_arbitraje_base).toLocaleString()}</td>
                        <td><span class="badge-azul">Activa</span></td>
                        <td>${liga.options}</td>
                    </tr>
                `;
            });
        }

        document.getElementById('formLiga').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('<?= base_url() ?>ligas/setLiga', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status) {
                alert(result.msg);
                closeModal();
                loadLigas();
                e.target.reset();
            } else {
                alert(result.msg);
            }
        }

        loadLigas();
    </script>
</body>

</html>