<?php
require_once("Models/CuotasModel.php");

class Cuotas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET: Obtiene la configuración de cuotas de un torneo
     * Endpoint: Cuotas/configuracion/{idTorneo}
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

        $model = new CuotasModel();
        $config = $model->getConfiguracion($idTorneo);

        $this->sendResponse([
            'status' => true,
            'data' => $config
        ]);
    }

    /**
     * POST: Guarda la configuración de cuotas de un torneo
     * Endpoint: Cuotas/guardarConfiguracion
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

        if (!isset($data['id_torneo']) || !isset($data['monto_mensual']) || !isset($data['dia_vencimiento'])) {
            $this->sendResponse(['status' => false, 'msg' => 'Datos incompletos'], 400);
            return;
        }

        $model = new CuotasModel();
        $result = $model->guardarConfiguracion(
            $data['id_torneo'],
            $data['monto_mensual'],
            $data['dia_vencimiento']
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
     * GET: Lista las cuotas de un torneo
     * Endpoint: Cuotas/listar/{idTorneo}?estado=PENDIENTE&idJugador=1&idEquipo=1
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
        $idJugador = isset($_GET['idJugador']) ? intval($_GET['idJugador']) : null;
        $idEquipo = isset($_GET['idEquipo']) ? intval($_GET['idEquipo']) : null;

        $model = new CuotasModel();
        $cuotas = $model->listarCuotas($idTorneo, $estado, $idJugador, $idEquipo);

        $this->sendResponse([
            'status' => true,
            'data' => $cuotas
        ]);
    }

    /**
     * POST: Genera cuotas para un jugador
     * Endpoint: Cuotas/generar
     */
    public function generar($params)
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
            !isset($data['id_torneo']) || !isset($data['id_jugador']) || !isset($data['id_equipo']) ||
            !isset($data['fecha_inicio']) || !isset($data['fecha_fin'])
        ) {
            $this->sendResponse(['status' => false, 'msg' => 'Datos incompletos'], 400);
            return;
        }

        $model = new CuotasModel();
        $result = $model->generarCuotasJugador(
            $data['id_torneo'],
            $data['id_jugador'],
            $data['id_equipo'],
            $data['fecha_inicio'],
            $data['fecha_fin']
        );

        if ($result) {
            $this->sendResponse([
                'status' => true,
                'msg' => 'Cuotas generadas exitosamente'
            ]);
        } else {
            $this->sendResponse([
                'status' => false,
                'msg' => 'Error al generar cuotas. Verifique que exista configuración para el torneo.'
            ], 500);
        }
    }

    /**
     * POST: Genera cuotas masivas para todos los inscritos
     * Endpoint: Cuotas/generarMasivas
     */
    public function generarMasivas($params)
    {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        if (empty($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token no proporcionado'], 401);
            return;
        }

        $jwt = new JwtHandler();
        if (!$jwt->validateToken($token)) {
            $this->sendResponse(['status' => false, 'msg' => 'Token inválido'], 401);
            return;
        }

        $idTorneo = intval($params);
        if ($idTorneo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de torneo inválido'], 400);
            return;
        }

        $model = new CuotasModel();
        $result = $model->generarCuotasTorneo($idTorneo);

        $this->sendResponse($result);
    }

    /**
     * PUT: Marca cuotas vencidas de un torneo
     * Endpoint: Cuotas/marcarVencidas/{idTorneo}
     */
    public function marcarVencidas($params)
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

        $model = new CuotasModel();
        $result = $model->marcarCuotasVencidas($idTorneo);

        $this->sendResponse([
            'status' => true,
            'msg' => 'Cuotas vencidas actualizadas'
        ]);
    }

    /**
     * GET: Obtiene el resumen de cuotas de un torneo
     * Endpoint: Cuotas/resumen/{idTorneo}
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

        $model = new CuotasModel();
        $resumen = $model->getResumenCuotas($idTorneo);

        $this->sendResponse([
            'status' => true,
            'data' => $resumen
        ]);
    }

    /**
     * GET: Obtiene cuotas pendientes de un jugador
     * Endpoint: Cuotas/pendientes/{idJugador}/{idTorneo}
     */
    public function pendientes($params)
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

        $arrParams = explode(',', $params);
        $idJugador = intval($arrParams[0] ?? 0);
        $idTorneo = intval($arrParams[1] ?? 0);

        if ($idJugador <= 0 || $idTorneo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'Parámetros inválidos'], 400);
            return;
        }

        $model = new CuotasModel();
        $cuotas = $model->getCuotasPendientesJugador($idJugador, $idTorneo);

        $this->sendResponse([
            'status' => true,
            'data' => $cuotas
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
