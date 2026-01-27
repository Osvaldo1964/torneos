<?php
class LoginModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function loginUser($email, $password)
    {
        $sql = "SELECT p.id_persona, p.nombres, p.email, p.id_rol, p.id_liga, l.logo as liga_logo 
                FROM personas p
                LEFT JOIN ligas l ON p.id_liga = l.id_liga
                WHERE p.email = '$email' AND p.password = '$password'";
        return $this->select($sql);
    }
}
?>