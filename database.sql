CREATE DATABASE IF NOT EXISTS `db-globalcup` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db-globalcup`;

-- 1. LIGAS
CREATE TABLE `ligas` (
  `id_liga` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `logo` VARCHAR(255) DEFAULT 'default_logo.png',
  `cuota_mensual_jugador` DECIMAL(10,2) DEFAULT 0.00,
  `valor_amarilla` DECIMAL(10,2) DEFAULT 0.00,
  `valor_roja` DECIMAL(10,2) DEFAULT 0.00,
  `valor_arbitraje_base` DECIMAL(10,2) DEFAULT 0.00,
  `estado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. PERSONAS (Jugadores, Árbitros, Delegados)
CREATE TABLE `personas` (
  `id_persona` INT AUTO_INCREMENT PRIMARY KEY,
  `identificacion` VARCHAR(20) UNIQUE NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `foto` VARCHAR(255) DEFAULT 'default_user.png',
  `telefono` VARCHAR(20),
  `email` VARCHAR(100),
  `fecha_nacimiento` DATE,
  `posicion` VARCHAR(50) COMMENT 'Portero, Defensa, etc',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. EQUIPOS
CREATE TABLE `equipos` (
  `id_equipo` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `escudo` VARCHAR(255) DEFAULT 'default_shield.png',
  `id_delegado` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_delegado`) REFERENCES `personas`(`id_persona`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. TORNEOS
CREATE TABLE `torneos` (
  `id_torneo` INT AUTO_INCREMENT PRIMARY KEY,
  `id_liga` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `categoria` VARCHAR(50),
  `fecha_inicio` DATE,
  `fecha_fin` DATE,
  `estado` ENUM('PROGRAMADO', 'EN CURSO', 'FINALIZADO') DEFAULT 'PROGRAMADO',
  FOREIGN KEY (`id_liga`) REFERENCES `ligas`(`id_liga`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. INSCRIPCION DE EQUIPOS EN TORNEOS
CREATE TABLE `torneo_equipos` (
  `id_torneo` INT NOT NULL,
  `id_equipo` INT NOT NULL,
  `pago_inscripcion` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id_torneo`, `id_equipo`),
  FOREIGN KEY (`id_torneo`) REFERENCES `torneos`(`id_torneo`) ON DELETE CASCADE,
  FOREIGN KEY (`id_equipo`) REFERENCES `equipos`(`id_equipo`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. NOMINA DE JUGADORES POR EQUIPO Y TORNEO
CREATE TABLE `equipo_jugadores` (
  `id_equipo` INT NOT NULL,
  `id_persona` INT NOT NULL,
  `id_torneo` INT NOT NULL,
  `dorsal` INT,
  `fecha_vinculacion` DATE,
  PRIMARY KEY (`id_equipo`, `id_persona`, `id_torneo`),
  FOREIGN KEY (`id_equipo`) REFERENCES `equipos`(`id_equipo`) ON DELETE CASCADE,
  FOREIGN KEY (`id_persona`) REFERENCES `personas`(`id_persona`) ON DELETE CASCADE,
  FOREIGN KEY (`id_torneo`) REFERENCES `torneos`(`id_torneo`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. PARTIDOS (ENCUENTROS)
CREATE TABLE `partidos` (
  `id_partido` INT AUTO_INCREMENT PRIMARY KEY,
  `id_torneo` INT NOT NULL,
  `id_local` INT NOT NULL,
  `id_visitante` INT NOT NULL,
  `id_arbitro` INT,
  `fecha_partido` DATETIME,
  `goles_local` INT DEFAULT 0,
  `goles_visitante` INT DEFAULT 0,
  `costo_arbitraje` DECIMAL(10,2) DEFAULT 0.00,
  `arbitraje_pagado` TINYINT(1) DEFAULT 0,
  `estado` ENUM('PENDIENTE', 'JUGADO', 'CANCELADO') DEFAULT 'PENDIENTE',
  FOREIGN KEY (`id_torneo`) REFERENCES `torneos`(`id_torneo`) ON DELETE CASCADE,
  FOREIGN KEY (`id_local`) REFERENCES `equipos`(`id_equipo`) ON DELETE CASCADE,
  FOREIGN KEY (`id_visitante`) REFERENCES `equipos`(`id_equipo`) ON DELETE CASCADE,
  FOREIGN KEY (`id_arbitro`) REFERENCES `personas`(`id_persona`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 8. ESTADISTICAS POR JUGADOR EN PARTIDO
CREATE TABLE `estadisticas_partido` (
  `id_estadistica` INT AUTO_INCREMENT PRIMARY KEY,
  `id_partido` INT NOT NULL,
  `id_persona` INT NOT NULL,
  `id_equipo` INT NOT NULL,
  `goles` INT DEFAULT 0,
  `asistencias` INT DEFAULT 0,
  `amarillas` INT DEFAULT 0,
  `rojas` INT DEFAULT 0,
  `minutos_jugados` INT DEFAULT 0,
  FOREIGN KEY (`id_partido`) REFERENCES `partidos`(`id_partido`) ON DELETE CASCADE,
  FOREIGN KEY (`id_persona`) REFERENCES `personas`(`id_persona`) ON DELETE CASCADE,
  FOREIGN KEY (`id_equipo`) REFERENCES `equipos`(`id_equipo`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. FACTURAS / CUENTAS DE COBRO (El motor financiero)
CREATE TABLE `facturas` (
  `id_factura` INT AUTO_INCREMENT PRIMARY KEY,
  `id_liga` INT NOT NULL,
  `pagable_type` ENUM('JUGADOR', 'EQUIPO') NOT NULL COMMENT 'A quien se le cobra',
  `pagable_id` INT NOT NULL COMMENT 'ID de la persona o el equipo',
  `mes_periodo` TINYINT NOT NULL,
  `anio_periodo` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `pagado` DECIMAL(10,2) DEFAULT 0.00,
  `estado` ENUM('PENDIENTE', 'PAGADA', 'ANULADA', 'VENCIDA') DEFAULT 'PENDIENTE',
  `fecha_vencimiento` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_liga`) REFERENCES `ligas`(`id_liga`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. DETALLES DE FACTURA
CREATE TABLE `detalles_factura` (
  `id_detalle` INT AUTO_INCREMENT PRIMARY KEY,
  `id_factura` INT NOT NULL,
  `concepto` VARCHAR(255) NOT NULL COMMENT 'Mensualidad, Multa Amarilla, Multa Roja, Inscripcion',
  `valor` DECIMAL(10,2) NOT NULL,
  `id_referencia` INT DEFAULT NULL COMMENT 'ID de la tabla estadisticas_partido si es multa',
  FOREIGN KEY (`id_factura`) REFERENCES `facturas`(`id_factura`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. PAGOS / RECIBOS DE CAJA
CREATE TABLE `pagos` (
  `id_pago` INT AUTO_INCREMENT PRIMARY KEY,
  `id_factura` INT NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `fecha_pago` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `metodo_pago` VARCHAR(50) DEFAULT 'EFECTIVO',
  `comprobante` VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (`id_factura`) REFERENCES `facturas`(`id_factura`) ON DELETE CASCADE
) ENGINE=InnoDB;
