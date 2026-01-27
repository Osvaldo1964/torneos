<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

echo "--- BUSCANDO 'OSVALDO' ---\n";
$res = $mysql->select_all("SELECT * FROM personas WHERE nombres LIKE '%osvaldo%'");
foreach ($res as $p) {
    echo "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Ident: {$p['identificacion']} | Email: {$p['email']} | Liga: {$p['id_liga']}\n";
}
?>