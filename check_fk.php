<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");

$mysql = new Mysql();
$res = $mysql->select_all("SHOW CREATE TABLE equipo_jugadores");
echo $res[0]['Create Table'];
?>