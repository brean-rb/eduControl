<?php
/**
 * =========================
 *  informe_ausencias.php (Informes de Ausencias)
 * =========================
 * 
 * Vista de generación de informes de ausencias.
 * Permite a los usuarios:
 * - Generar informes por docente/fecha
 * - Ver estadísticas
 * - Exportar datos
 * 
 * @package    ControlAsistencia
 * @author     Ruben Ferrer
 * @version    1.0
 * @since      2025
 * 
 * @requires   informe_ausencias.js     Lógica de informes
 * @requires   navbar.js                Componente de navegación
 * @requires   styles.css               Estilos de la aplicación
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informes de Ausencias - IES Joan Coromines</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <div id="navbar-container"></div>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center fw-bold mb-4">Informes de Ausencias</h2>
                        
                        <form id="form-informe" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de informe</label>
                                    <select class="form-select" id="tipo-informe" name="tipo_informe" required>
                                        <option value="dia">Por día</option>
                                        <option value="semana">Por semana</option>
                                        <option value="mes">Por mes</option>
                                        <option value="trimestre">Por trimestre</option>
                                        <option value="curso">Por curso académico</option>
                                        <option value="docente">Por docente</option>
                                    </select>
                                </div>

                                <div class="col-md-4" id="campo-fecha">
                                    <label for="fecha" class="form-label fw-bold">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>

                                <div class="col-md-4" id="campo-docente" style="display: none;">
                                    <label for="documento" class="form-label fw-bold">Docente</label>
                                    <select class="form-select" id="documento" name="documento">
                                        <option value="">Selecciona un docente...</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-file-alt me-2"></i>Generar Informe
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive mt-4">
                            <table class="table table-hover" id="tabla-ausencias">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Motivo</th>
                                        <th>Justificada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los resultados se cargarán aquí dinámicamente -->
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
    <script type="module" src="./js/informe_ausencias.js"></script>
</body>
</html>