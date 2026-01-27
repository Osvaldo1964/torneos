<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

$res = $mysql->select_all("SELECT * FROM personas");
$out = "";
foreach ($res as $p) {
    $out .= "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Ident: '{$p['identificacion']}' | Email: '{$p['email']}' | Liga: {$p['id_liga']} | Rol: {$p['id_rol']}\n";
}
file_put_contents("debug_full_users.txt", $out);
echo "LISTO";
?>