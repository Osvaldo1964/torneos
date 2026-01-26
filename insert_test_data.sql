-- Limpiar tablas antes de insertar (Opcional, para pruebas limpias)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `pagos`;
TRUNCATE TABLE `detalles_factura`;
TRUNCATE TABLE `facturas`;
TRUNCATE TABLE `estadisticas_partido`;
TRUNCATE TABLE `partidos`;
TRUNCATE TABLE `equipo_jugadores`;
TRUNCATE TABLE `torneo_equipos`;
TRUNCATE TABLE `torneos`;
TRUNCATE TABLE `equipos`;
TRUNCATE TABLE `personas`;
TRUNCATE TABLE `ligas`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insertar una Liga de Prueba
INSERT INTO `ligas` (`id_liga`, `nombre`, `logo`, `cuota_mensual_jugador`, `valor_amarilla`, `valor_roja`, `valor_arbitraje_base`, `estado`) 
VALUES (1, 'Liga Global Cup Pro', 'logo_liga.png', 50000.00, 5000.00, 10000.00, 80000.00, 1);

-- 2. Insertar Administrador de la Liga (Contraseña: 'admin123' en SHA256)
-- La clave 'admin123' en SHA256 es: 240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9
-- id_rol = 2 corresponde a 'Liga Admin'
INSERT INTO `personas` (`id_persona`, `id_liga`, `identificacion`, `nombres`, `apellidos`, `email`, `password`, `id_rol`) 
VALUES (1, 1, '12345678', 'Admin', 'Global', 'admin@globalcup.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 2);
