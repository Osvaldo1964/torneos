<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

echo "--- ANALISIS DE LIGAS ---\n";
$users = $mysql->select_all("SELECT id_persona, nombres, id_rol, id_liga FROM personas");
foreach ($users as $u) {
    $rol = ($u['id_rol'] == 1 ? "SUPER ADMIN" : ($u['id_rol'] == 2 ? "LIGA ADMIN" : "OTRO"));
    if ($u['id_rol'] == 4)
        $rol = "JUGADOR";
    echo "User: {$u['nombres']} | Rol: $rol | Liga ID: {$u['id_liga']}\n";
}

$ligas = $mysql->select_all("SELECT * FROM ligas");
foreach ($ligas as $l) {
    echo "Liga ID: {$l['id_liga']} | Nombre: {$l['nombre']}\n";
}
?>