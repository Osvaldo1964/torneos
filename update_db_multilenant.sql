ALTER TABLE `personas` ADD `id_liga` INT NULL AFTER `id_persona`, ADD `password` VARCHAR(255) NULL AFTER `email`, ADD `rol` ENUM('ADMIN','LIGA_ADMIN','DELEGADO','JUGADOR') DEFAULT 'JUGADOR' AFTER `password`;
ALTER TABLE `personas` ADD CONSTRAINT `fk_persona_liga` FOREIGN KEY (`id_liga`) REFERENCES `ligas`(`id_liga`) ON DELETE SET NULL;

-- Modificar equipos para que pertenezcan a una liga directamente
ALTER TABLE `equipos` ADD `id_liga` INT NOT NULL AFTER `id_equipo`;
ALTER TABLE `equipos` ADD CONSTRAINT `fk_equipo_liga` FOREIGN KEY (`id_liga`) REFERENCES `ligas`(`id_liga`) ON DELETE CASCADE;
