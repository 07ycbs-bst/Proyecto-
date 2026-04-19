<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';
$id_usuario = $_SESSION['id_usuario'];

try {
    // 1. Datos del Administrador
    $sql = "SELECT u.nombre_usuario, r.nombre_rol 
            FROM Usuario u
            INNER JOIN R_R_U rru ON u.id_usuario = rru.id_usuario
            INNER JOIN Roles r ON rru.id_rol = r.id_rol
            WHERE u.id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $admin = $stmt->fetch();

    // 2. Conteos para los indicadores (Dash Cards)
    // Conteo de Usuarios
    $total_usuarios = $pdo->query("SELECT COUNT(*) FROM Usuario")->fetchColumn();
    
    // Conteo de Servicios
    $total_servicios = $pdo->query("SELECT COUNT(*) FROM servicio")->fetchColumn();
    
    // Conteo de Proyectos
    $total_proyectos = $pdo->query("SELECT COUNT(*) FROM proyecto")->fetchColumn();
    
    // Conteo de Solicitudes (puedes filtrar por pendientes si prefieres)
    $total_solicitudes = $pdo->query("SELECT COUNT(*) FROM solicitud")->fetchColumn();
    
    // Conteo de Comentarios
    $total_comentarios = $pdo->query("SELECT COUNT(*) FROM comentarios")->fetchColumn();

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .card-box { border: none; border-radius: 12px; transition: transform 0.2s; background: white; }
        .card-box:hover { transform: translateY(-3px); }
        .num-count { font-size: 2rem; font-weight: 800; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-nav d-flex justify-content-between align-items-center">
            <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-none d-sm-block">
                <span class="text-muted small">Bienvenido al Sistema de Gestión</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary d-none d-md-inline-block"><?php echo strtoupper($admin['nombre_rol']); ?></span>
                <span class="fw-bold small"><?php echo htmlspecialchars($admin['nombre_usuario']); ?></span>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card card-box shadow-sm p-4 text-center h-100">
                    <i class="bi bi-people fs-1 text-primary mb-2"></i>
                    <h6 class="text-muted small text-uppercase">Usuarios</h6>
                    <h3 class="num-count"><?php echo $total_usuarios; ?></h3>
                    <a href="usuarios.php" class="btn btn-sm btn-primary w-100 mt-2 rounded-pill">Gestionar</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card card-box shadow-sm p-4 text-center h-100">
                    <i class="bi bi-gear fs-1 text-secondary mb-2"></i>
                    <h6 class="text-muted small text-uppercase">Servicios</h6>
                    <h3 class="num-count"><?php echo $total_servicios; ?></h3>
                    <a href="servicios.php" class="btn btn-sm btn-secondary w-100 mt-2 rounded-pill">Configurar</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card card-box shadow-sm p-4 text-center h-100">
                    <i class="bi bi-folder fs-1 text-success mb-2"></i>
                    <h6 class="text-muted small text-uppercase">Proyectos</h6>
                    <h3 class="num-count"><?php echo $total_proyectos; ?></h3>
                    <a href="proyectos.php" class="btn btn-sm btn-success w-100 mt-2 rounded-pill">Ver todo</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-6">
                <div class="card card-box shadow-sm p-4 text-center h-100 border-start border-warning border-4">
                    <i class="bi bi-file-earmark-text fs-1 text-warning mb-2"></i>
                    <h6 class="text-muted small text-uppercase">Solicitudes Totales</h6>
                    <h3 class="num-count"><?php echo $total_solicitudes; ?></h3>
                    <a href="solicitudes.php" class="btn btn-sm btn-warning w-100 mt-2 rounded-pill text-white">Revisar</a>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card card-box shadow-sm p-4 text-center h-100 border-start border-info border-4">
                    <i class="bi bi-chat-left-text fs-1 text-info mb-2"></i>
                    <h6 class="text-muted small text-uppercase">Comentarios por Moderar</h6>
                    <h3 class="num-count"><?php echo $total_comentarios; ?></h3>
                    <a href="comentarios.php" class="btn btn-sm btn-info text-white w-100 mt-2 rounded-pill">Moderar</a>
                </div>
            </div>
        </div>
    </div> 
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const btnOpen = document.getElementById('sidebarCollapse');
    const btnClose = document.getElementById('closeSidebar');

    if(btnOpen) {
        btnOpen.addEventListener('click', () => sidebar.classList.add('active'));
    }
    if(btnClose) {
        btnClose.addEventListener('click', () => sidebar.classList.remove('active'));
    }

    document.addEventListener('click', function(event) {
        if (sidebar.classList.contains('active')) {
            if (!sidebar.contains(event.target) && !btnOpen.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
</script>

</body>
</html>