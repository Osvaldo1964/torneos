<?php
class SancionesModel extends Mysql
{
    /**
     * Obtiene la configuración de sanciones de un torneo
     */
    public function getConfiguracion($idTorneo)
    {
        $sql = "SELECT * FROM configuracion_sanciones 
                WHERE id_torneo = $idTorneo 
                AND estado = 1";
        return $this->select($sql);
    }

    /**
     * Crea o actualiza la configuración de sanciones de un torneo
     */
    public function guardarConfiguracion($idTorneo, $montoAmarilla, $montoRoja)
    {
        // Verificar si ya existe configuración
        $existe = $this->getConfiguracion($idTorneo);

        if ($existe) {
            // Actualizar
            $sql = "UPDATE configuracion_sanciones 
                    SET monto_amarilla = ?, 
                        monto_roja = ? 
                    WHERE id_torneo = ?";
            $arrValues = array($montoAmarilla, $montoRoja, $idTorneo);
            return $this->update($sql, $arrValues);
        } else {
            // Insertar
            $sql = "INSERT INTO configuracion_sanciones (id_torneo, monto_amarilla, monto_roja, estado) 
                    VALUES (?, ?, ?, ?)";
            $arrValues = array($idTorneo, $montoAmarilla, $montoRoja, 1);
            return $this->insert($sql, $arrValues);
        }
    }

    /**
     * Lista todas las sanciones de un torneo con filtros opcionales
     */
    public function listarSanciones($idTorneo, $estado = null, $tipo = null, $idJugador = null, $idEquipo = null)
    {
        $sql = "SELECT 
                    s.id_sancion,
                    s.tipo_sancion,
                    s.concepto,
                    s.monto,
                    s.estado,
                    s.fecha_sancion,
                    s.fecha_pago,
                    s.id_recibo,
                    s.observaciones,
                    p.nombres,
                    p.apellidos,
                    e.nombre as equipo,
                    r.numero_recibo,
                    pa.fecha_partido
                FROM sanciones_economicas s
                LEFT JOIN jugadores j ON s.id_jugador = j.id_jugador
                LEFT JOIN personas p ON j.id_persona = p.id_persona
                LEFT JOIN equipos e ON s.id_equipo = e.id_equipo
                LEFT JOIN recibos_ingreso r ON s.id_recibo = r.id_recibo
                LEFT JOIN partidos pa ON s.id_partido = pa.id_partido
                WHERE s.id_torneo = $idTorneo";

        if ($estado) {
            $sql .= " AND s.estado = '$estado'";
        }

        if ($tipo) {
            $sql .= " AND s.tipo_sancion = '$tipo'";
        }

        if ($idJugador) {
            $sql .= " AND s.id_jugador = $idJugador";
        }

        if ($idEquipo) {
            $sql .= " AND s.id_equipo = $idEquipo";
        }

        $sql .= " ORDER BY s.fecha_sancion DESC";

        return $this->select_all($sql);
    }

