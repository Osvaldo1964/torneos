-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-01-2026 a las 19:58:18
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db-globalcup`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_factura`
--

CREATE TABLE `detalles_factura` (
  `id_detalle` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `concepto` varchar(255) NOT NULL COMMENT 'Mensualidad, Multa Amarilla, Multa Roja, Inscripcion',
  `valor` decimal(10,2) NOT NULL,
  `id_referencia` int(11) DEFAULT NULL COMMENT 'ID de la tabla estadisticas_partido si es multa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL,
  `id_liga` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `escudo` varchar(255) DEFAULT 'default_shield.png',
  `id_delegado` int(11) DEFAULT NULL,
  `estado` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id_equipo`, `id_liga`, `nombre`, `escudo`, `id_delegado`, `estado`, `created_at`) VALUES
(2, 4, 'EQUPO1', 'equipo_1769531346.jpg', 4, 1, '2026-01-27 16:29:06'),
(3, 4, 'EQUPO2', 'equipo_1769539106.png', 4, 1, '2026-01-27 18:38:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_jugadores`
--

CREATE TABLE `equipo_jugadores` (
  `id_equipo` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `id_torneo` int(11) NOT NULL,
  `dorsal` int(11) DEFAULT NULL,
  `fecha_vinculacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipo_jugadores`
--

INSERT INTO `equipo_jugadores` (`id_equipo`, `id_jugador`, `id_torneo`, `dorsal`, `fecha_vinculacion`) VALUES
(2, 3, 2, 11, '2026-01-27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas_partido`
--

CREATE TABLE `estadisticas_partido` (
  `id_estadistica` int(11) NOT NULL,
  `id_partido` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `goles` int(11) DEFAULT 0,
  `asistencias` int(11) DEFAULT 0,
  `amarillas` int(11) DEFAULT 0,
  `rojas` int(11) DEFAULT 0,
  `minutos_jugados` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL,
  `id_liga` int(11) NOT NULL,
  `pagable_type` enum('JUGADOR','EQUIPO') NOT NULL COMMENT 'A quien se le cobra',
  `pagable_id` int(11) NOT NULL COMMENT 'ID de la persona o el equipo',
  `mes_periodo` tinyint(4) NOT NULL,
  `anio_periodo` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pagado` decimal(10,2) DEFAULT 0.00,
  `estado` enum('PENDIENTE','PAGADA','ANULADA','VENCIDA') DEFAULT 'PENDIENTE',
  `fecha_vencimiento` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fase_grupos`
--

CREATE TABLE `fase_grupos` (
  `id_grupo` int(11) NOT NULL,
  `id_fase` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fase_grupos`
--

INSERT INTO `fase_grupos` (`id_grupo`, `id_fase`, `nombre`) VALUES
(1, 1, 'grupo a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fase_grupo_equipos`
--

CREATE TABLE `fase_grupo_equipos` (
  `id_grupo` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fase_grupo_equipos`
--

INSERT INTO `fase_grupo_equipos` (`id_grupo`, `id_equipo`) VALUES
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id_jugador` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL COMMENT 'Relación con la cuenta global',
  `id_liga` int(11) NOT NULL COMMENT 'Liga donde está inscrito',
  `identificacion_deportiva` varchar(20) DEFAULT NULL COMMENT 'DNI o carnet para esta liga',
  `posicion` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default_user.png',
  `fecha_nacimiento` date DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id_jugador`, `id_persona`, `id_liga`, `identificacion_deportiva`, `posicion`, `foto`, `fecha_nacimiento`, `estado`, `created_at`) VALUES
(3, 8, 4, NULL, 'Portero', 'jugador_8_1769535879.png', '2026-01-01', 1, '2026-01-27 17:44:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ligas`
--

CREATE TABLE `ligas` (
  `id_liga` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT 'default_logo.png',
  `cuota_mensual_jugador` decimal(10,2) DEFAULT 0.00,
  `valor_amarilla` decimal(10,2) DEFAULT 0.00,
  `valor_roja` decimal(10,2) DEFAULT 0.00,
  `valor_arbitraje_base` decimal(10,2) DEFAULT 0.00,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ligas`
--

INSERT INTO `ligas` (`id_liga`, `nombre`, `logo`, `cuota_mensual_jugador`, `valor_amarilla`, `valor_roja`, `valor_arbitraje_base`, `estado`, `created_at`) VALUES
(1, 'Liga Global Cup Pro', 'logo_liga.png', 50000.00, 5000.00, 10000.00, 80000.00, 1, '2026-01-26 19:30:38'),
(4, 'LIGA DE PRUEBA', 'logo_1769528139.jpg', 0.00, 0.00, 0.00, 0.00, 1, '2026-01-27 15:35:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `titulo`, `descripcion`, `estado`) VALUES
(1, 'Dashboard', 'Panel de control principal y métricas', 1),
(2, 'Ligas', 'Gestión de ligas (Global)', 1),
(3, 'Jugadores', 'Gestión de jugadores y fichajes', 1),
(4, 'Equipos', 'Administración de equipos y delegados', 1),
(5, 'Torneos', 'Creación de torneos y configuración', 1),
(6, 'Match Center', 'Registro de resultados y arbitraje', 1),
(7, 'Finanzas', 'Pagos, facturación y multas', 1),
(8, 'Usuarios', 'Gestión de usuarios del sistema', 1),
(9, 'Roles', 'Gestión de roles de usuario', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `metodo_pago` varchar(50) DEFAULT 'EFECTIVO',
  `comprobante` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE `partidos` (
  `id_partido` int(11) NOT NULL,
  `id_torneo` int(11) NOT NULL,
  `id_local` int(11) NOT NULL,
  `id_visitante` int(11) NOT NULL,
  `id_arbitro` int(11) DEFAULT NULL,
  `fecha_partido` datetime DEFAULT NULL,
  `goles_local` int(11) DEFAULT 0,
  `goles_visitante` int(11) DEFAULT 0,
  `costo_arbitraje` decimal(10,2) DEFAULT 0.00,
  `arbitraje_pagado` tinyint(1) DEFAULT 0,
  `estado` enum('PENDIENTE','JUGADO','CANCELADO') DEFAULT 'PENDIENTE',
  `id_fase` int(11) DEFAULT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `nro_jornada` int(11) DEFAULT 1,
  `parent_partido_a` int(11) DEFAULT NULL,
  `parent_partido_b` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partidos`
--

INSERT INTO `partidos` (`id_partido`, `id_torneo`, `id_local`, `id_visitante`, `id_arbitro`, `fecha_partido`, `goles_local`, `goles_visitante`, `costo_arbitraje`, `arbitraje_pagado`, `estado`, `id_fase`, `id_grupo`, `nro_jornada`, `parent_partido_a`, `parent_partido_b`) VALUES
(1, 2, 2, 3, NULL, NULL, 0, 0, 0.00, 0, 'PENDIENTE', 1, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partido_eventos`
--

CREATE TABLE `partido_eventos` (
  `id_evento` int(11) NOT NULL,
  `id_partido` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `tipo_evento` enum('GOL','AMARILLA','ROJA','AUTOGOL','CAMBIO') NOT NULL,
  `minuto` int(11) DEFAULT 0,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partido_eventos`
--

INSERT INTO `partido_eventos` (`id_evento`, `id_partido`, `id_jugador`, `id_equipo`, `tipo_evento`, `minuto`, `observacion`, `created_at`) VALUES
(2, 1, 3, 2, 'AMARILLA', 25, '', '2026-01-27 18:57:48'),
(3, 1, 3, 2, 'GOL', 15, '', '2026-01-27 18:57:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `r` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Read/Ver',
  `w` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Write/Crear',
  `u` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Update/Editar',
  `d` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Delete/Eliminar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `id_rol`, `id_modulo`, `r`, `w`, `u`, `d`) VALUES
(8, 2, 1, 1, 1, 1, 1),
(9, 2, 3, 1, 1, 1, 1),
(10, 2, 4, 1, 1, 1, 1),
(11, 2, 5, 1, 1, 1, 1),
(12, 2, 6, 1, 1, 1, 1),
(13, 2, 7, 1, 1, 1, 1),
(17, 1, 1, 1, 1, 1, 1),
(18, 1, 2, 1, 1, 1, 1),
(19, 1, 3, 1, 1, 1, 1),
(20, 1, 4, 1, 1, 1, 1),
(21, 1, 5, 1, 1, 1, 1),
(22, 1, 6, 1, 1, 1, 1),
(23, 1, 7, 1, 1, 1, 1),
(24, 1, 8, 1, 1, 1, 1),
(25, 1, 9, 1, 1, 1, 1),
(26, 4, 1, 0, 0, 0, 0),
(27, 4, 2, 0, 0, 0, 0),
(28, 4, 3, 1, 1, 1, 0),
(29, 4, 4, 0, 0, 0, 0),
(30, 4, 5, 0, 0, 0, 0),
(31, 4, 6, 0, 0, 0, 0),
(32, 4, 7, 0, 0, 0, 0),
(33, 4, 8, 0, 0, 0, 0),
(34, 4, 9, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id_persona` int(11) NOT NULL,
  `id_liga` bigint(20) NOT NULL,
  `identificacion` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT 'default_user.png',
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` text NOT NULL,
  `id_rol` int(11) NOT NULL DEFAULT 4,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_nacimiento` date DEFAULT NULL,
  `posicion` varchar(50) DEFAULT NULL COMMENT 'Portero, Defensa, etc',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id_persona`, `id_liga`, `identificacion`, `nombres`, `apellidos`, `foto`, `telefono`, `email`, `password`, `id_rol`, `estado`, `fecha_nacimiento`, `posicion`, `created_at`) VALUES
(1, 1, '12345678', 'Admin', 'Global', 'default_user.png', NULL, 'admin@globalcup.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 1, 1, NULL, NULL, '2026-01-26 19:30:38'),
(2, 2, '73111405', 'OSVALDO', 'JOSE', 'default_user.png', NULL, 'ligaprueba@globalcup.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 2, 1, NULL, NULL, '2026-01-26 19:42:56'),
(3, 3, '1082876077', 'DIEGO', 'GOMEZ', 'default_user.png', '3366', 'diego@gmail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 2, 1, NULL, NULL, '2026-01-26 19:58:57'),
(4, 4, '9999999', 'DIEGO', 'GOMEZ', 'default_user.png', NULL, 'mail@mail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 2, 1, NULL, NULL, '2026-01-27 15:35:39'),
(8, 0, '5566', 'osvaldo', 'villalobos', 'default_user.png', '3023898254', 'otro@mail.com', 'be41b7f1fa56ba2b0582910053c86cf6ee7e311efc51300220df0918bb9a287b', 4, 1, NULL, NULL, '2026-01-27 17:44:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona_roles`
--

CREATE TABLE `persona_roles` (
  `id_persona_rol` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_liga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona_roles`
--

INSERT INTO `persona_roles` (`id_persona_rol`, `id_persona`, `id_rol`, `id_liga`) VALUES
(1, 1, 2, 1),
(2, 2, 2, 2),
(3, 3, 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`, `estado`, `created_at`) VALUES
(1, 'Super Admin', 'Administrador total del sistema global', 1, '2026-01-26 19:29:16'),
(2, 'Liga Admin', 'Administrador de una liga específica', 1, '2026-01-26 19:29:16'),
(3, 'Delegado', 'Representante de un equipo', 1, '2026-01-26 19:29:16'),
(4, 'Jugador', 'Deportista registrado en el sistema', 1, '2026-01-26 19:29:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneos`
--

CREATE TABLE `torneos` (
  `id_torneo` int(11) NOT NULL,
  `id_liga` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT 'default_torneo.png',
  `categoria` varchar(50) DEFAULT NULL,
  `cuota_jugador` decimal(10,2) DEFAULT 0.00,
  `valor_amarilla` decimal(10,2) DEFAULT 0.00,
  `valor_roja` decimal(10,2) DEFAULT 0.00,
  `valor_arbitraje_base` decimal(10,2) DEFAULT 0.00,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('PROGRAMADO','EN CURSO','FINALIZADO') DEFAULT 'PROGRAMADO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `torneos`
--

INSERT INTO `torneos` (`id_torneo`, `id_liga`, `nombre`, `logo`, `categoria`, `cuota_jugador`, `valor_amarilla`, `valor_roja`, `valor_arbitraje_base`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(2, 4, 'RODILLONES', 'torneo_1769530182.jpg', 'SENIOR', 6000.00, 5000.00, 8000.00, 25000.00, '2026-01-01', '2026-01-31', 'PROGRAMADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneo_equipos`
--

CREATE TABLE `torneo_equipos` (
  `id_torneo` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `pago_inscripcion` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `torneo_equipos`
--

INSERT INTO `torneo_equipos` (`id_torneo`, `id_equipo`, `pago_inscripcion`) VALUES
(2, 2, 0),
(2, 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneo_fases`
--

CREATE TABLE `torneo_fases` (
  `id_fase` int(11) NOT NULL,
  `id_torneo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('GRUPOS','ELIMINACION') NOT NULL DEFAULT 'GRUPOS',
  `ida_vuelta` tinyint(1) DEFAULT 0,
  `orden` int(11) DEFAULT 1,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `torneo_fases`
--

INSERT INTO `torneo_fases` (`id_fase`, `id_torneo`, `nombre`, `tipo`, `ida_vuelta`, `orden`, `estado`) VALUES
(1, 2, 'Octavos', 'GRUPOS', 0, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalles_factura`
--
ALTER TABLE `detalles_factura`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_factura` (`id_factura`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id_equipo`),
  ADD KEY `id_delegado` (`id_delegado`),
  ADD KEY `id_liga` (`id_liga`);

--
-- Indices de la tabla `equipo_jugadores`
--
ALTER TABLE `equipo_jugadores`
  ADD PRIMARY KEY (`id_equipo`,`id_jugador`,`id_torneo`),
  ADD KEY `id_jugador` (`id_jugador`),
  ADD KEY `id_torneo` (`id_torneo`);

--
-- Indices de la tabla `estadisticas_partido`
--
ALTER TABLE `estadisticas_partido`
  ADD PRIMARY KEY (`id_estadistica`),
  ADD KEY `id_partido` (`id_partido`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `fk_estadisticas_jugador` (`id_jugador`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `id_liga` (`id_liga`);

--
-- Indices de la tabla `fase_grupos`
--
ALTER TABLE `fase_grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `id_fase` (`id_fase`);

--
-- Indices de la tabla `fase_grupo_equipos`
--
ALTER TABLE `fase_grupo_equipos`
  ADD PRIMARY KEY (`id_grupo`,`id_equipo`),
  ADD KEY `id_equipo` (`id_equipo`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id_jugador`),
  ADD KEY `id_persona` (`id_persona`),
  ADD KEY `id_liga` (`id_liga`);

--
-- Indices de la tabla `ligas`
--
ALTER TABLE `ligas`
  ADD PRIMARY KEY (`id_liga`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_factura` (`id_factura`);

--
-- Indices de la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD PRIMARY KEY (`id_partido`),
  ADD KEY `id_torneo` (`id_torneo`),
  ADD KEY `id_local` (`id_local`),
  ADD KEY `id_visitante` (`id_visitante`),
  ADD KEY `id_arbitro` (`id_arbitro`),
  ADD KEY `id_fase` (`id_fase`),
  ADD KEY `id_grupo` (`id_grupo`);

--
-- Indices de la tabla `partido_eventos`
--
ALTER TABLE `partido_eventos`
  ADD PRIMARY KEY (`id_evento`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `identificacion` (`identificacion`),
  ADD KEY `fk_persona_rol` (`id_rol`);

--
-- Indices de la tabla `persona_roles`
--
ALTER TABLE `persona_roles`
  ADD PRIMARY KEY (`id_persona_rol`),
  ADD KEY `id_persona` (`id_persona`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `torneos`
--
ALTER TABLE `torneos`
  ADD PRIMARY KEY (`id_torneo`),
  ADD KEY `id_liga` (`id_liga`);

--
-- Indices de la tabla `torneo_equipos`
--
ALTER TABLE `torneo_equipos`
  ADD PRIMARY KEY (`id_torneo`,`id_equipo`),
  ADD KEY `id_equipo` (`id_equipo`);

--
-- Indices de la tabla `torneo_fases`
--
ALTER TABLE `torneo_fases`
  ADD PRIMARY KEY (`id_fase`),
  ADD KEY `id_torneo` (`id_torneo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalles_factura`
--
ALTER TABLE `detalles_factura`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estadisticas_partido`
--
ALTER TABLE `estadisticas_partido`
  MODIFY `id_estadistica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fase_grupos`
--
ALTER TABLE `fase_grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id_jugador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ligas`
--
ALTER TABLE `ligas`
  MODIFY `id_liga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `partidos`
--
ALTER TABLE `partidos`
  MODIFY `id_partido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `partido_eventos`
--
ALTER TABLE `partido_eventos`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `persona_roles`
--
ALTER TABLE `persona_roles`
  MODIFY `id_persona_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `torneos`
--
ALTER TABLE `torneos`
  MODIFY `id_torneo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `torneo_fases`
--
ALTER TABLE `torneo_fases`
  MODIFY `id_fase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_factura`
--
ALTER TABLE `detalles_factura`
  ADD CONSTRAINT `detalles_factura_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`id_delegado`) REFERENCES `personas` (`id_persona`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_equipo_liga` FOREIGN KEY (`id_liga`) REFERENCES `ligas` (`id_liga`) ON DELETE CASCADE;

--
-- Filtros para la tabla `equipo_jugadores`
--
ALTER TABLE `equipo_jugadores`
  ADD CONSTRAINT `fk_ej_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ej_jugador` FOREIGN KEY (`id_jugador`) REFERENCES `jugadores` (`id_jugador`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ej_torneo` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estadisticas_partido`
--
ALTER TABLE `estadisticas_partido`
  ADD CONSTRAINT `estadisticas_partido_ibfk_1` FOREIGN KEY (`id_partido`) REFERENCES `partidos` (`id_partido`) ON DELETE CASCADE,
  ADD CONSTRAINT `estadisticas_partido_ibfk_3` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_estadisticas_jugador` FOREIGN KEY (`id_jugador`) REFERENCES `jugadores` (`id_jugador`) ON DELETE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_liga`) REFERENCES `ligas` (`id_liga`) ON DELETE CASCADE;

--
-- Filtros para la tabla `fase_grupos`
--
ALTER TABLE `fase_grupos`
  ADD CONSTRAINT `fk_grupo_fase` FOREIGN KEY (`id_fase`) REFERENCES `torneo_fases` (`id_fase`) ON DELETE CASCADE;

--
-- Filtros para la tabla `fase_grupo_equipos`
--
ALTER TABLE `fase_grupo_equipos`
  ADD CONSTRAINT `fk_ge_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ge_grupo` FOREIGN KEY (`id_grupo`) REFERENCES `fase_grupos` (`id_grupo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `fk_jugador_liga` FOREIGN KEY (`id_liga`) REFERENCES `ligas` (`id_liga`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_jugador_persona` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`) ON DELETE CASCADE;

--
-- Filtros para la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD CONSTRAINT `partidos_ibfk_1` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE CASCADE,
  ADD CONSTRAINT `partidos_ibfk_2` FOREIGN KEY (`id_local`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE,
  ADD CONSTRAINT `partidos_ibfk_3` FOREIGN KEY (`id_visitante`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE,
  ADD CONSTRAINT `partidos_ibfk_4` FOREIGN KEY (`id_arbitro`) REFERENCES `personas` (`id_persona`) ON DELETE SET NULL;

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `fk_permisos_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_permisos_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE;

--
-- Filtros para la tabla `personas`
--
ALTER TABLE `personas`
  ADD CONSTRAINT `fk_persona_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `persona_roles`
--
ALTER TABLE `persona_roles`
  ADD CONSTRAINT `fk_persona_rol_persona` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_persona_rol_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE;

--
-- Filtros para la tabla `torneos`
--
ALTER TABLE `torneos`
  ADD CONSTRAINT `torneos_ibfk_1` FOREIGN KEY (`id_liga`) REFERENCES `ligas` (`id_liga`) ON DELETE CASCADE;

--
-- Filtros para la tabla `torneo_equipos`
--
ALTER TABLE `torneo_equipos`
  ADD CONSTRAINT `torneo_equipos_ibfk_1` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE CASCADE,
  ADD CONSTRAINT `torneo_equipos_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `torneo_fases`
--
ALTER TABLE `torneo_fases`
  ADD CONSTRAINT `fk_fase_torneo` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
