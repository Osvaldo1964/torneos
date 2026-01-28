<?php
require_once("Models/PagosModel.php");

class Pagos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET: Lista los gastos de un torneo
     * Endpoint: Pagos/listar/{idTorneo}
     */
    public function listar($params)
    {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        if (empty($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
            return;
        }

        $jwt = new JwtHandler();
        $jwtData = $jwt->validateToken($token);
        if (!$jwtData) {
            $this->sendResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
            return;
        }

        $idTorneo = intval($params);
        if ($idTorneo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de torneo inválido'], 400);
            return;
        }

        $estado = $_GET['estado'] ?? null;
        $tipoGasto = $_GET['tipo'] ?? null;
        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;

        $model = new PagosModel();
        $gastos = $model->listarGastos($idTorneo, $estado, $tipoGasto, $fechaInicio, $fechaFin);

        $this->sendResponse([
            'status' => true,
            'data' => $gastos
        ]);
    }

    /**
     * GET: Obtiene un gasto específico
     * Endpoint: Pagos/detalle/{idPago}
     */
    public function detalle($params)
    {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        if (empty($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
            return;
        }

        $jwt = new JwtHandler();
        $jwtData = $jwt->validateToken($token);
        if (!$jwtData) {
            $this->sendResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
            return;
        }

        $idPago = intval($params);
        if ($idPago <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de pago inválido'], 400);
            return;
        }

        $model = new PagosModel();
        $gasto = $model->getGasto($idPago);

        if ($gasto) {
            $this->sendResponse([
                'status' => true,
                'data' => $gasto
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Gasto no encontrado'
            ], 404);
        }
    }

    /**
     * POST: Crea un gasto
     * Endpoint: Pagos/crear
     */
    public function crear($params)
    {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        if (empty($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
            return;
        }

        $jwt = new JwtHandler();
        $jwtData = $jwt->validateToken($token);
        if (!$jwtData) {
            $this->sendResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (
            !isset($data['id_torneo']) || !isset($data['tipo_gasto']) ||
            !isset($data['concepto']) || !isset($data['beneficiario']) ||
            !isset($data['monto']) || !isset($data['fecha_pago']) || !isset($data['forma_pago'])
        ) {
            $this->sendResponse(['status' => false, 'msg' => 'Datos incompletos'], 400);
            return;
        }

        $data['usuario_registro'] = $jwtData['id_persona'];

        $model = new PagosModel();
        $idPago = $model->crearGasto($data);

        if ($idPago) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Gasto registrado exitosamente',
                'id_pago' => $idPago
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al registrar el gasto'
            ], 500);
        }
    }

    /**
     * PUT: Actualiza un gasto
     * Endpoint: Pagos/actualizar/{idPago}
     */
    public function actualizar($params)
    {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        if (empty($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
            return;
        }

        $jwt = new JwtHandler();
        $jwtData = $jwt->validateToken($token);
        if (!$jwtData) {
            $this->sendResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
            return;
        }

        $idPago = intval($params);
        if ($idPago <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de pago inválido'], 400);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $model = new PagosModel();
        $result = $model->actualizarGasto($idPago, $data);

        if ($result) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Gasto actualizado exitosamente'
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al actualizar el gasto'
            ], 500);
        }
    }

    /**
     * PUT: Anula un gasto
     * Endpoint: Pagos/anular/{idPago}
     */
    public function anular($params)
    {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        if (empty($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
            return;
        }

        $jwt = new JwtHandler();
        $jwtData = $jwt->validateToken($token);
        if (!$jwtData) {
            $this->sendResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
            return;
        }

        $idPago = intval($params);
        if ($idPago <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de pago inválido'], 400);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $motivoAnulacion = $data['motivo_anulacion'] ?? 'Anulado por administrador';

        $model = new PagosModel();
        $result = $model->anularGasto($idPago, $motivoAnulacion);

        if ($result) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Gasto anulado exitosamente'
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al anular el gasto'
            ], 500);
        }
    }

    /**
     * GET: Obtiene totales de gastos
     * Endpoint: Pagos/totales/{idTorneo}
     */
    public function totales($params)
    {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        if (empty($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
            return;
        }

        $jwt = new JwtHandler();
        $jwtData = $jwt->validateToken($token);
        if (!$jwtData) {
            $this->sendResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
            return;
        }

        $idTorneo = intval($params);
        if ($idTorneo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de torneo inválido'], 400);
            return;
        }

        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;

        $model = new PagosModel();
        $porTipo = $model->getTotalGastosPorTipo($idTorneo, $fechaInicio, $fechaFin);
        $porFormaPago = $model->getTotalGastosPorFormaPago($idTorneo, $fechaInicio, $fechaFin);
        $total = $model->getTotalGastos($idTorneo, $fechaInicio, $fechaFin);

        $this->sendResponse([
            'status' => true,
            'data' => [
                'por_tipo' => $porTipo,
                'por_forma_pago' => $porFormaPago,
                'total' => $total
            ]
        ]);
    }

    /**
     * Envía respuesta JSON
     */
    private function sendResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
