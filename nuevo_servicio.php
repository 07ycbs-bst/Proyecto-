<?php
// 1. Configuración de conexión
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Fallo la conexión: " . mysqli_connect_error());
}

// 2. Procesar el formulario al recibir el POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo       = mysqli_real_escape_string($conexion, $_POST['tipo_servicio']);
    $precio     = mysqli_real_escape_string($conexion, $_POST['precio_referencial']);
    $unidad     = mysqli_real_escape_string($conexion, $_POST['unidad_medida']); 
    $status     = intval($_POST['id_status']);
    $cat        = intval($_POST['id_tipo']);
    $id_empresa = intval($_POST['id_empresa']); 

    $sql_servicio = "INSERT INTO servicio (tipo_servicio, precio_referencial, unidad_medida, id_status, id_tipo) 
                     VALUES ('$tipo', '$precio', '$unidad', $status, $cat)";

    if (mysqli_query($conexion, $sql_servicio)) {
        $nuevo_id_servicio = mysqli_insert_id($conexion);
        $sql_relacion = "INSERT INTO R_E_S (id_empresa, id_servicio) 
                         VALUES ($id_empresa, $nuevo_id_servicio)";
        
        if (mysqli_query($conexion, $sql_relacion)) {
            echo "<script>window.location.href='servicios.php?msj=1';</script>";
            exit();
        }
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Servicio | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { 
            background: white; padding: 15px; border-radius: 10px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; 
        }
        .form-card { border: none; border-radius: 12px; background: white; padding: 30px; }
        
        @media (max-width: 768px) {
            .form-card { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-nav d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold d-none d-sm-block">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Registrar Nuevo Servicio
                </h5>
                <h5 class="mb-0 fw-bold d-block d-sm-none">Nuevo Servicio</h5>
            </div>
            <a href="servicios.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> <span class="d-none d-md-inline">Volver</span>
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card form-card shadow-sm">
                    <form action="nuevo_servicio.php" method="POST">
                        <div class="row g-3 g-md-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">EMPRESA RESPONSABLE</label>
                                <select name="id_empresa" class="form-select border-0 bg-light py-2" required>
                                    <option value="" disabled selected>Selecciona la empresa...</option>
                                    <?php 
                                    $res_emp = mysqli_query($conexion, "SELECT id_empresa, nombre_empresa FROM Empresa");
                                    while($e = mysqli_fetch_assoc($res_emp)): ?>
                                        <option value="<?php echo $e['id_empresa']; ?>"><?php echo $e['nombre_empresa']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">NOMBRE DEL SERVICIO</label>
                                <input type="text" name="tipo_servicio" class="form-control border-0 bg-light py-2" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">PRECIO ($)</label>
                                <input type="number" step="0.01" name="precio_referencial" class="form-control border-0 bg-light py-2" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">UNIDAD DE MEDIDA</label>
                                <select name="unidad_medida" class="form-select border-0 bg-light py-2" required>
                                    <option value="" disabled selected>Selecciona unidad...</option>
                                    <option value="Global">Global</option>
                                    <option value="Hora">Hora</option>
                                    <option value="Metro">Metro</option>
                                    <option value="Kilo">Kilo</option>
                                    <option value="Unidad">Unidad</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">CATEGORÍA</label>
                                <select name="id_tipo" class="form-select border-0 bg-light py-2" required>
                                    <option value="" disabled selected>Selecciona categoría...</option>
                                    <?php 
                                    $res_tipos = mysqli_query($conexion, "SELECT * FROM tipo ORDER BY nombre_tipo ASC");
                                    while($t = mysqli_fetch_assoc($res_tipos)): ?>
                                        <option value="<?php echo $t['id_tipo']; ?>"><?php echo $t['nombre_tipo']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">ESTATUS</label>
                                <select name="id_status" class="form-select border-0 bg-light py-2" required>
                                    <option value="" disabled selected>Selecciona estatus...</option>
                                    <?php 
                                    $res_st = mysqli_query($conexion, "SELECT * FROM status_servicios");
                                    while($st = mysqli_fetch_assoc($res_st)): ?>
                                        <option value="<?php echo $st['id_status']; ?>"><?php echo $st['nombre_status']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">
                                    Registrar Servicio
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Usamos exactamente la misma lógica que te funcionó en el dashboard
    const sidebar = document.getElementById('sidebar');
    const btnOpen = document.getElementById('sidebarCollapse');
    const btnClose = document.getElementById('closeSidebar'); // Asegúrate que tu sidebar.php tenga este ID en la X

    // Abrir menú
    if(btnOpen) {
        btnOpen.addEventListener('click', function() {
            sidebar.classList.add('active');
        });
    }

    // Cerrar menú (al darle a la X)
    if(btnClose) {
        btnClose.addEventListener('click', function() {
            sidebar.classList.remove('active');
        });
    }

    // Cerrar al hacer clic fuera
    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickInsideButton = btnOpen.contains(event.target);

        if (!isClickInsideSidebar && !isClickInsideButton && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
</script>

</body>
</html>