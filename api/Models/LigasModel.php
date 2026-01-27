<?php
class LigasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertLiga(string $nombre)
    {
        $sql = "INSERT INTO ligas(nombre, logo, cuota_mensual_jugador, valor_amarilla, valor_roja, valor_arbitraje_base, estado) 
                VALUES(?,?,?,?,?,?,?)";
        $arrData = array($nombre, 'default_logo.png', 0, 0, 0, 0, 1);
        return $this->insert($sql, $arrData);
    }
}
?>