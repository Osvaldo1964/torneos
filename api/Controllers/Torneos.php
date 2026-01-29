<?php
class Torneos extends Controllers
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

    public function getTorneos()
    {
        $idLigaUsuario = intval($this->userData['id_liga']);

        if ($this->userData['id_rol'] == 1) {
            $filtroLiga = isset($_GET['id_liga']) ? intval($_GET['id_liga']) : 0;
            if ($filtroLiga > 0) {
                // Filtrar por liga específica
                $arrData = $this->model->selectTorneos($filtroLiga);
            } else {
                // Mostrar todos
                $sql = "SELECT * FROM torneos WHERE estado != 'ELIMINADO'";
                $arrData = $this->model->select_all($sql);
            }
        } else {
            // Mostrar solo de su liga
            $arrData = $this->model->selectTorneos($idLigaUsuario);
        }
        $this->res(true, "Listado de torneos", $arrData);
    }

    public function getTorneo($id)
    {
        $idTorneo = intval($id);
        if ($idTorneo > 0) {
            $arrData = $this->model->selectTorneo($idTorneo, $this->userData['id_liga']);
            if (empty($arrData)) {
                $this->res(false, "Torneo no encontrado");
            }
            $this->res(true, "Datos del torneo", $arrData);
        }
        $this->res(false, "ID inválido");
    }

    public function setTorneo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar Permisos Generales
            if ($this->userData['id_rol'] > 2) {
                $this->res(false, "No tienes permisos para gestionar torneos");
            }

            $idTorneo = intval($_POST['id_torneo'] ?? 0);

            // Determinar Liga
            $idLigaDestino = 0;
            if ($this->userData['id_rol'] == 1) {
                // Super Admin: Debe recibir la liga por POST
                $idLigaDestino = intval($_POST['id_liga'] ?? 0);
                if ($idLigaDestino <= 0 && $idTorneo == 0) { // Solo obligatorio al crear
                    $this->res(false, "Debes seleccionar una liga para el torneo");
                }
            } else {
                // Liga Admin: Usa su propia liga
                $idLigaDestino = $this->userData['id_liga'];
            }

            // Validar propiedad al Editar
            if ($idTorneo > 0 && $this->userData['id_rol'] != 1) {
                $check = $this->model->selectTorneo($idTorneo, $this->userData['id_liga']);
                if (empty($check)) {
                    $this->res(false, "No tienes permiso para editar este torneo");
                }
            }

            $nombre = trim($_POST['nombre'] ?? '');
            $categoria = trim($_POST['categoria'] ?? '');
            $cuota = floatval($_POST['cuota_jugador'] ?? 0);
            $amarilla = floatval($_POST['valor_amarilla'] ?? 0);
            $roja = floatval($_POST['valor_roja'] ?? 0);
            $arbitraje = floatval($_POST['valor_arbitraje_base'] ?? 0);
            $fechaInicio = $_POST['fecha_inicio'] ?? '';
            $fechaFin = $_POST['fecha_fin'] ?? '';
            $estado = $_POST['estado'] ?? 'PROGRAMADO';

            if (empty($nombre))
                $this->res(false, "El nombre del torneo es obligatorio");

            // Manejo del Logo
            $nombreLogo = "default_torneo.png";

            if ($idTorneo > 0) {
                // Obtener logo actual (si es admin, puede que necesitemos buscar sin filtro de liga o confiar en lo anterior)
                // Si rol 1, buscamos globalmente. Si rol 2, ya validamos propiedad.
                // Simplificamos usando selectTorneo con id_liga 0/null si es super admin?
                // El modelo selectTorneo usa id_liga...
                // Si ya validamos la propiedad arriba, podemos confiar en que existe.
                // Para Rol 1, selectTorneo podría fallar si filtra por liga.
                // Asumiremos que para obtener el logo anterior no es critico el filtro estricto aqui si ya validamos.
                // Pero usaremos la validacion correcta:
                $ligaFiltro = ($this->userData['id_rol'] == 1) ? 0 : $this->userData['id_liga'];
                // Ojo: selectTorneo del modelo filtra por liga si se le pasa. Si pasamos 0, ¿qué hace?
                // Revisare el modelo despues. Por ahora asumamos que el usuario tiene acceso visual al torneo.
                // Como parche rapido:
                $queryLogo = "SELECT logo FROM torneos WHERE id_torneo = $idTorneo";
                $curr = $this->model->select($queryLogo);
                if ($curr)
                    $nombreLogo = $curr['logo'];
            }

            if (!empty($_FILES['logo']['name'])) {
                $imgNombre = $_FILES['logo']['name'];
                $imgTemp = $_FILES['logo']['tmp_name'];
                $ext = pathinfo($imgNombre, PATHINFO_EXTENSION);
                $nombreLogo = "torneo_" . uniqid() . "." . $ext;

                $uploadDir = "../app/assets/images/torneos/";
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                $destino = $uploadDir . $nombreLogo;
                move_uploaded_file($imgTemp, $destino);
            }

            if ($idTorneo == 0) {
                $request = $this->model->insertTorneo($nombre, $nombreLogo, $idLigaDestino, $categoria, $cuota, $amarilla, $roja, $arbitraje, $fechaInicio, $fechaFin);
                if ($request > 0)
                    $this->res(true, "Torneo creado correctamente");
            } else {
                $request = $this->model->updateTorneo($idTorneo, $nombre, $nombreLogo, $categoria, $cuota, $amarilla, $roja, $arbitraje, $fechaInicio, $fechaFin, $estado);
                if ($request)
                    $this->res(true, "Torneo actualizado correctamente");
            }
            $this->res(false, "Error al guardar información");
        }
    }

    public function delTorneo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo']);

            // Verificar pertenencia a liga
            $check = $this->model->selectTorneo($idTorneo, $this->userData['id_liga']);
            if (!$check)
                $this->res(false, "Acceso denegado o torneo no existe");

            $request = $this->model->deleteTorneo($idTorneo);
            if ($request)
                $this->res(true, "Torneo eliminado correctamente");
            $this->res(false, "Error al eliminar");
        }
    }

    // --- Métodos de Inscripción ---
    public function getInscritos($idTorneo)
    {
        $idDelegado = ($this->userData['id_rol'] == 3) ? $this->userData['id_user'] : 0;
        $arrData = $this->model->selectInscritos(intval($idTorneo), $idDelegado);
        $this->res(true, "Equipos inscritos", $arrData);
    }

    public function getDisponibles($idTorneo)
    {
        $arrData = $this->model->selectEquiposParaInscribir($this->userData['id_liga'], intval($idTorneo));
        $this->res(true, "Equipos disponibles", $arrData);
    }

    public function setInscripcion()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo']);
            $idEquipo = intval($data['id_equipo']);

            $request = $this->model->insertInscripcion($idTorneo, $idEquipo);
            if ($request > 0 || $request === true) {
                $this->res(true, "Equipo inscrito correctamente");
            }
            $this->res(false, "Error al inscribir equipo");
        }
    }

    public function delInscripcion()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo']);
            $idEquipo = intval($data['id_equipo']);

            $request = $this->model->deleteInscripcion($idTorneo, $idEquipo);
            if ($request) {
                $this->res(true, "Inscripción eliminada correctamente");
            }
            $this->res(false, "Error al eliminar inscripción");
        }
    }
}

