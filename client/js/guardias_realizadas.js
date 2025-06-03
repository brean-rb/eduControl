import { obtenerToken, manejarErrorAutenticacion } from './utils.js';
import { API_CONFIG } from './config.js';

document.addEventListener('DOMContentLoaded', function() {
    const formFiltro = document.getElementById('filtroGuardias');
    const tablaGuardias = document.getElementById('tablaGuardias').getElementsByTagName('tbody')[0];

    // Cargar guardias al cargar la página
    cargarGuardias();

    // Manejar el envío del formulario
    formFiltro.addEventListener('submit', function(e) {
        e.preventDefault();
        cargarGuardias();
    });

    function cargarGuardias() {
        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora').value;

        // Construir la URL con los parámetros
        let url = `${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.OBTENER_GUARDIAS_REALIZADAS}&fecha=${fecha}`;
        if (hora) {
            url += `&hora=${hora}`;
        }

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
                'Authorization': `Bearer ${obtenerToken()}`
            }
        })
            .then(response => response.json())
            .then(data => {
                tablaGuardias.innerHTML = '';

                if (data.success) {
                    if (data.guardias.length === 0) {
                        const noDataRow = document.createElement('tr');
                        noDataRow.innerHTML = `
                            <td colspan="8" class="text-center">
                                No se encontraron guardias para los criterios seleccionados
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
                    const errorRow = document.createElement('tr');
                    errorRow.innerHTML = `
                        <td colspan="8" class="text-center text-danger">
                            Error: ${data.message}
                        </td>
                    `;
                    tablaGuardias.appendChild(errorRow);
                }
            })
            .catch(error => manejarErrorAutenticacion(error));
    }
});