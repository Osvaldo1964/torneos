<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");

$mysql = new Mysql();

echo "--- EQUIPOS ---\n";
$res = $mysql->select_all("DESCRIBE equipos");
foreach ($res as $col)
    echo $col['Field'] . "\n";
?>