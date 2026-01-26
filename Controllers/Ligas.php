<?php
class Ligas extends Controllers
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

    public function ligas()
    {
        $data['page_id'] = 3;
        $data['page_tag'] = "Gestión de Ligas - Global Cup";
        $data['page_title'] = "Administración de Ligas";
        $data['page_name'] = "ligas";
        $this->views->getView($this, "ligas", $data);
    }

    public function getLigas()
    {
        if ($_SESSION['userData']['rol'] != 'Super Admin') {
            $arrResponse = array('status' => false, 'msg' => 'Acceso denegado.');
        } else {
            $arrData = $this->model->selectLigas();
            for ($i = 0; $i < count($arrData); $i++) {
                $arrData[$i]['options'] = '<div class="text-center">
                <button class="btn btn-primary btn-sm btnEditLiga" onClick="fntEditLiga(' . $arrData[$i]['id_liga'] . ')" title="Editar">✏️</button>
                <button class="btn btn-danger btn-sm btnDelLiga" onClick="fntDelLiga(' . $arrData[$i]['id_liga'] . ')" title="Eliminar">🗑️</button>
                </div>';
            }
            $arrResponse = $arrData;
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function setLiga()
    {
        if ($_POST) {
            if (empty($_POST['txtNombre']) || empty($_POST['txtEmail']) || empty($_POST['txtIdentificacion'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incompletos.');
            } else {
                $strNombre = strClean($_POST['txtNombre']);
                $strIdentificacion = strClean($_POST['txtIdentificacion']);
                $strNombres = strClean($_POST['txtNombres']);
                $strApellidos = strClean($_POST['txtApellidos']);
                $strEmail = strtolower(strClean($_POST['txtEmail']));
                $strPassword = hash("SHA256", $_POST['txtPassword']);

                $floatCuota = floatval($_POST['txtCuota']);
                $floatAmarilla = floatval($_POST['txtAmarilla']);
                $floatRoja = floatval($_POST['txtRoja']);
                $floatArbitraje = floatval($_POST['txtArbitraje']);

                // 1. Crear la Liga
                $request_liga = $this->model->insertLiga($strNombre, $floatCuota, $floatAmarilla, $floatRoja, $floatArbitraje);

                if ($request_liga > 0) {
                    // 2. Crear el Administrador para esa Liga
                    $request_admin = $this->model->insertAdmin($request_liga, $strIdentificacion, $strNombres, $strApellidos, $strEmail, $strPassword);

                    if ($request_admin > 0) {
                        $arrResponse = array('status' => true, 'msg' => 'Liga y Administrador creados correctamente.');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'La liga se creó, pero el email/identificación del admin ya existe.');
                    }
                } else if ($request_liga == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! El nombre de la liga ya existe.');
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