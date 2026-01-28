<?php
class EquiposModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectEquipos(int $idLiga)
    {
        $sql = "SELECT e.id_equipo, e.nombre, e.escudo, e.estado, p.nombres as delegado_nombre, p.apellidos as delegado_apellido 
                FROM equipos e 
                LEFT JOIN personas p ON e.id_delegado = p.id_persona 
                WHERE e.id_liga = $idLiga AND e.estado != 0";
        return $this->select_all($sql);
    }

    public function selectEquipo(int $idEquipo, int $idLiga)
    {
        $sql = "SELECT * FROM equipos WHERE id_equipo = $idEquipo AND id_liga = $idLiga";
        return $this->select($sql);
    }

    public function insertEquipo(string $nombre, string $escudo, int $idDelegado, int $idLiga, int $estado)
    {
        $query_insert = "INSERT INTO equipos(nombre, escudo, id_delegado, id_liga, estado) VALUES(?,?,?,?,?)";
        $arrData = array($nombre, $escudo, $idDelegado, $idLiga, $estado);
        return $this->insert($query_insert, $arrData);
    }

    public function updateEquipo(int $idEquipo, string $nombre, string $escudo, int $idDelegado, int $estado)
    {
        $sql = "UPDATE equipos SET nombre=?, escudo=?, id_delegado=?, estado=? WHERE id_equipo = $idEquipo";
        $arrData = array($nombre, $escudo, $idDelegado, $estado);
        return $this->update($sql, $arrData);
    }

    public function deleteEquipo(int $idEquipo)
    {
        $sql = "UPDATE equipos SET estado = ? WHERE id_equipo = $idEquipo";
        return $this->update($sql, [0]);
    }

    public function selectDelegados(int $idLiga)
    {
        // Rol 3 es Delegado (según sugerencias anteriores, pero buscaremos por rol o cualquier persona de la liga)
        // Por ahora, traigamos a los usuarios que pertenecen a esa liga
        $sql = "SELECT id_persona, nombres, apellidos FROM personas WHERE id_liga = $idLiga AND estado != 0";
        return $this->select_all($sql);
    }
}

