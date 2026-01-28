<?php
class CuotasModel extends Mysql
{
    /**
     * Obtiene la configuración de cuotas de un torneo
     */
    public function getConfiguracion($idTorneo)
    {
        $sql = "SELECT * FROM configuracion_cuotas 
                WHERE id_torneo = $idTorneo 
                AND estado = 1";
        return $this->select($sql);
    }

    /**
     * Crea o actualiza la configuración de cuotas de un torneo
     */
    public function guardarConfiguracion($idTorneo, $montoMensual, $diaVencimiento)
    {
        // Verificar si ya existe configuración
        $existe = $this->getConfiguracion($idTorneo);

        if ($existe) {
            // Actualizar
            $sql = "UPDATE configuracion_cuotas 
                    SET monto_mensual = ?, 
                        dia_vencimiento = ? 
                    WHERE id_torneo = ?";
            $arrValues = array($montoMensual, $diaVencimiento, $idTorneo);
            return $this->update($sql, $arrValues);
        } else {
            // Insertar
            $sql = "INSERT INTO configuracion_cuotas (id_torneo, monto_mensual, dia_vencimiento, estado) 
                    VALUES (?, ?, ?, ?)";
            $arrValues = array($idTorneo, $montoMensual, $diaVencimiento, 1);
            return $this->insert($sql, $arrValues);
        }
    }

    /**
     * Lista todas las cuotas de un torneo con filtros opcionales
     */
    public function listarCuotas($idTorneo, $estado = null, $idJugador = null, $idEquipo = null)
    {
        $sql = "SELECT 
                    c.id_cuota,
                    c.id_jugador,
                    c.id_equipo,
                    c.mes,
                    c.anio,
                    c.monto,
                    c.fecha_vencimiento,
                    c.estado,
                    c.fecha_pago,
                    c.id_recibo,
                    c.observaciones,
                    p.nombres,
                    p.apellidos,
                    e.nombre as equipo,
                    r.numero_recibo
                FROM cuotas_jugadores c
                INNER JOIN jugadores j ON c.id_jugador = j.id_jugador
                INNER JOIN personas p ON j.id_persona = p.id_persona
                INNER JOIN equipos e ON c.id_equipo = e.id_equipo
                LEFT JOIN recibos_ingreso r ON c.id_recibo = r.id_recibo
                WHERE c.id_torneo = $idTorneo";

        if ($estado) {
            $sql .= " AND c.estado = '$estado'";
        }

        if ($idJugador) {
            $sql .= " AND c.id_jugador = $idJugador";
        }

        if ($idEquipo) {
            $sql .= " AND c.id_equipo = $idEquipo";
        }

        $sql .= " ORDER BY c.anio DESC, c.mes DESC, p.apellidos ASC";

        return $this->select_all($sql);
    }

    /**
     * Genera cuotas para un jugador inscrito en un torneo
     */
    public function generarCuotasJugador($idTorneo, $idJugador, $idEquipo, $fechaInicio, $fechaFin)
    {
        // Obtener configuración del torneo
        $config = $this->getConfiguracion($idTorneo);

        if (!$config) {
            return false; // No hay configuración
        }

        $montoMensual = $config['monto_mensual'];
        $diaVencimiento = $config['dia_vencimiento'];

        // Calcular meses entre fecha inicio y fin
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);

        // Asegurarnos de que el fin sea al menos igual al inicio
        if ($fin < $inicio) $fin = clone $inicio;

        $interval = $inicio->diff($fin);
        $meses = ($interval->y * 12) + $interval->m + 1; // +1 para incluir el mes actual

        // Generar cuotas
        $mesActual = (int)$inicio->format('m');
        $anioActual = (int)$inicio->format('Y');

        for ($i = 0; $i < $meses; $i++) {
            // Calcular fecha de vencimiento
            $fechaVencimiento = "$anioActual-" . str_pad($mesActual, 2, '0', STR_PAD_LEFT) . "-" . str_pad($diaVencimiento, 2, '0', STR_PAD_LEFT);

            // Verificar si ya existe la cuota
            $sqlCheck = "SELECT id_cuota FROM cuotas_jugadores 
                         WHERE id_jugador = $idJugador 
                         AND id_torneo = $idTorneo 
                         AND mes = $mesActual 
                         AND anio = $anioActual";
            $existe = $this->select($sqlCheck);

            if (!$existe) {
                // Insertar cuota
                $sql = "INSERT INTO cuotas_jugadores 
                        (id_torneo, id_jugador, id_equipo, mes, anio, monto, fecha_vencimiento, estado) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $arrValues = array($idTorneo, $idJugador, $idEquipo, $mesActual, $anioActual, $montoMensual, $fechaVencimiento, 'PENDIENTE');
                $this->insert($sql, $arrValues);
            }

            // Avanzar al siguiente mes
            $mesActual++;
            if ($mesActual > 12) {
                $mesActual = 1;
                $anioActual++;
            }
        }

