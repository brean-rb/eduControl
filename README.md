# Sistema de Control de Asistencia y Gestión de Guardias Docentes

## Descripción
Sistema integral desarrollado para la gestión eficiente de la asistencia del personal docente y la administración de guardias en instituciones educativas. Esta aplicación proporciona una solución robusta para el control de jornadas laborales y la gestión de suplencias.

## Características Principales
- ✅ Control de asistencia en tiempo real
- 📊 Gestión automatizada de guardias
- 🔐 Sistema de autenticación seguro
- 📱 Interfaz responsiva y moderna
- 📈 Generación de informes detallados
- 👥 Gestión de roles y permisos

## Requisitos Técnicos
- PHP >= 7.4
- MySQL/MariaDB
- Servidor web Apache
- Navegador web moderno

## Instalación

### 1. Preparación del Entorno
```bash
# Clonar el repositorio
git clone https://github.com/brean-rb/eduControl.git

# Mover al directorio de XAMPP
mv eduControl/ C:/xampp/htdocs/
```

### 2. Configuración de la Base de Datos
1. Acceder a phpMyAdmin (http://localhost/phpmyadmin)
2. Crear nueva base de datos: `gestion_guardias_asistencias`
3. Importar el esquema desde `database/gestion_guardias_asistencias.sql`

### 3. Configuración del Sistema
Editar el archivo de configuración en `server/config/config.php`:
```php
define('SERVER', 'localhost');
define('USER', 'root');
define('PASSWORD', '');
define('DATABASE', 'gestion_guardias_asistencias');
```

## Acceso al Sistema
URL: `http://localhost/eduControl/client/src/login.php`

### Credenciales de Prueba
| Rol | Documento | Contraseña |
|-----|-----------|------------|
| Administrador | 11111111A | secret |
| Profesor | 22222222B | secret |

## Estructura del Proyecto
```
eduControl/
├── client/                 # Frontend de la aplicación
│   ├── css/               # Estilos
│   ├── js/                # Scripts del cliente
│   └── src/               # Vistas PHP
├── server/                # Backend de la aplicación
│   ├── config/            # Configuraciones
│   └── api/               # Endpoints de la API
└── database/              # Scripts de base de datos
```

## Tecnologías Implementadas
- Frontend: HTML5, CSS3, JavaScript, Bootstrap 5
- Backend: PHP 7.4+
- Base de Datos: MySQL/MariaDB
- Servidor: Apache (XAMPP)

## Seguridad
- Autenticación basada en roles
- Registro de sesiones
- Protección contra inyección SQL
- Validación de datos en cliente y servidor

## Soporte
Para reportar problemas o solicitar nuevas características, por favor crear un issue en el repositorio del proyecto: [https://github.com/brean-rb/eduControl](https://github.com/brean-rb/eduControl)

## Licencia
Este proyecto está desarrollado con fines educativos como parte del módulo de Proyecto del Ciclo Superior de Desarrollo de Aplicaciones Web (DAW).

---
Desarrollado por Rubén Ferrer  
IES Joan Coromines  
DAW 2024-2025
