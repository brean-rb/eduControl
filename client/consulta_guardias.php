<?php
/**
 * =========================
 *  consulta_guardias.php (Consulta de Guardias)
 * =========================
 * 
 * Vista de consulta y gestión de guardias.
 * Permite a los usuarios:
 * - Ver profesores ausentes
 * - Asignar guardias
 * - Gestionar suplencias
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 * 
 * @requires   consulta_guardias.js    Lógica de consulta
 * @requires   navbar.js               Componente de navegación
 * @requires   styles.css              Estilos de la aplicación
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Guardias - IES Joan Coromines</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Añadir Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <div id="navbar-container"></div>

    <div class="container py-4">
        <input type="hidden" id="docente_actual" value="<?php echo $_SESSION['dni']; ?>">
        
        <div class="row">
            <div class="col-lg-12">
                <h2 class="text-center fw-bold mb-4">Profesores Ausentes Hoy</h2>
                <div class="table-responsive">
                    <table class="table table-hover" id="tabla-profesores-ausentes">
                        <thead class="table-dark">
                            <tr>
                                <th>Profesor</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los profesores ausentes se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-12 mt-5" id="horario-container" style="display: none;">
                <h2 class="text-center fw-bold mb-4">Horario del Profesor</h2>
                <div class="table-responsive">
                    <table class="table table-hover" id="tabla-horario">
                        <thead class="table-dark">
                            <tr>
                                <th>Horario</th>
                                <th>Asignatura</th>
                                <th>Grupo</th>
                                <th>Aula</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- El horario se cargará aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="module" src="./js/navbar.js"></script>
    <script type="module" src="./js/consulta_guardias.js"></script>
</body>
</html>