<?php
require_once("Models/ArbitrosModel.php");

class Arbitros extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET: Lista todos los árbitros
     * Endpoint: Arbitros/listar
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

        $estado = $_GET['estado'] ?? 1;

        $model = new ArbitrosModel();
        $arbitros = $model->listarArbitros($estado);

        $this->sendResponse([
            'status' => true,
            'data' => $arbitros
        ]);
    }

    /**
     * POST: Crea un árbitro
     * Endpoint: Arbitros/crear
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

        if (!isset($data['nombre_completo'])) {
            $this->sendResponse(['status' => false, 'msg' => 'Nombre completo es requerido'], 400);
            return;
        }

        $model = new ArbitrosModel();
        $idArbitro = $model->crearArbitro($data);

        if ($idArbitro) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Árbitro creado exitosamente',
                'id_arbitro' => $idArbitro
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al crear el árbitro'
            ], 500);
        }
    }

    /**
     * PUT: Actualiza un árbitro
     * Endpoint: Arbitros/actualizar/{idArbitro}
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

        $idArbitro = intval($params);
        if ($idArbitro <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de árbitro inválido'], 400);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $model = new ArbitrosModel();
        $result = $model->actualizarArbitro($idArbitro, $data);

        if ($result) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Árbitro actualizado exitosamente'
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al actualizar el árbitro'
            ], 500);
        }
    }

    /**
     * GET: Obtiene la configuración de pagos a árbitros
     * Endpoint: Arbitros/configuracion/{idTorneo}
     */
    public function configuracion($params)
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

        $model = new ArbitrosModel();
        $config = $model->getConfiguracion($idTorneo);

        $this->sendResponse([
            'status' => true,
            'data' => $config
        ]);
    }

    /**
     * POST: Guarda la configuración de pagos a árbitros
     * Endpoint: Arbitros/guardarConfiguracion
     */
    public function guardarConfiguracion($params)
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

        if (!isset($data['id_torneo']) || !isset($data['monto_por_partido'])) {
            $this->sendResponse(['status' => false, 'msg' => 'Datos incompletos'], 400);
            return;
        }

        $model = new ArbitrosModel();
        $result = $model->guardarConfiguracion($data['id_torneo'], $data['monto_por_partido']);

        if ($result) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Configuración guardada exitosamente'
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al guardar la configuración'
            ], 500);
        }
    }

    /**
     * GET: Lista pagos a árbitros
     * Endpoint: Arbitros/pagos/{idTorneo}
     */
    public function pagos($params)
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
        $idArbitro = isset($_GET['idArbitro']) ? intval($_GET['idArbitro']) : null;

        $model = new ArbitrosModel();
        $pagos = $model->listarPagos($idTorneo, $estado, $idArbitro);

        $this->sendResponse([
            'status' => true,
            'data' => $pagos
        ]);
    }

    /**
     * POST: Registra un pago a árbitro
     * Endpoint: Arbitros/registrarPago/{idPago}
     */
    public function registrarPago($params)
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

        if (!isset($data['fecha_pago']) || !isset($data['forma_pago'])) {
            $this->sendResponse(['status' => false, 'msg' => 'Datos incompletos'], 400);
            return;
        }

        $model = new ArbitrosModel();
        $result = $model->registrarPago($idPago, $data);

        if ($result) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Pago registrado exitosamente'
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al registrar el pago'
            ], 500);
        }
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
