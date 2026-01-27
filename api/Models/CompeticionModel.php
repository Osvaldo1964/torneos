<?php
class CompeticionModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // --- Gestión de Fases ---
    public function insertFase(int $idTorneo, string $nombre, string $tipo, int $idaVuelta, int $orden)
    {
        $query = "INSERT INTO torneo_fases(id_torneo, nombre, tipo, ida_vuelta, orden) VALUES(?,?,?,?,?)";
        return $this->insert($query, [$idTorneo, $nombre, $tipo, $idaVuelta, $orden]);
    }

    public function selectFases(int $idTorneo)
    {
        $sql = "SELECT * FROM torneo_fases WHERE id_torneo = $idTorneo AND estado != 0 ORDER BY orden ASC";
        return $this->select_all($sql);
    }

    public function selectFase(int $idFase)
    {
        $sql = "SELECT * FROM torneo_fases WHERE id_fase = $idFase";
        return $this->select($sql);
    }

    public function deleteFase(int $idFase)
    {
        $sql = "DELETE FROM torneo_fases WHERE id_fase = $idFase";
        return $this->delete($sql);
    }

    // --- Gestión de Grupos ---
    public function selectGruposFase(int $idFase)
    {
        $sql = "SELECT * FROM fase_grupos WHERE id_fase = $idFase";
        return $this->select_all($sql);
    }

    public function insertGrupo(int $idFase, string $nombre)
    {
        $query = "INSERT INTO fase_grupos(id_fase, nombre) VALUES(?,?)";
        return $this->insert($query, [$idFase, $nombre]);
    }

    public function updateGrupo(int $idGrupo, string $nombre)
    {
        $sql = "UPDATE fase_grupos SET nombre = ? WHERE id_grupo = ?";
        return $this->update($sql, [$nombre, $idGrupo]);
    }

    public function desvincularEquiposGrupo(int $idGrupo)
    {
        $sql = "DELETE FROM fase_grupo_equipos WHERE id_grupo = $idGrupo";
        return $this->delete($sql);
    }

    public function vincularEquipoGrupo(int $idGrupo, int $idEquipo)
    {
        $query = "INSERT INTO fase_grupo_equipos(id_grupo, id_equipo) VALUES(?,?)";
        return $this->update($query, [$idGrupo, $idEquipo]); // update() used for non-auto-inc keys
    }

    public function selectEquiposDelGrupo(int $idGrupo)
    {
        $sql = "SELECT id_equipo FROM fase_grupo_equipos WHERE id_grupo = $idGrupo";
        return $this->select_all($sql);
    }

    public function deleteGrupo(int $idGrupo)
    {
        $sql = "DELETE FROM fase_grupos WHERE id_grupo = $idGrupo";
        return $this->delete($sql);
    }

    // --- Gestión de Eventos del Partido ---
    public function insertEvento(int $idPartido, int $idJugador, int $idEquipo, string $tipo, int $minuto, string $obs = '')
    {
        $query = "INSERT INTO partido_eventos(id_partido, id_jugador, id_equipo, tipo_evento, minuto, observacion) VALUES(?,?,?,?,?,?)";
        return $this->insert($query, [$idPartido, $idJugador, $idEquipo, $tipo, $minuto, $obs]);
    }

    public function deleteEventosPartido(int $idPartido)
    {
        $sql = "DELETE FROM partido_eventos WHERE id_partido = $idPartido";
        return $this->delete($sql);
    }

    public function selectEventosPartido(int $idPartido)
    {
        $sql = "SELECT e.*, p.nombres, p.apellidos 
                FROM partido_eventos e
                INNER JOIN jugadores j ON e.id_jugador = j.id_jugador
                INNER JOIN personas p ON j.id_persona = p.id_persona
                WHERE e.id_partido = $idPartido ORDER BY e.minuto ASC";
        return $this->select_all($sql);
    }

    public function selectNominaMatch(int $idTorneo, int $idEquipo)
    {
        $sql = "SELECT j.id_jugador, p.nombres, p.apellidos, ej.dorsal 
                FROM equipo_jugadores ej 
                INNER JOIN jugadores j ON ej.id_jugador = j.id_jugador
                INNER JOIN personas p ON j.id_persona = p.id_persona
                WHERE ej.id_torneo = $idTorneo AND ej.id_equipo = $idEquipo AND j.estado != 0";
        return $this->select_all($sql);
    }

    public function selectSancionados(int $idTorneo)
    {
        // 1. Jugadores con acumulación de 3 Amarillas
        $sqlYellow = "SELECT id_jugador, COUNT(*) as total_amarillas 
                      FROM partido_eventos 
                      WHERE tipo_evento = 'AMARILLA' AND id_partido IN (SELECT id_partido FROM partidos WHERE id_torneo = $idTorneo)
                      GROUP BY id_jugador HAVING total_amarillas >= 3";

        // 2. Jugadores con Roja Directa en el torneo
        $sqlRed = "SELECT DISTINCT id_jugador FROM partido_eventos 
                   WHERE tipo_evento = 'ROJA' AND id_partido IN (SELECT id_partido FROM partidos WHERE id_torneo = $idTorneo)";

        return [
            'amarillas' => $this->select_all($sqlYellow),
            'rojas' => $this->select_all($sqlRed)
        ];
    }

    public function selectEquiposEnGrupo(int $idGrupo)
    {
        $sql = "SELECT e.id_equipo, e.nombre 
                FROM fase_grupo_equipos fge
                INNER JOIN equipos e ON fge.id_equipo = e.id_equipo
                WHERE fge.id_grupo = $idGrupo";
        return $this->select_all($sql);
    }

    // --- Generación de Partidos ---
    public function insertPartido(int $idTorneo, int $idFase, int $idGrupo, $idLocal, $idVisitante, int $jornada)
    {
        $query = "INSERT INTO partidos(id_torneo, id_fase, id_grupo, id_local, id_visitante, nro_jornada, estado) 
                  VALUES(?,?,?,?,?,?,'PENDIENTE')";
        return $this->insert($query, [$idTorneo, $idFase, $idGrupo, $idLocal, $idVisitante, $jornada]);
    }

    public function deletePartidosFase(int $idFase)
    {
        $sql = "DELETE FROM partidos WHERE id_fase = $idFase";
        return $this->delete($sql);
    }

    public function selectPartidosFase(int $idFase)
    {
        $sql = "SELECT p.*, el.nombre as local, ev.nombre as visitante, el.escudo as logo_local, ev.escudo as logo_visitante 
                FROM partidos p
                LEFT JOIN equipos el ON p.id_local = el.id_equipo
                LEFT JOIN equipos ev ON p.id_visitante = ev.id_equipo
                WHERE p.id_fase = $idFase ORDER BY p.nro_jornada ASC, p.id_partido ASC";
        return $this->select_all($sql);
    }

    public function selectPartidosGrupo(int $idGrupo)
    {
        $sql = "SELECT p.*, el.nombre as local, ev.nombre as visitante, el.escudo as logo_local, ev.escudo as logo_visitante 
                FROM partidos p
                LEFT JOIN equipos el ON p.id_local = el.id_equipo
                LEFT JOIN equipos ev ON p.id_visitante = ev.id_equipo
                WHERE p.id_grupo = $idGrupo ORDER BY p.nro_jornada ASC, p.id_partido ASC";
        return $this->select_all($sql);
    }

    public function selectPartido(int $idPartido)
    {
        $sql = "SELECT p.*, el.nombre as local, ev.nombre as visitante, el.escudo as logo_local, ev.escudo as logo_visitante 
                FROM partidos p
                LEFT JOIN equipos el ON p.id_local = el.id_equipo
                LEFT JOIN equipos ev ON p.id_visitante = ev.id_equipo
                WHERE p.id_partido = $idPartido";
        return $this->select($sql);
    }

    public function updateResultado(int $idPartido, int $golesLocal, int $golesVisitante, string $estado)
    {
        $sql = "UPDATE partidos SET goles_local = ?, goles_visitante = ?, estado = ? WHERE id_partido = ?";
        return $this->update($sql, [$golesLocal, $golesVisitante, $estado, $idPartido]);
    }
}
?>