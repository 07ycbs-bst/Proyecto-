<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Fallo la conexión: " . mysqli_connect_error());
}

// Obtener ID del servicio a editar
$id_servicio = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_servicio === 0) {
    header("Location: servicios.php");
    exit();
}

// Lógica de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empresa_id = intval($_POST['id_empresa']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['tipo_servicio']);
    $precio = floatval($_POST['precio_referencial']);
    $unidad = mysqli_real_escape_string($conexion, $_POST['unidad_medida']);
    $categoria = intval($_POST['id_tipo']);
    $status = intval($_POST['id_status']);

    // Actualizar tabla servicio
    $sql_update = "UPDATE servicio SET 
                   tipo_servicio = '$nombre', 
                   precio_referencial = '$precio', 
                   unidad_medida = '$unidad', 
                   id_status = '$status', 
                   id_tipo = '$categoria' 
                   WHERE id_servicio = $id_servicio";
    
    if(mysqli_query($conexion, $sql_update)){
        // Actualizar relación con empresa en R_E_S
        mysqli_query($conexion, "DELETE FROM R_E_S WHERE id_servicio = $id_servicio");
        mysqli_query($conexion, "INSERT INTO R_E_S (id_empresa, id_servicio) VALUES ($empresa_id, $id_servicio)");
        
        header("Location: servicios.php?msj=2");
        exit();
    }
}

// Consultar datos actuales del servicio
$query = "SELECT s.*, r.id_empresa 
          FROM servicio s 
          LEFT JOIN R_E_S r ON s.id_servicio = r.id_servicio 
          WHERE s.id_servicio = $id_servicio";
$res = mysqli_query($conexion, $query);
$datos = mysqli_fetch_assoc($res);

if (!$datos) {
    die("Servicio no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        
        .top-nav { 
            background: white; padding: 15px 25px; border-radius: 12px; 
            margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; 
        }
        
        .btn-volver { 
            border-radius: 20px; border: 1px solid #ccc; color: #666; 
            padding: 5px 20px; text-decoration: none; font-size: 14px; transition: 0.3s;
        }
        .btn-volver:hover { background: #f8f9fa; color: #333; }

        .form-card { border: none; border-radius: 15px; background: white; padding: 30px; max-width: 800px; margin: 0 auto; }
        .form-label { font-weight: 600; color: #444; margin-bottom: 8px; }
        .form-control, .form-select { background-color: #f8f9fa; border: 1px solid #eee; border-radius: 10px; padding: 12px; margin-bottom: 20px; }
        .form-control:focus, .form-select:focus { background-color: #fff; box-shadow: 0 0 0 0.25 margin-rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
        
        .btn-update { 
            background-color: #0d6efd; color: white; border: none; border-radius: 25px; 
            padding: 12px; font-weight: 600; width: 100%; margin-top: 10px; transition: 0.3s; 
        }
        .btn-update:hover { background-color: #0b5ed7; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-nav shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Editar Servicio
                </h5>
            </div>
            <a href="servicios.php" class="btn-volver shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="form-card shadow-sm">
            <form method="POST">
                <div class="row">
                    <div class="col-12">
                        <label class="form-label">Empresa Responsable</label>
                        <select name="id_empresa" class="form-select" required>
                            <?php 
                            $em = mysqli_query($conexion, "SELECT * FROM Empresa");
                            while($e = mysqli_fetch_assoc($em)) {
                                $selected = ($e['id_empresa'] == $datos['id_empresa']) ? 'selected' : '';
                                echo "<option value='{$e['id_empresa']}' $selected>{$e['nombre_empresa']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Nombre del Servicio / Descripción</label>
                        <input type="text" name="tipo_servicio" class="form-control" value="<?php echo htmlspecialchars($datos['tipo_servicio']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Precio ($)</label>
                        <input type="number" step="0.01" name="precio_referencial" class="form-control" value="<?php echo $datos['precio_referencial']; ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Unidad de Medida</label>
                        <select name="unidad_medida" class="form-select">
                            <?php 
                            $unidades = ['Global', 'Metro', 'Kilo', 'Hora'];
                            foreach($unidades as $u) {
                                $sel = ($u == $datos['unidad_medida']) ? 'selected' : '';
                                echo "<option value='$u' $sel>$u</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Categoría</label>
                        <select name="id_tipo" class="form-select">
                            <?php 
                            $cat = mysqli_query($conexion, "SELECT * FROM tipo");
                            while($c = mysqli_fetch_assoc($cat)) {
                                $sel = ($c['id_tipo'] == $datos['id_tipo']) ? 'selected' : '';
                                echo "<option value='{$c['id_tipo']}' $sel>{$c['nombre_tipo']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Estatus</label>
                        <select name="id_status" class="form-select">
                            <?php 
                            $st = mysqli_query($conexion, "SELECT * FROM status_servicios");
                            while($s = mysqli_fetch_assoc($st)) {
                                $sel = ($s['id_status'] == $datos['id_status']) ? 'selected' : '';
                                echo "<option value='{$s['id_status']}' $sel>{$s['nombre_status']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-update shadow-sm mt-3">
                    <i class="bi bi-arrow-repeat me-2"></i> Actualizar Servicio
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const btnOpen = document.getElementById('sidebarCollapse');
    const btnClose = document.getElementById('closeSidebar');

    if(btnOpen) {
        btnOpen.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.add('active');
        });
    }

    if(btnClose) {
        btnClose.addEventListener('click', function() {
            sidebar.classList.remove('active');
        });
    }

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