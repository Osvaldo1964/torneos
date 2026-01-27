<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

$dni = "5566";
echo "--- BUSCANDO IDENTIFICACION: $dni ---\n";
$res = $mysql->select_all("SELECT id_persona, identificacion, nombres, id_rol, id_liga, estado, email FROM personas WHERE identificacion = '$dni'");
if ($res) {
    foreach ($res as $p) {
        echo "ID: {$p['id_persona']} | Nombre: {$p['nombres']} | Rol: {$p['id_rol']} | Liga: {$p['id_liga']} | Estado: {$p['estado']} | Email: {$p['email']}\n";
    }
} else {
    echo "No se encontró ningún registro con esa identificación.\n";
}

echo "\n--- BUSCANDO POR EMAIL (si aplica) ---\n";
// El usuario no mostró el email en la imagen, pero revisemos si hay algo duplicado general
$res2 = $mysql->select_all("SELECT email, count(*) as total FROM personas GROUP BY email HAVING total > 1");
foreach ($res2 as $e) {
    echo "Email duplicado: {$e['email']} (Total: {$e['total']})\n";
}
?>