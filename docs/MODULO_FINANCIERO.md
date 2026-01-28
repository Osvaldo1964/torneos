# 💰 Módulo Financiero - Global Cup

**Versión:** 1.0 (Análisis y Diseño)  
**Fecha:** 27 de Enero, 2026  
**Estado:** 📋 En Planificación

---

## 📋 Tabla de Contenidos

- [Descripción General](#-descripción-general)
- [Alcance del Módulo](#-alcance-del-módulo)
- [Submódulos](#-submódulos)
- [Estructura de Base de Datos](#️-estructura-de-base-de-datos)
- [Arquitectura del Sistema](#️-arquitectura-del-sistema)
- [Flujos de Trabajo](#-flujos-de-trabajo)
- [Endpoints de API](#-endpoints-de-api)
- [Interfaz de Usuario](#-interfaz-de-usuario)
- [Seguridad y Permisos](#-seguridad-y-permisos)
- [Plan de Implementación](#-plan-de-implementación)

---

## 🎯 Descripción General

El **Módulo Financiero** es un sistema integral de gestión de ingresos y egresos para torneos deportivos, diseñado para operar bajo el modelo multi-tenant del sistema Global Cup.

### Características Principales

- ✅ Gestión de cuotas mensuales por jugador
- ✅ Registro de sanciones económicas (tarjetas y otras)
- ✅ Generación de recibos de ingreso
- ✅ Control de pagos a árbitros
- ✅ Gestión de gastos generales del torneo
- ✅ Reportes financieros y balances
- ✅ Exportación a PDF y Excel

### Contexto de Operación

**Multi-tenant:** Cada liga gestiona sus torneos de forma independiente  
**Nivel de Registro:** Liga-Torneo (todos los movimientos financieros están vinculados a un torneo específico)  
**Moneda:** Configurable por liga (por defecto: moneda local)

---

## 🎯 Alcance del Módulo

### Ingresos (Recaudos)

1. **Cuotas Mensuales**
   - Generadas automáticamente al inscribir jugadores
   - Configurables por torneo
   - Estados: Pendiente, Pagado, Vencido

2. **Sanciones Económicas**
   - Tarjetas amarillas
   - Tarjetas rojas
   - Otras sanciones (multas administrativas, etc.)

3. **Otros Ingresos**
   - Inscripciones especiales
   - Patrocinios
   - Donaciones

### Egresos (Pagos/Gastos)

1. **Pagos a Árbitros**
   - Por partido arbitrado
   - Configurables por categoría de torneo

2. **Alquiler de Escenarios**
   - Pago por uso de canchas/estadios
   - Con comprobante de pago

3. **Gastos Generales**
   - Premios y trofeos
   - Material deportivo
   - Gastos administrativos
   - Otros gastos

### Reportes y Análisis

1. **Reporte de Recaudos**
   - Por periodo (rango de fechas)
   - Por tipo de ingreso
   - Exportable a PDF/Excel

2. **Reporte de Pagos/Gastos**
   - Por periodo
   - Por categoría de gasto
   - Exportable a PDF/Excel

3. **Balance por Torneo**
   - Ingresos totales
   - Egresos totales
   - Utilidad o Pérdida
   - Gráficas de distribución

---

## 📦 Submódulos

### 1️⃣ Módulo de Cuotas Mensuales

#### Descripción
Sistema de generación y gestión de cuotas mensuales por jugador inscrito en un torneo.

#### Funcionalidades

**Configuración:**
- Definir monto de cuota mensual por torneo
- Establecer día de vencimiento (ej: día 5 de cada mes)
- Activar/desactivar generación automática

**Generación Automática:**
- Al inscribir jugador en torneo → Genera cuotas para los meses de duración
- Cálculo basado en fecha de inicio y fin del torneo
- Asignación individual por jugador

**Gestión:**
- Listar cuotas por torneo
- Filtrar por estado (Pendiente, Pagado, Vencido)
- Filtrar por jugador/equipo
- Marcar como vencida automáticamente

**Estados:**
- `PENDIENTE`: Cuota generada, no pagada, no vencida
- `PAGADO`: Cuota pagada (vinculada a recibo)
- `VENCIDO`: Cuota no pagada después de fecha de vencimiento

#### Tabla de Base de Datos

```sql
-- Configuración de cuotas por torneo
CREATE TABLE configuracion_cuotas (
    id_configuracion INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    monto_mensual DECIMAL(10,2) NOT NULL,
    dia_vencimiento INT DEFAULT 5,
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo)
);

-- Cuotas generadas por jugador
CREATE TABLE cuotas_jugadores (
    id_cuota INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    id_jugador INT NOT NULL,
    id_equipo INT NOT NULL,
    mes INT NOT NULL,
    anio INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    estado ENUM('PENDIENTE', 'PAGADO', 'VENCIDO') DEFAULT 'PENDIENTE',
    fecha_pago DATE NULL,
    id_recibo INT NULL,
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo),
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador),
    FOREIGN KEY (id_equipo) REFERENCES equipos(id_equipo),
    FOREIGN KEY (id_recibo) REFERENCES recibos_ingreso(id_recibo),
    UNIQUE KEY unique_cuota (id_jugador, id_torneo, mes, anio)
);
```

---

### 2️⃣ Módulo de Sanciones Económicas

#### Descripción
Sistema de gestión de multas y sanciones económicas aplicadas a jugadores y equipos.

#### Funcionalidades

**Configuración:**
- Definir monto por tarjeta amarilla
- Definir monto por tarjeta roja
- Configurar otras sanciones personalizadas

**Generación Automática:**
- Al registrar tarjeta en partido → Genera sanción económica automáticamente
- Asignación al jugador y/o equipo
- Cálculo según configuración del torneo

**Gestión Manual:**
- Crear sanción por comportamiento
- Crear sanción por no presentación
- Crear multas administrativas
- Modificar/anular sanciones

**Estados:**
- `PENDIENTE`: Sanción aplicada, no pagada
- `PAGADO`: Sanción pagada (vinculada a recibo)

#### Tabla de Base de Datos

```sql
-- Configuración de sanciones por torneo
CREATE TABLE configuracion_sanciones (
    id_configuracion INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    monto_amarilla DECIMAL(10,2) DEFAULT 0,
    monto_roja DECIMAL(10,2) DEFAULT 0,
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo)
);

-- Sanciones económicas
CREATE TABLE sanciones_economicas (
    id_sancion INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    tipo_sancion ENUM('AMARILLA', 'ROJA', 'COMPORTAMIENTO', 'NO_PRESENTACION', 'OTRA') NOT NULL,
    id_equipo INT NULL,
    id_jugador INT NULL,
    id_partido INT NULL,
    concepto VARCHAR(255) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    estado ENUM('PENDIENTE', 'PAGADO', 'ANULADO') DEFAULT 'PENDIENTE',
    fecha_sancion DATE NOT NULL,
    fecha_pago DATE NULL,
    id_recibo INT NULL,
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo),
    FOREIGN KEY (id_equipo) REFERENCES equipos(id_equipo),
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador),
    FOREIGN KEY (id_partido) REFERENCES partidos(id_partido),
    FOREIGN KEY (id_recibo) REFERENCES recibos_ingreso(id_recibo)
);
```

---

### 3️⃣ Módulo de Recibos de Ingreso

#### Descripción
Sistema de registro y generación de recibos por todos los ingresos del torneo.

#### Funcionalidades

**Generación de Recibos:**
- Numeración automática correlativa por torneo
- Registro de forma de pago (Efectivo, Transferencia, Tarjeta)
- Vinculación con cuota o sanción pagada
- Generación de PDF imprimible

**Tipos de Ingresos:**
- Pago de cuotas mensuales
- Pago de sanciones económicas
- Otros ingresos (patrocinios, donaciones, etc.)

**Gestión:**
- Listar recibos por torneo
- Filtrar por fecha, tipo, forma de pago
- Anular recibos (con justificación)
- Reimprimir recibos

**Información del Recibo:**
- Número de recibo
- Fecha de pago
- Concepto
- Monto
- Forma de pago
- Referencia (si aplica)
- Datos del pagador

#### Tabla de Base de Datos

```sql
-- Recibos de ingreso
CREATE TABLE recibos_ingreso (
    id_recibo INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    numero_recibo VARCHAR(50) UNIQUE NOT NULL,
    tipo_ingreso ENUM('CUOTA', 'SANCION', 'OTRO') NOT NULL,
    id_cuota INT NULL,
    id_sancion INT NULL,
    concepto VARCHAR(255) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    forma_pago ENUM('EFECTIVO', 'TRANSFERENCIA', 'TARJETA', 'OTRO') DEFAULT 'EFECTIVO',
    referencia VARCHAR(100),
    pagador VARCHAR(255),
    fecha_pago DATE NOT NULL,
    estado ENUM('ACTIVO', 'ANULADO') DEFAULT 'ACTIVO',
    motivo_anulacion TEXT,
    observaciones TEXT,
    usuario_registro INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo),
    FOREIGN KEY (id_cuota) REFERENCES cuotas_jugadores(id_cuota),
    FOREIGN KEY (id_sancion) REFERENCES sanciones_economicas(id_sancion),
    FOREIGN KEY (usuario_registro) REFERENCES personas(id_persona)
);
```

---

### 4️⃣ Módulo de Pagos a Árbitros

#### Descripción
Sistema de gestión de pagos por arbitraje de partidos.

#### Funcionalidades

**Configuración:**
- Definir tarifa por partido según categoría de torneo
- Configurar árbitros (pueden ser personas externas)
- Establecer forma de pago

**Generación Automática:**
- Al crear partido → Genera pago pendiente a árbitro
- Asignación de monto según configuración
- Estado inicial: PENDIENTE

**Gestión:**
- Listar pagos pendientes
- Registrar pago realizado
- Generar comprobante de pago
- Filtrar por árbitro, fecha, estado

**Estados:**
- `PENDIENTE`: Partido jugado, pago no realizado
- `PAGADO`: Pago realizado con comprobante

#### Tabla de Base de Datos

```sql
-- Configuración de pagos a árbitros
CREATE TABLE configuracion_arbitros (
    id_configuracion INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    monto_por_partido DECIMAL(10,2) NOT NULL,
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo)
);

-- Árbitros (pueden ser personas externas)
CREATE TABLE arbitros (
    id_arbitro INT PRIMARY KEY AUTO_INCREMENT,
    id_persona INT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    identificacion VARCHAR(50),
    telefono VARCHAR(20),
    email VARCHAR(100),
    estado TINYINT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_persona) REFERENCES personas(id_persona)
);

-- Pagos a árbitros
CREATE TABLE pagos_arbitros (
    id_pago INT PRIMARY KEY AUTO_INCREMENT,
    id_partido INT NOT NULL,
    id_arbitro INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NULL,
    estado ENUM('PENDIENTE', 'PAGADO') DEFAULT 'PENDIENTE',
    numero_comprobante VARCHAR(50),
    forma_pago ENUM('EFECTIVO', 'TRANSFERENCIA', 'OTRO'),
    observaciones TEXT,
    usuario_registro INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_partido) REFERENCES partidos(id_partido),
    FOREIGN KEY (id_arbitro) REFERENCES arbitros(id_arbitro),
    FOREIGN KEY (usuario_registro) REFERENCES personas(id_persona)
);
```

---

### 5️⃣ Módulo de Pagos/Gastos Generales

#### Descripción
Sistema de registro de todos los gastos y egresos del torneo.

#### Funcionalidades

**Categorías de Gastos:**
- 🏟️ Alquiler de escenarios/canchas
- 👨‍⚖️ Pago a árbitros (si no se usa el módulo específico)
- 🏆 Premios y trofeos
- 📋 Material deportivo
- 💼 Gastos administrativos
- 🚑 Otros gastos

**Registro:**
- Crear gasto con comprobante
- Asignar categoría
- Registrar beneficiario/proveedor
- Adjuntar documento soporte (factura, recibo)
- Generar comprobante de pago

**Gestión:**
- Listar gastos por torneo
- Filtrar por categoría, fecha, beneficiario
- Exportar listado
- Anular gastos (con justificación)

#### Tabla de Base de Datos

```sql
-- Pagos y gastos generales
CREATE TABLE pagos_gastos (
    id_pago INT PRIMARY KEY AUTO_INCREMENT,
    id_torneo INT NOT NULL,
    tipo_gasto ENUM('ESCENARIO', 'ARBITRO', 'PREMIO', 'MATERIAL', 'ADMINISTRATIVO', 'OTRO') NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    beneficiario VARCHAR(255) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATE NOT NULL,
    numero_comprobante VARCHAR(50),
    forma_pago ENUM('EFECTIVO', 'TRANSFERENCIA', 'CHEQUE', 'OTRO'),
    documento_soporte VARCHAR(255),
    estado ENUM('ACTIVO', 'ANULADO') DEFAULT 'ACTIVO',
    motivo_anulacion TEXT,
    observaciones TEXT,
    usuario_registro INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo),
    FOREIGN KEY (usuario_registro) REFERENCES personas(id_persona)
);
```

---

### 6️⃣ Módulo de Reportes Financieros

#### Descripción
Sistema de generación de informes y balances financieros.

#### Reportes Disponibles

**1. Reporte de Recaudos**
- Total de ingresos por periodo
- Desglose por tipo (cuotas, sanciones, otros)
- Desglose por forma de pago
- Filtros: Fecha inicio/fin, tipo de ingreso
- Exportable a PDF/Excel

**2. Reporte de Pagos/Gastos**
- Total de egresos por periodo
- Desglose por categoría
- Desglose por beneficiario
- Filtros: Fecha inicio/fin, tipo de gasto
- Exportable a PDF/Excel

**3. Balance por Torneo**

**Ingresos:**
- Cuotas mensuales: $X,XXX
- Sanciones pagadas: $X,XXX
- Otros ingresos: $X,XXX
- **Total Ingresos: $X,XXX**

**Egresos:**
- Pagos a árbitros: $X,XXX
- Alquiler de escenarios: $X,XXX
- Premios y trofeos: $X,XXX
- Gastos administrativos: $X,XXX
- Otros gastos: $X,XXX
- **Total Egresos: $X,XXX**

**Resultado:**
- **Utilidad: $X,XXX** (si Ingresos > Egresos) ✅
- **Pérdida: $X,XXX** (si Egresos > Ingresos) ❌
- **Balance: $0** (si Ingresos = Egresos)

**4. Comparación entre Torneos**
- Comparar ingresos/egresos de múltiples torneos
- Gráficas de evolución
- Identificar torneos más rentables

#### Visualizaciones

- 📊 Gráfica de barras: Ingresos vs Egresos
- 🥧 Gráfica de pastel: Distribución de ingresos por tipo
- 🥧 Gráfica de pastel: Distribución de gastos por categoría
- 📈 Gráfica de línea: Evolución mensual de ingresos/egresos

---

## 🗄️ Estructura de Base de Datos

### Resumen de Tablas

| Tabla | Propósito | Registros Estimados |
|-------|-----------|---------------------|
| `configuracion_cuotas` | Config de cuotas por torneo | 1 por torneo |
| `cuotas_jugadores` | Cuotas generadas | ~200 por torneo/mes |
| `configuracion_sanciones` | Config de sanciones | 1 por torneo |
| `sanciones_economicas` | Sanciones aplicadas | ~50 por torneo |
| `recibos_ingreso` | Recibos de pago | ~250 por torneo |
| `configuracion_arbitros` | Config de pagos árbitros | 1 por torneo |
| `arbitros` | Catálogo de árbitros | ~10-20 por liga |
| `pagos_arbitros` | Pagos a árbitros | ~30 por torneo |
| `pagos_gastos` | Gastos generales | ~20-50 por torneo |

### Relaciones Clave

```
torneos
  ├── configuracion_cuotas
  ├── cuotas_jugadores
  ├── configuracion_sanciones
  ├── sanciones_economicas
  ├── recibos_ingreso
  ├── configuracion_arbitros
  └── pagos_gastos

jugadores
  ├── cuotas_jugadores
  └── sanciones_economicas

equipos
  └── sanciones_economicas

partidos
  ├── sanciones_economicas
  └── pagos_arbitros

arbitros
  └── pagos_arbitros

recibos_ingreso
  ├── cuotas_jugadores (vinculación)
  └── sanciones_economicas (vinculación)
```

---

## 🏗️ Arquitectura del Sistema

### Backend (API)

```
api/
├── Models/
│   ├── CuotasModel.php
│   ├── SancionesModel.php
│   ├── RecibosModel.php
│   ├── ArbitrosModel.php
│   ├── PagosModel.php
│   └── FinanzasModel.php
│
└── Controllers/
    ├── Cuotas.php
    ├── Sanciones.php
    ├── Recibos.php
    ├── Arbitros.php
    ├── Pagos.php
    └── Finanzas.php
```

### Frontend (APP)

```
app/
├── finanzas/
│   ├── cuotas.php
│   ├── sanciones.php
│   ├── recibos.php
│   ├── arbitros.php
│   ├── pagos.php
│   ├── balance.php
│   └── reportes.php
│
└── assets/
    ├── js/
    │   ├── functions_cuotas.js
    │   ├── functions_sanciones.js
    │   ├── functions_recibos.js
    │   ├── functions_arbitros.js
    │   ├── functions_pagos.js
    │   └── functions_finanzas.js
    │
    └── css/
        └── finanzas.css
```

---

## 🔄 Flujos de Trabajo

### Flujo 1: Inscripción de Jugador → Generación de Cuotas

```
1. Usuario inscribe jugador en torneo
2. Sistema verifica configuración de cuotas del torneo
3. Si existe configuración:
   a. Calcula meses entre fecha inicio y fin del torneo
   b. Genera cuota por cada mes
   c. Asigna fecha de vencimiento (día configurado)
   d. Estado inicial: PENDIENTE
4. Cuotas quedan listas para pago
```

### Flujo 2: Tarjeta en Partido → Sanción Económica

```
1. Árbitro registra tarjeta en partido
2. Sistema verifica configuración de sanciones del torneo
3. Si existe configuración:
   a. Obtiene monto según tipo de tarjeta
   b. Crea sanción económica
   c. Asigna a jugador y equipo
   d. Estado inicial: PENDIENTE
4. Sanción queda lista para pago
```

### Flujo 3: Pago de Cuota/Sanción → Recibo de Ingreso

```
1. Usuario registra pago
2. Selecciona cuota(s) o sanción(es) a pagar
3. Ingresa datos del pago:
   - Monto
   - Forma de pago
   - Referencia (si aplica)
   - Fecha de pago
4. Sistema genera recibo:
   a. Asigna número correlativo
   b. Vincula con cuota/sanción
   c. Actualiza estado a PAGADO
   d. Genera PDF del recibo
5. Recibo queda disponible para imprimir
```

### Flujo 4: Partido Jugado → Pago Pendiente a Árbitro

```
1. Partido se marca como jugado
2. Sistema verifica configuración de árbitros
3. Si existe configuración:
   a. Obtiene monto por partido
   b. Crea pago pendiente
   c. Asigna árbitro del partido
   d. Estado inicial: PENDIENTE
4. Pago queda pendiente de realizar
```

### Flujo 5: Registro de Gasto → Comprobante

```
1. Usuario registra gasto
2. Ingresa datos:
   - Tipo de gasto
   - Concepto
   - Beneficiario
   - Monto
   - Fecha
   - Comprobante
3. Sistema registra gasto
4. Opcionalmente genera comprobante de pago
```

### Flujo 6: Generación de Balance

```
1. Usuario solicita balance de torneo
2. Sistema calcula:
   a. Total de ingresos (cuotas + sanciones + otros)
   b. Total de egresos (árbitros + gastos)
   c. Resultado (ingresos - egresos)
3. Genera reporte con:
   - Detalles de ingresos
   - Detalles de egresos
   - Gráficas
   - Resultado final
4. Permite exportar a PDF/Excel
```

---

## 🔌 Endpoints de API

### Cuotas

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/Cuotas/configuracion/{idTorneo}` | Obtener config de cuotas |
| POST | `/Cuotas/configuracion` | Crear/actualizar config |
| GET | `/Cuotas/listar/{idTorneo}` | Listar cuotas del torneo |
| POST | `/Cuotas/generar` | Generar cuotas manualmente |
| PUT | `/Cuotas/marcarVencidas` | Marcar cuotas vencidas |

### Sanciones

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/Sanciones/configuracion/{idTorneo}` | Obtener config de sanciones |
| POST | `/Sanciones/configuracion` | Crear/actualizar config |
| GET | `/Sanciones/listar/{idTorneo}` | Listar sanciones del torneo |
| POST | `/Sanciones/crear` | Crear sanción manual |
| PUT | `/Sanciones/anular/{id}` | Anular sanción |

### Recibos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/Recibos/listar/{idTorneo}` | Listar recibos del torneo |
| POST | `/Recibos/crear` | Crear recibo de ingreso |
| GET | `/Recibos/pdf/{id}` | Generar PDF del recibo |
| PUT | `/Recibos/anular/{id}` | Anular recibo |

### Árbitros

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/Arbitros/listar` | Listar árbitros |
| POST | `/Arbitros/crear` | Crear árbitro |
| GET | `/Arbitros/pagos/{idTorneo}` | Listar pagos del torneo |
| POST | `/Arbitros/registrarPago` | Registrar pago a árbitro |

### Pagos/Gastos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/Pagos/listar/{idTorneo}` | Listar gastos del torneo |
| POST | `/Pagos/crear` | Crear registro de gasto |
| PUT | `/Pagos/anular/{id}` | Anular gasto |

### Finanzas (Reportes)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/Finanzas/recaudos/{idTorneo}` | Reporte de recaudos |
| GET | `/Finanzas/gastos/{idTorneo}` | Reporte de gastos |
| GET | `/Finanzas/balance/{idTorneo}` | Balance del torneo |
| GET | `/Finanzas/comparacion` | Comparar torneos |
| GET | `/Finanzas/exportar/{tipo}/{idTorneo}` | Exportar a PDF/Excel |

---

## 🎨 Interfaz de Usuario

### Dashboard Financiero

**Vista Principal:**
- Resumen de ingresos del mes
- Resumen de gastos del mes
- Balance actual
- Cuotas pendientes de pago
- Sanciones pendientes de pago
- Pagos pendientes a árbitros

### Módulo de Cuotas

**Listado:**
- Tabla con todas las cuotas
- Filtros: Estado, Jugador, Equipo, Mes
- Acciones: Ver detalle, Registrar pago

**Configuración:**
- Formulario para configurar cuotas del torneo
- Monto mensual
- Día de vencimiento

### Módulo de Sanciones

**Listado:**
- Tabla con todas las sanciones
- Filtros: Estado, Tipo, Jugador, Equipo
- Acciones: Ver detalle, Registrar pago, Anular

**Configuración:**
- Formulario para configurar montos
- Monto por tarjeta amarilla
- Monto por tarjeta roja

### Módulo de Recibos

**Listado:**
- Tabla con todos los recibos
- Filtros: Fecha, Tipo, Forma de pago
- Acciones: Ver PDF, Reimprimir, Anular

**Crear Recibo:**
- Formulario para registrar pago
- Selección de cuotas/sanciones pendientes
- Datos del pago
- Generación automática de recibo

### Módulo de Árbitros

**Listado de Árbitros:**
- Tabla con árbitros registrados
- Acciones: Editar, Ver historial de pagos

**Pagos Pendientes:**
- Tabla con pagos pendientes
- Filtros: Árbitro, Fecha
- Acciones: Registrar pago

### Módulo de Gastos

**Listado:**
- Tabla con todos los gastos
- Filtros: Categoría, Fecha, Beneficiario
- Acciones: Ver detalle, Editar, Anular

**Crear Gasto:**
- Formulario para registrar gasto
- Categoría
- Concepto y beneficiario
- Monto y fecha
- Adjuntar comprobante

### Reportes

**Balance del Torneo:**
- Resumen visual de ingresos y egresos
- Gráficas de distribución
- Resultado final (Utilidad/Pérdida)
- Botón de exportar

**Reportes Personalizados:**
- Selector de tipo de reporte
- Filtros de fecha
- Opciones de agrupación
- Exportar a PDF/Excel

---

## 🔐 Seguridad y Permisos

### Autenticación
- ✅ Todos los endpoints requieren JWT válido
- ✅ Validación de token en cada petición

### Multi-tenancy
- ✅ Filtrado automático por `id_liga`
- ✅ Super Admin (Liga 1) ve todas las ligas
- ✅ Usuarios normales solo ven su liga

### Permisos por Rol

| Rol | Cuotas | Sanciones | Recibos | Árbitros | Gastos | Reportes |
|-----|--------|-----------|---------|----------|--------|----------|
| Super Admin | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ Ver |
| Liga Admin | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ Ver |
| Delegado | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Jugador | 👁️ Ver sus cuotas | 👁️ Ver sus sanciones | ❌ | ❌ | ❌ | ❌ |

### Auditoría
- ✅ Registro de usuario que crea/modifica
- ✅ Timestamp de creación
- ✅ Motivo de anulación (si aplica)
- ✅ Historial de cambios (opcional)

---

## 📅 Plan de Implementación

### Fase 1: Infraestructura (Semana 1)
- [ ] Crear tablas de base de datos
- [ ] Crear modelos base
- [ ] Crear controladores base
- [ ] Configurar permisos

### Fase 2: Módulo de Cuotas (Semana 2)
- [ ] Implementar configuración de cuotas
- [ ] Implementar generación automática
- [ ] Crear interfaz de gestión
- [ ] Integrar con inscripción de jugadores

### Fase 3: Módulo de Sanciones (Semana 3)
- [ ] Implementar configuración de sanciones
- [ ] Implementar generación automática
- [ ] Crear interfaz de gestión
- [ ] Integrar con registro de tarjetas

### Fase 4: Módulo de Recibos (Semana 4)
- [ ] Implementar generación de recibos
- [ ] Crear plantilla PDF
- [ ] Crear interfaz de registro de pagos
- [ ] Integrar con cuotas y sanciones

### Fase 5: Módulo de Árbitros (Semana 5)
- [ ] Implementar catálogo de árbitros
- [ ] Implementar configuración de pagos
- [ ] Crear interfaz de gestión
- [ ] Integrar con partidos

### Fase 6: Módulo de Gastos (Semana 6)
- [ ] Implementar registro de gastos
- [ ] Crear interfaz de gestión
- [ ] Implementar carga de comprobantes
- [ ] Crear categorías de gastos

### Fase 7: Reportes y Balance (Semana 7)
- [ ] Implementar cálculo de balance
- [ ] Crear reportes de recaudos
- [ ] Crear reportes de gastos
- [ ] Implementar gráficas

### Fase 8: Exportación y Finalización (Semana 8)
- [ ] Implementar exportación a PDF
- [ ] Implementar exportación a Excel
- [ ] Pruebas integrales
- [ ] Documentación de usuario

---

## 📊 Métricas de Éxito

### Funcionalidad
- ✅ Generación automática de cuotas funcional
- ✅ Generación automática de sanciones funcional
- ✅ Recibos numerados correctamente
- ✅ Balance calculado correctamente
- ✅ Exportación funcional

### Performance
- ✅ Carga de listados < 2 segundos
- ✅ Generación de PDF < 3 segundos
- ✅ Cálculo de balance < 5 segundos

### Usabilidad
- ✅ Interfaz intuitiva
- ✅ Flujos de trabajo claros
- ✅ Mensajes de error descriptivos
- ✅ Confirmaciones en acciones críticas

---

## 🔮 Futuras Mejoras

### Corto Plazo
- [ ] Notificaciones de cuotas vencidas
- [ ] Recordatorios de pago por email/SMS
- [ ] Dashboard con gráficas en tiempo real

### Mediano Plazo
- [ ] Pagos en línea (integración con pasarelas)
- [ ] App móvil para consulta de cuotas
- [ ] Reportes predictivos

### Largo Plazo
- [ ] Inteligencia artificial para predicción de ingresos
- [ ] Análisis comparativo automático
- [ ] Integración con sistemas contables

---

## 📚 Referencias

- [PROYECTO.md](PROYECTO.md) - Visión general del proyecto
- [ESTADO_PROYECTO.md](ESTADO_PROYECTO.md) - Estado actual
- [MOTOR_COMPETICION.md](MOTOR_COMPETICION.md) - Motor de competición
- [MODULO_POSICIONES.md](MODULO_POSICIONES.md) - Módulo de posiciones

---

**Última actualización:** 27 de Enero, 2026  
**Versión del documento:** 1.0  
**Estado:** 📋 En Planificación
