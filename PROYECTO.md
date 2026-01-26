# 🏆 Proyecto: Global Cup - Sistema de Gestión Deportiva

Este documento detalla el progreso actual, el plan maestro y los hitos pendientes del sistema integral de gestión para ligas de fútbol.

## 🚀 Estado Actual del Proyecto
**Versión:** 0.8.0 (Fase de Estructuración de Datos)
**Última Actualización:** 26 de Enero, 2026

---

## ✅ Hitos Completados

### 1. Infraestructura y Seguridad
- [x] **Base de Datos:** Diseño relacional completo con 11 tablas (Ligas, Personas, Equipos, Torneos, Facturación).
- [x] **Arquitectura MVC:** Implementación de Framework personalizado en PHP.
- [x] **Seguridad JWT:** Sistema de autenticación con tokens de 60 minutos y renovación por sesión.
- [x] **Auto-Registro Público:** Landing page funcional con opción para que nuevas ligas se inscriban solas.

### 2. Panel Administrativo (Core)
- [x] **Gestión de Ligas:** Módulo para que el Super Admin controle las ligas y sus costos base.
- [x] **Módulo de Jugadores:** 
    * Registro detallado (DNI, posición, fecha nac).
    * Subida de fotos de perfil.
    * Aislamiento por Liga (Multitenant).
- [x] **Módulo de Equipos:**
    * Creación de clubes con escudos/logos.
    * Asignación de delegados.
- [x] **Gestión de Nóminas (Plantillas):** 
    * Interfaz visual para "fichar" jugadores de la liga hacia un equipo específico.
    * Creación automática de torneo inicial ("Apertura 2026") para habilitar asociaciones.

### 3. Diseño y UX
- [x] **Paleta Premium:** Implementación de Navy Blue, Electric Blue y Emerald Green.
- [x] **Navegación Unificada:** Sidebar dinámico que resalta la sección activa.
- [x] **Responsive:** Tablas e interfaces adaptables.

---

## 🛠️ Plan General de Desarrollo

### Fase 1: Estructura y Nóminas (FINALIZADA)
Objetivo: Tener ligas, equipos y jugadores creados y vinculados.

### Fase 2: Torneos y Competencia (EN CURSO)
Objetivo: Automatizar la creación de calendarios y el registro de resultados.

### Fase 3: Motor Financiero (PENDIENTE)
Objetivo: Generación automática de facturas por mensualidades y multas (tarjetas).

### Fase 4: App Móvil y Consultas (PENDIENTE)
Objetivo: Interfaz para que jugadores y delegados vean sus estadísticas.

---

## 📋 Tareas Pendientes (Waitlist)

### Prioridad Alta: Torneos
- [ ] **Módulo de Torneos:** Definir categorías, fechas de inicio/fin y premios.
- [ ] **Generador de Fixture:** Algoritmo para crear jornadas (Todos contra todos).
- [ ] **Match Center:** Pantalla para que el árbitro/admin registre:
    * Marcador final.
    * Goleadores y Asistencias.
    * Tarjetas (reporte disciplinario).

### Prioridad Media: Finanzas
- [ ] **Configuración de Costos:** Ajustar valores de amarillas/rojas por torneo.
- [ ] **Facturación Automática:** Cronjob o disparador que genere cobros según el reporte del Match Center.
- [ ] **Pasarela de Pagos:** Integración de recibos y comprobantes de pago.

### Prioridad Baja: Plus y Ajustes
- [ ] **Módulo PQR:** Backend para procesar peticiones, quejas y reclamos desde la landing.
- [ ] **Reportes PDF:** Generación de carnets de jugadores y actas de partidos.
- [ ] **Estadísticas Globales:** Top 10 goleadores y valla menos vencida de la liga.

---

## 📝 Notas de Versión
*   *Aislamiento:* Se garantiza que la Liga A nunca verá los jugadores o finanzas de la Liga B.
*   *Seguridad:* Todas las contraseñas de jugadores se pre-configuran como su número de identificación (hasheado).
