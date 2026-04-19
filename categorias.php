<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';
$charset = 'utf8mb4';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Fallo la conexión: " . mysqli_connect_error());
}

// 1. LÓGICA PARA ELIMINAR (Validación de servicios asociados)
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    $sql_check = "SELECT id_servicio FROM servicio WHERE id_tipo = $id_eliminar LIMIT 1";
    $res_check = mysqli_query($conexion, $sql_check);

    if (mysqli_num_rows($res_check) > 0) {
        echo "<script>alert('No se puede eliminar: Esta categoría tiene servicios asociados.'); window.location.href='categorias.php';</script>";
        exit();
    } else {
        mysqli_query($conexion, "DELETE FROM tipo WHERE id_tipo = $id_eliminar");
        header("Location: categorias.php?msj=1"); 
        exit();
    }
}

// 2. LÓGICA PARA INSERTAR CATEGORÍA CON ICONO
if (isset($_POST['agregar_categoria'])) {
    $nombre_tipo = mysqli_real_escape_string($conexion, $_POST['nombre_tipo']);
    $icono_tipo = mysqli_real_escape_string($conexion, $_POST['icono_tipo']); 
    
    if (!empty($nombre_tipo)) {
        $sql_insert = "INSERT INTO tipo (nombre_tipo, icono_tipo) VALUES ('$nombre_tipo', '$icono_tipo')";
        if(mysqli_query($conexion, $sql_insert)){
            header("Location: categorias.php?msj=1");
            exit();
        }
    }
}

// 3. CONSULTA DE CATEGORÍAS
$query = "SELECT * FROM tipo ORDER BY id_tipo DESC";
$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .form-card, .table-card { border: none; border-radius: 15px; background: white; padding: 20px; margin-bottom: 20px; }
        
        .icon-preview-box { 
            width: 45px; height: 45px; 
            display: flex; align-items: center; justify-content: center; 
            background: #f8f9fa; border-radius: 10px; 
            font-size: 1.5rem; color: #0d6efd; border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-nav d-flex justify-content-between align-items-center shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-tags-fill text-primary me-2"></i>Gestión de Categorías
                </h5>
            </div>
        </div>

        <?php if(isset($_GET['msj'])): ?>
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i> Acción realizada con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card form-card shadow-sm">
            <form method="POST" action="categorias.php" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold small text-muted">NOMBRE DE CATEGORÍA</label>
                    <input type="text" name="nombre_tipo" class="form-control bg-light border-0" placeholder="Ej: Electricidad" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted d-flex justify-content-between">
                        ICONO 
                        <a href="https://icons.getbootstrap.com/" target="_blank" class="text-decoration-none small text-primary">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Buscar Iconos
                        </a>
                    </label>
                    <div class="input-group">
                        <div class="icon-preview-box me-2" id="liveIconPreview">
                            <i class="bi bi-tag"></i>
                        </div>
                        <input type="text" name="icono_tipo" id="iconInput" class="form-control bg-light border-0" placeholder="Ej: bi-gear" value="bi-tag">
                    </div>
                </div>

                <div class="col-md-3">
                    <button type="submit" name="agregar_categoria" class="btn btn-primary w-100 rounded-pill py-2">
                        <i class="bi bi-plus-circle me-1"></i> Guardar Categoría
                    </button>
                </div>
            </form>
        </div>

        <div class="card table-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Icono</th>
                            <th>Nombre de la Categoría</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($c = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td class="ps-4 text-muted">#<?php echo $c['id_tipo']; ?></td>
                            <td>
                                <div class="icon-preview-box" style="width: 35px; height: 35px; font-size: 1.2rem;">
                                    <i class="bi <?php echo !empty($c['icono_tipo']) ? $c['icono_tipo'] : 'bi-tag'; ?>"></i>
                                </div>
                            </td>
                            <td><span class="fw-bold text-dark"><?php echo htmlspecialchars($c['nombre_tipo']); ?></span></td>
                            <td class="text-center">
                                <div class="btn-group gap-2">
                                    <a href="editar_categoria.php?id=<?php echo $c['id_tipo']; ?>" class="btn btn-sm btn-outline-warning rounded-circle" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="categorias.php?eliminar=<?php echo $c['id_tipo']; ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('¿Seguro que deseas eliminar esta categoría?');" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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
        if (sidebar && sidebar.classList.contains('active')) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickInsideButton = btnOpen.contains(event.target);
            if (!isClickInsideSidebar && !isClickInsideButton) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Previsualización de icono en vivo
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