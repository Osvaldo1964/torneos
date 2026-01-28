<?php
class UsuariosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectUsuarios(int $idLiga, int $idRol)
    {
        $where = "";
        if ($idRol != 1) { // Si no es Super Admin, filtrar por su liga y ocultar Super Admins
            $where = " AND p.id_liga = $idLiga AND p.id_rol != 1";
        }
        $sql = "SELECT p.id_persona, p.identificacion, p.nombres, p.apellidos, p.email, p.estado, r.nombre_rol 
                FROM personas p 
                INNER JOIN roles r ON p.id_rol = r.id_rol 
                WHERE p.estado != 0 $where";
        return $this->select_all($sql);
    }

    public function selectUsuario(int $idPersona)
    {
        $sql = "SELECT * FROM personas WHERE id_persona = $idPersona";
        return $this->select($sql);
    }

    public function insertUsuario(string $identificacion, string $nombres, string $apellidos, string $email, string $password, int $idrol, int $idliga, int $estado)
    {
        $query_insert = "INSERT INTO personas(identificacion, nombres, apellidos, email, password, id_rol, id_liga, estado) VALUES(?,?,?,?,?,?,?,?)";
        $arrData = array($identificacion, $nombres, $apellidos, $email, $password, $idrol, $idliga, $estado);
        return $this->insert($query_insert, $arrData);
    }

    public function updateUsuario(int $idPersona, string $identificacion, string $nombres, string $apellidos, string $email, string $password, int $idrol, int $idliga, int $estado)
    {
        if ($password != "") {
            $sql = "UPDATE personas SET identificacion=?, nombres=?, apellidos=?, email=?, password=?, id_rol=?, id_liga=?, estado=? WHERE id_persona = $idPersona";
            $arrData = array($identificacion, $nombres, $apellidos, $email, $password, $idrol, $idliga, $estado);
        } else {
            $sql = "UPDATE personas SET identificacion=?, nombres=?, apellidos=?, email=?, id_rol=?, id_liga=?, estado=? WHERE id_persona = $idPersona";
            $arrData = array($identificacion, $nombres, $apellidos, $email, $idrol, $idliga, $estado);
        }
        return $this->update($sql, $arrData);
    }

    public function userExists(string $email, string $dni, int $idExcept = 0)
    {
        $where = ($idExcept > 0) ? " AND id_persona != $idExcept" : "";
        $sql = "SELECT id_persona FROM personas WHERE (email = '$email' OR identificacion = '$dni') $where";
        return $this->select($sql);
    }
}

