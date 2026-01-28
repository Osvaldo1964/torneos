<?php
class Roles extends Controllers
{
    public $userData;
    public function __construct()
    {
        parent::__construct();
        // Simple middleware check
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : "";
        $jwt = new JwtHandler();
        $this->userData = $jwt->validateToken($token);

        if (!$this->userData) {
            $this->res(false, "Token inválido o expirado");
        }

        // Solo Super Admin puede acceder a este controlador
        if ($this->userData['id_rol'] != 1) {
            $this->res(false, "No tienes permisos para acceder a este módulo");
        }
    }

    public function getRoles()
    {
        $arrData = $this->model->selectRoles();
        $this->res(true, "Listado de roles", $arrData);
    }

    public function getRol($id)
    {
        $intId = intval($id);
        if ($intId > 0) {
            $arrData = $this->model->selectRol($intId);
            if (empty($arrData)) {
                $this->res(false, "Rol no encontrado");
            } else {
                $this->res(true, "Datos del rol", $arrData);
            }
        }
        $this->res(false, "ID inválido");
    }

    public function setRol()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            $intIdRol = isset($data['idRol']) ? intval($data['idRol']) : 0;
            $strRol = trim($data['nombre_rol']);
            $strDescripcion = trim($data['descripcion']);
            $intStatus = intval($data['estado']);

            if ($intIdRol == 0) {
                // Crear
                $request = $this->model->insertRol($strRol, $strDescripcion, $intStatus);
                $opt = 1;
            } else {
                // Actualizar
                $request = $this->model->updateRol($intIdRol, $strRol, $strDescripcion, $intStatus);
                $opt = 2;
            }

            if ($request > 0) {
                $msg = ($opt == 1) ? "Rol creado correctamente" : "Rol actualizado correctamente";
                $this->res(true, $msg);
            } else if ($request == "exist") {
                $this->res(false, "El nombre del rol ya existe");
            } else {
                $this->res(false, "No se pudo guardar la información");
            }
        }
    }

    public function delRol()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $intIdRol = intval($data['id_rol']);

            $request = $this->model->deleteRol($intIdRol);
            if ($request == "ok") {
                $this->res(true, "Rol eliminado correctamente");
            } else if ($request == "exist") {
                $this->res(false, "No se puede eliminar un rol con usuarios asociados");
            } else {
                $this->res(false, "Error al eliminar el rol");
            }
        }
    }
}

