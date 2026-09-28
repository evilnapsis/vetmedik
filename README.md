# VetMedik v2 🐾
**Sistema Integral de Gestión para Clínicas Veterinarias, Citas Médicas, Mascotas, Cobros y Reportes**

Desarrollado con **PHP 8.2+**, **MySQL**, arquitectura **LegoBox**, motor de plantillas **Twig 3**, interfaz moderna **CoreUI v5** y analítica interactiva con **Chart.js**.

Sitio Oficial: [Evilnapsis](http://evilnapsis.com)

---

## 🚀 Características Principales

### 🐶 Gestión Clínica de Pacientes y Tutores (`/pacients`)
- Expediente clínico veterinario: Nombre, especie (*Canino, Felino, Ave, Roedor, Exótico, etc.*), raza, sexo, peso (kg), fecha de nacimiento aproximada, microchip/expediente, alergias, enfermedades previas y medicación.
- Datos de contacto y ubicación del tutor/propietario de la mascota.
- Historial clínico unificado por mascota con todas sus citas y atenciones previas.

### 📅 Agenda de Citas y Calendario Veterinario (`/reservations` & `/calendar`)
- Programación de consultas médicas, cirugías, desparasitaciones, vacunaciones y revisiones.
- **Calendario Interactivo con FullCalendar**: Vistas por Mes, Semana y Día totalmente en español con acceso directo a edición.
- Emisión e impresión de **Recetas y Comprobantes Médicos en PDF** con diagnóstico, síntomas y medicamentos prescritos.

### 📊 Dashboard Interactivo con Gráficas y Citas en Cards (`/home`)
- **5 Tarjetas KPI Estadísticas**: Total de Mascotas, Médicos Veterinarios Activos, Citas Próximas, Consultas Pendientes y Recaudación Total.
- **Gráficas Interactivas con Chart.js**:
  - *Distribución de Mascotas por Especie* (Gráfica tipo dona/doughnut).
  - *Estatus Global de Consultas* (Gráfica de barras: Pendientes, Aplicadas, No Asistió, Canceladas).
- **Monitor de Citas en Formato Cards**: Fichas visuales de atención rápida con badges de estatus, tiempo, avatar de especie, datos del tutor, médico veterinario asignado, costo y estado de cobro con botones de acción directa en miniatura (`btn-xs`).

### 💰 Finanzas y Registro de Pagos por Servicios (`/payments`)
- Módulo de cobro de consultas y servicios médicos veterinarios pendientes.
- Soporte para múltiples métodos de pago: Efectivo, Tarjeta de Débito/Crédito y Transferencia Bancaria.
- Generación de **Recibos de Pago Imprimibles Foliados** (`/payments/receipt/{id}`) con datos fiscales y desglose de cuenta.
- Marcado y liquidación automática de citas a estado **Pagado**.

### 📈 Reportes Clínicos y Financieros con Exportación a CSV (`/reports`)
- Métricas consolidadas en tiempo real: Consultas Totales, Consultas Atendidas, Ingresos Cobrados y Saldo Pendiente.
- Filtros multicriterio por: Rango de Fechas (`start_date` a `end_date`), Médico Veterinario, Paciente y Estatus.
- **Exportación a CSV con UTF-8 BOM**: Descarga inmediata con compatibilidad nativa para Microsoft Excel y caracteres especiales.

### 🖥️ Interfaz Responsiva a Ancho Completo
- Estructura `container-fluid` optimizada para ocupar el 100% del ancho del contenedor derecho.
- Tablas condensadas (`table-sm`) y botones compactos (`btn-xs`).
- Modo de visualización Claro / Oscuro / Automático.

---

## 🛠️ Requisitos del Sistema

1. **Servidor Web**: Apache / Nginx con módulo `mod_rewrite` habilitado (XAMPP, Laragon, WAMP o Docker).
2. **PHP**: PHP 8.0, 8.1, 8.2 o superior (extensiones requeridas: `pdo_mysql`, `mbstring`, `json`, `curl`).
3. **Base de Datos**: MySQL 5.7+ o MariaDB 10.3+.

---

## 📦 Instalación Rápida

1. **Crear y poblar la Base de Datos**:
   Importa el archivo `schema.sql` en tu gestor de base de datos (phpMyAdmin, MySQL Workbench o consola):
   ```sql
   CREATE DATABASE IF NOT EXISTS vetmedik;
   USE vetmedik;
   -- Ejecutar el contenido del archivo schema.sql
   ```

2. **Configurar Credenciales de Base de Datos**:
   Verifica la configuración en `core/controller/Database.php`:
   ```php
   $this->user = "root";
   $this->pass = "";
   $this->host = "localhost";
   $this->ddbb = "vetmedik";
   ```

3. **Acceder a la Aplicación**:
   Abre tu navegador web e ingresa a:
   ```
   http://localhost/update_mv_2026/vetmedik/
   ```

4. **Credenciales de Acceso por Defecto**:
   - **Usuario**: `admin`
   - **Contraseña**: `admin`

---

## 📁 Estructura del Proyecto

```
vetmedik/
├── assets/                  # CSS, JS, librerías (CoreUI v5, SimpleBar, FullCalendar)
├── core/
│   ├── app/
│   │   ├── controller/      # Controladores HTTP (Home, Pacient, Reservation, Payment, Report, etc.)
│   │   ├── model/           # Modelos de datos PDO tipados (PacientData, ReservationData, etc.)
│   │   ├── service/         # Capa de lógica de negocio desacoplada
│   │   ├── autoload.php     # Autocarga de componentes del aplicativo
│   │   └── routes.php       # Enrutamiento modular con FastRoute
│   └── controller/          # Clases base del núcleo (Database, LbModel, Session, Request, ViewEngine)
├── fpdf/                    # Motor de generación de documentos PDF
├── public/                  # Vistas del sistema en Twig 3
│   ├── auth/                # Login y autenticación
│   ├── categories/          # Especialidades veterinarias
│   ├── home/                # Dashboard principal con gráficas y cards
│   ├── layouts/             # Plantilla maestra CoreUI (main.html.twig)
│   ├── medics/              # Directorio de Médicos Veterinarios
│   ├── pacients/            # Catálogo y expedientes de mascotas
│   ├── payments/            # Módulo de cobros y recibos imprimibles
│   ├── reports/             # Reportes analíticos y exportador CSV
│   ├── reservations/        # Agenda de citas y calendario FullCalendar
│   └── users/               # Administración de usuarios y accesos
├── .htaccess                # Reescribidor URL para enrutamiento limpio
├── composer.json            # Dependencias PHP (Twig 3, FastRoute)
├── index.php                # Front Controller y despachador principal
├── README.md                # Documentación oficial del sistema
└── schema.sql               # Esquema de base de datos relacional y datos demo
```

---

## 🛡️ Seguridad y Buenas Prácticas
- **Protección CSRF**: Verificación de tokens de sesión en formularios con `{{ csrf_field() }}`.
- **Consultas Preparadas**: 100% de las consultas utilizan `PDO` parametrizado contra inyecciones SQL.
- **Compatibilidad PHP 8.2+**: Modelos con propiedades explícitamente declaradas para eliminar advertencias de propiedades dinámicas.

---

Desarrollado y mantenido por [Evilnapsis](http://evilnapsis.com).