-- 1. Crear tabla de roles
CREATE TABLE `roles` (
  `id_rol` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_rol` VARCHAR(50) NOT NULL,
  `descripcion` TEXT,
  `estado` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Insertar roles iniciales
INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES 
(1, 'Super Admin', 'Administrador total del sistema global'),
(2, 'Liga Admin', 'Administrador de una liga específica'),
(3, 'Delegado', 'Representante de un equipo'),
(4, 'Jugador', 'Deportista registrado en el sistema');

-- 3. Modificar la tabla personas
-- Primero eliminamos el campo anterior y agregamos la llave foránea
ALTER TABLE `personas` DROP COLUMN `rol`;
ALTER TABLE `personas` ADD `id_rol` INT NOT NULL DEFAULT 4 AFTER `password`;
ALTER TABLE `personas` ADD CONSTRAINT `fk_persona_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles`(`id_rol`) ON DELETE RESTRICT;
