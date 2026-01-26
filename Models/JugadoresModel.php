<?php
class JugadoresModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectJugadores(int $idLiga)
    {
        // Obtenemos solo los jugadores de la liga actual (id_rol 4 = Jugador)
        $sql = "SELECT id_persona, identificacion, nombres, apellidos, email, telefono, posicion, foto 
                FROM personas 
                WHERE id_liga = $idLiga AND id_rol = 4";
        $request = $this->select_all($sql);
        return $request;
    }

    public function insertJugador(int $idLiga, string $identificacion, string $nombres, string $apellidos, string $email, string $password, string $telefono, string $fechaNac, string $posicion, string $foto)
    {

        $sql = "SELECT * FROM personas WHERE (email = '$email' AND email != '') OR identificacion = '$identificacion'";
        $request = $this->select($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO personas(id_liga, identificacion, nombres, apellidos, email, password, telefono, fecha_nacimiento, posicion, foto, id_rol) 
                              VALUES(?,?,?,?,?,?,?,?,?,?,?)";
            $arrData = array($idLiga, $identificacion, $nombres, $apellidos, $email, $password, $telefono, $fechaNac, $posicion, $foto, 4);
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }
}
?>