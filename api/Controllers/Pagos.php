<?php
class Pagos extends Controllers
{
    public $userData;

    public function __construct()
    {
        parent::__construct();
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : "";
        $jwt = new JwtHandler();
        $this->userData = $jwt->validateToken($token);
        if (!$this->userData) {
            $this->res(false, "Token inválido");
            exit;
        }
    }

    public function listar($idTorneo)
    {
        if (empty($idTorneo)) {
            return $this->res(false, "ID de torneo no válido");
        }
        try {
            $data = $this->model->listarGastos($idTorneo);
            return $this->res(true, "Listado de gastos", $data);
        } catch (Exception $e) {
            return $this->res(false, "Error de base de datos: " . $e->getMessage());
        }
    }

    public function crear()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload)
            return $this->res(false, "Datos inválidos");

        $idTorneo = intval($payload['id_torneo'] ?? 0);
        $tipo = $payload['tipo_gasto'] ?? '';
        $concepto = $payload['concepto'] ?? '';
        $beneficiario = $payload['beneficiario'] ?? '';
        $monto = floatval($payload['monto'] ?? 0);
        $fecha = $payload['fecha_pago'] ?? date('Y-m-d');
        $forma = $payload['forma_pago'] ?? 'EFECTIVO';
        $comprobante = $payload['numero_comprobante'] ?? '';
        $soporte = $payload['documento_soporte'] ?? '';
        $obs = $payload['observaciones'] ?? '';
        $idUsuario = $this->userData['id_user'];

        if ($idTorneo <= 0 || empty($concepto) || $monto <= 0) {
            return $this->res(false, "Faltan datos obligatorios o monto inválido");
        }

        try {
            $request = $this->model->insertarGasto($idTorneo, $tipo, $concepto, $beneficiario, $monto, $fecha, $forma, $comprobante, $soporte, $obs, $idUsuario);

            if ($request > 0) {
                return $this->res(true, "Gasto registrado correctamente", ["id_pago" => $request]);
            } else {
                return $this->res(false, "Error al guardar el gasto");
            }
        } catch (Exception $e) {
            return $this->res(false, "Error de base de datos: " . $e->getMessage());
        }
    }

    public function anular($idPago)
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $motivo = $payload['motivo'] ?? 'Anulación administrativa';

        if (empty($idPago))
            return $this->res(false, "ID inválido");

        $request = $this->model->anularGasto($idPago, $motivo);
        if ($request) {
            return $this->res(true, "Gasto anulado correctamente");
        } else {
            return $this->res(false, "Error al anular el gasto");
        }
    }
}
