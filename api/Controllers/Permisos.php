<?php
class Permisos extends Controllers
{
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

    public function getPermisosRol($idrol)
    {
        $intIdrol = intval($idrol);
        if ($intIdrol > 0) {
            $arrModulos = $this->model->selectModulos();
            $arrPermisosRol = $this->model->selectPermisosRol($intIdrol);

            $arrPermisos = array('r' => 0, 'w' => 0, 'u' => 0, 'd' => 0);
            $arrPermisoRol = array('id_rol' => $intIdrol);

            if (empty($arrPermisosRol)) {
                for ($i = 0; $i < count($arrModulos); $i++) {
                    $arrModulos[$i]['permisos'] = $arrPermisos;
                }
            } else {
                for ($i = 0; $i < count($arrModulos); $i++) {
                    $arrPermisos = array('r' => 0, 'w' => 0, 'u' => 0, 'd' => 0);
                    for ($j = 0; $j < count($arrPermisosRol); $j++) {
                        if ($arrModulos[$i]['id_modulo'] == $arrPermisosRol[$j]['id_modulo']) {
                            $arrPermisos = array(
                                'r' => $arrPermisosRol[$j]['r'],
                                'w' => $arrPermisosRol[$j]['w'],
                                'u' => $arrPermisosRol[$j]['u'],
                                'd' => $arrPermisosRol[$j]['d']
                            );
                            break;
                        }
                    }
                    $arrModulos[$i]['permisos'] = $arrPermisos;
                }
            }
            $arrPermisoRol['modulos'] = $arrModulos;
            $this->res(true, "Permisos del rol", $arrPermisoRol);
        }
        $this->res(false, "ID de rol inválido");
    }

    public function setPermisos()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $intIdrol = intval($data['id_rol']);
            $modulos = $data['modulos'];

            $this->model->deletePermisos($intIdrol);
            foreach ($modulos as $modulo) {
                $idModulo = $modulo['id_modulo'];
                $r = $modulo['r'];
                $w = $modulo['w'];
                $u = $modulo['u'];
                $d = $modulo['d'];
                $this->model->insertPermisos($intIdrol, $idModulo, $r, $w, $u, $d);
            }
            $this->res(true, "Permisos asignados correctamente");
        }
    }
}
?>