    /**
     * Crea una sanción económica
     */
    public function crearSancion($data)
    {
        $idTorneo = $data['id_torneo'];
        $tipoSancion = $data['tipo_sancion'];
        $idEquipo = $data['id_equipo'] ?? null;
        $idJugador = $data['id_jugador'] ?? null;
        $idPartido = $data['id_partido'] ?? null;
        $concepto = $this->strClean($data['concepto']);
        $monto = $data['monto'];
        $fechaSancion = $data['fecha_sancion'];
        $observaciones = isset($data['observaciones']) ? $this->strClean($data['observaciones']) : '';

        $sql = "INSERT INTO sanciones_economicas 
                (id_torneo, tipo_sancion, id_equipo, id_jugador, id_partido, concepto, monto, fecha_sancion, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $arrValues = array($idTorneo, $tipoSancion, $idEquipo, $idJugador, $idPartido, $concepto, $monto, $fechaSancion, $observaciones);

        return $this->insert($sql, $arrValues);
    }

    /**
     * Genera sanción automática por tarjeta
     */
    public function generarSancionTarjeta($idTorneo, $idPartido, $idJugador, $idEquipo, $tipoTarjeta)
    {
        // Obtener configuración del torneo
        $config = $this->getConfiguracion($idTorneo);

        if (!$config) {
            return false; // No hay configuración
        }

        // Determinar monto según tipo de tarjeta
        $monto = 0;
        $tipoSancion = '';
        $concepto = '';

        if ($tipoTarjeta === 'AMARILLA') {
            $monto = $config['monto_amarilla'];
            $tipoSancion = 'AMARILLA';
            $concepto = 'Sanción por tarjeta amarilla';
        } elseif ($tipoTarjeta === 'ROJA') {
            $monto = $config['monto_roja'];
            $tipoSancion = 'ROJA';
            $concepto = 'Sanción por tarjeta roja';
        }

        // Si el monto es 0, no se genera sanción
        if ($monto <= 0) {
            return false;
        }

        // Obtener fecha del partido
        $sqlPartido = "SELECT fecha_partido FROM partidos WHERE id_partido = $idPartido";
        $partido = $this->select($sqlPartido);
        $fechaSancion = $partido['fecha_partido'] ?? date('Y-m-d');

        // Verificar si ya existe la sanción
        $sqlCheck = "SELECT id_sancion FROM sanciones_economicas 
                     WHERE id_partido = $idPartido 
                     AND id_jugador = $idJugador 
                     AND tipo_sancion = '$tipoSancion'";
        $existe = $this->select($sqlCheck);

        if ($existe) {
            return false; // Ya existe la sanción
        }

        // Crear sanción
        $sql = "INSERT INTO sanciones_economicas 
                (id_torneo, tipo_sancion, id_equipo, id_jugador, id_partido, concepto, monto, fecha_sancion, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $arrValues = array($idTorneo, $tipoSancion, $idEquipo, $idJugador, $idPartido, $concepto, $monto, $fechaSancion, 'PENDIENTE');

        return $this->insert($sql, $arrValues);
    }

    /**
     * Obtiene una sanción específica
     */
    public function getSancion($idSancion)
    {
        $sql = "SELECT 
                    s.*,
                    p.nombres,
                    p.apellidos,
                    e.nombre as equipo
                FROM sanciones_economicas s
                LEFT JOIN jugadores j ON s.id_jugador = j.id_jugador
                LEFT JOIN personas p ON j.id_persona = p.id_persona
                LEFT JOIN equipos e ON s.id_equipo = e.id_equipo
                WHERE s.id_sancion = $idSancion";
        return $this->select($sql);
    }

    /**
     * Actualiza el estado de una sanción al ser pagada
     */
    public function marcarComoPagada($idSancion, $idRecibo, $fechaPago)
    {
        $sql = "UPDATE sanciones_economicas 
                SET estado = ?, 
                    fecha_pago = ?, 
                    id_recibo = ? 
                WHERE id_sancion = ?";
        $arrValues = array('PAGADO', $fechaPago, $idRecibo, $idSancion);
        return $this->update($sql, $arrValues);
    }

    /**
     * Anula una sanción
     */
    public function anularSancion($idSancion, $observaciones)
    {
        $observaciones = $this->strClean($observaciones);
        $sql = "UPDATE sanciones_economicas 
                SET estado = ?, 
                    observaciones = CONCAT(observaciones, ?) 
                WHERE id_sancion = ?";
        $extraObs = " | ANULADO: $observaciones";
        $arrValues = array('ANULADO', $extraObs, $idSancion);
        return $this->update($sql, $arrValues);
    }

    /**
     * Obtiene el resumen de sanciones por estado
     */
    public function getResumenSanciones($idTorneo)
    {
        $sql = "SELECT 
                    estado,
                    COUNT(*) as cantidad,
                    SUM(monto) as total
                FROM sanciones_economicas
                WHERE id_torneo = $idTorneo
                GROUP BY estado";
        return $this->select_all($sql);
    }

    /**
     * Obtiene sanciones pendientes de un jugador
     */
    public function getSancionesPendientesJugador($idJugador, $idTorneo)
    {
        $sql = "SELECT * FROM sanciones_economicas 
                WHERE id_jugador = $idJugador 
                AND id_torneo = $idTorneo 
                AND estado = 'PENDIENTE'
                ORDER BY fecha_sancion ASC";
        return $this->select_all($sql);
    }

    /**
     * Obtiene sanciones pendientes de un equipo
     */
    public function getSancionesPendientesEquipo($idEquipo, $idTorneo)
    {
        $sql = "SELECT * FROM sanciones_economicas 
                WHERE id_equipo = $idEquipo 
                AND id_torneo = $idTorneo 
                AND estado = 'PENDIENTE'
                ORDER BY fecha_sancion ASC";
        return $this->select_all($sql);
    }
}
