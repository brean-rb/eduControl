import { obtenerToken, manejarErrorAutenticacion } from './utils.js';
import { API_CONFIG } from './config.js';

document.addEventListener('DOMContentLoaded', () => {
    const formInforme = document.getElementById('form-informe');
    const tipoInforme = document.getElementById('tipo-informe');
    const campoDocente = document.getElementById('campo-docente');
    const campoFecha = document.getElementById('campo-fecha');
    const documento = document.getElementById('documento');
    const fecha = document.getElementById('fecha');

    function toggleCampos() {
        if (tipoInforme.value === 'docente') {
            campoDocente.style.display = 'block';
            campoFecha.style.display = 'none';
            documento.required = true;
            fecha.required = false;
            // Cargar docentes solo si el select está vacío (evita recarga innecesaria)
            if (documento.options.length <= 1) {
                fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.DOCENTES}`, {
                    headers: {
                        'Authorization': `Bearer ${obtenerToken()}`
                    },
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Limpiar opciones previas excepto la primera
                        documento.innerHTML = '<option value="">Selecciona un docente...</option>';
                        data.docentes.forEach(docente => {
                            const option = document.createElement('option');
                            option.value = docente.document;
                            option.textContent = docente.nombre;
                            documento.appendChild(option);
                        });
                    } else {
                        alert('Error al cargar docentes: ' + data.message);
                    }
                })
                .catch(error => manejarErrorAutenticacion(error));
            }
        } else {
            campoDocente.style.display = 'none';
            campoFecha.style.display = 'block';
            documento.required = false;
            fecha.required = true;
        }
    }

    // Escuchar cambios en el select
    tipoInforme.addEventListener('change', toggleCampos);

    // Ejecutar la función al cargar la página
    toggleCampos();

    formInforme.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(formInforme);
        try {
            const response = await fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.INFORME_AUSENCIAS}`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarResultados(data.ausencias);
            } else {
                mostrarMensajeModal('Error al generar el informe: ' + data.message, 'danger');
            }
        } catch (error) {
            mostrarMensajeModal('Error al generar el informe', 'danger');
        }
    });

    function mostrarResultados(ausencias) {
        const tbody = document.querySelector('#tabla-ausencias tbody');
        tbody.innerHTML = '';

        if (ausencias.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">No se encontraron ausencias para los criterios seleccionados</td>
                </tr>
            `;
            return;
        }

        ausencias.forEach(ausencia => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${ausencia.nombre}</td>
                <td>${ausencia.fecha_inicio}</td>
                <td>${ausencia.fecha_fin}</td>
                <td>${ausencia.motivo}</td>
                <td>
                    <span class="badge ${ausencia.justificada ? 'bg-success' : 'bg-danger'}">
                        ${ausencia.justificada ? 'Sí' : 'No'}
                    </span>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
});