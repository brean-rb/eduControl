<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Asistencia - IES Joan Coromines</title>
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
                        <h2 class="text-center fw-bold mb-4">Consulta de Asistencia</h2>
                        
                        <form id="form-consulta" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de consulta</label>
                                    <select class="form-select" id="tipo-consulta" name="tipo_consulta" required>
                                        <option value="docente">Por docente</option>
                                        <option value="todos">Todos los docentes</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4" id="campo-docente">
                                    <label class="form-label fw-bold">Docente</label>
                                    <select class="form-select" id="documento" name="documento">
                                        <option value="">Selecciona un docente...</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de fecha</label>
                                    <select class="form-select" id="tipo-fecha" name="tipo_fecha" required>
                                        <option value="dia">Día específico</option>
                                        <option value="mes">Mes completo</option>
                                    </select>
                                </div>

                                <div class="col-md-4" id="campo-fecha">
                                    <label class="form-label fw-bold">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha">
                                </div>

                                <div class="col-md-4" id="campo-mes" style="display: none;">
                                    <label class="form-label fw-bold">Mes</label>
                                    <input type="month" class="form-control" id="mes" name="mes">
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-search me-2"></i>Consultar
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive mt-4">
                            <table class="table table-hover" id="tabla-asistencias">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Fecha</th>
                                        <th>Hora Entrada</th>
                                        <th>Hora Salida</th>
                                        <th>Estado</th>
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
    <script type="module" src="./js/consulta_asistencia.js"></script>
</body>
</html>