<?php
class ArbitrosModel extends Mysql
{
    /**
     * Obtiene la configuración de pagos a árbitros de un torneo
     */
    public function getConfiguracion($idTorneo)
    {
        $sql = "SELECT * FROM configuracion_arbitros 
                WHERE id_torneo = $idTorneo 
                AND estado = 1";
        return $this->select($sql);
    }

    /**
     * Crea o actualiza la configuración de pagos a árbitros
     */
    public function guardarConfiguracion($idTorneo, $montoPorPartido)
    {
        // Verificar si ya existe configuración
        $existe = $this->getConfiguracion($idTorneo);

        if ($existe) {
            // Actualizar
            $sql = "UPDATE configuracion_arbitros 
                    SET monto_por_partido = ? 
                    WHERE id_torneo = ?";
            $arrValues = array($montoPorPartido, $idTorneo);
            return $this->update($sql, $arrValues);
        } else {
            // Insertar
            $sql = "INSERT INTO configuracion_arbitros (id_torneo, monto_por_partido, estado) 
                    VALUES (?, ?, ?)";
            $arrValues = array($idTorneo, $montoPorPartido, 1);
            return $this->insert($sql, $arrValues);
        }
    }

    /**
     * Lista todos los árbitros
     */
    public function listarArbitros($estado = 1)
    {
        $sql = "SELECT 
                    a.id_arbitro,
                    a.nombre_completo,
                    a.identificacion,
                    a.telefono,
                    a.email,
                    a.estado,
                    p.nombres,
                    p.apellidos
                FROM arbitros a
                LEFT JOIN personas p ON a.id_persona = p.id_persona";

        if ($estado !== null) {
            $sql .= " WHERE a.estado = $estado";
        }

        $sql .= " ORDER BY a.nombre_completo ASC";

        return $this->select_all($sql);
    }

    /**
     * Obtiene un árbitro específico
     */
    public function getArbitro($idArbitro)
    {
        $sql = "SELECT * FROM arbitros WHERE id_arbitro = $idArbitro";
        return $this->select($sql);
    }

    /**
     * Crea un árbitro
     */
    public function crearArbitro($data)
    {
        $idPersona = $data['id_persona'] ?? null;
        $nombreCompleto = $this->strClean($data['nombre_completo']);
        $identificacion = isset($data['identificacion']) ? $this->strClean($data['identificacion']) : '';
        $telefono = isset($data['telefono']) ? $this->strClean($data['telefono']) : '';
        $email = isset($data['email']) ? $this->strClean($data['email']) : '';

        $sql = "INSERT INTO arbitros (id_persona, nombre_completo, identificacion, telefono, email) 
                VALUES (?, ?, ?, ?, ?)";
        $arrValues = array($idPersona, $nombreCompleto, $identificacion, $telefono, $email);

        return $this->insert($sql, $arrValues);
    }

    /**
     * Actualiza un árbitro
     */
    public function actualizarArbitro($idArbitro, $data)
    {
        $nombreCompleto = $this->strClean($data['nombre_completo']);
        $identificacion = isset($data['identificacion']) ? $this->strClean($data['identificacion']) : '';
        $telefono = isset($data['telefono']) ? $this->strClean($data['telefono']) : '';
        $email = isset($data['email']) ? $this->strClean($data['email']) : '';

        $sql = "UPDATE arbitros 
                SET nombre_completo = ?, 
                    identificacion = ?, 
                    telefono = ?, 
                    email = ? 
                WHERE id_arbitro = ?";
        $arrValues = array($nombreCompleto, $identificacion, $telefono, $email, $idArbitro);

        return $this->update($sql, $arrValues);
    }

    /**
     * Cambia el estado de un árbitro
     */
    public function cambiarEstado($idArbitro, $estado)
    {
        $sql = "UPDATE arbitros SET estado = ? WHERE id_arbitro = ?";
        $arrValues = array($estado, $idArbitro);
        return $this->update($sql, $arrValues);
    }

    /**
     * Lista pagos a árbitros con filtros
     */
    public function listarPagos($idTorneo, $estado = null, $idArbitro = null)
    {
        $sql = "SELECT 
                    pa.id_pago,
                    pa.id_partido,
                    pa.id_arbitro,
                    pa.monto,
                    pa.fecha_pago,
                    pa.estado,
                    pa.numero_comprobante,
                    pa.forma_pago,
                    pa.observaciones,
                    a.nombre_completo as arbitro,
                    p.fecha_partido,
                    el.nombre as equipo_local,
                    ev.nombre as equipo_visitante
                FROM pagos_arbitros pa
                INNER JOIN arbitros a ON pa.id_arbitro = a.id_arbitro
                INNER JOIN partidos p ON pa.id_partido = p.id_partido
                INNER JOIN equipos el ON p.id_local = el.id_equipo
                INNER JOIN equipos ev ON p.id_visitante = ev.id_equipo
                INNER JOIN fase_grupos fg ON p.id_grupo = fg.id_grupo
                INNER JOIN torneo_fases tf ON fg.id_fase = tf.id_fase
                WHERE tf.id_torneo = $idTorneo";

        if ($estado) {
            $sql .= " AND pa.estado = '$estado'";
        }

        if ($idArbitro) {
            $sql .= " AND pa.id_arbitro = $idArbitro";
        }

        $sql .= " ORDER BY p.fecha_partido DESC";

        return $this->select_all($sql);
    }

