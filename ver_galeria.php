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

// 1. Obtener información del proyecto para el título
$res_p = mysqli_query($conexion, "SELECT descripcion FROM proyecto WHERE id_proyecto = $id_proyecto");
$proyecto = mysqli_fetch_assoc($res_p);

if (!$proyecto) { header("Location: proyectos.php"); exit(); }

// 2. Lógica para eliminar una foto de la galería
if (isset($_GET['eliminar_foto'])) {
    $id_foto = intval($_GET['eliminar_foto']);
    
    // Buscar nombre del archivo para borrarlo físicamente
    $res_f = mysqli_query($conexion, "SELECT ruta_imagen FROM proyecto_imagenes WHERE id_img = $id_foto");
    $foto = mysqli_fetch_assoc($res_f);
    
    if ($foto) {
        $ruta = "uploads/proyectos/" . $foto['ruta_imagen'];
        if (file_exists($ruta)) {
            unlink($ruta);
        }
        mysqli_query($conexion, "DELETE FROM proyecto_imagenes WHERE id_img = $id_foto");
    }
    header("Location: ver_galeria.php?id=$id_proyecto&msj=3");
    exit();
}

// 3. Obtener todas las imágenes de la galería
$imagenes = mysqli_query($conexion, "SELECT * FROM proyecto_imagenes WHERE id_proyecto = $id_proyecto");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Proyecto | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; }
        
        .gallery-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.2s;
            background: white;
        }
        .gallery-card:hover { transform: translateY(-5px); }
        
        .img-container {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-delete-photo {
            position: absolute;
            top: 10px;
            right: 10px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            transition: 0.3s;
        }
        .btn-delete-photo:hover { background: #dc3545; transform: scale(1.1); }
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
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-images text-primary me-2"></i>Galería
                </h5>
            </div>
            <div class="d-flex gap-2">
                <a href="editar_proyecto.php?id=<?php echo $id_proyecto; ?>" class="btn btn-warning btn-sm rounded-pill px-3 text-white">
                    <i class="bi bi-plus-lg"></i> Añadir
                </a>
                <a href="proyectos.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Volver</a>
            </div>
        </div>

        <div class="mb-4">
            <p class="text-muted mb-1">Proyecto:</p>
            <h4 class="fw-bold"><?php echo htmlspecialchars($proyecto['descripcion']); ?></h4>
        </div>

        <?php if(isset($_GET['msj']) && $_GET['msj'] == 3): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <i class="bi bi-trash me-2"></i> Imagen eliminada de la galería.
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if(mysqli_num_rows($imagenes) > 0): ?>
                <?php while($img = mysqli_fetch_assoc($imagenes)): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card gallery-card shadow-sm">
                            <div class="img-container">
                                <img src="uploads/proyectos/<?php echo $img['ruta_imagen']; ?>" alt="Foto Galería">
                                <a href="ver_galeria.php?id=<?php echo $id_proyecto; ?>&eliminar_foto=<?php echo $img['id_img']; ?>" 
                                   class="btn-delete-photo shadow" 
                                   onclick="return confirm('¿Eliminar esta imagen de la galería?')">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="bg-white p-5 rounded-4 shadow-sm">
                        <i class="bi bi-camera-fill display-1 text-light"></i>
                        <p class="text-muted mt-3">No hay fotos adicionales en este proyecto.</p>
                        <a href="editar_proyecto.php?id=<?php echo $id_proyecto; ?>" class="btn btn-primary rounded-pill">
                            Subir fotos ahora
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Lógica del Sidebar
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