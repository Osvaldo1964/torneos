SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- 1. Agregar columna id_liga a EQUIPOS
ALTER TABLE `equipos` ADD `id_liga` INT(11) NOT NULL AFTER `id_equipo`;

-- 2. Crear índice para búsquedas rápidas
ALTER TABLE `equipos` ADD KEY `id_liga` (`id_liga`);

-- 3. Asignar liga por defecto a equipos existentes (Para evitar errores de FK)
-- Asumimos ID Liga 1, puedes cambiarlo si necesitas
UPDATE equipos SET id_liga = 1 WHERE id_liga = 0;

-- 4. Crear Foreing Key para integridad referencial
ALTER TABLE `equipos` ADD CONSTRAINT `fk_equipo_liga` FOREIGN KEY (`id_liga`) REFERENCES `ligas` (`id_liga`) ON DELETE CASCADE;

COMMIT;
