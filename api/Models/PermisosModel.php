<?php
class PermisosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectModulos()
    {
        $sql = "SELECT id_modulo, titulo as nombre, descripcion FROM modulos WHERE estado != 0";
        return $this->select_all($sql);
    }

    public function selectPermisosRol(int $idrol)
    {
        $sql = "SELECT * FROM permisos WHERE id_rol = $idrol";
        return $this->select_all($sql);
    }

    public function deletePermisos(int $idrol)
    {
        $sql = "DELETE FROM permisos WHERE id_rol = $idrol";
        return $this->delete($sql);
    }

    public function insertPermisos(int $idrol, int $idmodulo, int $r, int $w, int $u, int $d)
    {
        $query_insert = "INSERT INTO permisos(id_rol, id_modulo, r, w, u, d) VALUES(?,?,?,?,?,?)";
        $arrData = array($idrol, $idmodulo, $r, $w, $u, $d);
        return $this->insert($query_insert, $arrData);
    }
}
?>