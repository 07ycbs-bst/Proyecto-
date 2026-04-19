<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $f_inicio = $_POST['fecha_inicio'];
    $f_fin = $_POST['fecha_fin'];
    $presupuesto = floatval($_POST['presupuesto_estimado']);
    $id_servicio = intval($_POST['id_servicio']);
    
    // Carpeta de destino
    $directorio = "uploads/proyectos/";
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // 1. Procesar Imagen Principal
    $nombre_imagen_principal = "default.jpg";
    if (isset($_FILES['img_principal']) && $_FILES['img_principal']['error'] == 0) {
        $ext = pathinfo($_FILES['img_principal']['name'], PATHINFO_EXTENSION);
        $nombre_imagen_principal = "p_" . time() . "." . $ext;
        move_uploaded_file($_FILES['img_principal']['tmp_name'], $directorio . $nombre_imagen_principal);
    }

    // 2. Insertar en tabla Proyecto
    $sql = "INSERT INTO proyecto (descripcion, imagen_principal, fecha_inicio, fecha_fin, presupuesto_estimado, id_servicio) 
            VALUES ('$descripcion', '$nombre_imagen_principal', '$f_inicio', '$f_fin', '$presupuesto', $id_servicio)";
    
    if (mysqli_query($conexion, $sql)) {
        $ultimo_id = mysqli_insert_id($conexion);

        // 3. Procesar Galería (Múltiples imágenes)
        if (isset($_FILES['galeria']) && !empty($_FILES['galeria']['name'][0])) {
            $files = $_FILES['galeria'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] == 0) {
                    $ext_g = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $nombre_g = "gal_" . $ultimo_id . "_" . $i . "_" . time() . "." . $ext_g;
                    
                    if (move_uploaded_file($files['tmp_name'][$i], $directorio . $nombre_g)) {
                        mysqli_query($conexion, "INSERT INTO proyecto_imagenes (id_proyecto, ruta_imagen) VALUES ($ultimo_id, '$nombre_g')");
                    }
                }
            }
        }
        header("Location: proyectos.php?msj=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Proyecto | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        
        /* Nav superior responsive */
        .top-nav { 
            background: white; padding: 15px 25px; border-radius: 12px; 
            margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; 
        }

        .form-card { background: white; border-radius: 15px; padding: 30px; border: none; }
        .form-label { font-weight: 600; color: #555; }
        .form-control, .form-select { border-radius: 10px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; }
        .btn-save { border-radius: 30px; padding: 12px 30px; font-weight: 600; width: 100%; }
        
        @media (min-width: 768px) {
            .btn-save { width: auto; }
        }
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
                    <i class="bi bi-plus-circle text-primary me-2"></i>Nuevo Proyecto
                </h5>
            </div>
            <a href="proyectos.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="card form-card shadow-sm">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Descripción del Proyecto</label>
                        <textarea name="descripcion" class="form-control" rows="3" required placeholder="Describe el trabajo realizado..."></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Servicio Relacionado</label>
                        <select name="id_servicio" class="form-select" required>
                            <option value="">Seleccione un servicio...</option>
                            <?php 
                            $serv = mysqli_query($conexion, "SELECT id_servicio, tipo_servicio FROM servicio");
                            while($s = mysqli_fetch_assoc($serv)) echo "<option value='{$s['id_servicio']}'>{$s['tipo_servicio']}</option>";
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha Fin (Estimada)</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Presupuesto ($)</label>
                        <input type="number" step="0.01" name="presupuesto_estimado" class="form-control" placeholder="0.00">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Imagen Destacada (Portada)</label>
                        <input type="file" name="img_principal" class="form-control" accept="image/*" required>
                        <small class="text-muted">Imagen principal del listado.</small>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Galería (Varias fotos)</label>
                        <input type="file" name="galeria[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Puedes elegir varias fotos.</small>
                    </div>
                </div>

                <div class="text-end border-top pt-4">
                    <button type="submit" class="btn btn-primary btn-save shadow-sm">
                        <i class="bi bi-cloud-arrow-up me-2"></i>Guardar Proyecto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Lógica Unificada del Sidebar
    const sidebar = document.getElementById('sidebar');
    const btnOpen = document.getElementById('sidebarCollapse');
    const btnClose = document.getElementById('closeSidebar');

    if(btnOpen) {
        btnOpen.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.add('active');
        });
    }

    if(btnClose) {
        btnClose.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });
    }

    // Cerrar al hacer clic fuera del área del menú
    document.addEventListener('click', (event) => {
        if (sidebar && sidebar.classList.contains('active')) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickInsideButton = btnOpen.contains(event.target);
            if (!isClickInsideSidebar && !isClickInsideButton) {
                sidebar.classList.remove('active');
            }
        }
    });
</script>

</body>
</html>