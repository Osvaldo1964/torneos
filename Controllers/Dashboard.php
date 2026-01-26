<?php
class Dashboard extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . 'login');
        }

        // Validación de JWT
        $jwt = new JwtHandler();
        if (!isset($_SESSION['token']) || !$jwt->validateToken($_SESSION['token'])) {
            session_unset();
            session_destroy();
            header('Location: ' . base_url() . 'login?timeout=1');
            exit();
        }
    }

    public function dashboard()
    {
        $data['page_id'] = 2;
        $data['page_tag'] = "Dashboard - Global Cup";
        $data['page_title'] = "Panel de Control";
        $data['page_name'] = "dashboard";

        // Aquí es donde el aislamiento ocurre:
        $idLiga = $_SESSION['idLiga'];
        // Todas las consultas en este controlador usarán $idLiga

        $this->views->getView($this, "dashboard", $data);
    }
}
?>