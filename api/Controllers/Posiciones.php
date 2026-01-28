<?php
require_once("Models/PosicionesModel.php");

class Posiciones extends Controllers
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * GET: Obtiene la tabla de posiciones de un grupo
     * Endpoint: Posiciones/tabla/{idGrupo}
     */
    public function tabla($params)
    {
        // Validar JWT
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
        $idGrupo = intval($params);

        if ($idGrupo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de grupo inválido'], 400);
            return;
        }

        $model = new PosicionesModel();

        // Verificar que el grupo pertenece a la liga del usuario (multi-tenant)
        $infoGrupo = $model->getInfoGrupo($idGrupo);

        if (empty($infoGrupo)) {
            $this->sendResponse(['status' => false, 'msg' => 'Grupo no encontrado'], 404);
            return;
        }

        // Super Admin (id_liga = 1) puede ver todo
        if ($idLiga != 1 && $infoGrupo['id_liga'] != $idLiga) {
            $this->sendResponse(['status' => false, 'msg' => 'No tienes permisos para ver este grupo'], 403);
            return;
        }

        $tabla = $model->getTablaPosiciones($idGrupo);

        $this->sendResponse([
            'status' => true,
            'data' => [
                'info' => $infoGrupo,
                'tabla' => $tabla
            ]
        ]);
    }

    /**
     * GET: Obtiene la racha de un equipo en un grupo
     * Endpoint: Posiciones/racha/{idEquipo}/{idGrupo}
     */
    public function racha($params)
    {
        // Validar JWT
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
        $idEquipo = intval($arrParams[0] ?? 0);
        $idGrupo = intval($arrParams[1] ?? 0);

        if ($idEquipo <= 0 || $idGrupo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'Parámetros inválidos'], 400);
            return;
        }

        $model = new PosicionesModel();
        $racha = $model->getRachaEquipo($idEquipo, $idGrupo);

        $this->sendResponse([
            'status' => true,
            'data' => $racha
        ]);
    }

    /**
     * GET: Obtiene los goleadores de un grupo
     * Endpoint: Posiciones/goleadores/{idGrupo}
     */
    public function goleadores($params)
    {
        // Validar JWT
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

        $idGrupo = intval($params);

        if ($idGrupo <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de grupo inválido'], 400);
            return;
        }

        $model = new PosicionesModel();
        $goleadores = $model->getGoleadoresGrupo($idGrupo);

        $this->sendResponse([
            'status' => true,
            'data' => $goleadores
        ]);
    }

    /**
     * GET: Obtiene los torneos de la liga del usuario
     * Endpoint: Posiciones/torneos
     */
    public function torneos($params)
    {
        // Validar JWT
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
        $model = new PosicionesModel();

        // Super Admin puede ver todas las ligas, otros solo la suya
        if ($idLiga == 1) {
            // Para Super Admin, obtener todas las ligas primero
            $sqlLigas = "SELECT id_liga, nombre FROM ligas WHERE estado = 1 ORDER BY nombre";
            $ligas = $model->select_all($sqlLigas);

            $this->sendResponse([
                'status' => true,
                'data' => [
                    'ligas' => $ligas,
                    'is_super_admin' => true
                ]
            ]);
        } else {
            $torneos = $model->getTorneosPorLiga($idLiga);

            $this->sendResponse([
                'status' => true,
                'data' => [
                    'torneos' => $torneos,
                    'is_super_admin' => false
                ]
            ]);
        }
    }

    /**
     * GET: Obtiene las fases de un torneo
     * Endpoint: Posiciones/fases/{idTorneo}
     */
    public function fases($params)
    {
        // Validar JWT
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

        $model = new PosicionesModel();
        $fases = $model->getFasesPorTorneo($idTorneo);

        $this->sendResponse([
            'status' => true,
            'data' => $fases
        ]);
    }

    /**
     * GET: Obtiene los grupos de una fase
     * Endpoint: Posiciones/grupos/{idFase}
     */
    public function grupos($params)
    {
        // Validar JWT
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

        $idFase = intval($params);

        if ($idFase <= 0) {
            $this->sendResponse(['status' => false, 'msg' => 'ID de fase inválido'], 400);
            return;
        }

        $model = new PosicionesModel();
        $grupos = $model->getGruposPorFase($idFase);

        $this->sendResponse([
            'status' => true,
            'data' => $grupos
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
