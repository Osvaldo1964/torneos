<?php
class LoginModel extends Mysql
{
    private $intIdUsuario;
    private $strUsuario;
    private $strPassword;

    public function __construct()
    {
        parent::__construct();
    }

    public function loginUser(string $usuario, string $password)
    {
        $this->strUsuario = $usuario;
        $this->strPassword = $password;
        $sql = "SELECT p.id_persona, p.id_liga, l.nombre as nombre_liga, p.nombres, p.apellidos, p.email, r.nombre_rol as rol, l.estado 
                FROM personas p
                INNER JOIN ligas l ON p.id_liga = l.id_liga
                INNER JOIN roles r ON p.id_rol = r.id_rol
                WHERE p.email = '$this->strUsuario' AND p.password = '$this->strPassword' 
                AND r.id_rol != 4"; // El ID 4 es Jugador
        $request = $this->select($sql);
        return $request;
    }
}
?>