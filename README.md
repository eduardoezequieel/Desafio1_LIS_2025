# Sistema de Gestión Financiera - Desafío LIS 2025

## Integrantes del equipo

- López Rivera, Eduardo Ezequiel LR230061
- López Martínez, Diego René LM231893
- Esnard Romero, Diego Guillermo ER231474
- Crespin Lozano, Christian Gustavo CL060107

## 🚀 Características

- **Gestión de Transacciones**: CRUD para entradas y salidas
- **Dashboard Analítico**: Gráficos y reportes PDF
- **Facturas Digitales**: Subida y visualización de imágenes
- **Sistema de Usuarios**: Autenticación y administración

## 🛠️ Tecnologías

- PHP 8.0+ | MariaDB/MySQL | Bootstrap 5 | Chart.js | Apache (XAMPP)

## 📋 Prerrequisitos

- [XAMPP](https://www.apachefriends.org/download.html) versión 8.0+
- Navegador web moderno

## ⚡ Instalación Rápida

### 1. Descargar Proyecto

```bash
# Descomprimir en: c:\xampp\htdocs\Desafio1_LIS_2025
```

### 2. Iniciar XAMPP

- ✅ Apache
- ✅ MySQL

### 3. Configurar Base de Datos

1. **Ir a phpMyAdmin**: `http://localhost/phpmyadmin`
2. **Crear base de datos**:
   ```sql
   CREATE DATABASE Desafio1LIS;
   ```
3. **Importar backup**:
   - Seleccionar base de datos `Desafio1LIS`
   - Ir a pestaña "Importar"
   - Seleccionar archivo: `desafio1lis.sql` (raíz del proyecto)
   - Hacer clic en "Continuar"

## 🚦 Ejecutar

1. **Verificar servicios XAMPP** (Apache + MySQL activos)
2. **Acceder**: `http://localhost/Desafio1_LIS_2025/public/login`
3. **Credenciales**:
   ```
   Usuario: admin
   Contraseña: admin123
   ```

## 📁 Estructura Básica

```
Desafio1_LIS_2025/
├── desafio1lis.sql          # 🔥 BACKUP DB
├── app/                     # Backend PHP
├── public/                  # Punto entrada + uploads
├── resources/               # CSS + JS + librerías
└── views/                   # Vistas PHP
```

---

**Desarrollado para Desafío LIS 2025**
CREATE TABLE transaction_categories (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
type ENUM('income', 'expense') NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transacciones
CREATE TABLE transactions (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
category_id INT NOT NULL,
transaction_type ENUM('income', 'expense') NOT NULL,
amount DECIMAL(10,2) NOT NULL,
description TEXT,
invoice_path VARCHAR(255),
date DATE NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (category_id) REFERENCES transaction_categories(id)
);

````

4. **Datos incluidos en el backup:**

   Si usaste el archivo `desafio1lis.sql`, ya tienes:

   ```sql
   -- Usuario admin por defecto (password: admin123)
   -- Usuario: admin
   -- Contraseña: admin123

   -- Categorías de ejemplo preconfiguradas:
   -- Entradas: Salario, Freelance, Inversiones, Ventas
   -- Salidas: Alimentación, Transporte, Servicios, Entretenimiento

   -- Transacciones de ejemplo para demostración
````

## 🚦 Ejecutar el Proyecto

### 1. Verificar Servicios

- Apache: ✅ Ejecutándose
- MySQL: ✅ Ejecutándose

### 2. Acceder al Sistema

Abrir navegador y navegar a:

```
http://localhost/Desafio1_LIS_2025/public/login
```

### 3. Credenciales por Defecto

```
Usuario: admin
Contraseña: admin123
```

## 📁 Estructura del Proyecto

```
Desafio1_LIS_2025/
│
├── desafio1lis.sql               # 🔥 BACKUP DE BASE DE DATOS
│
├── app/                          # Lógica del backend
│   ├── api/                      # Endpoints API REST
│   │   ├── transactions.php      # API de transacciones
│   │   └── users.php            # API de usuarios
│   ├── config/                   # Configuraciones
│   │   ├── Database.php         # Conexión a BD
│   │   └── Validator.php        # Validaciones
│   ├── helpers/                  # Utilidades
│   │   └── DashboardPage.php    # Helper de layout
│   └── models/                   # Modelos de datos
│       ├── Transaction.php      # Modelo de transacciones
│       ├── TransactionCategory.php
│       └── User.php             # Modelo de usuarios
│
├── public/                       # Punto de entrada público
│   ├── uploads/                  # Archivos subidos
│   │   └── invoices/            # Facturas
│   ├── index.php               # Página de inicio
│   └── login/                   # Sistema de login
│
├── resources/                    # Recursos del frontend
│   ├── css/                     # Estilos
│   │   ├── globals.css          # Estilos globales
│   │   ├── dashboard-index.css  # Dashboard principal
│   │   ├── incomes.css          # Formularios
│   │   └── transactions.css     # Vista unificada
│   ├── js/                      # JavaScript
│   │   ├── components.js        # Utilidades UI
│   │   └── auth/                # Scripts del dashboard
│   │       ├── login.js         # Lógica de inicio de sesión
│   │   └── dashboard/           # Scripts del dashboard
│   │       ├── index.js         # Dashboard + PDF
│   │       ├── transactions.js  # Vista unificada
│   │       ├── users.js         # Gestión usuarios
│   │       └── sidebar.js       # Navegación
│   └── lib/                     # Librerías externas
│       ├── bootstrap/           # Bootstrap 5
│       └── swal/               # SweetAlert2
│
├── views/                       # Vistas PHP
│   └── auth/                    # Vistas de inicio de sesión
│       ├── login.php            # Inicio de sesión
│   └── dashboard/               # Vistas del dashboard
│       ├── index.php            # Dashboard principal
│       ├── transactions.php     # Vista unificada
│       └── users.php            # Usuarios
│
└── README.md                    # Este archivo
```

## 🎯 Funcionalidades Principales

### Dashboard Principal

- **Resumen financiero**: Total entradas, salidas y balance
- **Gráfico circular**: Distribución visual de transacciones
- **Tablas resumidas**: Últimas transacciones por tipo
- **Generación de PDF**: Reporte completo con gráficos

### Gestión de Transacciones

- **Vista unificada**: Tabla con filtros dinámicos
- **Formularios duales**: Modales separados para entradas/salidas
- **Subida de facturas**: Drag & drop con validación
- **Categorización**: Selección de categorías por tipo

### Sistema de Usuarios

- **Autenticación**: Login/logout con sesiones
- **CRUD completo**: Crear, editar, eliminar usuarios
- **Validaciones**: Campos únicos y requeridos

## 🔧 Configuración Avanzada

### Variables de Entorno (Opcional)

Editar `app/config/Database.php` para personalizar:

```php
// Configuración de base de datos
private $host = "localhost";
private $db_name = "Desafio1LIS";
private $username = "root";
private $password = "";
```

## 🐛 Solución de Problemas Comunes

### Error de Conexión a Base de Datos

```
Solución:
1. Verificar que MySQL esté ejecutándose en XAMPP
2. Confirmar credenciales en Database.php
3. Verificar que la base de datos existe
4. Re-importar desafio1lis.sql si es necesario
```

### JavaScript no Carga

```
Solución:
1. Verificar rutas en navegador (F12 > Network)
2. Confirmar que Apache está sirviendo archivos estáticos
3. Revisar console para errores de JavaScript
```

### Problema con PDF

```
Solución:
1. Verificar que Chart.js está cargando correctamente
2. Confirmar que jsPDF está incluido
3. Revisar permisos de descarga del navegador
```

## 🔒 Seguridad

- **Validación de entrada**: Sanitización en `Validator.php`
- **Prepared statements**: Prevención de SQL injection
- **Validación de archivos**: Tipos y tamaños permitidos

## 📊 Características Técnicas

- **Arquitectura MVC**: Separación clara de responsabilidades
- **API RESTful**: Endpoints consistentes con códigos HTTP
- **Backup automatizado**: Estructura y datos incluidos en `desafio1lis.sql`
- **Filtrado Client-Side**: Mejor experiencia de usuario
- **Carga asíncrona**: JavaScript moderno con async/await
