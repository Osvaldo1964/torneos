<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

// Buscar a DIEGO
$diego = $mysql->select("SELECT id_liga FROM personas WHERE nombres LIKE 'DIEGO%' LIMIT 1");
if ($diego) {
    $idLiga = $diego['id_liga'];
    echo "Diego tiene Liga ID: $idLiga\n";

    // Actualizar todos los jugadores (rol 4) para que pertenezcan a esa liga si no tienen liga
    $mysql->update("UPDATE personas SET id_liga = $idLiga WHERE id_rol = 4", []);
    echo "Jugadores actualizados a Liga $idLiga\n";
} else {
    echo "No se encontró a Diego\n";
}
?>