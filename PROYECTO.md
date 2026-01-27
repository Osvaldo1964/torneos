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

### 2. Estructura de Datos Avanzada
- [x] **Separación Identidad/Perfil:** Diferenciación clara entre la tabla `personas` (cuenta de acceso) y `jugadores` (perfil deportivo). Permite que un administrador sea también jugador.
- [x] **Nóminas por Torneo:** Sistema de vinculación de jugadores a equipos con asignación de dorsales específica para cada certamen.

### 3. Módulos Administrativos (CRUDs)
- [x] **Módulo de Ligas:** Gestión de configuración, logos y parámetros financieros.
- [x] **Módulo de Torneos:** Creación de certámenes, categorías y carga de logos.
- [x] **Módulo de Equipos:** Registro de equipos con escudos y asignados a ligas.
- [x] **Módulo de Jugadores:** Registro completo con fotografía, datos personales y perfil técnico.
- [x] **Módulo de Nóminas:** Interfaz visual para inscribir equipos en torneos y asignar jugadores con dorsales.

### 4. Frontend y Experiencia de Usuario
- [x] **Dashboard:** Estructura base con Sidebar dinámico.
- [x] **Assets Locales:** Eliminación total de dependencias de CDNs externos (FontAwesome, Bootstrap, DataTables incluidos localmente).
- [x] **Landing Page:** Interfaz pública de alto impacto para auto-registro.

---

## 🛠️ Plan General de Desarrollo (Roadmap 2026)

### Fase 1: Motor de Competencia (EN CURSO)
- [ ] **Calendario Automático:** Generación de fixtures basados en equipos inscritos.
- [ ] **Programación de Partidos:** Asignación de fechas, horas y canchas.
- [ ] **Planillas de Juego:** Interfaz para árbitros/delegados para reportar resultados y eventos (goles, tarjetas).

### Fase 2: Gestión de Estadísticas
- [ ] **Tabla de Posiciones:** Cálculo automático de puntos, DG, GF, GC.
- [ ] **Goleadores y Valla Menos Vencida:** Ranking en tiempo real.
- [ ] **Sistema de Sanciones:** Control automático de fechas de suspensión por tarjetas acumuladas.

### Fase 3: Motor Financiero
- [ ] **Facturación Automática:** Generación de cobros por arbitraje y mensualidades.
- [ ] **Módulo de Pagos:** Registro de ingresos y control de morosidad por equipo/jugador.

---

## � Notas Técnicas Recientes
*   **Modelo de Perfiles:** Se eliminó la dependencia directa de `equipo_jugadores` con `personas`. Ahora se usa la tabla intermedia `jugadores` para permitir que un mismo usuario tenga múltiples roles sociales y deportivos.
*   **Integridad Reforzada:** Todas las relaciones de base de datos cuentan con Foreign Keys con `ON DELETE CASCADE` para mantener la limpieza del sistema.
*   **Optimización de Archivos:** Se han eliminado scripts de diagnóstico y depuración, dejando un entorno de producción limpio.
