<?php
class JugadoresModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectJugadores(int $idLiga)
    {
        // Traemos datos de la persona unidos a su perfil de jugador
        $sql = "SELECT j.id_jugador, p.id_persona, p.identificacion, p.nombres, p.apellidos, 
                       j.foto, p.telefono, p.email, j.fecha_nacimiento, j.posicion, j.estado 
                FROM jugadores j
                INNER JOIN personas p ON j.id_persona = p.id_persona
                WHERE j.id_liga = $idLiga AND j.estado != 0";
        return $this->select_all($sql);
    }

    public function selectJugador(int $idJugador, int $idLiga)
    {
        $sql = "SELECT j.*, p.identificacion, p.nombres, p.apellidos, p.email, p.telefono 
                FROM jugadores j
                INNER JOIN personas p ON j.id_persona = p.id_persona
                WHERE j.id_jugador = $idJugador AND j.id_liga = $idLiga";
        return $this->select($sql);
    }

    public function selectPersonaByIdentificacion(string $dni)
    {
        $sql = "SELECT * FROM personas WHERE identificacion = '$dni'";
        return $this->select($sql);
    }

    public function insertPersona(string $dni, string $nombres, string $apellidos, string $email, string $telefono)
    {
        // Si es una persona nueva creada desde el módulo de jugadores, le asignamos rol 4 (Jugador) por defecto en la tabla personas
        // y una contraseña base (su mismo DNI hasheado)
        $pass = hash("sha256", $dni);
        $query = "INSERT INTO personas(identificacion, nombres, apellidos, email, telefono, password, id_rol, id_liga) VALUES(?,?,?,?,?,?,4,0)";
        $arrData = array($dni, $nombres, $apellidos, $email, $telefono, $pass);
        return $this->insert($query, $arrData);
    }

    public function insertJugador(int $idPersona, int $idLiga, string $foto, string $fechaNac, string $posicion)
    {
        $query = "INSERT INTO jugadores(id_persona, id_liga, foto, fecha_nacimiento, posicion, estado) VALUES(?,?,?,?,?,1)";
        $arrData = array($idPersona, $idLiga, $foto, $fechaNac, $posicion);
        return $this->insert($query, $arrData);
    }

    public function updateJugador(int $idJugador, string $foto, string $fechaNac, string $posicion, int $estado)
    {
        $sql = "UPDATE jugadores SET foto=?, fecha_nacimiento=?, posicion=?, estado=? WHERE id_jugador = $idJugador";
        $arrData = array($foto, $fechaNac, $posicion, $estado);
        return $this->update($sql, $arrData);
    }

    public function updatePersona(int $idPersona, string $dni, string $nombres, string $apellidos, string $email, string $telefono)
    {
        $sql = "UPDATE personas SET identificacion=?, nombres=?, apellidos=?, email=?, telefono=? WHERE id_persona = $idPersona";
        $arrData = array($dni, $nombres, $apellidos, $email, $telefono);
        return $this->update($sql, $arrData);
    }

    public function deleteJugador(int $idJugador)
    {
        $sql = "UPDATE jugadores SET estado = ? WHERE id_jugador = $idJugador";
        return $this->update($sql, [0]);
    }

    public function playerExistsInLiga(int $idPersona, int $idLiga)
    {
        $sql = "SELECT * FROM jugadores WHERE id_persona = $idPersona AND id_liga = $idLiga AND estado != 0";
        return $this->select($sql);
    }

    // --- Gestión de Nóminas (Rosters) ---
    public function selectNomina(int $idTorneo, int $idEquipo)
    {
        $sql = "SELECT j.id_jugador, p.nombres, p.apellidos, j.foto, ej.dorsal 
                FROM equipo_jugadores ej 
                INNER JOIN jugadores j ON ej.id_jugador = j.id_jugador
                INNER JOIN personas p ON j.id_persona = p.id_persona
                WHERE ej.id_torneo = $idTorneo AND ej.id_equipo = $idEquipo AND j.estado != 0";
        return $this->select_all($sql);
    }

    public function selectDisponiblesNomina(int $idLiga, int $idTorneo)
    {
        // Jugadores de la liga que NO están en ningún equipo para ESTE torneo
        $sql = "SELECT j.id_jugador, p.nombres, p.apellidos, j.foto 
                FROM jugadores j
                INNER JOIN personas p ON j.id_persona = p.id_persona
                WHERE j.id_liga = $idLiga AND j.estado != 0 
                AND j.id_jugador NOT IN (SELECT id_jugador FROM equipo_jugadores WHERE id_torneo = $idTorneo)";
        return $this->select_all($sql);
    }

    public function insertEnNomina(int $idTorneo, int $idEquipo, int $idJugador, int $dorsal)
    {
        $query = "INSERT INTO equipo_jugadores(id_torneo, id_equipo, id_jugador, dorsal, fecha_vinculacion) VALUES(?,?,?,?,?)";
        return $this->update($query, [$idTorneo, $idEquipo, $idJugador, $dorsal, date('Y-m-d')]);
    }

    public function deleteDeNomina(int $idTorneo, int $idEquipo, int $idJugador)
    {
        $query = "DELETE FROM equipo_jugadores WHERE id_torneo = ? AND id_equipo = ? AND id_jugador = ?";
        return $this->update($query, [$idTorneo, $idEquipo, $idJugador]);
    }
}
?>