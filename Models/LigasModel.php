<?php
class LigasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectLigas()
    {
        $sql = "SELECT * FROM ligas WHERE estado != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    public function insertLiga(string $nombre, float $cuota, float $amarilla, float $roja, float $arbitraje)
    {
        $this->strNombre = $nombre;
        $sql = "SELECT * FROM ligas WHERE nombre = '{$this->strNombre}'";
        $request = $this->select($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO ligas(nombre, cuota_mensual_jugador, valor_amarilla, valor_roja, valor_arbitraje_base) VALUES(?,?,?,?,?)";
            $arrData = array($nombre, $cuota, $amarilla, $roja, $arbitraje);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    public function insertAdmin(int $idLiga, string $identificacion, string $nombres, string $apellidos, string $email, string $password)
    {
        $sql = "SELECT * FROM personas WHERE email = '$email' OR identificacion = '$identificacion'";
        $request = $this->select($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO personas(id_liga, identificacion, nombres, apellidos, email, password, id_rol) VALUES(?,?,?,?,?,?,?)";
            $arrData = array($idLiga, $identificacion, $nombres, $apellidos, $email, $password, 2); // 2 = Liga Admin
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }
}
?>