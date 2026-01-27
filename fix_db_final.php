<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");
$mysql = new Mysql();

try {
    echo "--- INICIANDO REPARACION DE BASE DE DATOS ---\n";

    // 1. Eliminar la restricción problemática
    $mysql->update("ALTER TABLE equipo_jugadores DROP FOREIGN KEY fk_ej_jugador", []);
    echo "OK: FK fk_ej_jugador eliminada.\n";

    // 2. Renombrar columna para consistencia (opcional pero recomendado)
    // Vamos a dejarla como id_persona para que coincida con la tabla personas
    $mysql->update("ALTER TABLE equipo_jugadores CHANGE id_jugador id_persona INT(11) NOT NULL", []);
    echo "OK: Columna renombrada a id_persona.\n";

    // 3. Crear la relación correcta con personas
    $mysql->update("ALTER TABLE equipo_jugadores ADD CONSTRAINT fk_ej_persona FOREIGN KEY (id_persona) REFERENCES personas(id_persona) ON DELETE CASCADE", []);
    echo "OK: Nueva FK fk_ej_persona creada apuntando a la tabla personas.\n";

    // 4. Eliminar la tabla jugadores si está vacía o es basura
    $mysql->update("DROP TABLE IF EXISTS jugadores", []);
    echo "OK: Tabla 'jugadores' eliminada.\n";

    echo "--- REPARACION COMPLETADA CON EXITO ---";

} catch (Exception $e) {
    echo "Error durante la reparación: " . $e->getMessage();
}
?>