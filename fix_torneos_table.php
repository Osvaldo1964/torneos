<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");

$mysql = new Mysql();

$queries = [
    "ALTER TABLE torneos ADD COLUMN cuota_jugador DECIMAL(10,2) DEFAULT 0.00 AFTER categoria",
    "ALTER TABLE torneos ADD COLUMN valor_amarilla DECIMAL(10,2) DEFAULT 0.00 AFTER cuota_jugador",
    "ALTER TABLE torneos ADD COLUMN valor_roja DECIMAL(10,2) DEFAULT 0.00 AFTER valor_amarilla",
    "ALTER TABLE torneos ADD COLUMN valor_arbitraje_base DECIMAL(10,2) DEFAULT 0.00 AFTER valor_roja"
];

foreach ($queries as $sql) {
    try {
        $mysql->update($sql, []);
        echo "Ejecutado: $sql\n";
    } catch (Exception $e) {
        echo "Error en $sql: " . $e->getMessage() . "\n";
    }
}
?>