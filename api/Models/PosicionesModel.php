<?php
class PosicionesModel extends Mysql
{

    /**
     * Obtiene la tabla de posiciones para un grupo específico
     * Calcula: PJ, PG, PE, PP, GF, GC, DG, PTS
     */
    public function getTablaPosiciones($idGrupo)
    {
        // Primero obtenemos todos los equipos del grupo
        $sqlEquipos = "SELECT DISTINCT e.id_equipo, e.nombre, e.escudo 
                       FROM fase_grupo_equipos fge
                       INNER JOIN equipos e ON fge.id_equipo = e.id_equipo
                       WHERE fge.id_grupo = $idGrupo
                       ORDER BY e.nombre";

        $equipos = $this->select_all($sqlEquipos);

        if (empty($equipos)) {
            return [];
        }

        $tabla = [];

        foreach ($equipos as $equipo) {
            $idEquipo = $equipo['id_equipo'];

            // Partidos como LOCAL
            $sqlLocal = "SELECT 
                            COUNT(*) as partidos,
                            SUM(CASE WHEN goles_local > goles_visitante THEN 1 ELSE 0 END) as ganados,
                            SUM(CASE WHEN goles_local = goles_visitante THEN 1 ELSE 0 END) as empatados,
                            SUM(CASE WHEN goles_local < goles_visitante THEN 1 ELSE 0 END) as perdidos,
                            SUM(goles_local) as goles_favor,
                            SUM(goles_visitante) as goles_contra
                         FROM partidos
                         WHERE id_local = $idEquipo 
                         AND id_grupo = $idGrupo 
                         AND estado = 'JUGADO'";

            $statsLocal = $this->select($sqlLocal);

            // Partidos como VISITANTE
            $sqlVisitante = "SELECT 
                                COUNT(*) as partidos,
                                SUM(CASE WHEN goles_visitante > goles_local THEN 1 ELSE 0 END) as ganados,
                                SUM(CASE WHEN goles_visitante = goles_local THEN 1 ELSE 0 END) as empatados,
                                SUM(CASE WHEN goles_visitante < goles_local THEN 1 ELSE 0 END) as perdidos,
                                SUM(goles_visitante) as goles_favor,
                                SUM(goles_local) as goles_contra
                             FROM partidos
                             WHERE id_visitante = $idEquipo 
                             AND id_grupo = $idGrupo 
                             AND estado = 'JUGADO'";

            $statsVisitante = $this->select($sqlVisitante);

            // Consolidar estadísticas
            $pj = ($statsLocal['partidos'] ?? 0) + ($statsVisitante['partidos'] ?? 0);
            $pg = ($statsLocal['ganados'] ?? 0) + ($statsVisitante['ganados'] ?? 0);
            $pe = ($statsLocal['empatados'] ?? 0) + ($statsVisitante['empatados'] ?? 0);
            $pp = ($statsLocal['perdidos'] ?? 0) + ($statsVisitante['perdidos'] ?? 0);
            $gf = ($statsLocal['goles_favor'] ?? 0) + ($statsVisitante['goles_favor'] ?? 0);
            $gc = ($statsLocal['goles_contra'] ?? 0) + ($statsVisitante['goles_contra'] ?? 0);
            $dg = $gf - $gc;
            $pts = ($pg * 3) + ($pe * 1);

            $tabla[] = [
                'id_equipo' => $idEquipo,
                'equipo' => $equipo['nombre'],
                'escudo' => $equipo['escudo'],
                'pj' => $pj,
                'pg' => $pg,
                'pe' => $pe,
                'pp' => $pp,
                'gf' => $gf,
                'gc' => $gc,
                'dg' => $dg,
                'pts' => $pts
            ];
        }

        // Ordenar por: Puntos DESC, DG DESC, GF DESC
        usort($tabla, function ($a, $b) {
            if ($b['pts'] != $a['pts']) {
                return $b['pts'] - $a['pts'];
            }
            if ($b['dg'] != $a['dg']) {
                return $b['dg'] - $a['dg'];
            }
            return $b['gf'] - $a['gf'];
        });

        // Agregar posición
        $posicion = 1;
        foreach ($tabla as &$fila) {
            $fila['posicion'] = $posicion++;
        }

        return $tabla;
    }