        return true;
    }

    /**
     * Genera cuotas para TODOS los jugadores inscritos en un torneo
     */
    public function generarCuotasTorneo($idTorneo)
    {
        // 1. Obtener fechas del torneo
        $sqlTorneo = "SELECT fecha_inicio, fecha_fin FROM torneos WHERE id_torneo = $idTorneo";
        $torneo = $this->select($sqlTorneo);

        if (!$torneo) return ["status" => false, "msg" => "Torneo no encontrado"];
        if (empty($torneo['fecha_inicio']) || empty($torneo['fecha_fin'])) {
            return ["status" => false, "msg" => "El torneo no tiene fechas de inicio/fin configuradas"];
        }

        // 2. Obtener todos los jugadores en nóminas de este torneo
        $sqlNominas = "SELECT id_jugador, id_equipo FROM equipo_jugadores WHERE id_torneo = $idTorneo";
        $nominas = $this->select_all($sqlNominas);

        if (empty($nominas)) {
            return ["status" => false, "msg" => "No hay jugadores inscritos en las nóminas de este torneo"];
        }

        // 3. Generar cuotas para cada uno
        $cont = 0;
        foreach ($nominas as $nom) {
            $res = $this->generarCuotasJugador($idTorneo, $nom['id_jugador'], $nom['id_equipo'], $torneo['fecha_inicio'], $torneo['fecha_fin']);
            if ($res) $cont++;
        }

        return ["status" => true, "msg" => "Se procesaron cuotas para $cont jugadores"];
    }

    /**
     * Marca cuotas vencidas automáticamente
     */
    public function marcarCuotasVencidas($idTorneo)
    {
        $fechaHoy = date('Y-m-d');
        $sql = "UPDATE cuotas_jugadores 
                SET estado = ? 
                WHERE id_torneo = ? 
                AND estado = ? 
                AND fecha_vencimiento < ?";
        $arrValues = array('VENCIDO', $idTorneo, 'PENDIENTE', $fechaHoy);
        return $this->update($sql, $arrValues);
    }

    /**
     * Obtiene el resumen de cuotas por estado
     */
    public function getResumenCuotas($idTorneo)
    {
        $sql = "SELECT 
                    estado,
                    COUNT(*) as cantidad,
                    SUM(monto) as total
                FROM cuotas_jugadores
                WHERE id_torneo = $idTorneo
                GROUP BY estado";
        return $this->select_all($sql);
    }

    /**
     * Obtiene una cuota específica
     */
    public function getCuota($idCuota)
    {
        $sql = "SELECT 
                    c.*,
                    p.nombres,
                    p.apellidos,
                    e.nombre as equipo
                FROM cuotas_jugadores c
                INNER JOIN jugadores j ON c.id_jugador = j.id_jugador
                INNER JOIN personas p ON j.id_persona = p.id_persona
                INNER JOIN equipos e ON c.id_equipo = e.id_equipo
                WHERE c.id_cuota = $idCuota";
        return $this->select($sql);
    }

    /**
     * Actualiza el estado de una cuota al ser pagada
     */
    public function marcarComoPagada($idCuota, $idRecibo, $fechaPago)
    {
        $sql = "UPDATE cuotas_jugadores 
                SET estado = ?, 
                    fecha_pago = ?, 
                    id_recibo = ? 
                WHERE id_cuota = ?";
        $arrValues = array('PAGADO', $fechaPago, $idRecibo, $idCuota);
        return $this->update($sql, $arrValues);
    }

    /**
     * Obtiene cuotas pendientes de un jugador
     */
    public function getCuotasPendientesJugador($idJugador, $idTorneo)
    {
        $sql = "SELECT * FROM cuotas_jugadores 
                WHERE id_jugador = $idJugador 
                AND id_torneo = $idTorneo 
                AND estado IN ('PENDIENTE', 'VENCIDO')
                ORDER BY anio ASC, mes ASC";
        return $this->select_all($sql);
    }
}
