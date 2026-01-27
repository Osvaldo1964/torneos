<?php
class Torneos extends Controllers
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

    public function getTorneos()
    {
        $idLiga = intval($this->userData['id_liga']);
        // Si es la liga 1 (Admin Global), puede ver todos los torneos
        if ($idLiga == 1) {
            $sql = "SELECT * FROM torneos WHERE estado != 'ELIMINADO'";
            $arrData = $this->model->select_all($sql);
        } else {
            $arrData = $this->model->selectTorneos($idLiga);
        }
        $this->res(true, "Listado de torneos", $arrData);
    }

    public function getTorneo($id)
    {
        $idTorneo = intval($id);
        if ($idTorneo > 0) {
            $arrData = $this->model->selectTorneo($idTorneo, $this->userData['id_liga']);
            if (empty($arrData)) {
                $this->res(false, "Torneo no encontrado");
            }
            $this->res(true, "Datos del torneo", $arrData);
        }
        $this->res(false, "ID inválido");
    }

    public function setTorneo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idTorneo = intval($_POST['id_torneo'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $cuota = floatval($_POST['cuota_jugador'] ?? 0);
            $amarilla = floatval($_POST['valor_amarilla'] ?? 0);
            $roja = floatval($_POST['valor_roja'] ?? 0);
            $arbitraje = floatval($_POST['valor_arbitraje_base'] ?? 0);
            $fechaInicio = $_POST['fecha_inicio'] ?? '';
            $fechaFin = $_POST['fecha_fin'] ?? '';
            $estado = $_POST['estado'] ?? 'PROGRAMADO';

            if (empty($nombre))
                $this->res(false, "El nombre del torneo es obligatorio");

            // Manejo del Logo
            $nombreLogo = "default_torneo.png";

            // Si es edición, obtener el logo actual
            if ($idTorneo > 0) {
                $torneoActual = $this->model->selectTorneo($idTorneo, $this->userData['id_liga']);
                if ($torneoActual)
                    $nombreLogo = $torneoActual['logo'];
            }

            if (!empty($_FILES['logo']['name'])) {
                $imgNombre = $_FILES['logo']['name'];
                $imgTemp = $_FILES['logo']['tmp_name'];
                $ext = pathinfo($imgNombre, PATHINFO_EXTENSION);
                $nombreLogo = "torneo_" . time() . "." . $ext;
                $destino = "../app/assets/images/torneos/" . $nombreLogo;
                move_uploaded_file($imgTemp, $destino);
            }

            if ($idTorneo == 0) {
                $request = $this->model->insertTorneo($nombre, $nombreLogo, $this->userData['id_liga'], $categoria, $cuota, $amarilla, $roja, $arbitraje, $fechaInicio, $fechaFin);
                if ($request > 0)
                    $this->res(true, "Torneo creado correctamente");
            } else {
                $request = $this->model->updateTorneo($idTorneo, $nombre, $nombreLogo, $categoria, $cuota, $amarilla, $roja, $arbitraje, $fechaInicio, $fechaFin, $estado);
                if ($request)
                    $this->res(true, "Torneo actualizado correctamente");
            }
            $this->res(false, "Error al guardar información");
        }
    }

    public function delTorneo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo']);

            // Verificar pertenencia a liga
            $check = $this->model->selectTorneo($idTorneo, $this->userData['id_liga']);
            if (!$check)
                $this->res(false, "Acceso denegado o torneo no existe");

            $request = $this->model->deleteTorneo($idTorneo);
            if ($request)
                $this->res(true, "Torneo eliminado correctamente");
            $this->res(false, "Error al eliminar");
        }
    }

    // --- Métodos de Inscripción ---
    public function getInscritos($idTorneo)
    {
        $arrData = $this->model->selectInscritos(intval($idTorneo));
        $this->res(true, "Equipos inscritos", $arrData);
    }

    public function getDisponibles($idTorneo)
    {
        $arrData = $this->model->selectEquiposParaInscribir($this->userData['id_liga'], intval($idTorneo));
        $this->res(true, "Equipos disponibles", $arrData);
    }

    public function setInscripcion()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo']);
            $idEquipo = intval($data['id_equipo']);

            $request = $this->model->insertInscripcion($idTorneo, $idEquipo);
            if ($request > 0 || $request === true) {
                $this->res(true, "Equipo inscrito correctamente");
            }
            $this->res(false, "Error al inscribir equipo");
        }
    }

    public function delInscripcion()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo']);
            $idEquipo = intval($data['id_equipo']);

            $request = $this->model->deleteInscripcion($idTorneo, $idEquipo);
            if ($request) {
                $this->res(true, "Inscripción eliminada correctamente");
            }
            $this->res(false, "Error al eliminar inscripción");
        }
    }
}
?>