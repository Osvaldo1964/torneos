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
- [x] [Ver Documentación Técnica del Motor](MOTOR_COMPETICION.md)

### 4. Módulos Administrativos (CRUDs)
- [x] **Módulo de Ligas:** Gestión de configuración, logos y parámetros financieros.
- [x] **Módulo de Torneos:** Creación de certámenes, categorías y carga de logos.
- [x] **Módulo de Equipos:** Registro de equipos con escudos y asignados a ligas.
- [x] **Módulo de Jugadores:** Registro completo con fotografía, datos personales y perfil técnico.
- [x] **Módulo de Nóminas:** Interfaz visual para inscribir equipos en torneos y asignar jugadores con dorsales.
- [x] **Módulo de Posiciones:** Tabla de posiciones dinámica con estadísticas completas, racha de equipos y goleadores. [Ver Documentación](MODULO_POSICIONES.md)

---

## 🛠️ Plan General de Desarrollo (Roadmap 2026)

### Fase 1: Finalización Deportiva (EN CURSO)
- [x] **Tabla de Posiciones Dinámica:** Generación de la tabla con puntos, DG, GF, GC basada en los encuentros jugados. ✅ *Completado: 27/01/2026*
- [ ] **Estadísticas Individuales:** Ranking de goleadores y valla menos vencida.
- [ ] **Tablero de Inhabilitados:** Interfaz para delegados donde se listan jugadores sancionados para la siguiente fecha.

### Fase 2: Motor Financiero
- [ ] **Facturación Automática:** Generación de cobros por arbitraje y mensualidades.
- [ ] **Módulo de Pagos:** Registro de ingresos y control de morosidad por equipo/jugador.

---

## 🔍 Notas Técnicas Recientes

### Módulo de Posiciones (27/01/2026)
**Implementación Completa:** Se desarrolló el sistema integral de tabla de posiciones con las siguientes características:

#### Funcionalidades Principales:
- **Cálculo Dinámico:** Las posiciones se calculan en tiempo real basándose en los partidos jugados, sin almacenamiento en tablas adicionales
- **Estadísticas Completas:** PJ, PG, PE, PP, GF, GC, DG, PTS calculados automáticamente
- **Criterios de Desempate:** Ordenamiento por Puntos → Diferencia de Goles → Goles a Favor
- **Racha de Equipos:** Visualización de los últimos 5 resultados (Victoria/Empate/Derrota)
- **Top Goleadores:** Ranking de los 10 mejores goleadores del grupo
- **Estadísticas del Grupo:** Partidos jugados, goles totales, promedio y equipo líder

#### Aspectos Técnicos:
- **6 Endpoints REST:** Torneos, Fases, Grupos, Tabla, Racha, Goleadores
- **Filtros Jerárquicos:** Liga → Torneo → Fase → Grupo con carga dinámica
- **Multi-tenancy:** Super Admin ve todas las ligas, usuarios normales solo la suya
- **Seguridad:** Todos los endpoints requieren JWT válido
- **UI/UX:** Destacado visual de top 3, escudos de equipos, fotos de jugadores

#### Correcciones Realizadas:
- Rutas de imágenes corregidas (equipos/ y jugadores/ en lugar de uploads/)
- Loop infinito de imágenes resuelto con `this.onerror=null`
- Orden de carga de scripts optimizado con sistema `page_js`
- Token JWT corregido de `'token'` a `'gc_token'`
- Estadísticas adicionales implementadas con cálculos automáticos
- Contenido de boxes centrado para mejor presentación

#### Archivos Creados:
- `api/Models/PosicionesModel.php` (157 líneas)
- `api/Controllers/Posiciones.php` (279 líneas)
- `app/posiciones.php` (270 líneas)
- `app/assets/js/functions_posiciones.js` (507 líneas)
- `docs/MODULO_POSICIONES.md` (Documentación técnica)
- `docs/INSTALACION_POSICIONES.md` (Guía de instalación)
- `update_posiciones.sql` (Script de instalación)

#### Próximas Mejoras Sugeridas:
- Exportación a PDF y Excel
- Gráficas de evolución de posiciones
- Historial de posiciones por jornada
- Comparación entre grupos

---

### Notas Anteriores:
*   **Ajuste de Esquema:** Se corrigió el uso de la columna `escudo` en lugar de `logo` para los equipos, unificando la API con el frontend.
*   **Optimización de UI:** El modal de resultados fue rediseñado con tamaño `modal-lg` y carga dinámica de nómadas para soportar la gestión masiva de eventos.
*   **Limpieza de Entorno:** Se han eliminado scripts de diagnóstico de bases de datos, manteniendo solo el núcleo funcional y la documentación.

---

## 📚 Documentación Adicional

- **[../README.md](../README.md)** - Guía de inicio rápido
- **[ESTADO_PROYECTO.md](ESTADO_PROYECTO.md)** - Estado detallado del proyecto
- **[MOTOR_COMPETICION.md](MOTOR_COMPETICION.md)** - Documentación del motor de competición
- **[MODULO_POSICIONES.md](MODULO_POSICIONES.md)** - Documentación técnica del módulo de posiciones
- **[INSTALACION_POSICIONES.md](INSTALACION_POSICIONES.md)** - Guía de instalación del módulo
