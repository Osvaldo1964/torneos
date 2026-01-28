<?php
class RegistroModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function registerLiga(string $nombreLiga, string $logo, string $dni, string $nombres, string $apellidos, string $email, string $password)
    {
        // 1. Crear la liga
        $sqlLiga = "INSERT INTO ligas(nombre, logo, estado) VALUES(?,?,?)";
        $arrLiga = array($nombreLiga, $logo, 1);
        $idLiga = $this->insert($sqlLiga, $arrLiga);

        if ($idLiga > 0) {
            // 2. Crear el usuario administrador de la liga (id_rol = 2)
            $sqlUser = "INSERT INTO personas(id_liga, identificacion, nombres, apellidos, email, password, id_rol) 
                        VALUES(?,?,?,?,?,?,?)";
            $arrUser = array($idLiga, $dni, $nombres, $apellidos, $email, $password, 2);
            $idPersona = $this->insert($sqlUser, $arrUser);
            return $idPersona;
        }
        return 0;
    }

    public function userExists(string $email, string $dni)
    {
        $sql = "SELECT id_persona FROM personas WHERE email = '$email' OR identificacion = '$dni'";
        $request = $this->select($sql);
        return !empty($request);
    }
}

