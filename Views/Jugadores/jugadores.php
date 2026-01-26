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
            overflow: hidden;
        }

        .sidebar {
            width: 260px;
            background: var(--primary);
            color: white;
            padding: 30px;
            flex-shrink: 0;
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
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
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

        .badge-pos {
            background: #dbeafe;
            color: #1e40af;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
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
            margin: 50px auto;
            padding: 30px;
            border-radius: 20px;
            width: 600px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: #94a3b8;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            outline: none;
        }

        .btn-save {
            width: 100%;
            background: var(--accent);
            color: white;
            padding: 14px;
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
            <button class="btn-add" onclick="openModal()">+ Nuevo Jugador</button>
        </div>

        <div class="table-container">
            <table id="tableJugadores">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>ID</th>
                        <th>Nombres y Apellidos</th>
                        <th>Email / Teléfono</th>
                        <th>Posición</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="listaJugadores">
                    <!-- Datos cargados por JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nuevo Jugador -->
    <div id="modalJugador" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="color: var(--primary)">Registrar Jugador</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="formJugador">
                <div class="form-row">
                    <div class="form-group">
                        <label>Identificación (DNI)</label>
                        <input type="text" name="txtIdentificacion" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="txtEmail">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombres</label>
                        <input type="text" name="txtNombres" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="txtApellidos" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="txtTelefono">
                    </div>
                    <div class="form-group">
                        <label>Fecha de Nacimiento</label>
                        <input type="date" name="txtFechaNac">
                    </div>
                </div>
                <div class="form-group">
                    <label>Posición Habitual</label>
                    <select name="listPosicion">
                        <option value="Portero">Portero</option>
                        <option value="Defensa Central">Defensa Central</option>
                        <option value="Lateral">Lateral</option>
                        <option value="Mediocampista">Mediocampista</option>
                        <option value="Extremo">Extremo</option>
                        <option value="Delantero">Delantero</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Foto del Jugador</label>
                    <input type="file" name="foto" id="foto" accept="image/*">
                </div>
                <button type="submit" class="btn-save">Registrar Jugador</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('modalJugador').style.display = 'block'; }
        function closeModal() { document.getElementById('modalJugador').style.display = 'none'; }

        async function loadJugadores() {
            const response = await fetch('<?= base_url() ?>jugadores/getJugadores');
            const data = await response.json();
            const container = document.getElementById('listaJugadores');
            container.innerHTML = "";

            data.forEach(j => {
                container.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <img src="<?= base_url() ?>Assets/images/uploads/${j.foto || 'default_user.png'}" 
                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent);">
                        </td>
                        <td><strong>${j.identificacion}</strong></td>
                        <td>${j.nombres} ${j.apellidos}</td>
                        <td>
                            <div style="font-size: 0.85rem; color: #64748b;">${j.email || 'N/A'}</div>
                            <div style="font-size: 0.85rem; color: #64748b;">${j.telefono || ''}</div>
                        </td>
                        <td><span class="badge-pos">${j.posicion}</span></td>
                        <td>${j.options}</td>
                    </tr>
                `;
            });
        }

        document.getElementById('formJugador').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('<?= base_url() ?>jugadores/setJugador', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status) {
                alert(result.msg);
                closeModal();
                loadJugadores();
                e.target.reset();
            } else {
                alert(result.msg);
            }
        }

        loadJugadores();
    </script>
</body>

</html>