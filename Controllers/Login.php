<?php
class Login extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (isset($_SESSION['login'])) {
            header('Location: ' . base_url() . 'dashboard');
        }
    }

    public function login()
    {
        $data['page_tag'] = "Login - Global Cup";
        $data['page_title'] = "Acceso al Sistema";
        $data['page_name'] = "login";
        $this->views->getView($this, "login", $data);
    }

    public function loginUser()
    {
        if ($_POST) {
            if (empty($_POST['txtEmail']) || empty($_POST['txtPassword'])) {
                $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            } else {
                $strUsuario = strtolower(strClean($_POST['txtEmail']));
                $strPassword = hash("SHA256", $_POST['txtPassword']);
                $requestUser = $this->model->loginUser($strUsuario, $strPassword);

                if (empty($requestUser)) {
                    $arrResponse = array('status' => false, 'msg' => 'El usuario o la contraseña es incorrecta.');
                } else {
                    $arrData = $requestUser;
                    if ($arrData['estado'] == 1) {
                        $_SESSION['idUser'] = $arrData['id_persona'];
                        $_SESSION['login'] = true;
                        $_SESSION['idLiga'] = $arrData['id_liga']; // Este es el filtro para multi-tenancy
                        $_SESSION['userData'] = $arrData;

                        // Generar JWT Token (válido por 1 hora)
                        $jwt = new JwtHandler();
                        $tokenData = [
                            'idUser' => $arrData['id_persona'],
                            'idLiga' => $arrData['id_liga'],
                            'rol' => $arrData['rol']
                        ];
                        $_SESSION['token'] = $jwt->createToken($tokenData);

                        $arrResponse = array('status' => true, 'msg' => 'ok');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Usuario inactivo.');
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>