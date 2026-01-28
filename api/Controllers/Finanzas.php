<?php
require_once("Models/FinanzasModel.php");

class Finanzas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET: Obtiene el balance completo de un torneo
     * Endpoint: Finanzas/balance/{idTorneo}
     */
    public function balance($params)
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

        $model = new FinanzasModel();
        $balance = $model->getBalance($idTorneo, $fechaInicio, $fechaFin);

        $this->sendResponse([
            'status' => true,
            'data' => $balance
        ]);
    }

    /**
     * GET: Obtiene el reporte de recaudos (ingresos)
     * Endpoint: Finanzas/recaudos/{idTorneo}
     */
    public function recaudos($params)
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

        $model = new FinanzasModel();
        $reporte = $model->getReporteRecaudos($idTorneo, $fechaInicio, $fechaFin);

        $this->sendResponse([
            'status' => true,
            'data' => $reporte
        ]);
    }

    /**
     * GET: Obtiene el reporte de gastos (egresos)
     * Endpoint: Finanzas/gastos/{idTorneo}
     */
    public function gastos($params)
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

        $model = new FinanzasModel();
        $reporte = $model->getReporteGastos($idTorneo, $fechaInicio, $fechaFin);

        $this->sendResponse([
            'status' => true,
            'data' => $reporte
        ]);
    }

    /**
     * GET: Obtiene comparación entre torneos
     * Endpoint: Finanzas/comparacion?torneos=1,2,3
     */
    public function comparacion($params)
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

        $idLiga = $jwtData['id_liga'];

        if (!isset($_GET['torneos'])) {
            $this->sendResponse(['status' => false, 'msg' => 'Parámetro torneos requerido'], 400);
            return;
        }

        $torneos = array_map('intval', explode(',', $_GET['torneos']));

        $model = new FinanzasModel();
        $comparacion = $model->getComparacionTorneos($idLiga, $torneos);

        $this->sendResponse([
            'status' => true,
            'data' => $comparacion
        ]);
    }

    /**
     * GET: Obtiene evolución mensual de un torneo
     * Endpoint: Finanzas/evolucion/{idTorneo}/{anio}
     */
    public function evolucion($params)
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
        $idTorneo = intval($arrParams[0] ?? 0);
        $anio = intval($arrParams[1] ?? date('Y'));

        if ($idTorneo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de torneo inválido'], 400);
            return;
        }

        $model = new FinanzasModel();
        $evolucion = $model->getEvolucionMensual($idTorneo, $anio);

        $this->sendResponse([
            'status' => true,
            'data' => $evolucion
        ]);
    }

    /**
     * GET: Obtiene estadísticas generales del módulo financiero
     * Endpoint: Finanzas/estadisticas/{idTorneo}
     */
    public function estadisticas($params)
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

        $model = new FinanzasModel();
        $estadisticas = $model->getEstadisticas($idTorneo);

        $this->sendResponse([
            'status' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * GET: Exporta datos a PDF o Excel
     * Endpoint: Finanzas/exportar/{tipo}/{idTorneo}
     * Tipo: balance, recaudos, gastos
     */
    public function exportar($params)
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
        $tipo = $arrParams[0] ?? '';
        $idTorneo = intval($arrParams[1] ?? 0);

        if ($idTorneo <= 0 || empty($tipo)) {
            $this->sendResponse(['status' => false, 'msg' => 'Parámetros inválidos'], 400);
            return;
        }

        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;
        $formato = $_GET['formato'] ?? 'pdf'; // pdf o excel

        $model = new FinanzasModel();

        // Obtener datos según el tipo
        switch ($tipo) {
            case 'balance':
                $datos = $model->getBalance($idTorneo, $fechaInicio, $fechaFin);
                break;
            case 'recaudos':
                $datos = $model->getReporteRecaudos($idTorneo, $fechaInicio, $fechaFin);
                break;
            case 'gastos':
                $datos = $model->getReporteGastos($idTorneo, $fechaInicio, $fechaFin);
                break;
            default:
                $this->sendResponse(['status' => false, 'msg' => 'Tipo de reporte inválido'], 400);
                return;
        }

        // TODO: Implementar generación de PDF/Excel
        // Por ahora retornamos los datos en JSON
        $this->sendResponse([
            'status' => true,
            'msg' => 'Exportación pendiente de implementar',
            'data' => $datos,
            'formato' => $formato
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
