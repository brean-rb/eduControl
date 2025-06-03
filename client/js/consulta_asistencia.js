import { obtenerToken, manejarErrorAutenticacion } from './utils.js';
import { API_CONFIG } from './config.js';

document.addEventListener('DOMContentLoaded', () => {
    const formConsulta = document.getElementById('form-consulta');
    const tipoConsulta = document.getElementById('tipo-consulta');
    const campoDocente = document.getElementById('campo-docente');
    const tipoFecha = document.getElementById('tipo-fecha');
    const campoFecha = document.getElementById('campo-fecha');
    const campoMes = document.getElementById('campo-mes');
    const selectDocente = document.getElementById('documento');
    const fecha = document.getElementById('fecha');

    // Mostrar/ocultar campo de docente según el tipo de consulta
    if (tipoConsulta) {
        tipoConsulta.addEventListener('change', () => {
            if (tipoConsulta.value === 'docente') {
                campoDocente.style.display = 'block';
                if (selectDocente) selectDocente.required = true;
            } else {
                campoDocente.style.display = 'none';
                if (selectDocente) selectDocente.required = false;
            }
        });
    }

    // Mostrar/ocultar campos de fecha según el tipo
    if (tipoFecha) {
        tipoFecha.addEventListener('change', () => {
            if (tipoFecha.value === 'dia') {
                campoFecha.style.display = 'block';
                campoMes.style.display = 'none';
                if (fecha) fecha.required = true;
                const mes = document.getElementById('mes');
                if (mes) mes.required = false;
            } else {
                campoFecha.style.display = 'none';
                campoMes.style.display = 'block';
                if (fecha) fecha.required = false;
                const mes = document.getElementById('mes');
                if (mes) mes.required = true;
            }
        });
    }

    // Cargar docentes al iniciar
    cargarDocentes();

    // Modificar fetch para incluir el token JWT
    function cargarDocentes() {
        fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.DOCENTES}`, {
            headers: {
                'Authorization': `Bearer ${obtenerToken()}`
            },
            credentials: 'same-origin'
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (data.success && selectDocente) {
                selectDocente.innerHTML = '<option value="">Selecciona un docente...</option>';
                data.docentes.forEach(docente => {
                    const option = document.createElement('option');
                    option.value = docente.document;
                    option.textContent = docente.nombre;
                    selectDocente.appendChild(option);
                });
            } else {
                // Error al cargar docentes, solo alertar si es necesario
            }
        })
        .catch(error => manejarErrorAutenticacion(error));
    }

    // Manejar envío del formulario
    if (formConsulta) {
        formConsulta.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(formConsulta);
            
            fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.ASISTENCIA}`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    mostrarResultados(data.asistencias);
                } else {
                    mostrarMensajeModal('Error al consultar asistencia: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                mostrarMensajeModal('Error al consultar asistencia', 'danger');
            });
        });
    }

    function mostrarResultados(asistencias) {
        const tbody = document.querySelector('#tabla-asistencias tbody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (asistencias.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">No se encontraron registros para los criterios seleccionados</td>
                </tr>
            `;
            return;
        }

        asistencias.forEach(asistencia => {
            const estado = asistencia.hora_salida ? 'Completo' : 'Presente';
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${asistencia.nombre}</td>
                <td>${asistencia.fecha}</td>
                <td>${asistencia.hora_entrada}</td>
                <td>${asistencia.hora_salida || 'No registrada'}</td>
                <td><span class="badge ${estado === 'Completo' ? 'bg-success' : 'bg-warning'}">${estado}</span></td>
            `;
            tbody.appendChild(row);
        });
    }
});