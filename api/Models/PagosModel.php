<?php
class PagosModel extends Mysql
{
    /**
     * Lista todos los gastos de un torneo con filtros opcionales
     */
    public function listarGastos($idTorneo, $estado = null, $tipoGasto = null, $fechaInicio = null, $fechaFin = null)
    {
        $sql = "SELECT 
                    pg.id_pago,
                    pg.tipo_gasto,
                    pg.concepto,
                    pg.beneficiario,
                    pg.monto,
                    pg.fecha_pago,
                    pg.numero_comprobante,
                    pg.forma_pago,
                    pg.documento_soporte,
                    pg.estado,
                    pg.observaciones,
                    p.nombres as usuario_nombres,
                    p.apellidos as usuario_apellidos
                FROM pagos_gastos pg
                INNER JOIN personas p ON pg.usuario_registro = p.id_persona
                WHERE pg.id_torneo = $idTorneo";

        if ($estado) {
            $sql .= " AND pg.estado = '$estado'";
        }

        if ($tipoGasto) {
            $sql .= " AND pg.tipo_gasto = '$tipoGasto'";
        }

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND pg.fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sql .= " ORDER BY pg.fecha_pago DESC";

        return $this->select_all($sql);
    }

    /**
     * Obtiene un gasto específico
     */
    public function getGasto($idPago)
    {
        $sql = "SELECT 
                    pg.*,
                    p.nombres as usuario_nombres,
                    p.apellidos as usuario_apellidos,
                    t.nombre as torneo
                FROM pagos_gastos pg
                INNER JOIN personas p ON pg.usuario_registro = p.id_persona
                INNER JOIN torneos t ON pg.id_torneo = t.id_torneo
                WHERE pg.id_pago = $idPago";
        return $this->select($sql);
    }

    /**
     * Crea un registro de gasto
     */
    public function crearGasto($data)
    {
        $idTorneo = $data['id_torneo'];
        $tipoGasto = $data['tipo_gasto'];
        $concepto = $this->strClean($data['concepto']);
        $beneficiario = $this->strClean($data['beneficiario']);
        $monto = $data['monto'];
        $fechaPago = $data['fecha_pago'];
        $numeroComprobante = isset($data['numero_comprobante']) ? $this->strClean($data['numero_comprobante']) : '';
        $formaPago = $data['forma_pago'];
        $documentoSoporte = isset($data['documento_soporte']) ? $this->strClean($data['documento_soporte']) : '';
        $observaciones = isset($data['observaciones']) ? $this->strClean($data['observaciones']) : '';
        $usuarioRegistro = $data['usuario_registro'];

        $sql = "INSERT INTO pagos_gastos 
                (id_torneo, tipo_gasto, concepto, beneficiario, monto, fecha_pago, 
                 numero_comprobante, forma_pago, documento_soporte, observaciones, usuario_registro) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $arrValues = array(
            $idTorneo,
            $tipoGasto,
            $concepto,
            $beneficiario,
            $monto,
            $fechaPago,
            $numeroComprobante,
            $formaPago,
            $documentoSoporte,
            $observaciones,
            $usuarioRegistro
        );

        return $this->insert($sql, $arrValues);
    }

    /**
     * Actualiza un gasto
     */
    public function actualizarGasto($idPago, $data)
    {
        $tipoGasto = $data['tipo_gasto'];
        $concepto = $this->strClean($data['concepto']);
        $beneficiario = $this->strClean($data['beneficiario']);
        $monto = $data['monto'];
        $fechaPago = $data['fecha_pago'];
        $numeroComprobante = isset($data['numero_comprobante']) ? $this->strClean($data['numero_comprobante']) : '';
        $formaPago = $data['forma_pago'];
        $documentoSoporte = isset($data['documento_soporte']) ? $this->strClean($data['documento_soporte']) : '';
        $observaciones = isset($data['observaciones']) ? $this->strClean($data['observaciones']) : '';

        $sql = "UPDATE pagos_gastos 
                SET tipo_gasto = ?, 
                    concepto = ?, 
                    beneficiario = ?, 
                    monto = ?, 
                    fecha_pago = ?, 
                    numero_comprobante = ?, 
                    forma_pago = ?, 
                    documento_soporte = ?, 
                    observaciones = ? 
                WHERE id_pago = ?";
        $arrValues = array(
            $tipoGasto,
            $concepto,
            $beneficiario,
            $monto,
            $fechaPago,
            $numeroComprobante,
            $formaPago,
            $documentoSoporte,
            $observaciones,
            $idPago
        );

        return $this->update($sql, $arrValues);
    }

    /**
     * Anula un gasto
     */
    public function anularGasto($idPago, $motivoAnulacion)
    {
        $motivoAnulacion = $this->strClean($motivoAnulacion);
        $sql = "UPDATE pagos_gastos 
                SET estado = ?, 
                    motivo_anulacion = ? 
                WHERE id_pago = ?";
        $arrValues = array('ANULADO', $motivoAnulacion, $idPago);
        return $this->update($sql, $arrValues);
    }

    /**
     * Obtiene el total de gastos por tipo
     */
    public function getTotalGastosPorTipo($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $sql = "SELECT 
                    tipo_gasto,
                    COUNT(*) as cantidad,
                    SUM(monto) as total
                FROM pagos_gastos
                WHERE id_torneo = $idTorneo
                AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sql .= " GROUP BY tipo_gasto";

        return $this->select_all($sql);
    }

    /**
     * Obtiene el total de gastos por forma de pago
     */
    public function getTotalGastosPorFormaPago($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $sql = "SELECT 
                    forma_pago,
                    COUNT(*) as cantidad,
                    SUM(monto) as total
                FROM pagos_gastos
                WHERE id_torneo = $idTorneo
                AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sql .= " GROUP BY forma_pago";

        return $this->select_all($sql);
    }

    /**
     * Obtiene el total general de gastos
     */
    public function getTotalGastos($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $sql = "SELECT 
                    COUNT(*) as total_gastos,
                    SUM(monto) as total_monto
                FROM pagos_gastos
                WHERE id_torneo = $idTorneo
                AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        return $this->select($sql);
    }

    /**
     * Obtiene gastos para exportar
     */
    public function getGastosParaExportar($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $sql = "SELECT 
                    pg.fecha_pago,
                    pg.tipo_gasto,
                    pg.concepto,
                    pg.beneficiario,
                    pg.monto,
                    pg.forma_pago,
                    pg.numero_comprobante,
                    pg.estado
                FROM pagos_gastos pg
                WHERE pg.id_torneo = $idTorneo";

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND pg.fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sql .= " ORDER BY pg.fecha_pago DESC";

        return $this->select_all($sql);
    }

    /**
     * Obtiene gastos por beneficiario
     */
    public function getGastosPorBeneficiario($idTorneo, $fechaInicio = null, $fechaFin = null)
    {
        $sql = "SELECT 
                    beneficiario,
                    COUNT(*) as cantidad,
                    SUM(monto) as total
                FROM pagos_gastos
                WHERE id_torneo = $idTorneo
                AND estado = 'ACTIVO'";

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha_pago BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        $sql .= " GROUP BY beneficiario
                  ORDER BY total DESC";

        return $this->select_all($sql);
    }
}
