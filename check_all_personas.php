<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();
$res = $mysql->select_all("SELECT * FROM personas");
echo "TOTAL: " . count($res) . "\n";
foreach ($res as $p) {
    echo "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Ident: '{$p['identificacion']}' | Email: '{$p['email']}'\n";
}
?>