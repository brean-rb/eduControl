import { obtenerToken, manejarErrorAutenticacion } from './utils.js';
import { API_CONFIG } from './config.js';

document.addEventListener('DOMContentLoaded', () => {
    const formAusencia = document.getElementById('form-ausencia');
    const campoMismoDia = document.getElementById('campo-mismo-dia');
    const campoPeriodo = document.getElementById('campo-periodo');
    const tipoRadios = document.querySelectorAll('input[name="tipo"]');
    const selectDocente = document.getElementById('select-docente');

    function toggleCampos() {
        const tipo = document.querySelector('input[name="tipo"]:checked').value;

        if (tipo === 'dia') {
            campoMismoDia.style.display = 'block';
            campoPeriodo.style.display = 'none';
            document.getElementById('fecha').required = true;
            document.getElementById('fecha-inicio').required = false;
            document.getElementById('fecha-fin').required = false;
        } else {
            campoMismoDia.style.display = 'none';
            campoPeriodo.style.display = 'block';
            document.getElementById('fecha').required = false;
            document.getElementById('fecha-inicio').required = true;
            document.getElementById('fecha-fin').required = true;
        }
    }

    tipoRadios.forEach(radio => radio.addEventListener('change', toggleCampos));
    toggleCampos();

    // Manejar envío del formulario
    formAusencia.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(formAusencia);
        try {
            const response = await fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.AUSENCIAS}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': `Bearer ${obtenerToken()}`
                },
                credentials: 'same-origin'
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Mostrar modal de éxito
                const modal = new bootstrap.Modal(document.getElementById('exitoModal'));
                modal.show();
                
                // Limpiar formulario después de 2 segundos
                setTimeout(() => {
                    formAusencia.reset();
                    document.getElementById('horario-profesor').innerHTML = '';
                    modal.hide();
                }, 2000);
            } else {
                mostrarMensajeModal('Error al registrar la ausencia: ' + data.message, 'danger');
            }
        } catch (error) {
            mostrarMensajeModal('Error al registrar la ausencia. Por favor, inténtalo de nuevo.', 'danger');
        }
    });

    // Evento para cuando se selecciona una fecha
    document.getElementById('fecha').addEventListener('change', async function () {
        const documento = selectDocente.value;
        const fecha = this.value;

        if (documento && fecha) {
            const formData = new FormData();
            formData.append('documento', documento);
            formData.append('fecha', fecha);

            try {
                const response = await fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.HORARIOS}`, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    mostrarHorario(data.horarios);
                } else {
                    mostrarMensajeModal('Error al cargar el horario: ' + data.message, 'danger');
                }
            } catch (error) {
                mostrarMensajeModal('Error al cargar el horario', 'danger');
            }
        }
    });

    function mostrarHorario(horario) {
        const contenedor = document.getElementById('horario-profesor');
        contenedor.innerHTML = '';

        if (!horario || horario.length === 0) {
            contenedor.innerHTML = '<p class="text-muted">No hay clases para este día</p>';
            return;
        }

        const tabla = document.createElement('table');
        tabla.className = 'table table-hover mt-3';

        const thead = document.createElement('thead');
        thead.innerHTML = `
            <tr class="table-dark">
                <th>Seleccionar</th>
                <th>Horario</th>
                <th>Asignatura</th>
                <th>Grupo</th>
                <th>Aula</th>
            </tr>
        `;
        tabla.appendChild(thead);

        const tbody = document.createElement('tbody');
        horario.forEach(clase => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input class="form-check-input" type="checkbox" 
                           name="horas_seleccionadas[]" 
                           value="${clase.hora_desde}-${clase.hora_fins}"
                           data-inicio="${clase.hora_desde}"
                           data-fin="${clase.hora_fins}">
                </td>
                <td>${clase.hora_desde} - ${clase.hora_fins}</td>
                <td>${clase.asignatura || 'No disponible'}</td>
                <td>${clase.grupo || 'No disponible'}</td>
                <td>${clase.aula || 'No disponible'}</td>
            `;
            tbody.appendChild(tr);
        });
        tabla.appendChild(tbody);
        contenedor.appendChild(tabla);

        const inputHoraInicio = document.createElement('input');
        inputHoraInicio.type = 'hidden';
        inputHoraInicio.name = 'hora_inicio';
        inputHoraInicio.id = 'hora_inicio';

        const inputHoraFin = document.createElement('input');
        inputHoraFin.type = 'hidden';
        inputHoraFin.name = 'hora_fin';
        inputHoraFin.id = 'hora_fin';

        contenedor.appendChild(inputHoraInicio);
        contenedor.appendChild(inputHoraFin);

        const checkboxes = contenedor.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', actualizarHoras);
        });
    }

    function actualizarHoras() {
        const checkboxes = document.querySelectorAll('input[name="horas_seleccionadas[]"]:checked');
        let horaInicio = null;
        let horaFin = null;

        checkboxes.forEach(checkbox => {
            const inicio = checkbox.dataset.inicio;
            const fin = checkbox.dataset.fin;

            if (!horaInicio || inicio < horaInicio) horaInicio = inicio;
            if (!horaFin || fin > horaFin) horaFin = fin;
        });

        document.getElementById('hora_inicio').value = horaInicio || '';
        document.getElementById('hora_fin').value = horaFin || '';
    }

    // Cargar docentes en el select
    if (selectDocente) {
        fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.DOCENTES}`, {
            credentials: 'same-origin'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                selectDocente.innerHTML = '<option value="">Selecciona un docente...</option>';
                data.docentes.forEach(docente => {
                    const option = document.createElement('option');
                    option.value = docente.document;
                    option.textContent = docente.nombre;
                    selectDocente.appendChild(option);
                });
            } else {
                console.error('Error al cargar docentes:', data.message);
                alert('Error al cargar la lista de docentes: ' + data.message);
            }
        })
        .catch(error => manejarErrorAutenticacion(error));
    }
});