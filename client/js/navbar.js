import { API_CONFIG, STORAGE_KEYS } from './config.js';
import { mostrarMensajeModal } from './utils.js';

// Renderizar el navbar dinámicamente según el rol
function renderNavbar() {
    const userRole = localStorage.getItem(STORAGE_KEYS.ROL);
    const userName = localStorage.getItem(STORAGE_KEYS.NOMBRE);
    const navbarContainer = document.getElementById('navbar-container');
    if (!navbarContainer) return;

    let navLinks = '';
    if (userRole === 'admin') {
        navLinks = `
            <ul class="navbar-nav mx-auto" id="navLinks">
                <li class="nav-item">
                    <a class="nav-link" href="consulta_guardias.php">Guardias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="guardias_realizadas.php">Guardias realizadas</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="adminDropdown" role="button">
                        Administración
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="consulta_asistencia.php">Consulta Asistencia</a></li>
                        <li><a class="dropdown-item" href="registro_ausencia.php">Registro Ausencia</a></li>
                        <li><a class="dropdown-item" href="informe_ausencias.php">Informe Ausencias</a></li>
                    </ul>
                </li>
            </ul>
        `;
    } else {
        navLinks = `
            <ul class="navbar-nav mx-auto" id="navLinks">
                <li class="nav-item">
                    <a class="nav-link px-4" href="consulta_guardias.php">Guardias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4" href="guardias_realizadas.php">Guardias realizadas</a>
                </li>
            </ul>
        `;
    }

    navbarContainer.innerHTML = `
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
            <div class="container">
                <a class="navbar-brand text-dark fw-bold" href="index.php">
                    IES Joan Coromines
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    ${navLinks}
                    <div class="ms-auto">
                        <button type="button" id="logout-btn" class="btn btn-danger">
                            log out <i class="fas fa-sign-out-alt ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    `;
}

// Configurar mensaje de bienvenida si existe el elemento
function renderBienvenida() {
    const userName = localStorage.getItem(STORAGE_KEYS.NOMBRE);
    const welcomeMessage = document.getElementById('welcomeMessage');
    if (welcomeMessage) {
        welcomeMessage.textContent = `Bienvenido, ${userName || 'Usuario'}`;
    }
}

let logoutInProgress = false;

document.addEventListener('DOMContentLoaded', () => {
    renderNavbar();
    renderBienvenida();

    // Eliminar eventos anteriores y evitar duplicidad del botón
    const oldLogoutBtn = document.getElementById('logout-btn');
    if (oldLogoutBtn) {
        const newLogoutBtn = oldLogoutBtn.cloneNode(true);
        oldLogoutBtn.parentNode.replaceChild(newLogoutBtn, oldLogoutBtn);
    }

    // Solución definitiva: solo asignar el evento una vez por sesión de navegador
    if (!window.__navbarLogoutAssigned) {
        window.__navbarLogoutAssigned = true;
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async (event) => {
                event.preventDefault();
                if (logoutInProgress) return;
                logoutInProgress = true;
                logoutBtn.disabled = true;
                const token = localStorage.getItem('jwtToken');
                try {
                    await fetch('http://localhost/proyecto_control_asistencia_rest/server/index.php?ruta=logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });
                } catch (e) {}
                localStorage.removeItem('jwtToken');
                localStorage.removeItem('userRole');
                localStorage.removeItem('userName');
                window.location.href = 'login.php';
            });
        }
    }
}); 