    /**
     * Obtiene un pago específico
     */
    public function getPago($idPago)
    {
        $sql = "SELECT 
                    pa.*,
                    a.nombre_completo as arbitro,
                    p.fecha_partido,
                    el.nombre as equipo_local,
                    ev.nombre as equipo_visitante
                FROM pagos_arbitros pa
                INNER JOIN arbitros a ON pa.id_arbitro = a.id_arbitro
                INNER JOIN partidos p ON pa.id_partido = p.id_partido
                INNER JOIN equipos el ON p.id_local = el.id_equipo
                INNER JOIN equipos ev ON p.id_visitante = ev.id_equipo
                WHERE pa.id_pago = $idPago";
        return $this->select($sql);
    }

    /**
     * Genera pago pendiente a árbitro por partido
     */
    public function generarPagoPartido($idPartido, $idArbitro, $idTorneo, $usuarioRegistro)
    {
        // Obtener configuración del torneo
        $config = $this->getConfiguracion($idTorneo);

        if (!$config) {
            return false; // No hay configuración
        }

        $monto = $config['monto_por_partido'];

        // Verificar si ya existe el pago
        $sqlCheck = "SELECT id_pago FROM pagos_arbitros WHERE id_partido = $idPartido";
        $existe = $this->select($sqlCheck);

        if ($existe) {
            return false; // Ya existe el pago
        }

        // Crear pago pendiente
        $sql = "INSERT INTO pagos_arbitros (id_partido, id_arbitro, monto, estado, usuario_registro) 
                VALUES (?, ?, ?, ?, ?)";
        $arrValues = array($idPartido, $idArbitro, $monto, 'PENDIENTE', $usuarioRegistro);

        return $this->insert($sql, $arrValues);
    }

    /**
     * Registra el pago a un árbitro
     */
    public function registrarPago($idPago, $data)
    {
        $fechaPago = $data['fecha_pago'];
        $numeroComprobante = isset($data['numero_comprobante']) ? $this->strClean($data['numero_comprobante']) : '';
        $formaPago = $data['forma_pago'];
        $observaciones = isset($data['observaciones']) ? $this->strClean($data['observaciones']) : '';

        $sql = "UPDATE pagos_arbitros 
                SET estado = 'PAGADO', 
                    fecha_pago = ?, 
                    numero_comprobante = ?, 
                    forma_pago = ?, 
                    observaciones = ? 
                WHERE id_pago = ?";
        $arrValues = array($fechaPago, $numeroComprobante, $formaPago, $observaciones, $idPago);

        return $this->update($sql, $arrValues);
    }

    /**
     * Obtiene el resumen de pagos por estado
     */
    public function getResumenPagos($idTorneo)
    {
        $sql = "SELECT 
                    pa.estado,
                    COUNT(*) as cantidad,
                    SUM(pa.monto) as total
                FROM pagos_arbitros pa
                INNER JOIN partidos p ON pa.id_partido = p.id_partido
                INNER JOIN fase_grupos fg ON p.id_grupo = fg.id_grupo
                INNER JOIN torneo_fases tf ON fg.id_fase = tf.id_fase
                WHERE tf.id_torneo = $idTorneo
                GROUP BY pa.estado";
        return $this->select_all($sql);
    }

    /**
     * Obtiene pagos pendientes de un árbitro
     */
    public function getPagosPendientesArbitro($idArbitro)
    {
        $sql = "SELECT 
                    pa.*,
                    p.fecha_partido,
                    el.nombre as equipo_local,
                    ev.nombre as equipo_visitante
                FROM pagos_arbitros pa
                INNER JOIN partidos p ON pa.id_partido = p.id_partido
                INNER JOIN equipos el ON p.id_local = el.id_equipo
                INNER JOIN equipos ev ON p.id_visitante = ev.id_equipo
                WHERE pa.id_arbitro = $idArbitro 
                AND pa.estado = 'PENDIENTE'
                ORDER BY p.fecha_partido ASC";
        return $this->select_all($sql);
    }
}
