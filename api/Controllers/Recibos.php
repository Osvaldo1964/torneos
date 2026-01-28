<?php
class Recibos extends Controllers
{
    public $userData;
    public function __construct()
    {
        parent::__construct();
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : "";
        $jwt = new JwtHandler();
        $this->userData = $jwt->validateToken($token);
        if (!$this->userData)
            $this->res(false, "Token inválido");
    }

    /**
     * Obtiene todas las deudas pendientes de un torneo (Cuotas y Sanciones)
     * unificadas para facilitar el cobro.
     */
    public function pendientes($idTorneo)
    {
        $idTorneo = intval($idTorneo);
        if ($idTorneo > 0) {
            $arrData = $this->model->selectPendientes($idTorneo);
            $this->res(true, "Listado de deudas pendientes", $arrData);
        }
        $this->res(false, "ID de torneo inválido");
    }

    /**
     * Genera un nuevo recibo de ingreso
     */
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['items']) || empty($data['id_torneo'])) {
                $this->res(false, "Datos incompletos para generar el recibo");
            }

            // Datos del recibo
            $idTorneo = intval($data['id_torneo']);
            $pagador = $data['pagador'] ?? 'Consumidor Final';
            $formaPago = $data['forma_pago'] ?? 'EFECTIVO';
            $referencia = $data['referencia'] ?? '';
            $observaciones = $data['observaciones'] ?? '';
            $idUsuario = $this->userData['id_user'];

            // Procesar items y calcular total
            $items = $data['items']; // Array de {id: X, tipo: 'CUOTA'|'SANCION', monto: Y}

            $request = $this->model->insertRecibo(
                $idTorneo,
                $pagador,
                $formaPago,
                $referencia,
                $observaciones,
                $idUsuario,
                $items
            );

            if ($request > 0) {
                $this->res(true, "Recibo generado correctamente", ["id_recibo" => $request]);
            } else {
                $this->res(false, "Error al procesar el pago");
            }
        }
    }

    /**
     * Lista el historial de recibos de un torneo
     */
    public function listar($idTorneo)
    {
        $idTorneo = intval($idTorneo);
        if ($idTorneo > 0) {
            $arrData = $this->model->selectRecibos($idTorneo);
            $this->res(true, "Historial de recibos", $arrData);
        }
        $this->res(false, "ID de torneo inválido");
    }

    /**
     * Obtiene el detalle de un recibo específico
     */
    public function detalle($idRecibo)
    {
        $idRecibo = intval($idRecibo);
        if ($idRecibo > 0) {
            $arrData = $this->model->selectRecibo($idRecibo);
            if (empty($arrData)) {
                $this->res(false, "Recibo no encontrado");
            }
            $this->res(true, "Detalle del recibo", $arrData);
        }
        $this->res(false, "ID de recibo inválido");
    }

    /**
     * Anula un recibo
     */
    public function anular($idRecibo)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idRecibo = intval($idRecibo);
            $motivo = $data['observaciones'] ?? 'Sin motivo';

            if ($idRecibo > 0) {
                $request = $this->model->anularRecibo($idRecibo, $motivo);
                if ($request) {
                    $this->res(true, "Recibo anulado correctamente");
                }
            }
            $this->res(false, "Error al anular el recibo");
        }
    }
}
