<div class="sidebar">
    <h2 style="margin-bottom: 40px; font-weight: 800;">Global<span>Cup</span></h2>
    <ul style="list-style: none;">
        <li style="margin-bottom: 20px;">
            <a href="<?= base_url() ?>dashboard"
                style="color: <?= ($data['page_name'] == 'dashboard') ? 'white' : '#94a3b8' ?>; text-decoration: none; font-weight: <?= ($data['page_name'] == 'dashboard') ? '600' : '400' ?>;">
                🏠 Inicio
            </a>
        </li>

        <?php if ($_SESSION['userData']['rol'] == 'Super Admin'): ?>
            <li style="margin-bottom: 20px;">
                <a href="<?= base_url() ?>ligas"
                    style="color: <?= ($data['page_name'] == 'ligas') ? 'white' : '#94a3b8' ?>; text-decoration: none; font-weight: <?= ($data['page_name'] == 'ligas') ? '600' : '400' ?>;">
                    🏢 Ligas
                </a>
            </li>
        <?php endif; ?>

        <li style="margin-bottom: 20px;">
            <a href="<?= base_url() ?>jugadores"
                style="color: <?= ($data['page_name'] == 'jugadores') ? 'white' : '#94a3b8' ?>; text-decoration: none; font-weight: <?= ($data['page_name'] == 'jugadores') ? '600' : '400' ?>;">
                🏃‍♂️ Jugadores
            </a>
        </li>

        <li style="margin-bottom: 20px;">
            <a href="<?= base_url() ?>equipos"
                style="color: <?= ($data['page_name'] == 'equipos') ? 'white' : '#94a3b8' ?>; text-decoration: none; font-weight: <?= ($data['page_name'] == 'equipos') ? '600' : '400' ?>;">
                ⚽ Equipos
            </a>
        </li>

        <li style="margin-bottom: 20px;">
            <a href="<?= base_url() ?>torneos_mod"
                style="color: <?= ($data['page_name'] == 'torneos') ? 'white' : '#94a3b8' ?>; text-decoration: none; font-weight: <?= ($data['page_name'] == 'torneos') ? '600' : '400' ?>;">
                🏆 Torneos
            </a>
        </li>

        <li style="margin-bottom: 20px;">
            <a href="#" style="color: #64748b; text-decoration: none;">
                💰 Finanzas
            </a>
        </li>

        <li style="margin-top: 50px;">
            <a href="<?= base_url() ?>logout" style="color: #ef4444; text-decoration: none; font-weight: 600;">
                🚪 Cerrar Sesión
            </a>
        </li>
    </ul>
</div>
<style>
    .sidebar h2 span {
        color: #3b82f6;
    }

    .sidebar ul li a:hover {
        color: white !important;
    }
</style>