    /**
     * Obtiene los últimos 5 resultados de un equipo en un grupo (racha)
     */
    public function getRachaEquipo($idEquipo, $idGrupo)
    {
        $sql = "SELECT 
                    p.id_partido,
                    p.id_local,
                    p.id_visitante,
                    p.goles_local,
                    p.goles_visitante,
                    p.fecha_partido,
                    el.nombre as equipo_local,
                    ev.nombre as equipo_visitante
                FROM partidos p
                INNER JOIN equipos el ON p.id_local = el.id_equipo
                INNER JOIN equipos ev ON p.id_visitante = ev.id_equipo
                WHERE (p.id_local = $idEquipo OR p.id_visitante = $idEquipo)
                AND p.id_grupo = $idGrupo
                AND p.estado = 'JUGADO'
                ORDER BY p.fecha_partido DESC
                LIMIT 5";

        $partidos = $this->select_all($sql);
        $racha = [];

        foreach ($partidos as $partido) {
            $esLocal = ($partido['id_local'] == $idEquipo);
            $golesEquipo = $esLocal ? $partido['goles_local'] : $partido['goles_visitante'];
            $golesRival = $esLocal ? $partido['goles_visitante'] : $partido['goles_local'];

            if ($golesEquipo > $golesRival) {
                $resultado = 'V'; // Victoria
            } elseif ($golesEquipo < $golesRival) {
                $resultado = 'D'; // Derrota
            } else {
                $resultado = 'E'; // Empate
            }

            $racha[] = [
                'resultado' => $resultado,
                'goles_equipo' => $golesEquipo,
                'goles_rival' => $golesRival,
                'rival' => $esLocal ? $partido['equipo_visitante'] : $partido['equipo_local'],
                'fecha' => $partido['fecha_partido']
            ];
        }

        return $racha;
    }

    /**
     * Obtiene todos los grupos de una fase
     */
    public function getGruposPorFase($idFase)
    {
        $sql = "SELECT id_grupo, nombre 
                FROM fase_grupos 
                WHERE id_fase = $idFase
                ORDER BY nombre";
        return $this->select_all($sql);
    }

    /**
     * Obtiene todas las fases de un torneo
     */
    public function getFasesPorTorneo($idTorneo)
    {
        $sql = "SELECT id_fase, nombre, tipo 
                FROM torneo_fases 
                WHERE id_torneo = $idTorneo 
                AND estado = 1
                ORDER BY orden";
        return $this->select_all($sql);
    }

    /**
     * Obtiene todos los torneos de una liga
     */
    public function getTorneosPorLiga($idLiga)
    {
        $sql = "SELECT id_torneo, nombre, categoria, estado, logo
                FROM torneos 
                WHERE id_liga = $idLiga
                ORDER BY fecha_inicio DESC";
        return $this->select_all($sql);
    }

    /**
     * Obtiene información completa de un grupo (con fase y torneo)
     */
    public function getInfoGrupo($idGrupo)
    {
        $sql = "SELECT 
                    fg.id_grupo,
                    fg.nombre as nombre_grupo,
                    tf.id_fase,
                    tf.nombre as nombre_fase,
                    tf.tipo as tipo_fase,
                    t.id_torneo,
                    t.nombre as nombre_torneo,
                    t.categoria,
                    t.id_liga,
                    l.nombre as nombre_liga
                FROM fase_grupos fg
                INNER JOIN torneo_fases tf ON fg.id_fase = tf.id_fase
                INNER JOIN torneos t ON tf.id_torneo = t.id_torneo
                INNER JOIN ligas l ON t.id_liga = l.id_liga
                WHERE fg.id_grupo = $idGrupo";

        return $this->select($sql);
    }

    /**
     * Obtiene estadísticas de goleadores de un grupo
     */
    public function getGoleadoresGrupo($idGrupo, $limit = 10)
    {
        $sql = "SELECT 
                    j.id_jugador,
                    p.nombres,
                    p.apellidos,
                    j.foto,
                    e.nombre as equipo,
                    e.escudo,
                    COUNT(pe.id_evento) as goles
                FROM partido_eventos pe
                INNER JOIN partidos pa ON pe.id_partido = pa.id_partido
                INNER JOIN jugadores j ON pe.id_jugador = j.id_jugador
                INNER JOIN personas p ON j.id_persona = p.id_persona
                INNER JOIN equipos e ON pe.id_equipo = e.id_equipo
                WHERE pa.id_grupo = $idGrupo
                AND pe.tipo_evento = 'GOL'
                AND pa.estado = 'JUGADO'
                GROUP BY j.id_jugador, p.nombres, p.apellidos, j.foto, e.nombre, e.escudo
                ORDER BY goles DESC, p.apellidos ASC
                LIMIT $limit";

        return $this->select_all($sql);
    }
}
