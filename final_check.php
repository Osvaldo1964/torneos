<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

$res = $mysql->select_all("SELECT id_persona, nombres, identificacion, email, id_rol, estado FROM personas");
echo "Registros totales: " . count($res) . "\n";
foreach ($res as $p) {
    echo "ID: [{$p['id_persona']}] | Ident: [{$p['identificacion']}] | Email: [{$p['email']}] | Rol: [{$p['id_rol']}] | Estado: [{$p['estado']}]\n";
}
?>