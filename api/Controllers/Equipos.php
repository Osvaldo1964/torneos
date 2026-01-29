<?php
class Equipos extends Controllers
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

    public function getEquipos()
    {
        $idDelegado = ($this->userData['id_rol'] == 3) ? $this->userData['id_user'] : 0;
        $arrData = $this->model->selectEquipos($this->userData['id_liga'], $idDelegado);
        $this->res(true, "Listado de equipos", $arrData);
    }

    public function getEquipo($id)
    {
        $idEquipo = intval($id);
        if ($idEquipo > 0) {
            $arrData = $this->model->selectEquipo($idEquipo, $this->userData['id_liga']);
            if (empty($arrData)) {
                $this->res(false, "Equipo no encontrado");
            }
            $this->res(true, "Datos del equipo", $arrData);
        }
        $this->res(false, "ID inválido");
    }

    public function getDelegados()
    {
        $arrData = $this->model->selectDelegados($this->userData['id_liga']);
        $this->res(true, "Lista de delegados", $arrData);
    }

    public function setEquipo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->userData['id_rol'] > 2) {
                $this->res(false, "No tienes permisos de administración de equipos");
            }
            $idEquipo = intval($_POST['id_equipo'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $idDelegado = intval($_POST['id_delegado'] ?? 0);
            $estado = intval($_POST['estado'] ?? 1);

            if (empty($nombre))
                $this->res(false, "El nombre del equipo es obligatorio");

            // Manejo del Escudo
            $nombreEscudo = "default_shield.png";

            if ($idEquipo > 0) {
                $equipoActual = $this->model->selectEquipo($idEquipo, $this->userData['id_liga']);
                if ($equipoActual)
                    $nombreEscudo = $equipoActual['escudo'];
            }

            if (!empty($_FILES['escudo']['name'])) {
                $imgNombre = $_FILES['escudo']['name'];
                $imgTemp = $_FILES['escudo']['tmp_name'];
                $ext = pathinfo($imgNombre, PATHINFO_EXTENSION);
                $nombreEscudo = "equipo_" . time() . "." . $ext;
                $destino = "../app/assets/images/equipos/" . $nombreEscudo;

                if (move_uploaded_file($imgTemp, $destino)) {
                    // Opcional: eliminar el anterior si no es el default
                }
            }

            if ($idEquipo == 0) {
                $request = $this->model->insertEquipo($nombre, $nombreEscudo, $idDelegado, $this->userData['id_liga'], $estado);
                if ($request > 0)
                    $this->res(true, "Equipo creado correctamente");
            } else {
                $request = $this->model->updateEquipo($idEquipo, $nombre, $nombreEscudo, $idDelegado, $estado);
                if ($request)
                    $this->res(true, "Equipo actualizado correctamente");
            }
            $this->res(false, "Error al guardar información");
        }
    }

    public function delEquipo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->userData['id_rol'] > 2) {
                $this->res(false, "No tienes permisos de administración de equipos");
            }
            $data = json_decode(file_get_contents("php://input"), true);
            $idEquipo = intval($data['id_equipo']);

            $check = $this->model->selectEquipo($idEquipo, $this->userData['id_liga']);
            if (!$check)
                $this->res(false, "Acceso denegado");

            $request = $this->model->deleteEquipo($idEquipo);
            if ($request)
                $this->res(true, "Equipo eliminado correctamente");
            $this->res(false, "Error al eliminar");
        }
    }
    public function listarTorneo($idTorneo)
    {
        $idTorneo = intval($idTorneo);
        if ($idTorneo > 0) {
            require_once("Models/TorneosModel.php");
            $torneoModel = new TorneosModel();
            $arrData = $torneoModel->selectInscritos($idTorneo);
            $this->res(true, "Listado de equipos del torneo", $arrData);
        }
        $this->res(false, "ID de torneo inválido");
    }
}
