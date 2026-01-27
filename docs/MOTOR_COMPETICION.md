# ⚙️ Motor de Competición (Competition Engine)
**Versión de Documentación:** 1.0.0
**Última Revisión:** 27 de enero de 2026

Este documento detalla el funcionamiento técnico del motor de competencia desarrollado para la Global Cup, incluyendo la generación de fixtures, gestión de eventos y lógica de sanciones.

## 🏗️ Arquitectura de la Competencia

El sistema organiza los torneos en una jerarquía de tres niveles:
1. **Torneo**: Entidad principal (ej. Apertura 2026).
2. **Fase**: Etapas del torneo (ej. Fase de Grupos, Octavos, Final).
3. **Grupo**: Subdivisiones dentro de una fase donde se agrupan los equipos.

### Tablas Clave
- `torneo_fases`: Define el nombre y tipo de fase (Eliminación Directa o Todos contra Todos).
- `fase_grupos`: Define los grupos asociados a una fase.
- `fase_grupo_equipos`: Vincula equipos específicos a un grupo.
- `partidos`: Almacena la programación, resultados y metadatos del encuentro.
- `partido_eventos`: Registro detallado de sucesos (Goles, Tarjetas, Cambios) por minuto.

---

## 📅 Generación de Fixtures (Round Robin)

Se implementó el algoritmo de **Round Robin** (Todos contra todos) para la generación automática de encuentros.

### Funcionamiento:
1. El sistema toma los equipos vinculados a un grupo.
2. Si el número de equipos es impar, se añade un equipo "Dummy" (descanso).
3. Se rotan los equipos de forma horaria manteniendo uno fijo para garantizar que todos jueguen contra todos en el menor número de jornadas posible.
4. Genera `N-1` jornadas para un grupo de `N` equipos.

**Endpoint:** `Competicion/generarFixture/{idFase}`

---

## 📝 Planilla de Juego e Interfaz de Resultados

La gestión de resultados se realiza a través de un **modal ampliado (lg)** para garantizar una experiencia de usuario fluida, permitiendo:
- Registro de marcador final (Goles Local vs Visitante).
- Cambio de estado del encuentro: `PENDIENTE`, `JUGADO`, `CANCELADO`.
- **Eventos Granulares**: Registro por jugador y minuto de Goles, Autogoles, Tarjetas Amarillas y Rojas.
- **Optimización de UI**: Interfaz de carga rápida que precarga las nóminas de ambos equipos para evitar errores de digitación.

### Sincronización de Datos
Al guardar una planilla, el sistema:
1. Actualiza el marcador y estado en la tabla `partidos`.
2. Limpia los eventos anteriores del partido.
3. Inserta los nuevos eventos detallados en `partido_eventos`.

---

## ⚖️ Sistema de Sanciones y Siguiente Fecha

El motor incluye una lógica para determinar la elegibilidad de los jugadores basada en el reglamento estándar:

### Lógica de Suspensión:
- **Acumulación de Amarillas**: Por defecto, un jugador que acumule **3 tarjetas amarillas** en el mismo torneo queda inhabilitado para el siguiente encuentro.
- **Tarjeta Roja**: Una expulsión (Roja Directa) genera una suspensión automática inmediata.
- **Reset de Tarjetas**: El sistema permite consultar el histórico para decidir si las tarjetas se limpian en fases avanzadas (ej. de Fase de Grupos a Cuartos).

**Método de Consulta:** `CompeticionModel::selectSancionados($idTorneo)`

---

## 🔍 Notas de Seguridad y Acceso
- **Multi-tenant**: Los torneos están aislados por `id_liga`. Un delegado solo gestiona su liga.
- **Super Admin**: El usuario con `id_liga = 1` tiene visibilidad global sobre todos los torneos activos del sistema para soporte y supervisión.
- **Integridad**: No se pueden generar fixtures si el grupo no tiene al menos 2 equipos vinculados.
