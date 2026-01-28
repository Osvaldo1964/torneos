<?php
class RecibosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene el listado unificado de conceptos pendientes de cobro
     */
    public function selectPendientes(int $idTorneo)
    {
        // 1. Deudas por Cuotas Mensuales (Calculando el saldo pendiente)
        $sqlCuotas = "SELECT 
                        'CUOTA' as tipo_item,
                        c.id_cuota as id_item,
                        CONCAT('CUOTA - ', 
                               CASE c.mes 
                                 WHEN 1 THEN 'Enero' WHEN 2 THEN 'Febrero' WHEN 3 THEN 'Marzo' 
                                 WHEN 4 THEN 'Abril' WHEN 5 THEN 'Mayo' WHEN 6 THEN 'Junio'
                                 WHEN 7 THEN 'Julio' WHEN 8 THEN 'Agosto' WHEN 9 THEN 'Septiembre'
                                 WHEN 10 THEN 'Octubre' WHEN 11 THEN 'Noviembre' WHEN 12 THEN 'Diciembre'
                               END, ' ', c.anio) as concepto,
                        (c.monto - c.pago_acumulado) as monto,
                        c.fecha_vencimiento as fecha,
                        p.nombres, p.apellidos,
                        e.nombre as equipo,
                        c.id_jugador,
                        c.id_equipo,
                        c.monto as monto_total_original,
                        c.pago_acumulado,
                        p.email
                    FROM cuotas_jugadores c
                    INNER JOIN jugadores j ON c.id_jugador = j.id_jugador
                    INNER JOIN personas p ON j.id_persona = p.id_persona
                    INNER JOIN equipos e ON c.id_equipo = e.id_equipo
                    WHERE c.id_torneo = $idTorneo AND c.estado IN ('PENDIENTE', 'VENCIDO', 'PARCIAL')
                    AND (c.monto - c.pago_acumulado) > 0";

        // 2. Deudas por Sanciones Económicas (Calculando el saldo pendiente)
        $sqlSanciones = "SELECT 
                            'SANCION' as tipo_item,
                            s.id_sancion as id_item,
                            s.concepto,
                            (s.monto - s.pago_acumulado) as monto,
                            s.fecha_sancion as fecha,
                            IFNULL(p.nombres, 'GENERAL') as nombres, 
                            IFNULL(p.apellidos, '') as apellidos,
                            e.nombre as equipo,
                            s.id_jugador,
                            s.id_equipo,
                            s.monto as monto_total_original,
                            s.pago_acumulado,
                            p.email
                        FROM sanciones_economicas s
                        LEFT JOIN jugadores j ON s.id_jugador = j.id_jugador
                        LEFT JOIN personas p ON j.id_persona = p.id_persona
                        INNER JOIN equipos e ON s.id_equipo = e.id_equipo
                        WHERE s.id_torneo = $idTorneo AND s.estado IN ('PENDIENTE', 'PARCIAL')
                        AND (s.monto - s.pago_acumulado) > 0";

        $query = "($sqlCuotas) UNION ALL ($sqlSanciones) ORDER BY equipo ASC, nombres ASC";
        return $this->select_all($query);
    }

    /**
     * Inserta un recibo y registra su detalle
     */
    public function insertRecibo(int $idTorneo, string $pagador, string $formaPago, string $referencia, string $obs, int $idUsuario, array $items)
    {
        // 1. Generar número de recibo (REC-XXXXXX)
        $sqlNum = "SELECT COUNT(*) as total FROM recibos_ingreso WHERE id_torneo = $idTorneo";
        $resNum = $this->select($sqlNum);
        $consecutivo = $resNum['total'] + 1;
        $numeroRecibo = "REC-" . str_pad($consecutivo, 5, "0", STR_PAD_LEFT);

        // 2. Calcular total
        $total = 0;
        foreach ($items as $item) {
            $total += $item['monto'];
        }

        // 3. Insertar Cabecera del Recibo
        $query = "INSERT INTO recibos_ingreso(id_torneo, numero_recibo, pagador, total, forma_pago, referencia, fecha_pago, id_usuario_registro, observaciones) 
                  VALUES(?,?,?,?,?,?,?,?,?)";
        $arrData = array($idTorneo, $numeroRecibo, $pagador, $total, $formaPago, $referencia, date('Y-m-d'), $idUsuario, $obs);
        $idRecibo = $this->insert($query, $arrData);

        if ($idRecibo > 0) {
            // 4. Insertar Detalles y Actualizar Estados
            foreach ($items as $item) {
                $queryDetalle = "INSERT INTO recibos_detalle(id_recibo, tipo_item, id_item, concepto, monto) VALUES(?,?,?,?,?)";
                $this->insert($queryDetalle, array($idRecibo, $item['tipo'], $item['id'], $item['concepto'], $item['monto']));

                if ($item['tipo'] == 'CUOTA') {
                    // Obtener monto total para ver si completa el pago
                    $idItem = intval($item['id']);
                    $dataOrig = $this->select("SELECT monto, pago_acumulado FROM cuotas_jugadores WHERE id_cuota = " . $idItem);

                    if ($dataOrig) {
                        $nuevoAcumulado = $dataOrig['pago_acumulado'] + $item['monto'];
                        $nuevoEstado = ($nuevoAcumulado >= $dataOrig['monto']) ? 'PAGADO' : 'PARCIAL';

                        $upd = "UPDATE cuotas_jugadores SET estado = ?, fecha_pago = ?, id_recibo = ?, pago_acumulado = ? WHERE id_cuota = ?";
                        $this->update($upd, array($nuevoEstado, date('Y-m-d'), $idRecibo, $nuevoAcumulado, $idItem));
                    }
                } elseif ($item['tipo'] == 'SANCION') {
                    // Obtener monto total para ver si completa el pago
                    $idItem = intval($item['id']);
                    $dataOrig = $this->select("SELECT monto, pago_acumulado FROM sanciones_economicas WHERE id_sancion = " . $idItem);

                    if ($dataOrig) {
                        $nuevoAcumulado = $dataOrig['pago_acumulado'] + $item['monto'];
                        $nuevoEstado = ($nuevoAcumulado >= $dataOrig['monto']) ? 'PAGADO' : 'PARCIAL';

                        $upd = "UPDATE sanciones_economicas SET estado = ?, fecha_pago = ?, id_recibo = ?, pago_acumulado = ? WHERE id_sancion = ?";
                        $this->update($upd, array($nuevoEstado, date('Y-m-d'), $idRecibo, $nuevoAcumulado, $idItem));
                    }
                }
            }
        }

        return $idRecibo;
    }

    public function selectRecibos(int $idTorneo)
    {
        $sql = "SELECT r.*, p.nombres as usuario_nombre, p.apellidos as usuario_apellido 
                FROM recibos_ingreso r
                LEFT JOIN personas p ON r.id_usuario_registro = p.id_persona
                WHERE r.id_torneo = $idTorneo ORDER BY r.id_recibo DESC";
        return $this->select_all($sql);
    }

    public function selectRecibo(int $idRecibo)
    {
        $sql = "SELECT r.*, p.nombres as usuario_nombre, p.apellidos as usuario_apellido 
                FROM recibos_ingreso r
                LEFT JOIN personas p ON r.id_usuario_registro = p.id_persona
                WHERE r.id_recibo = $idRecibo";
        $recibo = $this->select($sql);

        if ($recibo) {
            $sqlDet = "SELECT * FROM recibos_detalle WHERE id_recibo = $idRecibo";
            $recibo['detalle'] = $this->select_all($sqlDet);
        }
        return $recibo;
    }

    public function anularRecibo(int $idRecibo, string $motivo)
    {
        // 1. Marcar recibo como anulado
        $sql = "UPDATE recibos_ingreso SET estado = 'ANULADO', motivo_anulacion = ? WHERE id_recibo = $idRecibo";
        $res = $this->update($sql, array($motivo));

        if ($res) {
            // 2. Descontar abonos y restaurar estados
            $detalles = $this->select_all("SELECT * FROM recibos_detalle WHERE id_recibo = $idRecibo");
            foreach ($detalles as $det) {
                if ($det['tipo_item'] == 'CUOTA') {
                    $item = $this->select("SELECT pago_acumulado FROM cuotas_jugadores WHERE id_cuota = " . $det['id_item']);
                    $nuevoAcumulado = max(0, $item['pago_acumulado'] - $det['monto']);
                    $nuevoEstado = ($nuevoAcumulado > 0) ? 'PARCIAL' : 'PENDIENTE';

                    $this->update(
                        "UPDATE cuotas_jugadores SET estado = ?, fecha_pago = NULL, id_recibo = NULL, pago_acumulado = ? WHERE id_cuota = ?",
                        array($nuevoEstado, $nuevoAcumulado, $det['id_item'])
                    );
                } elseif ($det['tipo_item'] == 'SANCION') {
                    $item = $this->select("SELECT pago_acumulado FROM sanciones_economicas WHERE id_sancion = " . $det['id_item']);
                    $nuevoAcumulado = max(0, $item['pago_acumulado'] - $det['monto']);
                    $nuevoEstado = ($nuevoAcumulado > 0) ? 'PARCIAL' : 'PENDIENTE';

                    $this->update(
                        "UPDATE sanciones_economicas SET estado = ?, fecha_pago = NULL, id_recibo = NULL, pago_acumulado = ? WHERE id_sancion = ?",
                        array($nuevoEstado, $nuevoAcumulado, $det['id_item'])
                    );
                }
            }
        }
        return $res;
    }
}
