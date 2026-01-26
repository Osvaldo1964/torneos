<?php
class EquiposModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectEquipos(int $idLiga)
    {
        $sql = "SELECT e.id_equipo, e.nombre, e.escudo, p.nombres as delegado_nombres, p.apellidos as delegado_apellidos 
                FROM equipos e
                LEFT JOIN personas p ON e.id_delegado = p.id_persona
                WHERE e.id_liga = $idLiga";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectDelegados(int $idLiga)
    {
        // Seleccionamos personas de la liga que puedan ser delegados
        $sql = "SELECT id_persona, nombres, apellidos, identificacion 
                FROM personas 
                WHERE id_liga = $idLiga AND id_rol IN (2, 3)"; // Admin Liga o Delegado
        $request = $this->select_all($sql);
        return $request;
    }

    public function insertEquipo(int $idLiga, string $nombre, int $idDelegado, string $escudo)
    {
        $sql = "SELECT * FROM equipos WHERE nombre = '$nombre' AND id_liga = $idLiga";
        $request = $this->select($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO equipos(id_liga, nombre, id_delegado, escudo) VALUES(?,?,?,?)";
            $arrData = array($idLiga, $nombre, $idDelegado, $escudo);
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }

    public function getEquipo(int $idEquipo)
    {
        $sql = "SELECT * FROM equipos WHERE id_equipo = $idEquipo";
        return $this->select($sql);
    }

    public function selectJugadoresDisponibles(int $idLiga)
    {
        $sql = "SELECT id_persona, identificacion, nombres, apellidos, foto FROM personas 
                WHERE id_liga = $idLiga AND id_rol = 4";
        return $this->select_all($sql);
    }

    public function insertNomina(int $idEquipo, int $idPersona, int $idTorneo)
    {
        $sql = "SELECT * FROM equipo_jugadores WHERE id_persona = $idPersona AND id_torneo = $idTorneo";
        $request = $this->select($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO equipo_jugadores(id_equipo, id_persona, id_torneo) VALUES(?,?,?)";
            $arrData = array($idEquipo, $idPersona, $idTorneo);
            return $this->insert($query_insert, $arrData);
        } else {
            return "exist";
        }
    }

    public function selectPlantilla(int $idEquipo, int $idTorneo)
    {
        $sql = "SELECT p.id_persona, p.identificacion, p.nombres, p.apellidos, p.foto, p.posicion 
                FROM equipo_jugadores ej
                INNER JOIN personas p ON ej.id_persona = p.id_persona
                WHERE ej.id_equipo = $idEquipo AND ej.id_torneo = $idTorneo";
        return $this->select_all($sql);
    }

    public function checkAndCreateDefaultTorneo(int $idLiga)
    {
        $sql = "SELECT id_torneo FROM torneos WHERE id_liga = $idLiga LIMIT 1";
        $request = $this->select($sql);
        if (empty($request)) {
            $sql_ins = "INSERT INTO torneos(id_liga, nombre, categoria, estado) VALUES(?,?,?,?)";
            return $this->insert($sql_ins, array($idLiga, "Torneo Apertura 2026", "Libre", "EN CURSO"));
        }
        return $request['id_torneo'];
    }
}
?>