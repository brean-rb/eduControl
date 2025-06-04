/**
 * =========================
 *  login.js (Autenticación)
 * =========================
 * 
 * Módulo de autenticación del sistema.
 * Gestiona el proceso de inicio de sesión:
 * - Validación del formulario
 * - Envío de credenciales
 * - Almacenamiento del token JWT
 * - Redirección post-login
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 */

import { API_CONFIG, STORAGE_KEYS } from './config.js';

document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const dni = document.getElementById('dni').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorDiv = document.getElementById('login-error');
    errorDiv.textContent = '';
    errorDiv.classList.add('d-none');

    const url = `${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.LOGIN}`;

    fetch(url, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ dni, password })
    })
    .then(async res => {
        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('La respuesta del servidor no es JSON válido');
        }
        const data = await res.json();
        if (!data) {
            throw new Error('La respuesta está vacía');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            if (!data.token) {
                throw new Error('No se recibió el token de autenticación');
            }
            localStorage.setItem(STORAGE_KEYS.TOKEN, data.token);
            localStorage.setItem(STORAGE_KEYS.ROL, data.rol);
            localStorage.setItem(STORAGE_KEYS.NOMBRE, data.nombre_completo);
            window.location.href = 'index.php';
        } else {
            errorDiv.textContent = data.message || data.error || 'Error desconocido';
            errorDiv.classList.remove('d-none');
        }
    })
    .catch(error => {
        errorDiv.textContent = error.message || 'Error de conexión con el servidor';
        errorDiv.classList.remove('d-none');
    });
});
