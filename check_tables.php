<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");

$mysql = new Mysql();

echo "--- TORNEOS ---\n";
$res = $mysql->select_all("DESCRIBE torneos");
foreach ($res as $col)
    echo $col['Field'] . "\n";

echo "\n--- LIGAS ---\n";
$res = $mysql->select_all("DESCRIBE ligas");
foreach ($res as $col)
    echo $col['Field'] . "\n";
?>