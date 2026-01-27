<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

$res = $mysql->select_all("SELECT p.id_persona, p.nombres, p.id_liga, r.nombre_rol FROM personas p JOIN roles r ON p.id_rol = r.id_rol");
foreach ($res as $p) {
    echo "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Liga: {$p['id_liga']} | Rol: {$p['nombre_rol']}\n";
}
?>