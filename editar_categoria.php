<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Fallo la conexión: " . mysqli_connect_error());
}

// Obtener ID de la categoría
$id_tipo = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_tipo === 0) {
    header("Location: categorias.php");
    exit();
}

// Lógica de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_tipo']);
    $icono = mysqli_real_escape_string($conexion, $_POST['icono_tipo']);

    $sql_update = "UPDATE tipo SET 
                   nombre_tipo = '$nombre', 
                   icono_tipo = '$icono' 
                   WHERE id_tipo = $id_tipo";
    
    if(mysqli_query($conexion, $sql_update)){
        header("Location: categorias.php?msj=2");
        exit();
    }
}

// Consultar datos actuales
$query = "SELECT * FROM tipo WHERE id_tipo = $id_tipo";
$res = mysqli_query($conexion, $query);
$datos = mysqli_fetch_assoc($res);

if (!$datos) {
    die("Categoría no encontrada.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoría | Fluye T&B</title>
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

        .form-card { border: none; border-radius: 15px; background: white; padding: 40px; max-width: 600px; margin: 0 auto; }
        .form-label { font-weight: 600; color: #444; margin-bottom: 8px; }
        .form-control { background-color: #f8f9fa; border: 1px solid #eee; border-radius: 10px; padding: 12px; margin-bottom: 20px; }
        
        .icon-preview-large {
            width: 80px; height: 80px;
            background: #f8f9fa; border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #0d6efd; border: 2px dashed #dee2e6;
            margin: 0 auto 25px auto;
        }

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
                    <i class="bi bi-pencil-square text-primary me-2"></i>Modificar Categoría
                </h5>
            </div>
            <a href="categorias.php" class="btn-volver shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="form-card shadow-sm text-center">
            <div class="icon-preview-large" id="liveIconPreview">
                <i class="bi <?php echo !empty($datos['icono_tipo']) ? $datos['icono_tipo'] : 'bi-tag'; ?>"></i>
            </div>

            <form method="POST" class="text-start">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Categoría</label>
                    <input type="text" name="nombre_tipo" class="form-control" 
                           value="<?php echo htmlspecialchars($datos['nombre_tipo']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between">
                        Icono (Bootstrap Icons)
                        <a href="https://icons.getbootstrap.com/" target="_blank" class="text-primary small text-decoration-none">
                            <i class="bi bi-search me-1"></i>Ver catálogo
                        </a>
                    </label>
                    <input type="text" name="icono_tipo" id="iconInput" class="form-control" 
                           value="<?php echo htmlspecialchars($datos['icono_tipo']); ?>" placeholder="Ej: bi-gear">
                </div>

                <button type="submit" class="btn-update shadow-sm">
                    <i class="bi bi-arrow-repeat me-2"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar logic
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

    document.addEventListener('click', (event) => {
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(event.target) && !btnOpen.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Icon Preview logic
    const iconInput = document.getElementById('iconInput');
    const livePreview = document.getElementById('liveIconPreview');

    if(iconInput) {
        iconInput.addEventListener('input', function() {
            const iconName = this.value.trim() !== '' ? this.value.trim() : 'bi-tag';
            livePreview.innerHTML = `<i class="bi ${iconName}"></i>`;
        });
    }
</script>

</body>
</html>