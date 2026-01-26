<?php
class Registro extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function registro()
    {
        $data['page_tag'] = "Registro de Liga - Global Cup";
        $data['page_title'] = "Crea tu propia Liga";
        $data['page_name'] = "registro";
        $this->views->getView($this, "registro", $data);
    }

    public function createLiga()
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

                // Valores financieros por defecto para auto-registro
                $floatCuota = 0;
                $floatAmarilla = 0;
                $floatRoja = 0;
                $floatArbitraje = 0;

                // 1. Crear la Liga (Usamos el modelo de Ligas que ya tiene la lógica)
                // Cargamos el modelo de ligas manualmente si es necesario o usamos uno propio
                require_once("Models/LigasModel.php");
                $objLigas = new LigasModel();

                $request_liga = $objLigas->insertLiga($strNombre, $floatCuota, $floatAmarilla, $floatRoja, $floatArbitraje);

                if ($request_liga > 0) {
                    $request_admin = $objLigas->insertAdmin($request_liga, $strIdentificacion, $strNombres, $strApellidos, $strEmail, $strPassword);

                    if ($request_admin > 0) {
                        $arrResponse = array('status' => true, 'msg' => '¡Felicidades! Tu liga ha sido creada. Ahora puedes iniciar sesión en el Panel Administrativo.');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'La liga se creó, pero hubo un error con el usuario administrador.');
                    }
                } else if ($request_liga == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => 'El nombre de esa liga ya está registrado.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible procesar el registro en este momento.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>