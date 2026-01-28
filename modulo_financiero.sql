-- ============================================
-- MÓDULO FINANCIERO - GLOBAL CUP
-- Versión: 1.0
-- Fecha: 27 de Enero, 2026
-- ============================================

-- ============================================
-- 1. CONFIGURACIÓN DE CUOTAS
-- ============================================

CREATE TABLE IF NOT EXISTS configuracion_cuotas (
    id_configuracion INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    monto_mensual DECIMAL(10,2) NOT NULL,
    dia_vencimiento INT DEFAULT 5,
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE CASCADE,
    INDEX idx_torneo (id_torneo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. CUOTAS DE JUGADORES
-- ============================================

CREATE TABLE IF NOT EXISTS cuotas_jugadores (
    id_cuota INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    id_jugador INT NOT NULL,
    id_equipo INT NOT NULL,
    mes INT NOT NULL,
    anio INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    estado ENUM('PENDIENTE', 'PAGADO', 'VENCIDO') DEFAULT 'PENDIENTE',
    fecha_pago DATE NULL,
    id_recibo INT NULL,
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE CASCADE,
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador) ON DELETE CASCADE,
    FOREIGN KEY (id_equipo) REFERENCES equipos(id_equipo) ON DELETE CASCADE,
    UNIQUE KEY unique_cuota (id_jugador, id_torneo, mes, anio),
    INDEX idx_torneo (id_torneo),
    INDEX idx_jugador (id_jugador),
    INDEX idx_equipo (id_equipo),
    INDEX idx_estado (estado),
    INDEX idx_fecha_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. CONFIGURACIÓN DE SANCIONES
-- ============================================

CREATE TABLE IF NOT EXISTS configuracion_sanciones (
    id_configuracion INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    monto_amarilla DECIMAL(10,2) DEFAULT 0,
    monto_roja DECIMAL(10,2) DEFAULT 0,
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE CASCADE,
    INDEX idx_torneo (id_torneo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. SANCIONES ECONÓMICAS
-- ============================================

CREATE TABLE IF NOT EXISTS sanciones_economicas (
    id_sancion INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    tipo_sancion ENUM('AMARILLA', 'ROJA', 'COMPORTAMIENTO', 'NO_PRESENTACION', 'OTRA') NOT NULL,
    id_equipo INT NULL,
    id_jugador INT NULL,
    id_partido INT NULL,
    concepto VARCHAR(255) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    estado ENUM('PENDIENTE', 'PAGADO', 'ANULADO') DEFAULT 'PENDIENTE',
    fecha_sancion DATE NOT NULL,
    fecha_pago DATE NULL,
    id_recibo INT NULL,
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE CASCADE,
    FOREIGN KEY (id_equipo) REFERENCES equipos(id_equipo) ON DELETE SET NULL,
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador) ON DELETE SET NULL,
    FOREIGN KEY (id_partido) REFERENCES partidos(id_partido) ON DELETE SET NULL,
    INDEX idx_torneo (id_torneo),
    INDEX idx_equipo (id_equipo),
    INDEX idx_jugador (id_jugador),
    INDEX idx_estado (estado),
    INDEX idx_tipo (tipo_sancion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. RECIBOS DE INGRESO
-- ============================================

-- ============================================
-- 5. RECIBOS DE INGRESO
-- ============================================

CREATE TABLE IF NOT EXISTS recibos_ingreso (
    id_recibo INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    numero_recibo VARCHAR(50) UNIQUE NOT NULL,
    pagador VARCHAR(255) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    forma_pago ENUM('EFECTIVO', 'TRANSFERENCIA', 'TARJETA', 'OTRO') DEFAULT 'EFECTIVO',
    referencia VARCHAR(100),
    fecha_pago DATE NOT NULL,
    estado ENUM('ACTIVO', 'ANULADO') DEFAULT 'ACTIVO',
    motivo_anulacion TEXT,
    observaciones TEXT,
    id_usuario_registro INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario_registro) REFERENCES personas(id_persona) ON DELETE RESTRICT,
    INDEX idx_torneo (id_torneo),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_pago)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS recibos_detalle (
    id_detalle INT PRIMARY KEY AUTO_INCREMENT,
    id_recibo INT NOT NULL,
    tipo_item ENUM('CUOTA', 'SANCION', 'OTRO') NOT NULL,
    id_item INT NULL,
    concepto VARCHAR(255) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_recibo) REFERENCES recibos_ingreso(id_recibo) ON DELETE CASCADE,
    INDEX idx_recibo (id_recibo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. CONFIGURACIÓN DE ÁRBITROS
-- ============================================

CREATE TABLE IF NOT EXISTS configuracion_arbitros (
    id_configuracion INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    monto_por_partido DECIMAL(10,2) NOT NULL,
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE CASCADE,
    INDEX idx_torneo (id_torneo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. ÁRBITROS
-- ============================================

CREATE TABLE IF NOT EXISTS arbitros (
    id_arbitro INT PRIMARY KEY AUTO_INCREMENT,
    id_persona INT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    identificacion VARCHAR(50),
    telefono VARCHAR(20),
    email VARCHAR(100),
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_persona) REFERENCES personas(id_persona) ON DELETE SET NULL,
    INDEX idx_nombre (nombre_completo),
    INDEX idx_identificacion (identificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. PAGOS A ÁRBITROS
-- ============================================

CREATE TABLE IF NOT EXISTS pagos_arbitros (
    id_pago INT PRIMARY KEY AUTO_INCREMENT,
    id_partido INT NOT NULL,
    id_arbitro INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NULL,
    estado ENUM('PENDIENTE', 'PAGADO') DEFAULT 'PENDIENTE',
    numero_comprobante VARCHAR(50),
    forma_pago ENUM('EFECTIVO', 'TRANSFERENCIA', 'OTRO'),
    observaciones TEXT,
    usuario_registro INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_partido) REFERENCES partidos(id_partido) ON DELETE CASCADE,
    FOREIGN KEY (id_arbitro) REFERENCES arbitros(id_arbitro) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_registro) REFERENCES personas(id_persona) ON DELETE RESTRICT,
    INDEX idx_partido (id_partido),
    INDEX idx_arbitro (id_arbitro),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. PAGOS Y GASTOS GENERALES
-- ============================================

CREATE TABLE IF NOT EXISTS pagos_gastos (
    id_pago INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    tipo_gasto ENUM('ESCENARIO', 'ARBITRO', 'PREMIO', 'MATERIAL', 'ADMINISTRATIVO', 'OTRO') NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    beneficiario VARCHAR(255) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NOT NULL,
    numero_comprobante VARCHAR(50),
    forma_pago ENUM('EFECTIVO', 'TRANSFERENCIA', 'CHEQUE', 'OTRO'),
    documento_soporte VARCHAR(255),
    estado ENUM('ACTIVO', 'ANULADO') DEFAULT 'ACTIVO',
    motivo_anulacion TEXT,
    observaciones TEXT,
    usuario_registro INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE CASCADE,
    FOREIGN KEY (usuario_registro) REFERENCES personas(id_persona) ON DELETE RESTRICT,
    INDEX idx_torneo (id_torneo),
    INDEX idx_tipo (tipo_gasto),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_pago)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES (OPCIONAL)
-- ============================================

-- Nota: Las configuraciones se crearán desde la interfaz
-- según las necesidades de cada torneo

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
