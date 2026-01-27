<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");

$mysql = new Mysql();

$queries = [
    "ALTER TABLE equipos ADD COLUMN estado INT DEFAULT 1 AFTER id_delegado"
];

foreach ($queries as $sql) {
    try {
        $mysql->update($sql, []);
        echo "Ejecutado: $sql\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Crear carpetas de assets
$dirs = [
    "app/assets/images/equipos",
    "app/assets/images/jugadores"
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
        echo "Carpeta $dir creada.\n";
    }
}
?>