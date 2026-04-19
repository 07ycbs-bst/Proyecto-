<?php
session_start();
// --- SECCIÓN: CONEXIÓN (Sincronizada con tu referencia) ---
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Fallo la conexión: " . mysqli_connect_error());
}

// --- SECCIÓN: VALIDACIÓN DEL ID ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_tipo = mysqli_real_escape_string($conexion, $_GET['id']);
    
    // 1. Obtener información de la Categoría
    $query_cat = "SELECT nombre_tipo, icono_tipo FROM tipo WHERE id_tipo = '$id_tipo' LIMIT 1";
    $res_cat = mysqli_query($conexion, $query_cat);
    $datos_cat = mysqli_fetch_assoc($res_cat);

    if (!$datos_cat) {
        die("Categoría no encontrada.");
    }
} else {
    header("Location: index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $datos_cat['nombre_tipo']; ?> - FLUYE T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --blue-brand: #34aadc; }
        .text-brand { color: var(--blue-brand); }
        .header-categoria { background-color: #f8f9fa; border-bottom: 3px solid var(--blue-brand); }
        .icon-large { font-size: 4rem; }
        .card-servicio {
            transition: all 0.3s ease;
            border-left: 5px solid var(--blue-brand);
            border-radius: 12px;
            margin-bottom: 15px;
        }
        .card-servicio:hover { 
            transform: translateX(10px); 
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .badge-activo { background-color: #d1e7dd; color: #0f5132; }
    </style>
</head>
<body class="bg-light">

    <?php include 'menu.php'; ?>

    <header class="header-categoria py-5 mb-5 text-center shadow-sm">
        <div class="container">
            <i class="bi <?php echo $datos_cat['icono_tipo']; ?> text-brand icon-large"></i>
            <h1 class="display-4 fw-bold text-uppercase mt-3"><?php echo $datos_cat['nombre_tipo']; ?></h1>
            <p class="lead text-muted">Soluciones profesionales de mantenimiento</p>
        </div>
    </header>

    <main class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h3 class="fw-bold mb-4 text-dark"><i class="bi bi-list-stars me-2"></i>Servicios Disponibles</h3>
                
                <div class="list-group border-0">
                    <?php
                    // Consulta usando la tabla status_servicios (como en tu referencia)
                    $query_serv = "SELECT s.id_servicio, s.tipo_servicio, st.nombre_status 
                                   FROM servicio s
                                   LEFT JOIN status_servicios st ON s.id_status = st.id_status
                                   WHERE s.id_tipo = '$id_tipo' 
                                   AND s.id_status = 1 
                                   ORDER BY s.tipo_servicio ASC";

                    $res_serv = mysqli_query($conexion, $query_serv);

                    if ($res_serv && mysqli_num_rows($res_serv) > 0) {
                        while ($servicio = mysqli_fetch_assoc($res_serv)) {
                            ?>
                            <a href="servicio_detalle.php?id=<?php echo $servicio['id_servicio']; ?>" 
                               class="list-group-item list-group-item-action p-4 card-servicio border-0 shadow-sm">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-uppercase text-dark"><?php echo htmlspecialchars($servicio['tipo_servicio']); ?></h5>
                                        <span class="badge rounded-pill badge-activo">
                                            <i class="bi bi-check-circle-fill me-1"></i><?php echo htmlspecialchars($servicio['nombre_status']); ?>
                                        </span>
                                    </div>
                                    <div class="text-brand">
                                        <span class="me-2 d-none d-sm-inline">Ver detalles</span>
                                        <i class="bi bi-arrow-right-circle-fill fs-3"></i>
                                    </div>
                                </div>
                            </a>
                            <?php
                        }
                    } else {
                        echo "
                        <div class='text-center py-5 bg-white shadow-sm rounded-4'>
                            <i class='bi bi-search mb-3 d-block text-muted' style='font-size: 3rem;'></i>
                            <p class='text-muted fs-5'>No se encontraron servicios activos para esta categoría en este momento.</p>
                            <a href='index.php#servicios' class='btn btn-primary rounded-pill px-4'>Volver al inicio</a>
                        </div>";
                    }
                    ?>
                </div>

                <div class="mt-5 border-top pt-4 text-center">
                    <a href="index.php#servicios" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-house-door me-2"></i>Volver a Categorías
                    </a>
                </div>
            </div>
        </div>
    </main>
    <footer class="py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0 small opacity-50">&copy; <?php echo date('Y'); ?> FLUYE T&B, C.A. - Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>