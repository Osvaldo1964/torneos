<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

$res = $mysql->select_all("DESCRIBE equipo_jugadores");
$out = "EQUIPO_JUGADORES:\n";
foreach ($res as $r)
    $out .= $r['Field'] . "\n";

$res2 = $mysql->select_all("DESCRIBE personas");
$out .= "\nPERSONAS:\n";
foreach ($res2 as $r)
    $out .= $r['Field'] . "\n";

file_put_contents("real_structure.txt", $out);
echo "OK";
?>