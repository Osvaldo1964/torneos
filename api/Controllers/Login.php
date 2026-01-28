<?php
class Login extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            if (empty($data['email']) || empty($data['password'])) {
                $this->res(false, "Datos incompletos");
            }

            $strUser = strtolower($data['email']);
            $strPass = hash("SHA256", $data['password']);

            $requestUser = $this->model->loginUser($strUser, $strPass);

            if (empty($requestUser)) {
                $this->res(false, "Usuario o contraseña incorrectos");
            } else {
                if ($requestUser['id_rol'] == 4) { // Jugador no entra al admin
                    $this->res(false, "Acceso denegado para jugadores");
                }

                $tokenPayload = [
                    "id_user" => $requestUser['id_persona'],
                    "id_rol" => $requestUser['id_rol'],
                    "id_liga" => $requestUser['id_liga'],
                    "liga_logo" => $requestUser['liga_logo'],
                    "email" => $requestUser['email'],
                    "nombre" => $requestUser['nombres']
                ];

                $jwt = new JwtHandler();
                $token = $jwt->createToken($tokenPayload);

                $this->res(true, "Acceso concedido", ["token" => $token, "user" => $tokenPayload]);
            }
        } else {
            $this->res(false, "Método no permitido");
        }
    }
}

