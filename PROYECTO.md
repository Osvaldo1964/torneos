# 🏆 Proyecto: Global Cup - Sistema de Gestión Deportiva (Nueva Arquitectura)

Este documento detalla el progreso actual, el plan maestro y los hitos pendientes del sistema integral de gestión para ligas de fútbol, ahora bajo una arquitectura desacoplada (API + APP + MÓVIL).

## 🚀 Estado Actual del Proyecto
**Versión:** 1.0.0 (Re-arquitectura Exitosa)
**Última Actualización:** 27 de Enero, 2026
**Arquitectura:** API-First (Backend PHP + JWT, Frontend desacoplado con Bootstrap 5).

---

## ✅ Hitos Completados (Nueva Estructura)

### 1. Infraestructura y Seguridad (API)
- [x] **API Core:** Sistema base con enrutamiento dinámico, controladores y modelos.
- [x] **Seguridad JWT:** Implementado sistema de tokens con duración de **1 hora** (3600s).
- [x] **Conexión PDO:** Capa de datos optimizada y segura contra inyecciones SQL.
- [x] **Local Assets:** Todas las librerías (Bootstrap, FontAwesome, DataTables, jQuery, SweetAlert2) cargadas localmente para máxima velocidad y privacidad.

### 2. Módulo de Roles y Permisos
- [x] **Gestión de Roles:** CRUD completo de roles (SuperAdmin, Liga Admin, Delegado, Jugador).
- [x] **Sistema de Permisos (Wow UI):** 
    * Interfaz tipo switch (iOS style) para asignar Leer, Escribir, Actualizar y Eliminar por módulo.
    * Integración total con la API.

### 3. Frontend Administrativo (APP)
- [x] **Login Pro:** Interfaz moderna que consume la API y gestiona el ciclo de vida del JWT.
- [x] **Dashboard:** Estructura base con Sidebar dinámico y plantillas unificadas.
- [x] **Landing Page:** Página de inicio pública de alto impacto con acceso al sistema.

---

## 🛠️ Plan General de Desarrollo (Roadmap 2026)

### Fase 1: Migración y Core (EN CURSO)
Objetivo: Migrar todos los módulos existentes a la nueva arquitectura API-First.
- [ ] Módulo de Usuarios (Personas).
- [ ] Módulo de Ligas.
- [ ] Módulo de Equipos.

### Fase 2: Torneos y Competencia (PENDIENTE)
Objetivo: Automatizar la creación de calendarios y el registro de resultados.

### Fase 3: Motor Financiero (PENDIENTE)
Objetivo: Generación automática de facturas por mensualidades y multas (tarjetas).

---

## 📋 Tareas Pendientes Inmediatas

### Prioridad Alta
- [ ] **Módulo de Ligas:** Implementar el CRUD de ligas consumiendo la nueva API.
- [ ] **Módulo de Usuarios:** Registro de personas asignando roles y ligas.
- [ ] **Validación de Permisos del lado de la APP:** Ocultar/mostrar botones según el rol.

### Prioridad Media
- [ ] **App Móvil:** Iniciar el desarrollo de la interfaz `/app-movil` para consulta de resultados.

---

## 📝 Notas Técnicas
*   **Tokens:** Duración de 1 hora. Se requiere re-login al expirar (mejor seguridad).
*   **Offline First:** El sistema no depende de CDNs externos para sus funciones principales.
*   **Aislamiento:** La API filtra los datos según el `id_liga` asociado al usuario en el token.
