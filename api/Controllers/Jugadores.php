<?php
class Jugadores extends Controllers
{
    public $userData;
    public function __construct()
    {
        parent::__construct();
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : "";
        $jwt = new JwtHandler();
        $this->userData = $jwt->validateToken($token);
        if (!$this->userData)
            $this->res(false, "Token inválido");
    }

    public function getJugadores()
    {
        $arrData = $this->model->selectJugadores($this->userData['id_liga']);
        $this->res(true, "Listado de jugadores", $arrData);
    }

    public function getJugador($id)
    {
        $idJugador = intval($id);
        if ($idJugador > 0) {
            $arrData = $this->model->selectJugador($idJugador, $this->userData['id_liga']);
            if (empty($arrData)) {
                $this->res(false, "Jugador no encontrado");
            }
            $this->res(true, "Datos del jugador", $arrData);
        }
        $this->res(false, "ID inválido");
    }

    public function setJugador()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Recibimos id_jugador (si es edición) o 0 (si es nuevo)
            $idJugador = intval($_POST['id_jugador'] ?? 0);
            $identificacion = trim($_POST['identificacion'] ?? '');
            $nombres = trim($_POST['nombres'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email = strtolower(trim($_POST['email'] ?? ''));
            $telefono = trim($_POST['telefono'] ?? '');
            $fechaNac = $_POST['fecha_nacimiento'] ?? '';
            $posicion = $_POST['posicion'] ?? '';
            $estado = intval($_POST['estado'] ?? 1);

            if (empty($identificacion) || empty($nombres)) {
                $this->res(false, "Identificación y Nombres son obligatorios");
            }

            // PROCESO DE PERSONA
            $persona = $this->model->selectPersonaByIdentificacion($identificacion);
            $idPersona = 0;

            if ($persona) {
                $idPersona = $persona['id_persona'];
                // Actualizamos datos de la persona por si cambiaron
                $this->model->updatePersona($idPersona, $identificacion, $nombres, $apellidos, $email, $telefono);
            } else {
                // Creamos la persona nueva
                $idPersona = $this->model->insertPersona($identificacion, $nombres, $apellidos, $email, $telefono);
            }

            if ($idPersona <= 0) {
                $this->res(false, "No se pudo registrar/vincular la identidad de la persona.");
            }

            // PROCESO DE PERFIL DE JUGADOR
            // Manejo de Foto
            $nombreFoto = "default_user.png";
            if ($idJugador > 0) {
                $perfilActual = $this->model->selectJugador($idJugador, $this->userData['id_liga']);
                if ($perfilActual)
                    $nombreFoto = $perfilActual['foto'];
            }

            if (!empty($_FILES['foto']['name'])) {
                $imgTemp = $_FILES['foto']['tmp_name'];
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $nombreFoto = "jugador_" . $idPersona . "_" . time() . "." . $ext;
                $destino = "../app/assets/images/jugadores/" . $nombreFoto;
                move_uploaded_file($imgTemp, $destino);
            }

            // Verificamos si ya tiene perfil en esta liga
            $perfilExistente = $this->model->playerExistsInLiga($idPersona, $this->userData['id_liga']);

            if ($idJugador == 0) {
                if ($perfilExistente) {
                    $this->res(false, "Esta persona ya está registrada como jugador en tu liga.");
                }
                $request = $this->model->insertJugador($idPersona, $this->userData['id_liga'], $nombreFoto, $fechaNac, $posicion);
                if ($request > 0)
                    $this->res(true, "Jugador registrado correctamente");
            } else {
                $request = $this->model->updateJugador($idJugador, $nombreFoto, $fechaNac, $posicion, $estado);
                if ($request)
                    $this->res(true, "Información del jugador actualizada");
            }

            $this->res(false, "Error al procesar la solicitud");
        }
    }

    public function delJugador()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idJugador = intval($data['id_jugador'] ?? 0);

            $check = $this->model->selectJugador($idJugador, $this->userData['id_liga']);
            if (!$check)
                $this->res(false, "Jugador no encontrado o acceso denegado");

            $request = $this->model->deleteJugador($idJugador);
            if ($request)
                $this->res(true, "Perfil de jugador eliminado correctamente");
            $this->res(false, "Error al eliminar perfil");
        }
    }

    // --- Métodos de Nómina (Roster) ---
    public function getNomina($params)
    {
        $arrParams = explode(",", $params);
        $idTorneo = intval($arrParams[0] ?? 0);
        $idEquipo = intval($arrParams[1] ?? 0);
        $arrData = $this->model->selectNomina($idTorneo, $idEquipo);
        $this->res(true, "Listado de nómina", $arrData);
    }

    public function getDisponiblesNomina($idTorneo)
    {
        $arrData = $this->model->selectDisponiblesNomina($this->userData['id_liga'], intval($idTorneo));
        $this->res(true, "Jugadores disponibles", $arrData);
    }

    public function setNomina()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo'] ?? 0);
            $idEquipo = intval($data['id_equipo'] ?? 0);
            $idJugador = intval($data['id_jugador'] ?? 0);
            $dorsal = intval($data['dorsal'] ?? 0);

            if ($idTorneo > 0 && $idEquipo > 0 && $idJugador > 0) {
                try {
                    $request = $this->model->insertEnNomina($idTorneo, $idEquipo, $idJugador, $dorsal);
                    if ($request) {
                        $this->res(true, "Jugador inscrito en nómina correctamente");
                    }
                } catch (Exception $e) {
                    $this->res(false, "Error: " . $e->getMessage());
                }
            }
            $this->res(false, "Datos incompletos para procesar la inscripción");
        }
    }

    public function delNomina()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo'] ?? 0);
            $idEquipo = intval($data['id_equipo'] ?? 0);
            $idJugador = intval($data['id_jugador'] ?? 0);

            if ($idTorneo && $idEquipo && $idJugador) {
                $request = $this->model->deleteDeNomina($idTorneo, $idEquipo, $idJugador);
                if ($request)
                    $this->res(true, "Jugador retirado de nómina");
            }
            $this->res(false, "Error al retirar jugador");
        }
    }

    public function listarEquipo($idEquipo)
    {
        $idEquipo = intval($idEquipo);
        if ($idEquipo > 0) {
            $arrData = $this->model->selectJugadoresEquipo($idEquipo);
            $this->res(true, "Listado de jugadores del equipo", $arrData);
        }
        $this->res(false, "ID de equipo inválido");
    }
}
