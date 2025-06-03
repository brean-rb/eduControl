<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Ausencia - IES Joan Coromines</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <div id="navbar-container"></div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="exitoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">¡Éxito!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>La ausencia se ha registrado correctamente.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center fw-bold mb-4">Registro de Ausencia</h2>
                        
                        <form id="form-ausencia" action="../server/procesar_ausencia.php" method="POST">
                            <!-- Reemplazar la sección de búsqueda de profesor por un select -->
                            <div class="mb-4">
                                <label for="select-docente" class="form-label fw-bold">Seleccionar docente</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-light">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <select id="select-docente" name="documento" class="form-select" required>
                                        <option value="">Selecciona un docente...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tipo de ausencia -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tipo de ausencia</label>
                                <div class="bg-light p-3 rounded border">
                                    <div class="form-check mb-2">
                                        <input type="radio" name="tipo" id="mismo-dia" value="dia" checked class="form-check-input">
                                        <label for="mismo-dia" class="form-check-label">
                                            <i class="fas fa-clock me-2"></i>El mismo día
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="tipo" id="periodo" value="periodo" class="form-check-input">
                                        <label for="periodo" class="form-check-label">
                                            <i class="fas fa-calendar-alt me-2"></i>Periodo de tiempo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Para mismo día -->
                            <div id="campo-mismo-dia" class="mb-4">
                                <div class="bg-light p-3 rounded border">
                                    <div class="mb-3">
                                        <label for="fecha" class="form-label fw-bold">Fecha</label>
                                        <input type="date" id="fecha" name="fecha" class="form-control" required>
                                    </div>
                                    <div id="horario-profesor" class="mb-3">
                                        <!-- Aquí se cargarán dinámicamente las horas del profesor -->
                                    </div>
                                </div>
                            </div>

                            <!-- Para periodo -->
                            <div id="campo-periodo" class="mb-4" style="display:none;">
                                <div class="bg-light p-3 rounded border">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="fecha-inicio" class="form-label fw-bold">Fecha inicio</label>
                                            <input type="date" id="fecha-inicio" name="fecha_inicio" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="fecha-fin" class="form-label fw-bold">Fecha fin</label>
                                            <input type="date" id="fecha-fin" name="fecha_fin" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo motivo -->
                            <div class="mb-4">
                                <div class="bg-light p-3 rounded border">
                                    <label for="motivo" class="form-label fw-bold">Motivo de la ausencia</label>
                                    <textarea id="motivo" name="motivo" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>

                            <!-- Después del campo motivo y antes de los botones -->
                            <div class="mb-4">
                                <div class="bg-light p-3 rounded border">
                                    <div class="form-check">
                                        <input type="checkbox" id="justificada" name="justificada" class="form-check-input" value="1">
                                        <label for="justificada" class="form-label fw-bold">
                                            <i class="fas fa-check-circle me-2"></i>Ausencia justificada
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Reemplazar la sección de los botones -->
                            <div class="d-flex justify-content-center gap-3 mt-4">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save me-2"></i>guardar
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="module" src="./js/navbar.js"></script>
    <script type="module" src="./js/registro_ausencia.js"></script>
</body>
</html>