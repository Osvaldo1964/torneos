-- ========================================
-- SCRIPT DE ACTUALIZACIÓN: MÓDULO DE POSICIONES
-- Fecha: 27 de Enero, 2026
-- ========================================

-- 1. Insertar el módulo de Posiciones en la tabla modulos
INSERT INTO `modulos` (`id_modulo`, `titulo`, `descripcion`, `estado`) VALUES
(10, 'Posiciones', 'Tabla de posiciones y estadísticas por grupo', 1);

-- 2. Asignar permisos completos al Super Admin (Rol 1)
INSERT INTO `permisos` (`id_rol`, `id_modulo`, `r`, `w`, `u`, `d`) VALUES
(1, 10, 1, 1, 1, 1);

-- 3. Asignar permisos de solo lectura al Liga Admin (Rol 2)
INSERT INTO `permisos` (`id_rol`, `id_modulo`, `r`, `w`, `u`, `d`) VALUES
(2, 10, 1, 0, 0, 0);

-- 4. Asignar permisos de solo lectura al Delegado (Rol 3)
INSERT INTO `permisos` (`id_rol`, `id_modulo`, `r`, `w`, `u`, `d`) VALUES
(3, 10, 1, 0, 0, 0);

-- 5. Asignar permisos de solo lectura al Jugador (Rol 4)
INSERT INTO `permisos` (`id_rol`, `id_modulo`, `r`, `w`, `u`, `d`) VALUES
(4, 10, 1, 0, 0, 0);

-- ========================================
-- VERIFICACIÓN
-- ========================================
-- Ejecutar esta consulta para verificar que el módulo fue creado correctamente:
-- SELECT * FROM modulos WHERE id_modulo = 10;
-- SELECT * FROM permisos WHERE id_modulo = 10;
