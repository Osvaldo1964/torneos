<?php
class PagosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listarGastos($idTorneo)
    {
        $sql = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) as usuario 
                FROM pagos_gastos p
                INNER JOIN personas u ON p.usuario_registro = u.id_persona
                WHERE p.id_torneo = $idTorneo
                ORDER BY p.fecha_pago DESC";
        return $this->select_all($sql);
    }

    public function insertarGasto(int $idTorneo, string $tipo, string $concepto, string $beneficiario, float $monto, string $fecha, string $forma, string $comprobante, string $soporte, string $obs, int $usuario)
    {
        $query_insert = "INSERT INTO pagos_gastos (id_torneo, tipo_gasto, concepto, beneficiario, monto, fecha_pago, forma_pago, numero_comprobante, documento_soporte, observaciones, usuario_registro) 
                         VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $arrData = [$idTorneo, $tipo, $concepto, $beneficiario, $monto, $fecha, $forma, $comprobante, $soporte, $obs, $usuario];
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;
    }

    public function anularGasto(int $idPago, string $motivo)
    {
        $sql = "UPDATE pagos_gastos SET estado = ?, motivo_anulacion = ? WHERE id_pago = ?";
        return $this->update($sql, ["ANULADO", $motivo, $idPago]);
    }
}
