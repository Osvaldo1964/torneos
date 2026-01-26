<?php
class Equipos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . 'login');
        }

        $jwt = new JwtHandler();
        if (!isset($_SESSION['token']) || !$jwt->validateToken($_SESSION['token'])) {
            session_unset();
            session_destroy();
            header('Location: ' . base_url() . 'login?timeout=1');
            exit();
        }
    }

    public function equipos()
    {
        $data['page_id'] = 5;
        $data['page_tag'] = "Equipos - Global Cup";
        $data['page_title'] = "Gestión de Equipos";
        $data['page_name'] = "equipos";
        $this->views->getView($this, "equipos", $data);
    }

    public function getEquipos()
    {
        $idLiga = $_SESSION['idLiga'];
        $arrData = $this->model->selectEquipos($idLiga);

        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['options'] = '<div class="text-center">
            <a class="btn btn-success btn-sm" href="' . base_url() . 'equipos/plantilla/' . $arrData[$i]['id_equipo'] . '" title="Ver Plantilla">🏃‍♂️ Plantilla</a>
            <button class="btn btn-primary btn-sm" onClick="fntEditEquipo(' . $arrData[$i]['id_equipo'] . ')" title="Editar">✏️</button>
            <button class="btn btn-danger btn-sm" onClick="fntDelEquipo(' . $arrData[$i]['id_equipo'] . ')" title="Eliminar">🗑️</button>
            </div>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getDelegados()
    {
        $idLiga = $_SESSION['idLiga'];
        $arrData = $this->model->selectDelegados($idLiga);
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function setEquipo()
    {
        if ($_POST) {
            if (empty($_POST['txtNombre'])) {
                $arrResponse = array("status" => false, "msg" => 'El nombre del equipo es obligatorio.');
            } else {
                $idLiga = $_SESSION['idLiga'];
                $strNombre = strClean($_POST['txtNombre']);
                $intIdDelegado = intval($_POST['listDelegado']);

                $escudo = "default_shield.png";
                if ($_FILES['escudo']['name'] != "") {
                    $escudo = uploadImage($_FILES['escudo'], "equipo_" . time());
                }

                $request_equipo = $this->model->insertEquipo($idLiga, $strNombre, $intIdDelegado, $escudo);

                if ($request_equipo > 0) {
                    $arrResponse = array('status' => true, 'msg' => 'Equipo registrado correctamente.');
                } else if ($request_equipo == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! Ya existe un equipo con ese nombre en tu liga.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function plantilla($idEquipo)
    {
        if (empty($idEquipo)) {
            header('Location: ' . base_url() . 'equipos');
        }
        $data['page_id'] = 5;
        $data['page_tag'] = "Plantilla de Equipo - Global Cup";
        $data['page_title'] = "Gestión de Plantilla";
        $data['page_name'] = "plantilla";
        $data['equipo'] = $this->model->getEquipo($idEquipo);
        $this->views->getView($this, "plantilla", $data);
    }

    public function getPlantilla($idEquipo)
    {
        $idLiga = $_SESSION['idLiga'];
        $idTorneo = $this->model->checkAndCreateDefaultTorneo($idLiga);

        $arrData = $this->model->selectPlantilla($idEquipo, $idTorneo);
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getJugadoresLiga()
    {
        $idLiga = $_SESSION['idLiga'];
        $arrData = $this->model->selectJugadoresDisponibles($idLiga);
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function setFichaje()
    {
        if ($_POST) {
            $idEquipo = intval($_POST['idEquipo']);
            $idPersona = intval($_POST['idPersona']);
            $idLiga = $_SESSION['idLiga'];
            $idTorneo = $this->model->checkAndCreateDefaultTorneo($idLiga);

            $request = $this->model->insertNomina($idEquipo, $idPersona, $idTorneo);
            if ($request > 0) {
                $arrResponse = array('status' => true, 'msg' => 'Jugador fichado con éxito.');
            } else if ($request == 'exist') {
                $arrResponse = array('status' => false, 'msg' => 'Este jugador ya está inscrito en un equipo para este torneo.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'No se pudo realizar el fichaje.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>