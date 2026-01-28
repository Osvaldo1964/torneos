<?php
class FinanzasModel extends Mysql
{
    /**
     * Obtiene el balance completo de un torneo
     */
    public function getBalance($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $balance = [
            'ingresos' => [],
            'egresos' => [],
            'totales' => []
        ];

        // ========== INGRESOS ==========

        // Total de cuotas pagadas
        $sqlCuotas = "SELECT 
                        COUNT(*) as cantidad,
                        SUM(monto) as total
                      FROM cuotas_jugadores
                      WHERE id_torneo = $idTorneo
                      AND estado = 'PAGADO'";

        if ($fechaInicio && $fechaFin) {
            $sqlCuotas .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $cuotas = $this->select($sqlCuotas);
        $balance['ingresos']['cuotas'] = [
            'cantidad' => $cuotas['cantidad'] ?? 0,
            'total' => $cuotas['total'] ?? 0
        ];

        // Total de sanciones pagadas
        $sqlSanciones = "SELECT 
                            COUNT(*) as cantidad,
                            SUM(monto) as total
                         FROM sanciones_economicas
                         WHERE id_torneo = $idTorneo
                         AND estado = 'PAGADO'";

        if ($fechaInicio && $fechaFin) {
            $sqlSanciones .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sanciones = $this->select($sqlSanciones);
        $balance['ingresos']['sanciones'] = [
            'cantidad' => $sanciones['cantidad'] ?? 0,
            'total' => $sanciones['total'] ?? 0
        ];

        // Otros ingresos
        $sqlOtros = "SELECT 
                        COUNT(*) as cantidad,
                        SUM(monto) as total
                     FROM recibos_ingreso
                     WHERE id_torneo = $idTorneo
                     AND tipo_ingreso = 'OTRO'
                     AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sqlOtros .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $otros = $this->select($sqlOtros);
        $balance['ingresos']['otros'] = [
            'cantidad' => $otros['cantidad'] ?? 0,
            'total' => $otros['total'] ?? 0
        ];

        // Total de ingresos
        $totalIngresos = ($cuotas['total'] ?? 0) + ($sanciones['total'] ?? 0) + ($otros['total'] ?? 0);
        $balance['ingresos']['total'] = $totalIngresos;

        // ========== EGRESOS ==========

        // Pagos a árbitros
        $sqlArbitros = "SELECT 
                            COUNT(*) as cantidad,
                            SUM(pa.monto) as total
                        FROM pagos_arbitros pa
                        INNER JOIN partidos p ON pa.id_partido = p.id_partido
                        INNER JOIN fase_grupos fg ON p.id_grupo = fg.id_grupo
                        INNER JOIN torneo_fases tf ON fg.id_fase = tf.id_fase
                        WHERE tf.id_torneo = $idTorneo
                        AND pa.estado = 'PAGADO'";

        if ($fechaInicio && $fechaFin) {
            $sqlArbitros .= " AND pa.fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $arbitros = $this->select($sqlArbitros);
        $balance['egresos']['arbitros'] = [
            'cantidad' => $arbitros['cantidad'] ?? 0,
            'total' => $arbitros['total'] ?? 0
        ];

        // Gastos por categoría
        $sqlGastos = "SELECT 
                        tipo_gasto,
                        COUNT(*) as cantidad,
                        SUM(monto) as total
                      FROM pagos_gastos
                      WHERE id_torneo = $idTorneo
                      AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sqlGastos .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sqlGastos .= " GROUP BY tipo_gasto";

        $gastos = $this->select_all($sqlGastos);
        $totalGastos = 0;

        foreach ($gastos as $gasto) {
            $tipo = strtolower($gasto['tipo_gasto']);
            $balance['egresos'][$tipo] = [
                'cantidad' => $gasto['cantidad'],
                'total' => $gasto['total']
            ];
            $totalGastos += $gasto['total'];
        }

        // Total de egresos
        $totalEgresos = ($arbitros['total'] ?? 0) + $totalGastos;
        $balance['egresos']['total'] = $totalEgresos;

        // ========== TOTALES ==========

        $balance['totales']['ingresos'] = $totalIngresos;
        $balance['totales']['egresos'] = $totalEgresos;
        $balance['totales']['resultado'] = $totalIngresos - $totalEgresos;
        $balance['totales']['tipo'] = $totalIngresos > $totalEgresos ? 'UTILIDAD' : ($totalIngresos < $totalEgresos ? 'PERDIDA' : 'EQUILIBRIO');

        return $balance;
    }

    /**
     * Obtiene reporte de recaudos (ingresos)
     */
    public function getReporteRecaudos($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $reporte = [];

        // Recibos de ingreso
        $sqlRecibos = "SELECT 
                        tipo_ingreso,
                        forma_pago,
                        COUNT(*) as cantidad,
                        SUM(monto) as total
                       FROM recibos_ingreso
                       WHERE id_torneo = $idTorneo
                       AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sqlRecibos .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sqlRecibos .= " GROUP BY tipo_ingreso, forma_pago";

        $reporte['detalle'] = $this->select_all($sqlRecibos);

        // Total general
        $sqlTotal = "SELECT 
                        COUNT(*) as total_recibos,
                        SUM(monto) as total_monto
                     FROM recibos_ingreso
                     WHERE id_torneo = $idTorneo
                     AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sqlTotal .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $reporte['total'] = $this->select($sqlTotal);

        return $reporte;
    }

    /**
     * Obtiene reporte de gastos (egresos)
     */
    public function getReporteGastos($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $reporte = [];

        // Gastos generales
        $sqlGastos = "SELECT 
                        tipo_gasto,
                        forma_pago,
                        COUNT(*) as cantidad,
                        SUM(monto) as total
                      FROM pagos_gastos
                      WHERE id_torneo = $idTorneo
                      AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sqlGastos .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sqlGastos .= " GROUP BY tipo_gasto, forma_pago";

        $reporte['gastos'] = $this->select_all($sqlGastos);

        // Pagos a árbitros
        $sqlArbitros = "SELECT 
                            COUNT(*) as cantidad,
                            SUM(pa.monto) as total
                        FROM pagos_arbitros pa
                        INNER JOIN partidos p ON pa.id_partido = p.id_partido
                        INNER JOIN fase_grupos fg ON p.id_grupo = fg.id_grupo
                        INNER JOIN torneo_fases tf ON fg.id_fase = tf.id_fase
                        WHERE tf.id_torneo = $idTorneo
                        AND pa.estado = 'PAGADO'";

        if ($fechaInicio && $fechaFin) {
            $sqlArbitros .= " AND pa.fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $reporte['arbitros'] = $this->select($sqlArbitros);

        // Total general
        $totalGastos = 0;
        foreach ($reporte['gastos'] as $gasto) {
            $totalGastos += $gasto['total'];
        }
        $totalArbitros = $reporte['arbitros']['total'] ?? 0;

        $reporte['total'] = [
            'total_gastos' => count($reporte['gastos']),
            'total_monto' => $totalGastos + $totalArbitros
        ];

        return $reporte;
    }

    /**
     * Obtiene comparación entre múltiples torneos
     */
    public function getComparacionTorneos($idLiga, $torneos = [])
    {
        $comparacion = [];

        foreach ($torneos as $idTorneo) {
            $balance = $this->getBalance($idTorneo);

            // Obtener nombre del torneo
            $sqlTorneo = "SELECT nombre FROM torneos WHERE id_torneo = $idTorneo";
            $torneo = $this->select($sqlTorneo);

            $comparacion[] = [
                'id_torneo' => $idTorneo,
                'nombre' => $torneo['nombre'] ?? 'Torneo ' . $idTorneo,
                'ingresos' => $balance['totales']['ingresos'],
                'egresos' => $balance['totales']['egresos'],
                'resultado' => $balance['totales']['resultado'],
                'tipo' => $balance['totales']['tipo']
            ];
        }

        return $comparacion;
    }

    /**
     * Obtiene evolución mensual de ingresos/egresos
     */
    public function getEvolucionMensual($idTorneo, $anio)
    {
        $evolucion = [];

        for ($mes = 1; $mes <= 12; $mes++) {
            $fechaInicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
            $ultimoDia = date('t', strtotime($fechaInicio));
            $fechaFin = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-$ultimoDia";

            $balance = $this->getBalance($idTorneo, $fechaInicio, $fechaFin);

            $evolucion[] = [
                'mes' => $mes,
                'mes_nombre' => date('F', mktime(0, 0, 0, $mes, 1)),
                'ingresos' => $balance['totales']['ingresos'],
                'egresos' => $balance['totales']['egresos'],
                'resultado' => $balance['totales']['resultado']
            ];
        }

        return $evolucion;
    }

    /**
     * Obtiene estadísticas generales del módulo financiero
     */
    public function getEstadisticas($idTorneo)
    {
        $stats = [];

        // Cuotas
        $sqlCuotas = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN estado = 'PAGADO' THEN 1 ELSE 0 END) as pagadas,
                        SUM(CASE WHEN estado = 'VENCIDO' THEN 1 ELSE 0 END) as vencidas
                      FROM cuotas_jugadores
                      WHERE id_torneo = $idTorneo";
        $stats['cuotas'] = $this->select($sqlCuotas);

        // Sanciones
        $sqlSanciones = "SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 0 END) as pendientes,
                            SUM(CASE WHEN estado = 'PAGADO' THEN 1 ELSE 0 END) as pagadas
                         FROM sanciones_economicas
                         WHERE id_torneo = $idTorneo";
        $stats['sanciones'] = $this->select($sqlSanciones);

        // Recibos
        $sqlRecibos = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'ACTIVO' THEN 1 ELSE 0 END) as activos,
                        SUM(CASE WHEN estado = 'ANULADO' THEN 1 ELSE 0 END) as anulados
                       FROM recibos_ingreso
                       WHERE id_torneo = $idTorneo";
        $stats['recibos'] = $this->select($sqlRecibos);

        // Gastos
        $sqlGastos = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'ACTIVO' THEN 1 ELSE 0 END) as activos,
                        SUM(CASE WHEN estado = 'ANULADO' THEN 1 ELSE 0 END) as anulados
                      FROM pagos_gastos
                      WHERE id_torneo = $idTorneo";
        $stats['gastos'] = $this->select($sqlGastos);

        return $stats;
    }
}
