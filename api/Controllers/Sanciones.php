<?php
require_once("Models/SancionesModel.php");

class Sanciones extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        if (empty($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
            exit;
        }

        $jwt = new JwtHandler();
        $this->userData = $jwt->validateToken($token);
        if (!$this->userData) {
            $this->sendResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
            exit;
        }

        if ($this->userData['id_rol'] > 2) {
            $this->sendResponse(['status' => false, 'msg' => 'Acceso denegado'], 403);
            exit;
        }
    }

    /**
     * GET: Obtiene la configuración de sanciones de un torneo
     * Endpoint: Sanciones/configuracion/{idTorneo}
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

        $model = new SancionesModel();
        $config = $model->getConfiguracion($idTorneo);

        $this->sendResponse([
            'status' => true,
            'data' => $config
        ]);
    }

    /**
     * POST: Guarda la configuración de sanciones de un torneo
     * Endpoint: Sanciones/guardarConfiguracion
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

        if (!isset($data['id_torneo']) || !isset($data['monto_amarilla']) || !isset($data['monto_roja'])) {
            $this->sendResponse(['status' => false, 'msg' => 'Datos incompletos'], 400);
            return;
        }

        $model = new SancionesModel();
        $result = $model->guardarConfiguracion(
            $data['id_torneo'],
            $data['monto_amarilla'],
            $data['monto_roja']
        );

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
     * GET: Lista las sanciones de un torneo
     * Endpoint: Sanciones/listar/{idTorneo}?estado=PENDIENTE&tipo=AMARILLA
     */
    public function listar($params)
    {
        // Token validation is now handled in the constructor
        // $headers = getallheaders();
        // $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        // if (empty($token)) {
        //     $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
        //     return;
        // }

        // $jwt = new JwtHandler();
        // $jwtData = $jwt->validateToken($token);
        // if (!$jwtData) {
        //     $this->sendResponse(['status' => false, 'msg' => 'Token inválido o expirado'], 401);
        //     return;
        // }

        $idTorneo = intval($params);
        if ($idTorneo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de torneo inválido'], 400);
            return;
        }

        $estado = $_GET['estado'] ?? null;
        $tipo = $_GET['tipo'] ?? null;
        $idJugador = isset($_GET['idJugador']) ? intval($_GET['idJugador']) : null;
        $idEquipo = isset($_GET['idEquipo']) ? intval($_GET['idEquipo']) : null;

        $model = new SancionesModel();
        $sanciones = $model->listarSanciones($idTorneo, $estado, $tipo, $idJugador, $idEquipo);

        $this->sendResponse([
            'status' => true,
            'data' => $sanciones
        ]);
    }

    /**
     * POST: Crea una sanción manual
     * Endpoint: Sanciones/crear
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
            !isset($data['id_torneo']) || !isset($data['tipo_sancion']) ||
            !isset($data['concepto']) || !isset($data['monto']) || !isset($data['fecha_sancion'])
        ) {
            $this->sendResponse(['status' => false, 'msg' => 'Datos incompletos'], 400);
            return;
        }

        $model = new SancionesModel();
        $result = $model->crearSancion($data);

        if ($result) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Sanción creada exitosamente',
                'id_sancion' => $result
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al crear la sanción'
            ], 500);
        }
    }

    /**
     * PUT: Anula una sanción
     * Endpoint: Sanciones/anular/{idSancion}
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

        $idSancion = intval($params);
        if ($idSancion <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de sanción inválido'], 400);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $observaciones = $data['observaciones'] ?? 'Anulada por administrador';

        $model = new SancionesModel();
        $result = $model->anularSancion($idSancion, $observaciones);

        if ($result) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Sanción anulada exitosamente'
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al anular la sanción'
            ], 500);
        }
    }

    /**
     * GET: Obtiene el resumen de sanciones de un torneo
     * Endpoint: Sanciones/resumen/{idTorneo}
     */
    public function resumen($params)
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

        $model = new SancionesModel();
        $resumen = $model->getResumenSanciones($idTorneo);

        $this->sendResponse([
            'status' => true,
            'data' => $resumen
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
