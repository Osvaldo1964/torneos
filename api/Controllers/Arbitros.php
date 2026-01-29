<?php
class Arbitros extends Controllers
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
        if ($this->userData['id_rol'] > 2) {
            $this->res(false, "Acceso denegado");
            exit;
        }
    }

    /**
     * GET: Lista todos los árbitros
     * Endpoint: Arbitros/listar
     */
    public function listar()
    {
        $estado = $_GET['estado'] ?? 1;
        $data = $this->model->listarArbitros($estado);
        $this->res(true, "Listado de árbitros", $data);
    }

    /**
     * POST: Crea un árbitro
     * Endpoint: Arbitros/crear
     */
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['nombre_completo'])) {
                $this->res(false, "El nombre completo es obligatorio");
            }

            $request = $this->model->crearArbitro($data);

            if ($request > 0) {
                $this->res(true, "Árbitro creado exitosamente", ["id_arbitro" => $request]);
            } else {
                $this->res(false, "Error al crear el árbitro");
            }
        }
    }

    /**
     * PUT: Actualiza un árbitro
     * Endpoint: Arbitros/actualizar/{idArbitro}
     */
    public function actualizar($idArbitro)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'POST') {
            $idArbitro = intval($idArbitro);
            if ($idArbitro <= 0) {
                $this->res(false, "ID de árbitro inválido");
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['nombre_completo'])) {
                $this->res(false, "El nombre completo es obligatorio");
            }

            $request = $this->model->actualizarArbitro($idArbitro, $data);

            if ($request) {
                $this->res(true, "Árbitro actualizado exitosamente");
            } else {
                $this->res(false, "Error al actualizar el árbitro");
            }
        }
    }

    /**
     * GET: Obtiene los roles y tarifas de un torneo
     * Endpoint: Arbitros/roles/{idTorneo}
     */
    public function roles($idTorneo)
    {
        $idTorneo = intval($idTorneo);
        if ($idTorneo > 0) {
            $data = $this->model->getRoles($idTorneo);
            $this->res(true, "Roles obtenidos", $data);
        }
        $this->res(false, "ID Torneo inválido");
    }

    /**
     * POST: Guarda un rol de arbitraje
     * Endpoint: Arbitros/guardarRol
     */
    public function guardarRol()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo'] ?? 0);
            $idRol = intval($data['id_rol'] ?? 0);
            $nombre = $data['nombre'] ?? '';
            $monto = floatval($data['monto'] ?? 0);

            if ($idTorneo > 0 && !empty($nombre)) {
                $request = $this->model->guardarRol($idTorneo, $nombre, $monto, $idRol);
                if ($request) {
                    $this->res(true, "Rol guardado correctamente");
                }
            }
            $this->res(false, "Datos incompletos");
        }
    }

    /**
     * DELETE: Elimina un rol
     * Endpoint: Arbitros/eliminarRol/{idRol}
     */
    public function eliminarRol($idRol)
    {
        $idRol = intval($idRol);
        if ($idRol > 0) {
            $request = $this->model->eliminarRol($idRol);
            if ($request) {
                $this->res(true, "Rol eliminado");
            }
        }
        $this->res(false, "No se pudo eliminar el rol");
    }

    /**
     * GET: Lista pagos a árbitros
     * Endpoint: Arbitros/pagos/{idTorneo}
     */
    public function pagos($idTorneo)
    {
        $idTorneo = intval($idTorneo);
        if ($idTorneo <= 0) {
            $this->res(false, "ID de torneo inválido");
        }

        $estado = $_GET['estado'] ?? null;
        $idArbitro = isset($_GET['idArbitro']) ? intval($_GET['idArbitro']) : null;

        $pagos = $this->model->listarPagos($idTorneo, $estado, $idArbitro);
        $this->res(true, "Listado de pagos a árbitros", $pagos);
    }

    /**
     * POST: Registra un pago a árbitro
     * Endpoint: Arbitros/registrarPago/{idPago}
     */
    public function registrarPago($idPago)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'PUT') {
            $idPago = intval($idPago);
            if ($idPago <= 0) {
                $this->res(false, "ID de pago inválido");
            }

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['fecha_pago']) || empty($data['forma_pago'])) {
                $this->res(false, "Datos del pago incompletos");
            }

            $request = $this->model->registrarPago($idPago, $data);

            if ($request) {
                $this->res(true, "Pago registrado correctamente");
            } else {
                $this->res(false, "Error al registrar el pago");
            }
        }
    }
}
