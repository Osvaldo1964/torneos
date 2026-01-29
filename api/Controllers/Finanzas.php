<?php
class Finanzas extends Controllers
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
        // Restricción de acceso para Finanzas (Solo Super Admin y Liga Admin)
        if ($this->userData['id_rol'] > 2) {
            $this->res(false, "Acceso denegado: No tienes permisos para acceder al módulo de finanzas");
            exit;
        }
    }

    /**
     * GET: Obtiene el balance completo de un torneo
     * Endpoint: Finanzas/balance/{idTorneo}
     */
    public function balance($params)
    {
        $idTorneo = intval($params);
        if ($idTorneo <= 0) {
            return $this->res(false, "ID de torneo inválido");
        }

        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;

        $data = $this->model->getBalance($idTorneo, $fechaInicio, $fechaFin);
        return $this->res(true, "Balance obtenido", $data);
    }

    /**
     * GET: Obtiene el reporte de recaudos (ingresos)
     * Endpoint: Finanzas/recaudos/{idTorneo}
     */
    public function recaudos($params)
    {
        $idTorneo = intval($params);
        if ($idTorneo <= 0) {
            return $this->res(false, "ID de torneo inválido");
        }

        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;

        $data = $this->model->getReporteRecaudos($idTorneo, $fechaInicio, $fechaFin);
        return $this->res(true, "Reporte de recaudos obtenido", $data);
    }

    /**
     * GET: Obtiene el reporte de gastos (egresos)
     * Endpoint: Finanzas/gastos/{idTorneo}
     */
    public function gastos($params)
    {
        $idTorneo = intval($params);
        if ($idTorneo <= 0) {
            return $this->res(false, "ID de torneo inválido");
        }

        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;

        $data = $this->model->getReporteGastos($idTorneo, $fechaInicio, $fechaFin);
        return $this->res(true, "Reporte de gastos obtenido", $data);
    }

    /**
     * GET: Obtiene comparación entre torneos
     * Endpoint: Finanzas/comparacion?torneos=1,2,3
     */
    public function comparacion($params)
    {
        if (!isset($_GET['torneos'])) {
            return $this->res(false, "Parámetro torneos requerido");
        }

        $torneos = array_map('intval', explode(',', $_GET['torneos']));
        $data = $this->model->getComparacionTorneos($this->userData['id_liga'], $torneos);

        return $this->res(true, "Comparación de torneos obtenida", $data);
    }

    /**
     * GET: Obtiene evolución mensual de un torneo
     * Endpoint: Finanzas/evolucion/{idTorneo}/{anio}
     */
    public function evolucion($params)
    {
        $arrParams = explode(',', $params);
        $idTorneo = intval($arrParams[0] ?? 0);
        $anio = intval($arrParams[1] ?? date('Y'));

        if ($idTorneo <= 0) {
            return $this->res(false, "ID de torneo inválido");
        }

        $data = $this->model->getEvolucionMensual($idTorneo, $anio);
        return $this->res(true, "Evolución mensual obtenida", $data);
    }

    /**
     * GET: Obtiene estadísticas generales del módulo financiero
     * Endpoint: Finanzas/estadisticas/{idTorneo}
     */
    public function estadisticas($params)
    {
        $idTorneo = intval($params);
        if ($idTorneo <= 0) {
            return $this->res(false, "ID de torneo inválido");
        }

        $data = $this->model->getEstadisticas($idTorneo);
        return $this->res(true, "Estadísticas obtenidas", $data);
    }

    /**
     * GET: Exporta datos a PDF o Excel
     * Endpoint: Finanzas/exportar/{tipo}/{idTorneo}
     */
    public function exportar($params)
    {
        $arrParams = explode(',', $params);
        $tipo = $arrParams[0] ?? '';
        $idTorneo = intval($arrParams[1] ?? 0);

        if ($idTorneo <= 0 || empty($tipo)) {
            return $this->res(false, "Parámetros inválidos");
        }

        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;
        $formato = $_GET['formato'] ?? 'pdf';

        switch ($tipo) {
            case 'balance':
                $datos = $this->model->getBalance($idTorneo, $fechaInicio, $fechaFin);
                break;
            case 'recaudos':
                $datos = $this->model->getReporteRecaudos($idTorneo, $fechaInicio, $fechaFin);
                break;
            case 'gastos':
                $datos = $this->model->getReporteGastos($idTorneo, $fechaInicio, $fechaFin);
                break;
            default:
                return $this->res(false, "Tipo de reporte inválido");
        }

        return $this->res(true, "Datos para exportación obtenidos", [
            "datos" => $datos,
            "tipo" => $tipo,
            "formato" => $formato
        ]);
    }
}

