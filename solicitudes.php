<?php
// 1. FORZAR ENCABEZADO PHP
header('Content-Type: text/html; charset=utf-8');

$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';
mysqli_set_charset($conexion, "utf8mb4");

$conexion = mysqli_connect($host, $user, $pass, $db);

// 2. FORZAR CHARSET EN LA CONEXIÓN MYSQL
mysqli_set_charset($conexion, "utf8mb4");

$query = "SELECT s.*, ser.tipo_servicio, st.nombre_status, u.nombre_usuario, u.correo as correo_perfil
          FROM solicitud s
          LEFT JOIN servicio ser ON s.id_servicio = ser.id_servicio
          LEFT JOIN status_solicitud st ON s.id_status = st.id_status_solicitud
          LEFT JOIN Usuario u ON s.id_usuario = u.id_usuario
          ORDER BY s.id_solicitud DESC";

$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Solicitudes | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; }
        .table-card { background: white; border-radius: 15px; padding: 20px; border: none; }
        .badge-Baja { background-color: #d1e7dd; color: #0f5132; }
        .badge-Media { background-color: #fff3cd; color: #664d03; }
        .badge-Alta { background-color: #f8d7da; color: #842029; }
        .badge-Urgente { background-color: #212529; color: #ffffff; }
        .status-pill { border-radius: 30px; padding: 5px 15px; font-weight: 600; font-size: 0.85rem; }
        .user-info { font-size: 0.85rem; }
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
                <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard-check text-primary me-2"></i>Gesti&oacute;n de Solicitudes</h5>
            </div>
            <a href="nueva_solicitud.php" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i>Nueva Solicitud
            </a>
        </div>

        <div class="table-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Solicitante</th>
                            <th>Servicio</th>
                            <th>Descripci&oacute;n</th>
                            <th>Prioridad</th>
                            <th>Estatus</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                            $nombreMostrar = !empty($row['nombre_contacto']) ? $row['nombre_contacto'] : ($row['nombre_usuario'] ?? 'Anónimo');
                            $correoMostrar = !empty($row['correo_contacto']) ? $row['correo_contacto'] : ($row['correo_perfil'] ?? 'Sin correo');
                        ?>
                        <tr>
                            <td class="fw-bold">#<?php echo $row['id_solicitud']; ?></td>
                            <td>
                                <div class="user-info">
                                    <span class="d-block fw-bold text-dark"><?php echo htmlspecialchars($nombreMostrar, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <small class="text-muted"><i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($correoMostrar, ENT_QUOTES, 'UTF-8'); ?></small>
                                    <?php if(!empty($row['telefono_contacto'])): ?>
                                        <br><small class="text-muted"><i class="bi bi-whatsapp me-1"></i><?php echo htmlspecialchars($row['telefono_contacto'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="d-block fw-semibold text-primary"><?php echo htmlspecialchars($row['tipo_servicio'] ?? 'No asignado', ENT_QUOTES, 'UTF-8'); ?></span>
                            </td>
                            <td>
                                <small class="text-muted d-inline-block text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($row['descripcion_falla'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($row['descripcion_falla'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $row['prioridad']; ?> rounded-pill px-3">
                                    <?php echo $row['prioridad']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-pill bg-light text-dark shadow-sm border">
                                    <i class="bi bi-circle-fill me-2 small text-primary"></i>
                                    <?php echo htmlspecialchars($row['nombre_status'] ?? 'Pendiente', ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="editar_solicitud.php?id=<?php echo $row['id_solicitud']; ?>" class="btn btn-outline-warning btn-sm rounded-circle shadow-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="eliminar_solicitud.php?id=<?php echo $row['id_solicitud']; ?>" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm" onclick="return confirm('¿Borrar solicitud?')">
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
</body>
</html>