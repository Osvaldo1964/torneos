<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

echo "--- RELACIONES DE EQUIPO_JUGADORES ---\n";
$res = $mysql->select_all("
    SELECT 
        COLUMN_NAME, 
        CONSTRAINT_NAME, 
        REFERENCED_TABLE_NAME, 
        REFERENCED_COLUMN_NAME 
    FROM 
        INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE 
        TABLE_NAME = 'equipo_jugadores' 
        AND TABLE_SCHEMA = '" . DB_NAME . "'
        AND REFERENCED_TABLE_NAME IS NOT NULL
");

foreach ($res as $rel) {
    echo "Columna: {$rel['COLUMN_NAME']} -> Referencia: {$rel['REFERENCED_TABLE_NAME']}({$rel['REFERENCED_COLUMN_NAME']}) [FK: {$rel['CONSTRAINT_NAME']}]\n";
}

echo "\n--- ¿ EXISTE LA TABLA JUGADORES ? ---\n";
$tables = $mysql->select_all("SHOW TABLES LIKE 'jugadores'");
echo count($tables) > 0 ? "SÍ existe la tabla 'jugadores'\n" : "NO existe la tabla 'jugadores'\n";
?>