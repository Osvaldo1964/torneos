<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

$res = $mysql->select_all("SELECT id_persona, nombres, id_rol, id_liga, estado FROM personas WHERE id_rol = 4");
echo "Total jugadores encontrados: " . count($res) . "\n";
foreach ($res as $p) {
    echo "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Liga: {$p['id_liga']}\n";
}
?>