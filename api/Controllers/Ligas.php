<?php
class Ligas extends Controllers
{
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

    public function getLigas()
    {
        // Solo Super Admin (id_rol 1) puede ver todas las ligas
        if ($this->userData['id_rol'] == 1) {
            $sql = "SELECT * FROM ligas WHERE estado != 0";
            $arrData = $this->model->select_all($sql);
            $this->res(true, "Listado de ligas", $arrData);
        } else {
            // Otros ven solo su propia liga
            $idLiga = $this->userData['id_liga'];
            $sql = "SELECT * FROM ligas WHERE id_liga = $idLiga";
            $arrData = $this->model->select_all($sql);
            $this->res(true, "Información de la liga", $arrData);
        }
    }

    public function getLiga($id)
    {
        $idLiga = intval($id);
        if ($idLiga > 0) {
            $sql = "SELECT * FROM ligas WHERE id_liga = $idLiga";
            $arrData = $this->model->select($sql);
            if (empty($arrData)) {
                $this->res(false, "Liga no encontrada");
            }
            $this->res(true, "Datos de la liga", $arrData);
        }
        $this->res(false, "ID inválido");
    }

    public function setLiga()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idLiga = intval($data['id_liga']);
            $nombre = trim($data['nombre']);
            $cuota = floatval($data['cuota_mensual_jugador']);
            $amarilla = floatval($data['valor_amarilla']);
            $roja = floatval($data['valor_roja']);
            $arbitraje = floatval($data['valor_arbitraje_base']);
            $estado = intval($data['estado']);

            if ($idLiga == 0) {
                // Crear (Solo Super Admin)
                if ($this->userData['id_rol'] != 1)
                    $this->res(false, "No tienes permiso para crear ligas");
                $query = "INSERT INTO ligas(nombre, cuota_mensual_jugador, valor_amarilla, valor_roja, valor_arbitraje_base, estado) VALUES(?,?,?,?,?,?)";
                $arrParams = [$nombre, $cuota, $amarilla, $roja, $arbitraje, $estado];
                $request = $this->model->insert($query, $arrParams);
                if ($request > 0)
                    $this->res(true, "Liga creada correctamente");
            } else {
                // Editar (Super Admin o Admin de esa liga)
                if ($this->userData['id_rol'] != 1 && $this->userData['id_liga'] != $idLiga) {
                    $this->res(false, "No tienes permiso para editar esta liga");
                }
                $query = "UPDATE ligas SET nombre=?, cuota_mensual_jugador=?, valor_amarilla=?, valor_roja=?, valor_arbitraje_base=?, estado=? WHERE id_liga=?";
                $arrParams = [$nombre, $cuota, $amarilla, $roja, $arbitraje, $estado, $idLiga];
                $request = $this->model->update($query, $arrParams);
                if ($request)
                    $this->res(true, "Liga actualizada correctamente");
            }
            $this->res(false, "No se pudo procesar la solicitud");
        }
    }

    public function delLiga()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->userData['id_rol'] != 1)
                $this->res(false, "Solo el Super Admin puede eliminar ligas");
            $data = json_decode(file_get_contents("php://input"), true);
            $idLiga = intval($data['id_liga']);

            // Soft delete
            $query = "UPDATE ligas SET estado = 0 WHERE id_liga = ?";
            $request = $this->model->update($query, [$idLiga]);
            if ($request)
                $this->res(true, "Liga eliminada correctamente");
            $this->res(false, "Error al eliminar la liga");
        }
    }
}
?>