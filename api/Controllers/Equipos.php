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

        $idLiga = 0;
        if ($this->userData['id_rol'] == 1) {
            $idLiga = isset($_GET['id_liga']) ? intval($_GET['id_liga']) : 0;
        } else {
            $idLiga = intval($this->userData['id_liga']);
        }

        $idTorneo = isset($_GET['id_torneo']) ? intval($_GET['id_torneo']) : 0;

        $arrData = $this->model->selectEquipos($idLiga, $idDelegado, $idTorneo);
        $this->res(true, "Listado de equipos", $arrData);
    }

    public function getEquipo($id)
    {
        $idEquipo = intval($id);
        if ($idEquipo > 0) {
            $idLigaUsuario = ($this->userData['id_rol'] == 1) ? 0 : $this->userData['id_liga'];
            $arrData = $this->model->selectEquipo($idEquipo, $idLigaUsuario);
            if (empty($arrData)) {
                $this->res(false, "Equipo no encontrado");
            }
            $this->res(true, "Datos del equipo", $arrData);
        }
        $this->res(false, "ID inválido");
    }

    public function getDelegados()
    {
        // Si Rol 1, deberia poder ver delegados de la liga seleccionada... pero este metodo usa userData.
        // Asumiremos que al crear equipo el super admin vera delegados de la liga que haya seleccionado.
        // Este endpoint requiere id_liga por GET si es Rol 1?
        // Por simplicidad mantengo userData, pero Rol 1 podria tener problemas si id_liga user no coincide.
        // Lo ideal sería recibir id_liga. 
        // Parche rápido:
        $idLiga = $this->userData['id_liga'];
        if ($this->userData['id_rol'] == 1)
            $idLiga = intval($_GET['id_liga'] ?? 0);

        $arrData = [];
        if ($idLiga > 0)
            $arrData = $this->model->selectDelegados($idLiga);

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
            $idTorneo = intval($_POST['id_torneo'] ?? 0);

            if ($idEquipo == 0 && $idTorneo <= 0) {
                $this->res(false, "Debes seleccionar un torneo para crear el equipo");
            }

            // Determinar Liga
            $idLiga = intval($this->userData['id_liga']);
            if ($this->userData['id_rol'] == 1) {
                $idLiga = intval($_POST['id_liga'] ?? 0);
                if ($idLiga <= 0 && $idEquipo == 0) { // Liga obligatoria al crear
                    $this->res(false, "Debes seleccionar una liga");
                }
            }

            if (empty($nombre))
                $this->res(false, "El nombre del equipo es obligatorio");

            // Manejo del Escudo
            $nombreEscudo = "default_shield.png";

            if ($idEquipo > 0) {
                $ligaCheck = ($this->userData['id_rol'] == 1) ? 0 : $idLiga;
                $equipoActual = $this->model->selectEquipo($idEquipo, $ligaCheck);
                if ($equipoActual)
                    $nombreEscudo = $equipoActual['escudo'];
            }

            if (!empty($_FILES['escudo']['name'])) {
                $imgNombre = $_FILES['escudo']['name'];
                $imgTemp = $_FILES['escudo']['tmp_name'];
                $ext = pathinfo($imgNombre, PATHINFO_EXTENSION);
                $nombreEscudo = "equipo_" . uniqid() . "." . $ext;
                $destino = "../app/assets/images/equipos/" . $nombreEscudo;

                $uploadDir = "../app/assets/images/equipos/";
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                move_uploaded_file($imgTemp, $destino);
            }

            if ($idEquipo == 0) {
                $request = $this->model->insertEquipo($nombre, $nombreEscudo, $idDelegado, $idLiga, $estado, $idTorneo);
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
