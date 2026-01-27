<?php
class LoginModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function loginUser($email, $password)
    {
        $sql = "SELECT id_persona, nombres, email, id_rol FROM personas 
                WHERE email = '$email' AND password = '$password'";
        return $this->select($sql);
    }
}
?>