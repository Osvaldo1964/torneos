<?php
class Registro extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createLiga()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['nombre_liga']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['identificacion'])) {
                $this->res(false, "Todos los campos son obligatorios.");
            }

            $strNombreLiga = trim($_POST['nombre_liga']);
            $strIdentificacion = trim($_POST['identificacion']);
            $strNombres = trim($_POST['nombres']);
            $strApellidos = trim($_POST['apellidos']);
            $strEmail = strtolower(trim($_POST['email']));
            $strPassword = hash("SHA256", $_POST['password']);

            // Verificar si el usuario ya existe
            if ($this->model->userExists($strEmail, $strIdentificacion)) {
                $this->res(false, "El correo electrónico o la identificación ya están registrados.");
            }

            // Manejo del Logo
            $nombreLogo = "default_logo.png";
            if (!empty($_FILES['logo']['name'])) {
                $imgNombre = $_FILES['logo']['name'];
                $imgTemp = $_FILES['logo']['tmp_name'];
                $ext = pathinfo($imgNombre, PATHINFO_EXTENSION);
                $nombreLogo = "logo_" . time() . "." . $ext;
                $destino = "../app/assets/images/logos/" . $nombreLogo;
                if (!move_uploaded_file($imgTemp, $destino)) {
                    $nombreLogo = "default_logo.png"; // Fallback if upload fails
                }
            }

            // Registrar Liga y Admin
            $idPersona = $this->model->registerLiga(
                $strNombreLiga,
                $nombreLogo,
                $strIdentificacion,
                $strNombres,
                $strApellidos,
                $strEmail,
                $strPassword
            );

            if ($idPersona > 0) {
                $this->res(true, "¡Registro exitoso! Ya puedes iniciar sesión con tu cuenta de Administrador de Liga.");
            } else {
                $this->res(false, "No se pudo completar el registro. Intente más tarde.");
            }
        } else {
            $this->res(false, "Método no permitido.");
        }
    }
}
?>