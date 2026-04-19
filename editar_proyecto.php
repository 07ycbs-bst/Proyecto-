<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

$id_proyecto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_proyecto === 0) {
    header("Location: proyectos.php");
    exit();
}

// 1. Obtener datos actuales
$res = mysqli_query($conexion, "SELECT * FROM proyecto WHERE id_proyecto = $id_proyecto");
$p = mysqli_fetch_assoc($res);

if (!$p) { header("Location: proyectos.php"); exit(); }

// 2. Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $f_inicio = $_POST['fecha_inicio'];
    $f_fin = $_POST['fecha_fin'];
    $presupuesto = floatval($_POST['presupuesto_estimado']);
    $id_servicio = intval($_POST['id_servicio']);
    $imagen_actual = $_POST['imagen_actual'];

    // Lógica para cambiar la imagen principal
    $nombre_imagen = $imagen_actual;
    if (isset($_FILES['img_principal']) && $_FILES['img_principal']['error'] == 0) {
        $ext = pathinfo($_FILES['img_principal']['name'], PATHINFO_EXTENSION);
        $nombre_imagen = "p_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['img_principal']['tmp_name'], "uploads/proyectos/" . $nombre_imagen)) {
            // Borrar la imagen anterior si no es la default
            if ($imagen_actual != 'default.jpg' && file_exists("uploads/proyectos/" . $imagen_actual)) {
                unlink("uploads/proyectos/" . $imagen_actual);
            }
        }
    }

    $sql = "UPDATE proyecto SET 
            descripcion = '$descripcion', 
            fecha_inicio = '$f_inicio', 
            fecha_fin = '$f_fin', 
            presupuesto_estimado = '$presupuesto', 
            id_servicio = $id_servicio,
            imagen_principal = '$nombre_imagen'
            WHERE id_proyecto = $id_proyecto";

    if (mysqli_query($conexion, $sql)) {
        
        // --- LOGICA AGREGADA PARA GALERIA ---
        if (isset($_FILES['galeria']) && !empty($_FILES['galeria']['name'][0])) {
            $files = $_FILES['galeria'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] == 0) {
                    $ext_g = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $nombre_g = "gal_" . $id_proyecto . "_" . time() . "_" . $i . "." . $ext_g;
                    
                    if (move_uploaded_file($files['tmp_name'][$i], "uploads/proyectos/" . $nombre_g)) {
                        mysqli_query($conexion, "INSERT INTO proyecto_imagenes (id_proyecto, ruta_imagen) VALUES ($id_proyecto, '$nombre_g')");
                    }
                }
            }
        }
        // -------------------------------------

        header("Location: proyectos.php?msj=2"); 
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; }
        .form-card { background: white; border-radius: 15px; padding: 30px; border: none; }
        .form-label { font-weight: 600; color: #555; }
        .form-control, .form-select { border-radius: 10px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; }
        
        .current-img-preview {
            width: 120px; height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn-update { border-radius: 30px; padding: 12px 40px; font-weight: 600; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-nav shadow-sm d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-warning me-2"></i>Editar Proyecto</h5>
            </div>
            <a href="proyectos.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Volver</a>
        </div>

        <div class="card form-card shadow-sm">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="imagen_actual" value="<?php echo $p['imagen_principal']; ?>">

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Descripción del Proyecto</label>
                        <textarea name="descripcion" class="form-control" rows="3" required><?php echo htmlspecialchars($p['descripcion']); ?></textarea>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Servicio</label>
                        <select name="id_servicio" class="form-select" required>
                            <?php 
                            $serv = mysqli_query($conexion, "SELECT id_servicio, tipo_servicio FROM servicio");
                            while($s = mysqli_fetch_assoc($serv)):
                                $selected = ($s['id_servicio'] == $p['id_servicio']) ? 'selected' : '';
                                echo "<option value='{$s['id_servicio']}' $selected>{$s['tipo_servicio']}</option>";
                            endwhile;
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $p['fecha_inicio']; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?php echo $p['fecha_fin']; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Presupuesto ($)</label>
                        <input type="number" step="0.01" name="presupuesto_estimado" class="form-control" value="<?php echo $p['presupuesto_estimado']; ?>">
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="row align-items-center bg-light p-4 rounded-4 h-100">
                            <div class="col-auto">
                                <label class="d-block mb-2 fw-bold text-muted small">Imagen Actual</label>
                                <img src="uploads/proyectos/<?php echo $p['imagen_principal']; ?>" 
                                     onerror="this.src='https://placehold.co/120x120?text=S/I'"
                                     class="current-img-preview">
                            </div>
                            <div class="col">
                                <label class="form-label">Cambiar Imagen Principal</label>
                                <input type="file" name="img_principal" class="form-control" accept="image/*">
                                <small class="text-muted">Dejar vacío para no cambiar.</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="bg-light p-4 rounded-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Añadir más fotos a Galería</label>
                                <a href="ver_galeria.php?id=<?php echo $id_proyecto; ?>" class="btn btn-sm btn-link text-decoration-none p-0">
                                    <i class="bi bi-images"></i> Gestionar actual
                                </a>
                            </div>
                            <input type="file" name="galeria[]" class="form-control" accept="image/*" multiple>
                            <small class="text-muted">Puedes seleccionar varias fotos nuevas.</small>
                        </div>
                    </div>
                </div>

                <div class="text-end border-top pt-4 mt-4">
                    <button type="submit" class="btn btn-warning btn-update shadow-sm text-white">
                        <i class="bi bi-save2 me-2"></i>Guardar Cambios
                    </button>
                </div>
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
        btnOpen.addEventListener('click', (e) => { e.stopPropagation(); sidebar.classList.add('active'); });
    }
    if(btnClose) {
        btnClose.addEventListener('click', () => { sidebar.classList.remove('active'); });
    }
    document.addEventListener('click', (event) => {
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(event.target) && !btnOpen.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
</script>
</body>
</html>