<?php
class Jugadores extends Controllers
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

    public function jugadores()
    {
        $data['page_id'] = 4;
        $data['page_tag'] = "Jugadores - Global Cup";
        $data['page_title'] = "Gestión de Jugadores";
        $data['page_name'] = "jugadores";
        $this->views->getView($this, "jugadores", $data);
    }

    public function getJugadores()
    {
        $idLiga = $_SESSION['idLiga'];
        $arrData = $this->model->selectJugadores($idLiga);

        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['options'] = '<div class="text-center">
            <button class="btn btn-primary btn-sm" onClick="fntEditJugador(' . $arrData[$i]['id_persona'] . ')" title="Editar">✏️</button>
            <button class="btn btn-danger btn-sm" onClick="fntDelJugador(' . $arrData[$i]['id_persona'] . ')" title="Eliminar">🗑️</button>
            </div>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function setJugador()
    {
        if ($_POST) {
            if (empty($_POST['txtIdentificacion']) || empty($_POST['txtNombres']) || empty($_POST['txtApellidos'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos obligatorios incompletos.');
            } else {
                $idLiga = $_SESSION['idLiga'];
                $strIdentificacion = strClean($_POST['txtIdentificacion']);
                $strNombres = strClean($_POST['txtNombres']);
                $strApellidos = strClean($_POST['txtApellidos']);
                $strEmail = strtolower(strClean($_POST['txtEmail']));
                $strTelefono = strClean($_POST['txtTelefono']);
                $strFechaNac = $_POST['txtFechaNac'];
                $strPosicion = strClean($_POST['listPosicion']);

                $foto = "default_user.png";
                if ($_FILES['foto']['name'] != "") {
                    $foto = uploadImage($_FILES['foto'], "jugador_" . $strIdentificacion . "_" . time());
                }

                // Si es un nuevo registro la contraseña por defecto es la identificación
                $strPassword = hash("SHA256", $strIdentificacion);

                $request_jugador = $this->model->insertJugador(
                    $idLiga,
                    $strIdentificacion,
                    $strNombres,
                    $strApellidos,
                    $strEmail,
                    $strPassword,
                    $strTelefono,
                    $strFechaNac,
                    $strPosicion,
                    $foto
                );

                if ($request_jugador > 0) {
                    $arrResponse = array('status' => true, 'msg' => 'Jugador registrado correctamente.');
                } else if ($request_jugador == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! La identificación o el email ya están registrados.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>