# 🏆 Global Cup - Sistema de Gestión Deportiva

Sistema integral de gestión para ligas de fútbol con arquitectura API-First, multi-tenant y motor de competición completo.

![Versión](https://img.shields.io/badge/versión-1.2.0-blue)
![Estado](https://img.shields.io/badge/estado-activo-success)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Módulos](#-módulos)
- [Arquitectura](#-arquitectura)
- [Documentación](#-documentación)
- [Changelog](#-changelog)

---

## ✨ Características

### 🔐 Seguridad
- Autenticación JWT con tokens de 1 hora
- Sistema de roles y permisos granulares
- Multi-tenancy por liga
- Super Admin con visibilidad global

### ⚽ Gestión Deportiva
- CRUD completo de Ligas, Torneos, Equipos y Jugadores
- Sistema de nóminas por torneo con dorsales
- Motor de competición con Round Robin
- Registro de resultados y eventos (goles, tarjetas)
- **Tabla de posiciones dinámica** ⭐ NUEVO

### 📊 Estadísticas
- Tabla de posiciones con cálculo automático
- Racha de equipos (últimos 5 partidos)
- Top goleadores por grupo
- Estadísticas del grupo (partidos, goles, promedios)
- Sistema de sanciones automático

### 🎨 Interfaz
- Diseño responsivo con Bootstrap 5
- Notificaciones con SweetAlert2
- Tablas interactivas con DataTables
- Carga asíncrona de datos

---

## 🔧 Requisitos

### Servidor
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache/Nginx con mod_rewrite
- Extensiones PHP: mysqli, json, gd

### Cliente
- Navegador moderno (Chrome, Firefox, Edge)
- JavaScript habilitado
- Resolución mínima: 1024x768

---

## 🚀 Instalación

### 1. Clonar/Descargar el Proyecto
```bash
git clone https://github.com/tu-usuario/torneos.git
cd torneos
```

### 2. Configurar Base de Datos
```bash
# Crear base de datos
mysql -u root -p -e "CREATE DATABASE db_globalcup CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importar esquema
mysql -u root -p db_globalcup < db-globalcup.sql

# Instalar módulo de posiciones
mysql -u root -p db_globalcup < update_posiciones.sql
```

### 3. Configurar API
Editar `api/Config/Config.php`:
```php
const DB_HOST = "localhost";
const DB_NAME = "db_globalcup";
const DB_USER = "root";
const DB_PASSWORD = "";

const BASE_URL = "http://localhost/torneos/api/";
const APP_URL = "http://localhost/torneos/app/";
```

### 4. Configurar APP
Editar `app/assets/js/main.js`:
```javascript
const app_config = {
    api_url: "http://localhost/torneos/api/",
    base_url: "http://localhost/torneos/",
    // ...
};
```

### 5. Acceder al Sistema
```
URL: http://localhost/torneos/app/login.php

Credenciales por defecto:
Usuario: admin@globalcup.com
Contraseña: admin123
```

---

## 📦 Módulos

### ✅ Completados

#### 1. **Autenticación y Usuarios**
- Login con JWT
- Gestión de roles
- Permisos granulares

#### 2. **Ligas y Torneos**
- CRUD de ligas
- CRUD de torneos
- Configuración de categorías

#### 3. **Equipos y Jugadores**
- Registro de equipos con escudos
- Registro de jugadores con fotos
- Sistema de nóminas por torneo

#### 4. **Motor de Competición**
- Creación de fases y grupos
- Generación automática de fixtures
- Registro de resultados
- Sistema de eventos y sanciones

#### 5. **Tabla de Posiciones** ⭐ NUEVO
- Cálculo automático de estadísticas
- Racha de equipos
- Top goleadores
- Estadísticas del grupo
- Filtros jerárquicos

### 🔄 En Desarrollo

- Exportación PDF/Excel
- Estadísticas avanzadas
- Tablero de inhabilitados
- Módulo financiero

---

## 🏗️ Arquitectura

### Backend (API)
```
api/
├── Config/              # Configuración
├── Controllers/         # Controladores REST
├── Models/             # Modelos de datos
├── Libraries/          # Librerías (JWT, etc)
└── index.php           # Router principal
```

### Frontend (APP)
```
app/
├── assets/
│   ├── css/           # Estilos
│   ├── js/            # JavaScript
│   └── images/        # Imágenes
├── template/          # Header/Footer
└── *.php              # Vistas
```

### Base de Datos
```
Tablas principales:
- ligas
- personas
- equipos
- jugadores
- torneos
- partidos
- estadisticas_partido
```

---

## 📚 Documentación

### Documentos Disponibles

1. **[docs/PROYECTO.md](docs/PROYECTO.md)** - Visión general y roadmap
2. **[docs/ESTADO_PROYECTO.md](docs/ESTADO_PROYECTO.md)** - Estado actual detallado
3. **[docs/MOTOR_COMPETICION.md](docs/MOTOR_COMPETICION.md)** - Motor de competición
4. **[docs/MODULO_POSICIONES.md](docs/MODULO_POSICIONES.md)** - Módulo de posiciones
5. **[docs/INSTALACION_POSICIONES.md](docs/INSTALACION_POSICIONES.md)** - Instalación del módulo

### API Endpoints

#### Autenticación
- `POST /Login` - Iniciar sesión

#### Posiciones
- `GET /Posiciones/torneos` - Lista de torneos
- `GET /Posiciones/fases/{id}` - Fases de un torneo
- `GET /Posiciones/grupos/{id}` - Grupos de una fase
- `GET /Posiciones/tabla/{id}` - Tabla de posiciones
- `GET /Posiciones/racha/{idEquipo}/{idGrupo}` - Racha del equipo
- `GET /Posiciones/goleadores/{id}` - Top goleadores

[Ver documentación completa de API](docs/API.md)

---

## 📝 Changelog

### [1.2.0] - 2026-01-27

#### ✨ Agregado
- **Módulo de Tabla de Posiciones**
  - Cálculo automático de estadísticas (PJ, PG, PE, PP, GF, GC, DG, PTS)
  - Visualización de racha de equipos (últimos 5 partidos)
  - Tabla de goleadores (Top 10)
  - Estadísticas adicionales del grupo
  - Filtros jerárquicos (Liga → Torneo → Fase → Grupo)
  - Destacado visual de top 3 posiciones
  - Sistema de permisos integrado

#### 🔧 Corregido
- Rutas de imágenes de equipos y jugadores
- Loop infinito en fallback de imágenes
- Orden de carga de scripts JavaScript
- Token JWT en localStorage
- Alineación de contenido en boxes de estadísticas

#### 📚 Documentación
- Agregada documentación técnica del módulo
- Agregada guía de instalación
- Actualizado estado del proyecto

### [1.1.0] - 2026-01-20
- Motor de competición completo
- Sistema de nóminas
- Registro de resultados

### [1.0.0] - 2026-01-15
- Versión inicial
- CRUDs básicos
- Autenticación JWT

---

## 🤝 Contribuir

Este es un proyecto privado. Para reportar bugs o sugerir mejoras, contactar al administrador del sistema.

---

## 📄 Licencia

Todos los derechos reservados © 2026 Global Cup

---

## 👥 Equipo

- **Desarrollo:** Antigravity AI
- **Cliente:** Osvaldo1964
- **Proyecto:** Global Cup

---

## 📞 Soporte

Para soporte técnico:
1. Revisar la [documentación](docs/)
2. Consultar el [estado del proyecto](docs/ESTADO_PROYECTO.md)
3. Verificar los [logs de errores](api/logs/)

---

**Última actualización:** 27 de Enero, 2026  
**Versión:** 1.2.0  
**Estado:** ✅ Producción
