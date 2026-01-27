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
### 3. Registro Público de Ligas
- [x] **Flujo Auto-Registro:** Landing page con modal para crear ligas y administradores automáticamente.
- [x] **Seguridad Unificada:** JWT incluye `id_liga` y `id_rol` para aislamiento total de datos.
- [x] **UX Mejorada:** Icono de acceso rápido al sitio público desde el admin.

### 4. Módulo de Ligas
- [x] **CRUD Ligas:** Gestión completa para Super Admin y configuración personalizada para Liga Admin.
- [x] **Local Assets:** Eliminación total de dependencias de CDNs externos.

### 3. Frontend Administrativo (APP)
- [x] **Login Pro:** Interfaz moderna que consume la API y gestiona el ciclo de vida del JWT.
- [x] **Dashboard:** Estructura base con Sidebar dinámico y plantillas unificadas.
- [x] **Landing Page:** Página de inicio pública de alto impacto con acceso al sistema.

---

## 🛠️ Plan General de Desarrollo (Roadmap 2026)

### Fase 1: Migración y Core (EN CURSO)
Objetivo: Migrar todos los módulos- [x] Configuración inicial y arquitectura API-First.
- [x] Login con JWT y multitenencia por `id_liga`.
- [x] Módulo de Roles (CRUD y Permisos iOS-style).
- [x] Módulo de Usuarios/Personas (Seguridad jerárquica).
- [x] Módulo de Ligas (Configuración base y Logo).
- [ ] **Módulo de Torneos** (Configuración financiera descentralizada).
- [ ] Módulo de Equipos (Escudos y delegados).
- [ ] Módulo de Jugadores (Nóminas y dorsales).
- [ ] Calendario y Resultados (Encuentros).
- [ ] Motor Financiero (Facturación de multas y mensualidades).

## Próximos Pasos (Inmediato)
1. **Torneos**: Implementar la gestión de torneos donde cada torneo define su propia categoría y lista de precios (multas, arbitraje).
2. **Equipos**: Registro de equipos vinculados a la liga y a los torneos.

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
