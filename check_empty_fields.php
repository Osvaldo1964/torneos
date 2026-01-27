<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

echo "--- REGISTROS CON EMAIL VACIO ---\n";
$res_email = $mysql->select_all("SELECT id_persona, nombres, email, identificacion FROM personas WHERE email = '' OR email IS NULL");
foreach ($res_email as $p) {
    echo "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Ident: {$p['identificacion']}\n";
}

echo "\n--- REGISTROS CON IDENTIFICACION VACIA ---\n";
$res_ident = $mysql->select_all("SELECT id_persona, nombres, email, identificacion FROM personas WHERE identificacion = '' OR identificacion IS NULL");
foreach ($res_ident as $p) {
    echo "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Email: {$p['email']}\n";
}
?>