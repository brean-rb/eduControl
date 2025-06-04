/**
 * =========================
 *  utils.js (Utilidades)
 * =========================
 * 
 * Archivo de utilidades y funciones auxiliares.
 * Contiene funciones comunes utilizadas en toda la aplicación:
 * - Manejo de tokens JWT
 * - Gestión de errores
 * - Mostrar mensajes modales
 * - Funciones de autenticación
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 */

import { API_CONFIG, ROLES, MENSAJES, STORAGE_KEYS } from './config.js';

// Función para obtener el token JWT desde localStorage
export function obtenerToken() {
    const token = localStorage.getItem(STORAGE_KEYS.TOKEN);
    if (!token) {
        window.location.href = 'login.php';
        return null;
    }
    return token;
}

// Función para verificar si el usuario está autenticado
export function verificarAutenticacion() {
    const token = localStorage.getItem(STORAGE_KEYS.TOKEN);
    const rol = localStorage.getItem(STORAGE_KEYS.ROL);
    
    if (!token || !rol) {
        window.location.href = 'login.php';
        return false;
    }
    
    return true;
}

// Función para manejar errores de autenticación
export function manejarErrorAutenticacion(error) {
    console.error('Error de autenticación:', error);
    
    // Limpiar datos de sesión
    localStorage.removeItem(STORAGE_KEYS.TOKEN);
    localStorage.removeItem(STORAGE_KEYS.ROL);
    localStorage.removeItem(STORAGE_KEYS.NOMBRE);
    
    // Mostrar mensaje apropiado
    if (error.message.includes('expired')) {
        mostrarMensajeModal(MENSAJES.SESION_EXPIRADA, 'danger');
    } else if (error.message.includes('invalid')) {
        mostrarMensajeModal(MENSAJES.TOKEN_INVALIDO, 'danger');
    } else {
        mostrarMensajeModal(MENSAJES.ERROR_AUTENTICACION, 'danger');
    }
    
    // Redirigir al login
    window.location.href = 'login.php';
}

// Función para hacer peticiones autenticadas
export async function fetchAutenticado(url, options = {}) {
    try {
        const token = obtenerToken();
        if (!token) return null;

        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            credentials: 'same-origin'
        };

        const response = await fetch(url, { ...defaultOptions, ...options });
        
        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Token inválido o expirado');
            }
            throw new Error(`Error del servidor: ${response.status}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        manejarErrorAutenticacion(error);
        return null;
    }
}

export function tienePermiso(rolRequerido) {
    const rolUsuario = localStorage.getItem(STORAGE_KEYS.ROL);
    return rolUsuario === rolRequerido;
}

export function esAdmin() {
    return tienePermiso(ROLES.ADMIN);
}

export function esDocente() {
    return tienePermiso(ROLES.DOCENTE);
}

export function esJefeDepartamento() {
    return tienePermiso(ROLES.JEFE_DEPARTAMENTO);
}

// Función para mostrar un modal de mensaje bonito
export function mostrarMensajeModal(mensaje, tipo = 'success') {
    let modal = document.getElementById('mensajeModal');
    if (!modal) {
        // Crear el modal si no existe
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'mensajeModal';
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-${tipo} text-white">
                        <h5 class="modal-title">${tipo === 'success' ? 'Éxito' : 'Error'}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="mensajeModalTexto"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-${tipo}" data-bs-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    // Cambiar color y texto según tipo
    const header = modal.querySelector('.modal-header');
    const btn = modal.querySelector('.modal-footer .btn');
    if (header && btn) {
        header.className = `modal-header bg-${tipo} text-white`;
        btn.className = `btn btn-${tipo}`;
        modal.querySelector('.modal-title').textContent = tipo === 'success' ? 'Éxito' : 'Error';
    }
    // Mensaje
    modal.querySelector('#mensajeModalTexto').textContent = mensaje;
    // Mostrar modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}
