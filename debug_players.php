<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

echo "--- PERSONAS CON ROL JUGADOR (4) ---\n";
$res = $mysql->select_all("SELECT id_persona, nombres, apellidos, id_rol, id_liga, estado FROM personas");
foreach ($res as $p) {
    echo "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Rol: {$p['id_rol']} | Liga: {$p['id_liga']} | Estado: {$p['estado']}\n";
}

echo "\n--- ROLES DISPONIBLES ---\n";
$roles = $mysql->select_all("SELECT * FROM roles");
foreach ($roles as $r) {
    echo "ID: {$r['id_rol']} | Nombre: {$r['nombre_rol']}\n";
}
?>