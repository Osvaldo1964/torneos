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

        // Total de cuotas pagadas (Basado en recibos para precisión de flujo de caja)
        $sqlCuotas = "SELECT 
                        COUNT(DISTINCT rd.id_recibo) as cantidad,
                        SUM(rd.monto) as total
                      FROM recibos_detalle rd
                      INNER JOIN recibos_ingreso r ON rd.id_recibo = r.id_recibo
                      WHERE r.id_torneo = $idTorneo
                      AND r.estado = 'ACTIVO'
                      AND rd.tipo_item = 'CUOTA'";

        if ($fechaInicio && $fechaFin) {
            $sqlCuotas .= " AND r.fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $cuotas = $this->select($sqlCuotas);
        $balance['ingresos']['cuotas'] = [
            'cantidad' => $cuotas['cantidad'] ?? 0,
            'total' => $cuotas['total'] ?? 0
        ];

        // Total de sanciones pagadas
        $sqlSanciones = "SELECT 
                            COUNT(DISTINCT rd.id_recibo) as cantidad,
                            SUM(rd.monto) as total
                         FROM recibos_detalle rd
                         INNER JOIN recibos_ingreso r ON rd.id_recibo = r.id_recibo
                         WHERE r.id_torneo = $idTorneo
                         AND r.estado = 'ACTIVO'
                         AND rd.tipo_item = 'SANCION'";

        if ($fechaInicio && $fechaFin) {
            $sqlSanciones .= " AND r.fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sanciones = $this->select($sqlSanciones);
        $balance['ingresos']['sanciones'] = [
            'cantidad' => $sanciones['cantidad'] ?? 0,
            'total' => $sanciones['total'] ?? 0
        ];

        // Otros ingresos (Conceptos no clasificados como cuota/sanción o directos en recibos)
        $sqlOtros = "SELECT 
                        COUNT(*) as cantidad,
                        SUM(total) as total
                     FROM recibos_ingreso
                     WHERE id_torneo = $idTorneo
                     AND estado = 'ACTIVO'
                     AND id_recibo NOT IN (SELECT id_recibo FROM recibos_detalle WHERE tipo_item IN ('CUOTA','SANCION'))";

        if ($fechaInicio && $fechaFin) {
            $sqlOtros .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $otros = $this->select($sqlOtros);
        $balance['ingresos']['otros'] = [
            'cantidad' => $otros['cantidad'] ?? 0,
            'total' => $otros['total'] ?? 0
        ];

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

        $totalEgresos = ($arbitros['total'] ?? 0) + $totalGastos;
        $balance['egresos']['total'] = $totalEgresos;

        // ========== TOTALES ==========
        $balance['totales']['ingresos'] = $totalIngresos;
        $balance['totales']['egresos'] = $totalEgresos;
        $balance['totales']['resultado'] = $totalIngresos - $totalEgresos;
        $balance['totales']['tipo'] = $totalIngresos > $totalEgresos ? 'UTILIDAD' : ($totalIngresos < $totalEgresos ? 'PERDIDA' : 'EQUILIBRIO');

        return $balance;
    }

    public function getReporteRecaudos($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $reporte = [];

        $sqlRecibos = "SELECT 
                        forma_pago,
                        COUNT(*) as cantidad,
                        SUM(total) as total
                       FROM recibos_ingreso
                       WHERE id_torneo = $idTorneo
                       AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sqlRecibos .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sqlRecibos .= " GROUP BY forma_pago";
        $reporte['detalle'] = $this->select_all($sqlRecibos);

        $sqlTotal = "SELECT 
                        COUNT(*) as total_recibos,
                        SUM(total) as total_monto
                     FROM recibos_ingreso
                     WHERE id_torneo = $idTorneo
                     AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sqlTotal .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $reporte['total'] = $this->select($sqlTotal);
        return $reporte;
    }

    public function getReporteGastos($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $reporte = [];

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

        $totalGastos = 0;
        foreach ($reporte['gastos'] as $gasto) {
            $totalGastos += $gasto['total'];
        }
        $totalArbitros = $reporte['arbitros']['total'] ?? 0;

        $reporte['total'] = [
            'total_items' => count($reporte['gastos']) + ($reporte['arbitros']['cantidad'] > 0 ? 1 : 0),
            'total_monto' => $totalGastos + $totalArbitros
        ];

        return $reporte;
    }

    public function getComparacionTorneos($idLiga, $torneos = [])
    {
        $comparacion = [];
        if (empty($torneos))
            return [];

        $strTorneos = implode(',', $torneos);
        $sqlNames = "SELECT id_torneo, nombre FROM torneos WHERE id_torneo IN ($strTorneos)";
        $names = $this->select_all($sqlNames);

        $nameMap = [];
        foreach ($names as $n)
            $nameMap[$n['id_torneo']] = $n['nombre'];

        foreach ($torneos as $idTorneo) {
            $balance = $this->getBalance($idTorneo);
            $comparacion[] = [
                'id_torneo' => $idTorneo,
                'nombre' => $nameMap[$idTorneo] ?? 'Torneo ' . $idTorneo,
                'ingresos' => $balance['totales']['ingresos'],
                'egresos' => $balance['totales']['egresos'],
                'resultado' => $balance['totales']['resultado'],
                'tipo' => $balance['totales']['tipo']
            ];
        }

        return $comparacion;
    }

    public function getEvolucionMensual($idTorneo, $anio)
    {
        $evolucion = [];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        for ($mes = 1; $mes <= 12; $mes++) {
            $fechaInicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
            $ultimoDia = date('t', strtotime($fechaInicio));
            $fechaFin = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-$ultimoDia";

            $balance = $this->getBalance($idTorneo, $fechaInicio, $fechaFin);

            $evolucion[] = [
                'mes' => $mes,
                'mes_nombre' => $meses[$mes - 1],
                'ingresos' => $balance['totales']['ingresos'],
                'egresos' => $balance['totales']['egresos'],
                'resultado' => $balance['totales']['resultado']
            ];
        }

        return $evolucion;
    }

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

