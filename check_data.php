<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

echo "--- TORNEOS ---\n";
$res = $mysql->select_all("SELECT id_torneo, nombre, id_liga FROM torneos");
foreach ($res as $t) {
    echo "ID: {$t['id_torneo']} | Nombre: {$t['nombre']} | Liga: {$t['id_liga']}\n";
}

echo "\n--- EQUIPOS ---\n";
$res2 = $mysql->select_all("SELECT id_equipo, nombre, id_liga FROM equipos");
foreach ($res2 as $e) {
    echo "ID: {$e['id_equipo']} | Nombre: {$e['nombre']} | Liga: {$e['id_liga']}\n";
}
?>