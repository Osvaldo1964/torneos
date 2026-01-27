<?php
class Competicion extends Controllers
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

    public function getEstructura($idTorneo)
    {
        $idTorneo = intval($idTorneo);
        $fases = $this->model->selectFases($idTorneo);

        foreach ($fases as &$fase) {
            $fase['grupos'] = $this->model->selectGruposFase($fase['id_fase']);
        }

        $this->res(true, "Estructura del torneo", $fases);
    }

    public function getPartidos($params)
    {
        $arrParams = explode(",", $params);
        $tipo = $arrParams[0] ?? ''; // 'fase' o 'grupo'
        $id = intval($arrParams[1] ?? 0);

        if ($tipo == 'fase') {
            $arrData = $this->model->selectPartidosFase($id);
        } else {
            $arrData = $this->model->selectPartidosGrupo($id);
        }

        $this->res(true, "Lista de partidos", $arrData);
    }

    public function getPartido($id)
    {
        $idPartido = intval($id);
        $arrData = $this->model->selectPartido($idPartido);
        $this->res(true, "Detalle del partido", $arrData);
    }

    public function getNominasMatch($id)
    {
        $idPartido = intval($id);
        $p = $this->model->selectPartido($idPartido);
        if ($p) {
            $local = $this->model->selectNominaMatch($p['id_torneo'], $p['id_local']);
            $visitante = $this->model->selectNominaMatch($p['id_torneo'], $p['id_visitante']);
            $this->res(true, "Nóminas del encuentro", [
                'local' => $local,
                'visitante' => $visitante
            ]);
        }
        $this->res(false, "Partido no encontrado");
    }

    public function getEventosMatch($id)
    {
        $idPartido = intval($id);
        $arrData = $this->model->selectEventosPartido($idPartido);
        $this->res(true, "Eventos del partido", $arrData);
    }

    public function setResultado()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idPartido = intval($data['id_partido'] ?? 0);
            $golesLocal = intval($data['goles_local'] ?? 0);
            $golesVisitante = intval($data['goles_visitante'] ?? 0);
            $estado = $data['estado'] ?? 'JUGADO';
            $eventos = $data['eventos'] ?? []; // Detalle de goles, tarjetas

            if ($idPartido > 0) {
                // 1. Actualizar Score
                $request = $this->model->updateResultado($idPartido, $golesLocal, $golesVisitante, $estado);

                // 2. Sincronizar Eventos (Borrar y reinsertar es lo más simple para edición)
                $this->model->deleteEventosPartido($idPartido);
                foreach ($eventos as $ev) {
                    $this->model->insertEvento(
                        $idPartido,
                        intval($ev['id_jugador']),
                        intval($ev['id_equipo']),
                        $ev['tipo'],
                        intval($ev['minuto']),
                        $ev['obs'] ?? ''
                    );
                }

                if ($request) {
                    $this->res(true, "Planilla guardada correctamente");
                }
            }
            $this->res(false, "No se pudo actualizar el resultado");
        }
    }

    public function setFase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idTorneo = intval($data['id_torneo'] ?? 0);
            $nombre = trim($data['nombre'] ?? '');
            $tipo = $data['tipo'] ?? 'GRUPOS'; // GRUPOS o ELIMINACION
            $idaVuelta = intval($data['ida_vuelta'] ?? 0);
            $orden = intval($data['orden'] ?? 1);

            if ($idTorneo && $nombre) {
                $request = $this->model->insertFase($idTorneo, $nombre, $tipo, $idaVuelta, $orden);
                if ($request > 0) {
                    $this->res(true, "Fase creada correctamente", ["id_fase" => $request]);
                }
            }
            $this->res(false, "No se pudo crear la fase");
        }
    }

    public function delFase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idFase = intval($data['id_fase'] ?? 0);
            if ($idFase > 0) {
                $request = $this->model->deleteFase($idFase);
                if ($request) {
                    $this->res(true, "Fase eliminada correctamente");
                }
            }
            $this->res(false, "No se pudo eliminar la fase");
        }
    }

    public function setGrupo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idFase = intval($data['id_fase'] ?? 0);
            $idGrupo = intval($data['id_grupo'] ?? 0);
            $nombre = trim($data['nombre'] ?? 'Grupo Único');
            $equipos = $data['equipos'] ?? []; // Array de IDs de equipos

            if ($idGrupo > 0) {
                // Actualizar existente
                $request = $this->model->updateGrupo($idGrupo, $nombre);
                if ($request) {
                    $this->model->desvincularEquiposGrupo($idGrupo);
                    foreach ($equipos as $idEquipo) {
                        $this->model->vincularEquipoGrupo($idGrupo, intval($idEquipo));
                    }
                    $this->res(true, "Grupo actualizado correctamente");
                }
            } else if ($idFase) {
                // Crear nuevo
                $idGrupo = $this->model->insertGrupo($idFase, $nombre);
                if ($idGrupo > 0) {
                    foreach ($equipos as $idEquipo) {
                        $this->model->vincularEquipoGrupo($idGrupo, intval($idEquipo));
                    }
                    $this->res(true, "Grupo configurado con " . count($equipos) . " equipos");
                }
            }
            $this->res(false, "Error al configurar grupo");
        }
    }

    public function delGrupo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $idGrupo = intval($data['id_grupo'] ?? 0);
            if ($idGrupo > 0) {
                $request = $this->model->deleteGrupo($idGrupo);
                if ($request) {
                    $this->res(true, "Grupo eliminado correctamente");
                }
            }
            $this->res(false, "No se pudo eliminar el grupo");
        }
    }

    public function getDetalleGrupo($id)
    {
        $idGrupo = intval($id);
        $teams = $this->model->selectEquiposDelGrupo($idGrupo);
        $arrIds = array_column($teams, 'id_equipo');
        $this->res(true, "Detalle del grupo", ['teams' => $arrIds]);
    }

    public function generarFixture($idFase)
    {
        $idFase = intval($idFase);
        $fase = $this->model->selectFase($idFase);
        if (!$fase)
            $this->res(false, "Fase no encontrada");

        $grupos = $this->model->selectGruposFase($idFase);
        if (empty($grupos))
            $this->res(false, "No hay grupos creados en esta fase");

        // Limpiar partidos previos de esta fase si existen
        $this->model->deletePartidosFase($idFase);

        $totalPartidos = 0;

        foreach ($grupos as $grupo) {
            $idGrupo = intval($grupo['id_grupo']);
            $equiposData = $this->model->selectEquiposEnGrupo($idGrupo);
            $idEquipos = [];
            foreach ($equiposData as $e)
                $idEquipos[] = $e['id_equipo'];

            if (count($idEquipos) < 2)
                continue;

            $partidos = $this->generarRoundRobin($idEquipos, $fase['ida_vuelta']);

            foreach ($partidos as $p) {
                $this->model->insertPartido($fase['id_torneo'], $idFase, $idGrupo, $p['local'], $p['visitante'], $p['jornada']);
                $totalPartidos++;
            }
        }

        $this->res(true, "Fixture generado exitosamente. Se crearon $totalPartidos partidos.", ["total" => $totalPartidos]);
    }

    // --- ALGORITMO ROUND ROBIN (TODOS CONTRA TODOS) ---
    private function generarRoundRobin($equipos, $idaVuelta = false)
    {
        if (count($equipos) % 2 != 0) {
            $equipos[] = null; // Equipo fantasma para descansos
        }

        $numEquipos = count($equipos);
        $numRondas = $numEquipos - 1;
        $mitad = $numEquipos / 2;
        $fixture = [];

        // Asegurar que el array tenga índices correlativos
        $equipos = array_values($equipos);

        for ($ronda = 0; $ronda < $numRondas; $ronda++) {
            for ($i = 0; $i < $mitad; $i++) {
                $local = $equipos[$i];
                $visitante = $equipos[$numEquipos - 1 - $i];

                if ($local !== null && $visitante !== null) {
                    $fixture[] = [
                        'jornada' => $ronda + 1,
                        'local' => intval($local),
                        'visitante' => intval($visitante)
                    ];
                }
            }
            // Rotación fija (el primero se queda, los demas rotan)
            $fixed = $equipos[0];
            $rotating = array_slice($equipos, 1);
            $last = array_pop($rotating);
            array_unshift($rotating, $last);
            $equipos = array_merge([$fixed], $rotating);
        }

        if ($idaVuelta) {
            $segundaVuelta = [];
            foreach ($fixture as $match) {
                $segundaVuelta[] = [
                    'jornada' => $match['jornada'] + $numRondas,
                    'local' => $match['visitante'],
                    'visitante' => $match['local']
                ];
            }
            $fixture = array_merge($fixture, $segundaVuelta);
        }

        return $fixture;
    }
}
?>