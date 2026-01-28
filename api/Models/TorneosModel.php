<?php
class TorneosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectTorneos(int $idLiga)
    {
        $sql = "SELECT id_torneo, id_liga, nombre, logo, categoria, cuota_jugador, valor_amarilla, valor_roja, valor_arbitraje_base, estado 
                FROM torneos 
                WHERE id_liga = $idLiga AND estado != 'ELIMINADO'";
        return $this->select_all($sql);
    }

    public function selectTorneo(int $idTorneo, int $idLiga)
    {
        $sql = "SELECT * FROM torneos WHERE id_torneo = $idTorneo AND id_liga = $idLiga";
        return $this->select($sql);
    }

    public function insertTorneo(string $nombre, string $logo, int $idLiga, string $categoria, float $cuota, float $amarilla, float $roja, float $arbitraje, string $fechaInicio, string $fechaFin)
    {
        $query_insert = "INSERT INTO torneos(nombre, logo, id_liga, categoria, cuota_jugador, valor_amarilla, valor_roja, valor_arbitraje_base, fecha_inicio, fecha_fin, estado) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        $arrData = array($nombre, $logo, $idLiga, $categoria, $cuota, $amarilla, $roja, $arbitraje, $fechaInicio, $fechaFin, 'PROGRAMADO');
        return $this->insert($query_insert, $arrData);
    }

    public function updateTorneo(int $idTorneo, string $nombre, string $logo, string $categoria, float $cuota, float $amarilla, float $roja, float $arbitraje, string $fechaInicio, string $fechaFin, string $estado)
    {
        $sql = "UPDATE torneos SET nombre=?, logo=?, categoria=?, cuota_jugador=?, valor_amarilla=?, valor_roja=?, valor_arbitraje_base=?, fecha_inicio=?, fecha_fin=?, estado=? WHERE id_torneo = $idTorneo";
        $arrData = array($nombre, $logo, $categoria, $cuota, $amarilla, $roja, $arbitraje, $fechaInicio, $fechaFin, $estado);
        return $this->update($sql, $arrData);
    }

    public function deleteTorneo(int $idTorneo)
    {
        $sql = "UPDATE torneos SET estado = ? WHERE id_torneo = $idTorneo";
        return $this->update($sql, ['ELIMINADO']);
    }

    // --- Gestión de Inscripciones ---
    public function selectInscritos(int $idTorneo)
    {
        $sql = "SELECT e.id_equipo, e.nombre, e.escudo, te.pago_inscripcion 
                FROM torneo_equipos te 
                INNER JOIN equipos e ON te.id_equipo = e.id_equipo 
                WHERE te.id_torneo = $idTorneo AND e.estado != 0";
        return $this->select_all($sql);
    }

    public function selectEquiposParaInscribir(int $idLiga, int $idTorneo)
    {
        // Equipos de la liga que NO están en este torneo
        $sql = "SELECT id_equipo, nombre, escudo FROM equipos 
                WHERE id_liga = $idLiga AND estado != 0 
                AND id_equipo NOT IN (SELECT id_equipo FROM torneo_equipos WHERE id_torneo = $idTorneo)";
        return $this->select_all($sql);
    }

    public function insertInscripcion(int $idTorneo, int $idEquipo)
    {
        $query = "INSERT INTO torneo_equipos(id_torneo, id_equipo) VALUES(?,?)";
        return $this->update($query, [$idTorneo, $idEquipo]);
    }

    public function deleteInscripcion(int $idTorneo, int $idEquipo)
    {
        $query = "DELETE FROM torneo_equipos WHERE id_torneo = ? AND id_equipo = ?";
        return $this->update($query, [$idTorneo, $idEquipo]);
    }
}

