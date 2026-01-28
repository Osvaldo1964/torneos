<?php
class RolesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectRoles()
    {
        $sql = "SELECT * FROM roles WHERE estado != 0";
        return $this->select_all($sql);
    }

    public function selectRol(int $idrol)
    {
        $sql = "SELECT * FROM roles WHERE id_rol = $idrol";
        return $this->select($sql);
    }

    public function insertRol(string $rol, string $descripcion, int $estado)
    {
        $sql = "SELECT * FROM roles WHERE nombre_rol = '$rol'";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO roles(nombre_rol, descripcion, estado) VALUES(?,?,?)";
            $arrData = array($rol, $descripcion, $estado);
            return $this->insert($query_insert, $arrData);
        } else {
            return "exist";
        }
    }

    public function updateRol(int $idrol, string $rol, string $descripcion, int $estado)
    {
        $sql = "SELECT * FROM roles WHERE nombre_rol = '$rol' AND id_rol != $idrol";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE roles SET nombre_rol = ?, descripcion = ?, estado = ? WHERE id_rol = $idrol";
            $arrData = array($rol, $descripcion, $estado);
            return $this->update($sql, $arrData);
        } else {
            return "exist";
        }
    }

    public function deleteRol(int $idrol)
    {
        // Verificar si tiene usuarios activos antes de eliminar
        $sql = "SELECT * FROM personas WHERE id_rol = $idrol";
        $request = $this->select_all($sql);
        if (empty($request)) {
            $sql = "UPDATE roles SET estado = ? WHERE id_rol = $idrol";
            $arrData = array(0);
            return $this->update($sql, $arrData) ? "ok" : "error";
        } else {
            return "exist";
        }
    }
}

