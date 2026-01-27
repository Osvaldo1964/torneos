# 🏆 Proyecto: Global Cup - Sistema de Gestión Deportiva (Nueva Arquitectura)

Este documento detalla el progreso actual, el plan maestro y los hitos pendientes del sistema integral de gestión para ligas de fútbol, ahora bajo una arquitectura desacoplada (API + APP + MÓVIL).

## 🚀 Estado Actual del Proyecto
**Versión:** 1.1.0 (Arquitectura de Perfiles Cooperativos)
**Última Actualización:** 27 de Enero, 2026
**Arquitectura:** API-First (Backend PHP + JWT, Frontend desacoplado con Bootstrap 5).

---

## ✅ Hitos Completados (Nueva Estructura)

### 1. Infraestructura y Seguridad (API)
- [x] **API Core:** Sistema base con enrutamiento dinámico, controladores y modelos.
- [x] **Seguridad JWT:** Implementado sistema de tokens con duración de **1 hora** (3600s).
- [x] **Aislamiento Multitenant:** Datos filtrados automáticamente por `id_liga` según el token.
- [x] **Visibilidad Global:** Super Administrador (Liga 1) con acceso a todos los torneos.

### 2. Estructura de Datos Avanzada
- [x] **Separación Identidad/Perfil:** Diferenciación clara entre la tabla `personas` (cuenta de acceso) y `jugadores` (perfil deportivo). Permite que un administrador sea también jugador.
- [x] **Nóminas por Torneo:** Sistema de vinculación de jugadores a equipos con asignación de dorsales específica para cada certamen.

### 3. Motor de Competición
- [x] **Calendario Automático:** Implementación del algoritmo Round Robin para generación de fixtures.
- [x] **Planilla de Juego Digital:** Registro de resultados, goleadores y tarjetas por jugador/minuto.
- [x] **Sistema de Sanciones:** Lógica de seguimiento de amarillas acumuladas y tarjetas rojas.
- [x] [Ver Documentación Técnica del Motor](docs/MOTOR_COMPETICION.md)

### 4. Módulos Administrativos (CRUDs)
- [x] **Módulo de Ligas:** Gestión de configuración, logos y parámetros financieros.
- [x] **Módulo de Torneos:** Creación de certámenes, categorías y carga de logos.
- [x] **Módulo de Equipos:** Registro de equipos con escudos y asignados a ligas.
- [x] **Módulo de Jugadores:** Registro completo con fotografía, datos personales y perfil técnico.
- [x] **Módulo de Nóminas:** Interfaz visual para inscribir equipos en torneos y asignar jugadores con dorsales.

---

## 🛠️ Plan General de Desarrollo (Roadmap 2026)

### Fase 1: Finalización Deportiva (EN CURSO)
- [ ] **Tabla de Posiciones Dinámica:** Generación de la tabla con puntos, DG, GF, GC basada en los encuentros jugados.
- [ ] **Estadísticas Individuales:** Ranking de goleadores y valla menos vencida.
- [ ] **Tablero de Inhabilitados:** Interfaz para delegados donde se listan jugadores sancionados para la siguiente fecha.

### Fase 2: Motor Financiero
- [ ] **Facturación Automática:** Generación de cobros por arbitraje y mensualidades.
- [ ] **Módulo de Pagos:** Registro de ingresos y control de morosidad por equipo/jugador.

---

## 🔍 Notas Técnicas Recientes
*   **Ajuste de Esquema:** Se corrigió el uso de la columna `escudo` en lugar de `logo` para los equipos, unificando la API con el frontend.
*   **Optimización de UI:** El modal de resultados fue rediseñado con tamaño `modal-lg` y carga dinámica de nómadas para soportar la gestión masiva de eventos.
*   **Limpieza de Entorno:** Se han eliminado scripts de diagnóstico de bases de datos, manteniendo solo el núcleo funcional y la documentación.
