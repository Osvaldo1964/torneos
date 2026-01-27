<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

echo "--- LIMPIEZA TOTAL DE RELACIONES EN EQUIPO_JUGADORES ---\n";

// Obtener todas las FKs
$sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'equipo_jugadores' AND TABLE_SCHEMA = '" . DB_NAME . "' 
        AND REFERENCED_TABLE_NAME IS NOT NULL";
$fks = $mysql->select_all($sql);

foreach ($fks as $fk) {
    try {
        $name = $fk['CONSTRAINT_NAME'];
        $mysql->update("ALTER TABLE equipo_jugadores DROP FOREIGN KEY $name", []);
        echo "OK: Eliminada FK $name\n";
    } catch (Exception $e) {
        echo "Aviso: No se pudo eliminar $name (" . $e->getMessage() . ")\n";
    }
}

// Ahora sí, reestructuración limpia
try {
    // 1. Asegurar que tenemos las columnas correctas
    // Primero, si existe id_jugador la pasamos a id_persona
    $cols = $mysql->select_all("DESCRIBE equipo_jugadores");
    $hasJugador = false;
    foreach ($cols as $c)
        if ($c['Field'] == 'id_jugador')
            $hasJugador = true;

    if ($hasJugador) {
        $mysql->update("ALTER TABLE equipo_jugadores CHANGE id_jugador id_persona INT(11) NOT NULL", []);
        echo "OK: Columna id_jugador cambiada a id_persona.\n";
    }

    // 2. Crear las FKs correctas
    $mysql->update("ALTER TABLE equipo_jugadores ADD CONSTRAINT fk_ej_equipo FOREIGN KEY (id_equipo) REFERENCES equipos(id_equipo) ON DELETE CASCADE", []);
    $mysql->update("ALTER TABLE equipo_jugadores ADD CONSTRAINT fk_ej_persona FOREIGN KEY (id_persona) REFERENCES personas(id_persona) ON DELETE CASCADE", []);
    $mysql->update("ALTER TABLE equipo_jugadores ADD CONSTRAINT fk_ej_torneo FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE CASCADE", []);
    echo "OK: Relaciones recreadas correctamente apuntando a PERSONAS.\n";

    // 3. Borrar tabla basura
    $mysql->update("DROP TABLE IF EXISTS jugadores", []);
    echo "OK: Tabla 'jugadores' eliminada.\n";

} catch (Exception $e) {
    echo "Error final: " . $e->getMessage();
}
?>