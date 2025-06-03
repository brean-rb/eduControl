// Configuración de la API REST
const API_CONFIG = {
    BASE_URL: 'http://localhost/proyecto_control_asistencia_rest/server/index.php',
    RUTAS: {
        LOGIN: '?ruta=login',
        LOGOUT: '?ruta=logout',
        HORARIOS: '?ruta=horarios',
        JORNADA: '?ruta=jornada',
        ASISTENCIA: '?ruta=asistencia',
        GUARDIAS: '?ruta=guardias',
        CONSULTAR_GUARDIAS: '?ruta=consultar_guardias',
        AUSENCIAS: '?ruta=ausencias',
        INFORME_AUSENCIAS: '?ruta=informe_ausencias',
        DOCENTES: '?ruta=docentes',
        HORARIO_AUSENTE: '?ruta=horario_ausente',
        REGISTRAR_GUARDIA: '?ruta=registrar_guardia',
        OBTENER_GUARDIAS_REALIZADAS: '?ruta=obtener_guardias_realizadas'
    }
};

// Constantes de roles de usuario
const ROLES = {
    ADMIN: 'admin',
    DOCENTE: 'docente',
    JEFE_DEPARTAMENTO: 'jefe_departamento'
};

// Constantes de mensajes
const MENSAJES = {
    ERROR_AUTENTICACION: 'Error de autenticación. Por favor, inicie sesión nuevamente.',
    ERROR_SERVIDOR: 'Error del servidor. Por favor, intente más tarde.',
    SESION_EXPIRADA: 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.',
    TOKEN_INVALIDO: 'Token inválido. Por favor, inicie sesión nuevamente.'
};

// Constantes de almacenamiento
const STORAGE_KEYS = {
    TOKEN: 'jwtToken',
    ROL: 'userRole',
    NOMBRE: 'userName'
};

// Exportar configuración
export { API_CONFIG, ROLES, MENSAJES, STORAGE_KEYS }; 