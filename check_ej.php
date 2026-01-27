<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();
echo "--- EQUIPO_JUGADORES ---\n";
$res = $mysql->select_all("DESCRIBE equipo_jugadores");
foreach ($res as $col)
    echo $col['Field'] . "\n";
?>