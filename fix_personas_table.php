<?php
require_once("api/Config/Config.php");
require_once("api/Libraries/Core/Conexion.php");
require_once("api/Libraries/Core/Mysql.php");

$mysql = new Mysql();
$sql = "ALTER TABLE personas ADD COLUMN estado TINYINT(1) DEFAULT 1 AFTER id_rol";
$res = $mysql->update($sql, []);
if ($res)
    echo "Columna 'estado' añadida a personas.";
else
    echo "Error o la columna ya existe.";
?>