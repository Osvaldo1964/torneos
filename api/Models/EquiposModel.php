<?php
class EquiposModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectEquipos(int $idLiga = 0, int $idDelegado = 0, int $idTorneo = 0)
    {
        $where = "WHERE e.estado != 0";
        $join = "";

        if ($idLiga > 0) {
            $where .= " AND e.id_liga = $idLiga";
        }
        if ($idDelegado > 0) {
            $where .= " AND e.id_delegado = $idDelegado";
        }
        if ($idTorneo > 0) {
            $join = "INNER JOIN torneo_equipos te ON e.id_equipo = te.id_equipo";
            $where .= " AND te.id_torneo = $idTorneo";
        }

        $sql = "SELECT e.id_equipo, e.nombre, e.escudo, e.estado, p.nombres as delegado_nombre, p.apellidos as delegado_apellido 
                FROM equipos e 
                LEFT JOIN personas p ON e.id_delegado = p.id_persona 
                $join
                $where";
        return $this->select_all($sql);
    }

    public function selectEquipo(int $idEquipo, int $idLiga = 0)
    {
        $where = "WHERE id_equipo = $idEquipo";
        if ($idLiga > 0)
            $where .= " AND id_liga = $idLiga";
        $sql = "SELECT * FROM equipos $where";
        return $this->select($sql);
    }

    public function insertEquipo(string $nombre, string $escudo, int $idDelegado, int $idLiga, int $estado, int $idTorneo = 0)
    {
        $query_insert = "INSERT INTO equipos(nombre, escudo, id_delegado, id_liga, estado) VALUES(?,?,?,?,?)";
        $arrData = array($nombre, $escudo, $idDelegado, $idLiga, $estado);
        $idEquipo = $this->insert($query_insert, $arrData);

        if ($idEquipo > 0 && $idTorneo > 0) {
            $query_t = "INSERT INTO torneo_equipos(id_torneo, id_equipo) VALUES(?,?)";
            $this->insert($query_t, [$idTorneo, $idEquipo]);
        }
        return $idEquipo;
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
        // Rol 3 es Delegado
        $sql = "SELECT id_persona, nombres, apellidos FROM personas WHERE id_liga = $idLiga AND id_rol = 3 AND estado != 0";
        return $this->select_all($sql);
    }
}

