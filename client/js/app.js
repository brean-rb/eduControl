import { obtenerToken, manejarErrorAutenticacion, fetchAutenticado, verificarAutenticacion } from './utils.js';
import { API_CONFIG } from './config.js';

document.addEventListener('DOMContentLoaded', function() {
    // Verificar autenticación
    if (!verificarAutenticacion()) return;

    // Prevenir que el enlace redirija al hacer clic
    document.querySelector('#adminDropdown')?.addEventListener('click', function(e) {
        e.preventDefault();
    });

    // Mejorar la experiencia en dispositivos táctiles
    if('ontouchstart' in document.documentElement) {
        document.querySelector('#adminDropdown')?.addEventListener('click', function() {
            this.parentElement.classList.toggle('show');
            document.querySelector('.dropdown-menu').classList.toggle('show');
        });
    }

    // Cargar horario
    cargarHorario();

    const btnInicio = document.getElementById('btn-inicio-jornada');
    const btnFin = document.getElementById('btn-fin-jornada');
    const mensaje = document.getElementById('mensaje-jornada');

    if (btnInicio) {
        btnInicio.addEventListener('click', async function() {
            const data = await fetchAutenticado(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.JORNADA}`, {
                method: 'POST'
            });
            
            if (data) {
                if (data.success) {
                    mensaje.innerText = data.message;
                    mensaje.className = 'alert alert-success mt-3';
                } else {
                    mensaje.innerText = data.message || 'Error desconocido';
                    mensaje.className = 'alert alert-danger mt-3';
                }
            }
        });
    }

    if (btnFin) {
        btnFin.addEventListener('click', async function() {
            const data = await fetchAutenticado(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.JORNADA}`, {
                method: 'PUT'
            });
            
            if (data) {
                if (data.success) {
                    mensaje.innerText = data.message;
                    mensaje.className = 'alert alert-success mt-3';
                } else {
                    mensaje.innerText = data.message || 'Error desconocido';
                    mensaje.className = 'alert alert-danger mt-3';
                }
            }
        });
    }

    // Manejo global de logout
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function() {
            try {
                await fetchAutenticado(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.LOGOUT}`, {
                    method: 'POST'
                });
            } finally {
                localStorage.removeItem('jwtToken');
                localStorage.removeItem('userRole');
                localStorage.removeItem('userName');
                window.location.href = 'login.php';
            }
        });
    }

    // Verificar si estamos en la página de login
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            
            fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.LOGIN}`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    mostrarMensajeModal('Error de login: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                mostrarMensajeModal('Error al iniciar sesión', 'danger');
            });
        });
    }
});

async function cargarHorario() {
    const tbody = document.getElementById('tablaHorario');
    if (!tbody) return;

    const data = await fetchAutenticado(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.HORARIOS}`);
    if (!data) return;

        tbody.innerHTML = '';

        if (!data.success) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">
                ${data.message || 'Error al cargar el horario'}
            </td></tr>`;
            return;
        }

        if (!data.horarios || data.horarios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay horario disponible</td></tr>';
            return;
        }

        const diasSemana = {
            'L': 'Lunes',
            'M': 'Martes',
            'X': 'Miércoles',
            'J': 'Jueves',
            'V': 'Viernes'
        };

        data.horarios.forEach(horario => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${diasSemana[horario.dia_setmana] || horario.dia_setmana}</td>
                <td>${horario.hora_desde}</td>
                <td>${horario.hora_fins}</td>
                <td>${horario.asignatura || 'No disponible'}</td>
                <td>${horario.grupo || 'No disponible'}</td>
                <td>${horario.aula || 'No disponible'}</td>
            `;
            tbody.appendChild(tr);
    });
}