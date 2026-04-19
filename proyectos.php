<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Fallo la conexión: " . mysqli_connect_error());
}

// Consulta con JOIN para traer el nombre del servicio asociado
$query = "SELECT p.*, s.tipo_servicio 
          FROM proyecto p 
          LEFT JOIN servicio s ON p.id_servicio = s.id_servicio 
          ORDER BY p.id_proyecto DESC";
$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px; border-radius: 10px; margin-bottom: 25px; }
        .table-card { border: none; border-radius: 15px; background: white; padding: 20px; }
        
        /* Estilo para la miniatura de la imagen */
        .img-thumb {
            width: 60px; height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .badge-price { background-color: #e9ecef; color: #0d6efd; font-weight: 600; font-size: 0.9rem; }
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
                <h5 class="mb-0 fw-bold"><i class="bi bi-kanban text-primary me-2"></i>Gestión de Proyectos</h5>
            </div>
            <a href="registrar_proyecto.php" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Proyecto
            </a>
        </div>
        
        <?php if(isset($_GET['msj'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <?php 
            if($_GET['msj'] == 1) echo '<i class="bi bi-check-circle me-2"></i>Proyecto registrado con éxito.';
            if($_GET['msj'] == 3) echo '<i class="bi bi-trash me-2"></i>Proyecto eliminado correctamente.';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

        <div class="card table-card shadow-sm">
            <div class="table-responsive">
                
                
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Imagen</th>
                            <th>Proyecto / Servicio</th>
                            <th>Fechas</th>
                            <th>Presupuesto</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td>
                                <img src="uploads/proyectos/<?php echo $row['imagen_principal']; ?>" 
                                     onerror="this.src='https://placehold.co/60x60?text=S/I'" 
                                     class="img-thumb shadow-sm">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['descripcion']); ?></div>
                                <small class="text-muted"><i class="bi bi-gear-fill me-1"></i><?php echo $row['tipo_servicio']; ?></small>
                            </td>
                            <td>
                                <div class="small"><strong>Inicio:</strong> <?php echo $row['fecha_inicio']; ?></div>
                                <div class="small text-danger"><strong>Fin:</strong> <?php echo $row['fecha_fin']; ?></div>
                            </td>
                            <td>
                                <span class="badge badge-price px-3 py-2 rounded-pill">
                                    $<?php echo number_format($row['presupuesto_estimado'], 2); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group gap-2">
                                    <a href="ver_galeria.php?id=<?php echo $row['id_proyecto']; ?>" class="btn btn-sm btn-outline-info rounded-circle" title="Galería">
                                        <i class="bi bi-images"></i>
                                    </a>
                                    <a href="editar_proyecto.php?id=<?php echo $row['id_proyecto']; ?>" class="btn btn-sm btn-outline-warning rounded-circle" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="eliminar_proyecto.php?id=<?php echo $row['id_proyecto']; ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('¿Eliminar proyecto?')" title="Eliminar">
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

    // Cerrar al hacer clic fuera
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