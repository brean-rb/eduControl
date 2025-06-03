import { obtenerToken, manejarErrorAutenticacion } from './utils.js';
import { API_CONFIG } from './config.js';

document.addEventListener('DOMContentLoaded', () => {
    cargarProfesoresAusentes();

    function cargarProfesoresAusentes() {
        fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.CONSULTAR_GUARDIAS}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${obtenerToken()}`
            },
            credentials: 'same-origin'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarProfesoresAusentes(data.profesores_ausentes);
            } else {
                mostrarMensajeModal('Error al cargar los profesores ausentes: ' + data.message, 'danger');
            }
        })
        .catch(error => manejarErrorAutenticacion(error));
    }

    function mostrarHorarioProfesor(e) {
        const documento = e.target.dataset.documento;
        const fecha = new Date().toISOString().split('T')[0];
        const formData = new FormData();
        formData.append('documento', documento);
        formData.append('fecha', fecha);

        // Mostrar el contenedor del horario antes de la petición
        document.getElementById('horario-container').style.display = 'block';

        fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.HORARIO_AUSENTE}`, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarHorario(data.horario, documento);
            } else {
                mostrarMensajeModal('Error al cargar el horario: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            document.getElementById('horario-container').style.display = 'none';
        });
    }

    function reservarGuardia(e) {
        const btn = e.target;
        const formData = new FormData();
        formData.append('fecha', new Date().toISOString().split('T')[0]);
        formData.append('docente_ausente', btn.dataset.docente);
        formData.append('hora_inicio', btn.dataset.horaInicio);
        formData.append('hora_fin', btn.dataset.horaFin);
        formData.append('grupo', btn.dataset.grupo);
        formData.append('aula', btn.dataset.aula);
        formData.append('contenido', btn.dataset.contenido);
        formData.append('docente_guardia', document.getElementById('docente_actual').value);

        fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.RUTAS.REGISTRAR_GUARDIA}`, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                mostrarMensajeModal('Guardia registrada correctamente', 'success');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-lock"></i> Reservada';
                btn.classList.replace('btn-success', 'btn-danger');
                window.location.reload();
            } else {
                mostrarMensajeModal('Error al registrar la guardia: ' + data.message, 'danger');
            }
        })
        .catch(error => {
        });
    }

    function mostrarHorario(horario, docente_ausente) {
        const tbody = document.querySelector('#tabla-horario tbody');
        tbody.innerHTML = '';

        if (!horario || horario.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">No hay clases disponibles para hoy</td>
                </tr>`;
            return;
        }

        horario.forEach(clase => {
            const row = document.createElement('tr');
            const botonGuardia = clase.reservada ? 
                `<button class="btn btn-danger btn-sm" disabled>
                    <i class="fas fa-lock"></i> Reservada
                </button>` :
                `<button class="btn btn-success btn-sm reservar-guardia"
                    data-docente="${docente_ausente}"
                    data-hora-inicio="${clase.hora_inicio}"
                    data-hora-fin="${clase.hora_fin}"
                    data-grupo="${clase.grupo}"
                    data-aula="${clase.aula}"
                    data-contenido="${clase.asignatura}">
                    <i class="fas fa-plus"></i> Reservar
                </button>`;

            row.innerHTML = `
                <td>${clase.hora_inicio} - ${clase.hora_fin}</td>
                <td>${clase.asignatura || 'No disponible'}</td>
                <td>${clase.grupo || 'No disponible'}</td>
                <td>${clase.aula || 'No disponible'}</td>
                <td>${botonGuardia}</td>
            `;
            tbody.appendChild(row);
        });
        
        // Añadir eventos a los botones
        document.querySelectorAll('.reservar-guardia').forEach(btn => {
            btn.addEventListener('click', reservarGuardia);
        });
    }

    function mostrarProfesoresAusentes(profesores) {
        const tbody = document.querySelector('#tabla-profesores-ausentes tbody');
        tbody.innerHTML = '';

        if (profesores.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">No hay profesores ausentes hoy</td>
                </tr>`;
            return;
        }

        profesores.forEach(profesor => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${profesor.nombre}</td>
                <td>${profesor.fecha_inicio}</td>
                <td>${profesor.fecha_fin}</td>
                <td>
                    <button class="btn btn-danger ver-horario" 
                            data-documento="${profesor.documento}">
                        Ver Horario
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Añadir eventos a los botones
        document.querySelectorAll('.ver-horario').forEach(btn => {
            btn.addEventListener('click', mostrarHorarioProfesor);
        });
    }
});