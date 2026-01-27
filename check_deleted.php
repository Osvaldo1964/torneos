<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

$res = $mysql->select_all("SELECT * FROM personas WHERE estado = 0");
echo "Registros eliminados: " . count($res) . "\n";
foreach ($res as $p) {
    echo "ID: {$p['id_persona']} | Ident: {$p['identificacion']} | Email: {$p['email']}\n";
}
?>