<?php
class Usuarios extends Controllers
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

    public function getUsuarios()
    {
        $arrData = $this->model->selectUsuarios($this->userData['id_liga'], $this->userData['id_rol']);
        $this->res(true, "Listado de usuarios", $arrData);
    }

    public function getUsuario($id)
    {
        $idPersona = intval($id);
        if ($idPersona > 0) {
            $arrData = $this->model->selectUsuario($idPersona);
            if (empty($arrData)) {
                $this->res(false, "Usuario no encontrado");
            }
            // Seguridad: Un Liga Admin no puede ver a un Super Admin
            if ($this->userData['id_rol'] != 1 && $arrData['id_rol'] == 1) {
                $this->res(false, "Acceso denegado");
            }
            $this->res(true, "Datos del usuario", $arrData);
        }
        $this->res(false, "ID inválido");
    }

    public function setUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idPersona = intval($data['id_user']);
            $dni = trim($data['identificacion']);
            $nombres = trim($data['nombres']);
            $apellidos = trim($data['apellidos']);
            $email = strtolower(trim($data['email']));
            $idrol = intval($data['id_rol']);
            $idliga = ($this->userData['id_rol'] == 1) ? intval($data['id_liga']) : $this->userData['id_liga'];
            $estado = intval($data['estado']);
            $password = !empty($data['password']) ? hash("SHA256", $data['password']) : "";

            // Validaciones de seguridad
            if ($this->userData['id_rol'] != 1) {
                if ($idrol == 1)
                    $this->res(false, "No puedes crear Super Admins");
                if ($idliga != $this->userData['id_liga'])
                    $this->res(false, "No puedes crear usuarios para otra liga");
            }

            if ($this->model->userExists($email, $dni, $idPersona)) {
                $this->res(false, "El correo o la identificación ya existen");
            }

            if ($idPersona == 0) {
                // Crear
                if ($password == "")
                    $this->res(false, "La contraseña es obligatoria para nuevos usuarios");
                $request = $this->model->insertUsuario($dni, $nombres, $apellidos, $email, $password, $idrol, $idliga, $estado);
                if ($request > 0)
                    $this->res(true, "Usuario creado correctamente");
            } else {
                // Editar
                $request = $this->model->updateUsuario($idPersona, $dni, $nombres, $apellidos, $email, $password, $idrol, $idliga, $estado);
                if ($request)
                    $this->res(true, "Usuario actualizado correctamente");
            }
            $this->res(false, "Error al guardar información");
        }
    }

    public function delUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idPersona = intval($data['id_user']);

            $userToDel = $this->model->selectUsuario($idPersona);
            if (!$userToDel)
                $this->res(false, "Usuario no existe");

            // Seguridad
            if ($this->userData['id_rol'] != 1) {
                if ($userToDel['id_rol'] == 1)
                    $this->res(false, "No puedes eliminar al Super Admin");
                if ($userToDel['id_liga'] != $this->userData['id_liga'])
                    $this->res(false, "No puedes eliminar usuarios de otra liga");
            }

            $query = "UPDATE personas SET estado = 0 WHERE id_persona = ?";
            $request = $this->model->update($query, [$idPersona]);
            if ($request)
                $this->res(true, "Usuario eliminado correctamente");
            $this->res(false, "Error al eliminar");
        }
    }
}
?>