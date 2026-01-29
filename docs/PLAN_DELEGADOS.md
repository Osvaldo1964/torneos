# Plan de Implementación: Asignación de Delegados a Equipos

Este documento detalla los pasos para completar la funcionalidad de asignar Delegados a Equipos y asegurar que la jerarquía de permisos se respete en todo el ciclo de vida de la gestión de equipos y jugadores.

## Estado Actual
- **Roles y Usuarios:** La API de `Roles` y `Usuarios` ya filtra correctamente según el rol del usuario logueado (Liga Admin vs Super Admin).
- **Autoedición:** Los usuarios pueden editarse a sí mismos sin elevar privilegios.
- **Competición:** Métodos críticos de escritura en `Competicion.php` están protegidos.
- **Base de Datos:** La tabla `equipos` ya tiene la columna `id_delegado`.

## Objetivos
1. **Gestión de Equipos (Creación/Edición):** Permitir asignar un usuario "Delegado" a un equipo.
2. **Restricción de Delegados:** Asegurar que un Delegado solo pueda gestionar (CRUD) los jugadores de *sus* propios equipos asignados.

---

## Pasos de Implementación

### Fase 1: Asignación en Módulo Equipos (Admin)
**Objetivo:** Que el Liga Admin pueda seleccionar un Delegado al crear/editar un equipo.

1. **Backend - `api/Controllers/Equipos.php`:**
   - Verificar método `setEquipo`.
   - Asegurar que reciba y guarde `id_delegado`.
   - Validar que el `id_delegado` proporcionado sea realmente un usuario con rol Delegado (3) y de la misma liga.

2. **Backend - `api/Models/EquiposModel.php`:**
   - Actualizar sentencias INSERT/UPDATE para incluir la columna `id_delegado`.

3. **Frontend - `app/assets/js/equipos.js`:**
   - Agregar función `fntLoadDelegados()` para llenar un `<select>` en el modal de equipos.
   - Esta función debe llamar a `Usuarios/getUsuarios` (que ya está filtrado) y filtrar en el front solo aquellos con `id_rol == 3` (Delegado), o crear un endpoint específico si se prefiere (usaremos el filtro JS por simplicidad si la lista no es enorme).

4. **Frontend - `app/equipos.php`:**
   - Agregar el campo `<select id="id_delegado">` en el formulario modal.

### Fase 2: Restricción de Acceso para Delegados
**Objetivo:** Que cuando un Delegado entre al sistema, solo pueda gestionar "sus" cosas.

1. **Backend - `api/Controllers/Jugadores.php`:**
   - Modificar `getJugadores`:
     - Si es Delegado (Rol 3): Filtrar jugadores que pertenezcan a equipos donde `equipos.id_delegado == id_usuario_actual`.
   - Modificar `setJugador`:
     - Al crear/editar, verificar que el equipo al que se asigna el jugador pertenece al Delegado logueado.

2. **Backend - `api/Controllers/Equipos.php` (Lectura para Delegado):**
   - Modificar `getEquipos`:
     - Si es Delegado (Rol 3): Solo devolver los equipos donde él es el delegado.

### Fase 3: Pruebas de Flujo
1. **Como Liga Admin:**
   - Crear Usuario Delegado "Del1".
   - Crear Equipo "Eq1" y asignar a "Del1".
2. **Como Delegado "Del1":**
   - Iniciar sesión.
   - Ir a "Jugadores".
   - Crear Jugador -> Solo debería dejar asignarlo a "Eq1".
   - Intentar ver equipos -> Solo debería ver "Eq1".

---

## Notas de Seguridad
- El Delegado NO debe poder cambiarse de equipo a sí mismo ni editar el equipo en sí (nombre, escudo), solo gestionar su nómina (jugadores).
- Se debe validar en el Backend que el Delegado no manipule IDs de equipos ajenos al enviar peticiones POST.
