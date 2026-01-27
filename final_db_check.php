<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");

try {
    $mysql = new Mysql();
    $con = new Conexion();
    $pdo = $con->conect();

    echo "--- ESTRUCTURA REAL EQUIPO_JUGADORES ---\n";
    $q = $pdo->query("DESCRIBE equipo_jugadores");
    $cols = $q->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c)
        echo "Campo: {$c['Field']} | Tipo: {$c['Type']}\n";

    echo "\n--- ESTRUCTURA REAL PERSONAS ---\n";
    $q2 = $pdo->query("DESCRIBE personas");
    $cols2 = $q2->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols2 as $c)
        echo "Campo: {$c['Field']} | Tipo: {$c['Type']}\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>