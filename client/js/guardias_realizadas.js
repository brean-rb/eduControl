/**
 * =========================
 *  guardias_realizadas.js (Historial de Guardias)
 * =========================
 * 
 * Módulo de historial de guardias realizadas.
 * Gestiona la visualización del historial:
 * - Filtrado por fecha/hora
 * - Visualización de detalles
 * - Estadísticas de guardias
 * - Exportación de informes
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 */

import { obtenerToken, manejarErrorAutenticacion, mostrarMensajeModal } from './utils.js';
import { API_CONFIG } from './config.js';

document.addEventListener('DOMContentLoaded', function() {
    const formFiltro = document.getElementById('filtroGuardias');
    const tablaGuardias = document.getElementById('tablaGuardias').getElementsByTagName('tbody')[0];
    const inputFecha = document.getElementById('fecha');

    // Establecer la fecha actual por defecto
    const hoy = new Date();
    const fechaFormateada = hoy.toISOString().split('T')[0];
    inputFecha.value = fechaFormateada;

    // Cargar guardias al cargar la página
    cargarGuardias();

    // Manejar el envío del formulario
    formFiltro.addEventListener('submit', function(e) {
        e.preventDefault();
        cargarGuardias();
    });

    function cargarGuardias() {
        const token = obtenerToken();
        if (!token) {
            mostrarMensajeModal('No se ha iniciado sesión en la aplicación', 'danger');
            return;
        }

        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora').value;

        // Construir la URL con los parámetros
        let url = `${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.OBTENER_GUARDIAS_REALIZADAS}&fecha=${fecha}`;
        if (hora) {
            url += `&hora=${hora}`;
        }

        console.log('URL de búsqueda:', url); // Log para depuración

        // Limpiar la tabla
        tablaGuardias.innerHTML = '';

        // Mostrar mensaje de carga
        const loadingRow = document.createElement('tr');
        loadingRow.innerHTML = `
            <td colspan="8" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </td>
        `;
        tablaGuardias.appendChild(loadingRow);

        // Realizar la petición
        fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta del servidor:', data); // Log para depuración
            tablaGuardias.innerHTML = '';

            if (data.success) {
                if (data.guardias.length === 0) {
                    const noDataRow = document.createElement('tr');
                    noDataRow.innerHTML = `
                        <td colspan="8" class="text-center">
                            No se encontraron guardias para la fecha ${fecha}${hora ? ' y hora ' + hora : ''}
                        </td>
                    `;
                    tablaGuardias.appendChild(noDataRow);
                } else {
                    data.guardias.forEach(guardia => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${guardia.fecha}</td>
                            <td>${guardia.hora}</td>
                            <td>${guardia.hora_fin}</td>
                            <td>${guardia.profesor_ausente}</td>
                            <td>${guardia.profesor_guardia}</td>
                            <td>${guardia.asignatura}</td>
                            <td>${guardia.grupo}</td>
                            <td>${guardia.aula}</td>
                        `;
                        tablaGuardias.appendChild(row);
                    });
                }
            } else {
                mostrarMensajeModal('Error al cargar las guardias: ' + data.message, 'danger');
                const errorRow = document.createElement('tr');
                errorRow.innerHTML = `
                    <td colspan="8" class="text-center text-danger">
                        Error: ${data.message}
                    </td>
                `;
                tablaGuardias.appendChild(errorRow);
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error); // Log para depuración
            manejarErrorAutenticacion(error);
            tablaGuardias.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        Error al cargar las guardias. Por favor, intente nuevamente.
                    </td>
                </tr>
            `;
        });
    }
});