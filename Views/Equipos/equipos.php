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

        .badge-delegado {
            background: #fef3c7;
            color: #92400e;
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
            margin: 100px auto;
            padding: 30px;
            border-radius: 20px;
            width: 450px;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
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
            <button class="btn-add" onclick="openModal()">+ Nuevo Equipo</button>
        </div>

        <div class="table-container">
            <table id="tableEquipos">
                <thead>
                    <tr>
                        <th>Escudo</th>
                        <th>ID</th>
                        <th>Nombre del Equipo</th>
                        <th>Delegado Asignado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="listaEquipos">
                    <!-- Datos cargados por JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nuevo Equipo -->
    <div id="modalEquipo" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="color: var(--primary)">Registrar Equipo</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="formEquipo">
                <div class="form-group">
                    <label>Nombre del Equipo</label>
                    <input type="text" id="txtNombre" name="txtNombre" required placeholder="Ej: Real Madrid FC">
                </div>
                <div class="form-group">
                    <label>Delegado del Equipo</label>
                    <select id="listDelegado" name="listDelegado">
                        <!-- Opciones cargadas por JS -->
                    </select>
                    <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 5px;">* El delegado debe estar registrado
                        primero como persona.</p>
                </div>
                <div class="form-group">
                    <label>Logo / Escudo del Equipo</label>
                    <input type="file" id="escudo" name="escudo" accept="image/*">
                </div>
                <button type="submit" class="btn-save">Guardar Equipo</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            loadDelegados();
            document.getElementById('modalEquipo').style.display = 'block';
        }
        function closeModal() { document.getElementById('modalEquipo').style.display = 'none'; }

        async function loadDelegados() {
            const response = await fetch('<?= base_url() ?>equipos/getDelegados');
            const data = await response.json();
            const select = document.getElementById('listDelegado');
            select.innerHTML = '<option value="0">--- Seleccionar Delegado ---</option>';
            data.forEach(d => {
                select.innerHTML += `<option value="${d.id_persona}">${d.nombres} ${d.apellidos} (${d.identificacion})</option>`;
            });
        }

        async function loadEquipos() {
            const response = await fetch('<?= base_url() ?>equipos/getEquipos');
            const data = await response.json();
            const container = document.getElementById('listaEquipos');
            container.innerHTML = "";

            data.forEach(e => {
                const delegado = e.delegado_nombres ? `${e.delegado_nombres} ${e.delegado_apellidos}` : '<span style="color:#cbd5e1">Sin asignar</span>';
                container.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <img src="<?= base_url() ?>Assets/images/uploads/${e.escudo || 'default_shield.png'}" 
                                 style="width: 40px; height: 40px; object-fit: contain;">
                        </td>
                        <td><strong>#${e.id_equipo}</strong></td>
                        <td>${e.nombre}</td>
                        <td><span class="badge-delegado">${delegado}</span></td>
                        <td>${e.options}</td>
                    </tr>
                `;
            });
        }

        document.getElementById('formEquipo').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('<?= base_url() ?>equipos/setEquipo', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status) {
                alert(result.msg);
                closeModal();
                loadEquipos();
                e.target.reset();
            } else {
                alert(result.msg);
            }
        }

        loadEquipos();
    </script>
</body>

</html>