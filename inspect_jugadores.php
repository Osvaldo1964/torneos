<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

echo "--- CONTENIDO TABLA JUGADORES ---\n";
$res = $mysql->select_all("SELECT * FROM jugadores");
echo "Total registros: " . count($res) . "\n";
foreach ($res as $r) {
    print_r($r);
}

echo "\n--- ESTRUCTURA JUGADORES ---\n";
$res2 = $mysql->select_all("DESCRIBE jugadores");
foreach ($res2 as $c)
    echo $c['Field'] . " (" . $c['Type'] . ")\n";
?>