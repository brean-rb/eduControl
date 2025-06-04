<?php
/**
 * =========================
 *  index.php (Panel de Control)
 * =========================
 * 
 * Vista principal del sistema de control de asistencia.
 * Permite a los usuarios:
 * - Iniciar/finalizar jornada
 * - Ver horario del día
 * - Acceder a funcionalidades según rol
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 * 
 * @requires   navbar.js    Componente de navegación
 * @requires   app.js       Lógica principal de la aplicación
 * @requires   styles.css   Estilos de la aplicación
 */

// Verificar si el usuario está autenticado mediante el token JWT
$token = isset($_COOKIE['jwtToken']) ? $_COOKIE['jwtToken'] : null;
if (!$token) {
    // Si no hay token en la cookie, verificar localStorage
    echo "<script>
        if (!localStorage.getItem('jwtToken')) {
            window.location.href = 'login.php';
        }
    </script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Asistencia y Guardias</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">

    <div id="navbar-container"></div>
    <!-- Contenido principal -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="text-center mb-4" id="welcomeMessage">Bienvenido</h2>
                
                <!-- Botones para inicio/fin de jornada -->
                <div class="d-flex justify-content-center gap-4 mb-5">
                    <button id="btn-inicio-jornada" class="btn btn-inicio-jornada">
                        <i class="fas fa-play me-2"></i>Inicio de jornada
                    </button>
                    <button id="btn-fin-jornada" class="btn btn-fin-jornada">
                        <i class="fas fa-stop me-2"></i>Finalizar jornada
                    </button>
                </div>
                <div id="mensaje-jornada"></div>

                <!-- Título de horario -->
                <h3 class="text-center fw-bold mb-4">Su horario</h3>

                <!-- Tabla de horario -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Día</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Asignatura</th>
                                <th>Grupo</th>
                                <th>Aula</th>
                            </tr>
                        </thead>
                        <tbody id="tablaHorario">
                            <!-- Aquí se cargarán los datos dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JS de Bootstrap CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="module" src="./js/navbar.js"></script>
    <script type="module" src="./js/app.js"></script>
</body>
</html>
