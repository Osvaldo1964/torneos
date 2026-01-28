# 📊 Módulo de Tabla de Posiciones - Documentación Técnica

**Versión:** 1.0.0  
**Fecha de Implementación:** 27 de Enero, 2026  
**Estado:** ✅ Completado

---

## 🎯 Descripción General

El módulo de **Tabla de Posiciones** permite visualizar la clasificación de equipos dentro de un grupo específico de un torneo, calculando automáticamente las estadísticas basadas en los partidos jugados.

---

## 🏗️ Arquitectura

### Jerarquía de Filtros
```
Liga → Torneo → Fase → Grupo → Tabla de Posiciones
```

### Componentes del Sistema

#### **Backend (API)**
- **Modelo**: `PosicionesModel.php`
- **Controlador**: `Posiciones.php`
- **Endpoints**:
  - `GET /Posiciones/torneos` - Lista de torneos disponibles
  - `GET /Posiciones/fases/{idTorneo}` - Fases de un torneo
  - `GET /Posiciones/grupos/{idFase}` - Grupos de una fase
  - `GET /Posiciones/tabla/{idGrupo}` - Tabla de posiciones de un grupo
  - `GET /Posiciones/racha/{idEquipo}/{idGrupo}` - Últimos 5 resultados de un equipo
  - `GET /Posiciones/goleadores/{idGrupo}` - Top 10 goleadores del grupo

#### **Frontend (APP)**
- **Vista**: `posiciones.php`
- **JavaScript**: `functions_posiciones.js`

---

## 📈 Cálculo de Estadísticas

### Columnas de la Tabla

| Columna | Descripción | Cálculo |
|---------|-------------|---------|
| **#** | Posición | Ordenamiento automático |
| **Equipo** | Nombre y escudo | - |
| **PJ** | Partidos Jugados | Total de partidos con estado 'JUGADO' |
| **PG** | Partidos Ganados | Partidos donde goles_equipo > goles_rival |
| **PE** | Partidos Empatados | Partidos donde goles_equipo = goles_rival |
| **PP** | Partidos Perdidos | Partidos donde goles_equipo < goles_rival |
| **GF** | Goles a Favor | Suma de goles anotados |
| **GC** | Goles en Contra | Suma de goles recibidos |
| **DG** | Diferencia de Goles | GF - GC |
| **PTS** | Puntos | (PG × 3) + (PE × 1) |

### Criterios de Ordenamiento

1. **Puntos** (descendente)
2. **Diferencia de Goles** (descendente)
3. **Goles a Favor** (descendente)

---

## 🎨 Características de UI/UX

### Visualización de Posiciones
- **1er Lugar**: Fondo verde (`table-success`)
- **2do Lugar**: Fondo azul (`table-info`)
- **3er Lugar**: Fondo amarillo (`table-warning`)

### Racha de Equipos
- **V** (Victoria): Badge verde
- **E** (Empate): Badge amarillo
- **D** (Derrota): Badge rojo
- Muestra los últimos 5 partidos jugados

### Tabla de Goleadores
- Top 10 goleadores del grupo
- Incluye foto del jugador, nombre, equipo y total de goles
- El líder goleador se destaca con fondo amarillo

---

## 🔐 Seguridad y Permisos

### Multi-tenancy
- Los usuarios solo ven torneos de su liga (`id_liga`)
- **Super Admin** (Liga 1) puede ver todas las ligas

### Autenticación
- Todos los endpoints requieren JWT válido
- Token incluido en header: `Authorization: Bearer {token}`

### Permisos por Rol

| Rol | Ver | Crear | Editar | Eliminar |
|-----|-----|-------|--------|----------|
| Super Admin | ✅ | ✅ | ✅ | ✅ |
| Liga Admin | ✅ | ❌ | ❌ | ❌ |
| Delegado | ✅ | ❌ | ❌ | ❌ |
| Jugador | ✅ | ❌ | ❌ | ❌ |

---

## 🚀 Funcionalidades Implementadas

### ✅ Completadas
- [x] Cálculo automático de tabla de posiciones
- [x] Filtros jerárquicos (Liga → Torneo → Fase → Grupo)
- [x] Visualización de racha de equipos (últimos 5 partidos)
- [x] Tabla de goleadores por grupo
- [x] Ordenamiento por criterios de desempate
- [x] Diseño responsivo con Bootstrap 5
- [x] Integración con sistema de permisos
- [x] Soporte multi-tenant

### 🔄 Pendientes (Mejoras Futuras)
- [ ] Exportación a PDF
- [ ] Exportación a Excel
- [ ] Gráficas de evolución de posiciones
- [ ] Comparación entre grupos
- [ ] Estadísticas avanzadas (valla menos vencida, fair play)
- [ ] Historial de posiciones por jornada

---

## 📝 Ejemplos de Uso

### Consultar Tabla de Posiciones

**Request:**
```http
GET /api/Posiciones/tabla/1
Authorization: Bearer {token}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "info": {
      "id_grupo": 1,
      "nombre_grupo": "Grupo A",
      "nombre_fase": "Octavos",
      "nombre_torneo": "RODILLONES",
      "categoria": "SENIOR",
      "id_liga": 4,
      "nombre_liga": "LIGA DE PRUEBA"
    },
    "tabla": [
      {
        "posicion": 1,
        "id_equipo": 2,
        "equipo": "EQUIPO1",
        "escudo": "equipo_1769531346.jpg",
        "pj": 1,
        "pg": 1,
        "pe": 0,
        "pp": 0,
        "gf": 3,
        "gc": 1,
        "dg": 2,
        "pts": 3
      }
    ]
  }
}
```

---

## 🔧 Instalación

### 1. Ejecutar Script SQL
```bash
mysql -u root -p db-globalcup < update_posiciones.sql
```

### 2. Verificar Archivos
- ✅ `api/Models/PosicionesModel.php`
- ✅ `api/Controllers/Posiciones.php`
- ✅ `app/posiciones.php`
- ✅ `app/assets/js/functions_posiciones.js`

### 3. Acceder al Módulo
```
http://localhost/torneos/app/posiciones.php
```

---

## 🐛 Troubleshooting

### Problema: No se muestran datos en la tabla
**Solución**: Verificar que:
1. Existan partidos con estado 'JUGADO' en el grupo
2. Los equipos estén correctamente vinculados al grupo
3. El usuario tenga permisos para ver el torneo

### Problema: Error 401 (Token inválido)
**Solución**: 
1. Verificar que el token no haya expirado (duración: 1 hora)
2. Hacer logout y login nuevamente

---

## 📚 Referencias

- [Documentación del Motor de Competición](MOTOR_COMPETICION.md)
- [Esquema de Base de Datos](../database.sql)
- [Proyecto General](../PROYECTO.md)

---

**Desarrollado para Global Cup** | Enero 2026
