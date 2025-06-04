<?php
/**
 * =========================
 *  login.php (Autenticación)
 * =========================
 * 
 * Vista de inicio de sesión del sistema.
 * Permite a los usuarios:
 * - Ingresar credenciales
 * - Autenticarse en el sistema
 * - Recuperar acceso
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 * 
 * @requires   login.js     Lógica de autenticación
 * @requires   styles.css   Estilos de la aplicación
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <!-- Bootstrap CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">

    <!-- Contenedor principal -->
    <div class="login-container p-4">
        <!-- Título -->
        <h1 class="login-title mb-4">login</h1>

        <!-- Formulario de login -->
        <form id="login-form">
            <div class="mb-3">
                <label for="dni" class="form-label text-white">DNI:</label>
                <input 
                    type="text" 
                    class="form-control rounded-input" 
                    id="dni" 
                    name="dni" 
                    placeholder="DNI"
                    required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-white">PASSWORD:</label>
                <input 
                    type="password" 
                    class="form-control rounded-input" 
                    id="password" 
                    name="password" 
                    placeholder="password"
                    required>
            </div>

            <div id="login-error" class="text-danger mb-2"></div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-custom">
                    sign in <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- JavaScript personalizado -->
    <script type="module" src="./js/login.js"></script>
</body>
</html>
