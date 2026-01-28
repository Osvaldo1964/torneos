# 🚀 Instalación del Módulo de Posiciones

Este documento describe los pasos para instalar y configurar el módulo de **Tabla de Posiciones** en el sistema Global Cup.

---

## ✅ Pre-requisitos

- Sistema Global Cup instalado y funcionando
- Acceso a la base de datos MySQL
- Servidor web (Apache/XAMPP) corriendo
- PHP 7.4 o superior

---

## 📦 Archivos del Módulo

### Backend (API)
```
api/
├── Models/
│   └── PosicionesModel.php          ✅ Modelo de datos
├── Controllers/
│   └── Posiciones.php               ✅ Controlador de API
```

### Frontend (APP)
```
app/
├── posiciones.php                   ✅ Vista principal
└── assets/
    └── js/
        └── functions_posiciones.js  ✅ Lógica JavaScript
```

### Documentación
```
docs/
└── MODULO_POSICIONES.md            ✅ Documentación técnica
```

### Scripts SQL
```
update_posiciones.sql               ✅ Script de instalación
```

---

## 🔧 Pasos de Instalación

### 1. Ejecutar Script SQL

Abre tu cliente MySQL (phpMyAdmin, MySQL Workbench, o terminal) y ejecuta:

```bash
# Opción 1: Desde terminal
mysql -u root -p db-globalcup < update_posiciones.sql

# Opción 2: Desde phpMyAdmin
# - Selecciona la base de datos 'db-globalcup'
# - Ve a la pestaña 'SQL'
# - Copia y pega el contenido de 'update_posiciones.sql'
# - Haz clic en 'Continuar'
```

### 2. Verificar Instalación en Base de Datos

Ejecuta estas consultas para verificar:

```sql
-- Verificar que el módulo fue creado
SELECT * FROM modulos WHERE id_modulo = 10;

-- Verificar permisos asignados
SELECT * FROM permisos WHERE id_modulo = 10;
```

**Resultado esperado:**
- 1 registro en `modulos` con id_modulo = 10
- 4 registros en `permisos` (uno por cada rol)

### 3. Verificar Archivos

Asegúrate de que todos los archivos estén en su lugar:

```bash
# Backend
✅ c:\xampp\htdocs\torneos\api\Models\PosicionesModel.php
✅ c:\xampp\htdocs\torneos\api\Controllers\Posiciones.php

# Frontend
✅ c:\xampp\htdocs\torneos\app\posiciones.php
✅ c:\xampp\htdocs\torneos\app\assets\js\functions_posiciones.js

# Documentación
✅ c:\xampp\htdocs\torneos\docs\MODULO_POSICIONES.md
```

### 4. Verificar Menú de Navegación

El archivo `app/template/header.php` debe incluir el enlace al módulo:

```php
<a href="posiciones.php"
    class="nav-link <?= $data['page_name'] == 'posiciones' ? 'active fw-bold' : '' ?>">
    <i class="fa-solid fa-medal me-2"></i> Posiciones
</a>
```

---

## 🧪 Pruebas de Funcionamiento

### 1. Acceder al Módulo

1. Inicia sesión en el sistema
2. Ve al menú lateral
3. Haz clic en **"Posiciones"** (icono de medalla 🏅)

### 2. Probar Filtros

1. Selecciona un **Torneo**
2. Selecciona una **Fase**
3. Selecciona un **Grupo**
4. Haz clic en **"Consultar"**

### 3. Verificar Datos

Si hay partidos jugados, deberías ver:
- ✅ Tabla de posiciones con estadísticas
- ✅ Información del grupo seleccionado
- ✅ Tabla de goleadores (si hay goles registrados)

Si NO hay partidos jugados:
- ℹ️ Mensaje: "No hay datos disponibles"

### 4. Probar Racha de Equipos

1. En la tabla de posiciones, haz clic en el botón de racha (📊)
2. Debe aparecer un modal con los últimos 5 partidos del equipo

---

## 🐛 Solución de Problemas

### Problema: El módulo no aparece en el menú

**Solución:**
1. Verifica que el archivo `header.php` tenga el enlace
2. Limpia la caché del navegador (Ctrl + F5)
3. Cierra sesión y vuelve a iniciar

### Problema: Error 404 al acceder a posiciones.php

**Solución:**
1. Verifica que el archivo `app/posiciones.php` exista
2. Verifica los permisos del archivo (debe ser legible)

### Problema: Error "Token inválido"

**Solución:**
1. Cierra sesión
2. Inicia sesión nuevamente
3. El token JWT tiene duración de 1 hora

### Problema: No se muestran datos en la tabla

**Causas posibles:**
1. No hay partidos con estado 'JUGADO' en el grupo
2. Los equipos no están vinculados al grupo
3. Error en la configuración de la base de datos

**Solución:**
```sql
-- Verificar partidos jugados en un grupo
SELECT * FROM partidos WHERE id_grupo = 1 AND estado = 'JUGADO';

-- Verificar equipos del grupo
SELECT * FROM fase_grupo_equipos WHERE id_grupo = 1;
```

### Problema: Error en consola JavaScript

**Solución:**
1. Abre la consola del navegador (F12)
2. Verifica que `API_URL` esté definida
3. Verifica que el archivo `functions_posiciones.js` se cargue correctamente

---

## 🔐 Permisos por Rol

| Rol | Puede Ver Posiciones |
|-----|---------------------|
| Super Admin | ✅ Todas las ligas |
| Liga Admin | ✅ Solo su liga |
| Delegado | ✅ Solo su liga |
| Jugador | ✅ Solo su liga |

---

## 📚 Documentación Adicional

- [Documentación Técnica del Módulo](MODULO_POSICIONES.md)
- [Documentación del Motor de Competición](MOTOR_COMPETICION.md)
- [Proyecto General](../PROYECTO.md)

---

## ✅ Checklist de Instalación

- [ ] Script SQL ejecutado correctamente
- [ ] Módulo visible en tabla `modulos`
- [ ] Permisos creados en tabla `permisos`
- [ ] Archivos backend verificados
- [ ] Archivos frontend verificados
- [ ] Enlace en menú de navegación
- [ ] Prueba de acceso al módulo exitosa
- [ ] Prueba de filtros funcionando
- [ ] Prueba de visualización de datos

---

## 🎉 ¡Instalación Completada!

Si todos los pasos fueron exitosos, el módulo de Posiciones está listo para usar.

**Próximos pasos sugeridos:**
1. Registrar partidos jugados para ver datos en la tabla
2. Explorar la funcionalidad de racha de equipos
3. Revisar la tabla de goleadores

---

**Soporte:** Si encuentras algún problema, revisa la documentación técnica o contacta al equipo de desarrollo.

**Versión:** 1.0.0 | **Fecha:** 27 de Enero, 2026
