<?php
/**
 * =========================
 *  guardias_realizadas.php (Historial de Guardias)
 * =========================
 * 
 * Vista del historial de guardias realizadas.
 * Permite a los usuarios:
 * - Ver historial de guardias
 * - Filtrar por fecha/hora
 * - Exportar informes
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 * 
 * @requires   guardias_realizadas.js   Lógica de consulta
 * @requires   navbar.js                Componente de navegación
 * @requires   styles.css               Estilos de la aplicación
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guardias Realizadas - IES Joan Coromines</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <div id="navbar-container"></div>

    <div class="container py-4">
        <h2 class="text-center fw-bold mb-4">Control de Guardias Realizadas</h2>
        <div class="row justify-content-center">
            <div class="col-md-10 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Filtros de Búsqueda</h5>
                        <form id="filtroGuardias">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="hora" class="form-label">Hora (opcional)</label>
                                <input type="time" class="form-control" id="hora" name="hora">
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Guardias Realizadas</h5>
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaGuardias">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora Inicio</th>
                                        <th>Hora Fin</th>
                                        <th>Profesor Ausente</th>
                                        <th>Profesor Guardia</th>
                                        <th>Asignatura</th>
                                        <th>Grupo</th>
                                        <th>Aula</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Las guardias se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="module" src="./js/navbar.js"></script>
    <script type="module" src="./js/guardias_realizadas.js"></script>
</body>
</html> 