<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

// CONSULTA CORREGIDA según tus capturas de pantalla:
// Tabla: Usuario (Singular)
// Columna nombre: nombre_usuario
$query = "SELECT c.*, p.descripcion as nombre_proyecto, u.nombre_usuario as nombre_real_persona
          FROM comentarios c
          LEFT JOIN proyecto p ON c.id_proyecto = p.id_proyecto
          LEFT JOIN Usuario u ON c.id_usuario = u.id_usuario 
          ORDER BY c.fecha_registro DESC";

$resultado = mysqli_query($conexion, $query);

// Lógica para eliminar
if (isset($_GET['eliminar'])) {
    $id_com = intval($_GET['eliminar']);
    mysqli_query($conexion, "DELETE FROM comentarios WHERE id_comentario = $id_com");
    header("Location: comentarios.php?msj=3");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderación de Comentarios | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; }
        .comment-card { background: white; border-radius: 15px; border: none; transition: 0.3s; }
        .comment-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .project-tag { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #6c757d; }
        .user-type-tag { font-size: 0.7rem; padding: 2px 8px; border-radius: 10px; font-weight: 600; }
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
                <h5 class="mb-0 fw-bold"><i class="bi bi-chat-left-dots text-secondary me-2"></i>Moderación de Comentarios</h5>
            </div>
            <span class="badge bg-dark rounded-pill px-3"><?php echo mysqli_num_rows($resultado); ?> totales</span>
        </div>

        <?php if(isset($_GET['msj']) && $_GET['msj'] == 3): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-4">Comentario eliminado correctamente.</div>
        <?php endif; ?>

        <div class="row">
            <?php while($c = mysqli_fetch_assoc($resultado)): ?>
                <div class="col-12 mb-3">
                    <div class="card comment-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-100">
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    
                                    <?php if(!empty($c['id_usuario'])): ?>
                                        <span class="user-type-tag bg-primary-subtle text-primary border border-primary-subtle">
                                            <i class="bi bi-person-check-fill me-1"></i>Registrado
                                        </span>
                                        <span class="fw-bold text-dark">
                                            <?php echo htmlspecialchars($c['nombre_real_persona'] ?? 'Usuario ID: '.$c['id_usuario']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="user-type-tag bg-light text-secondary border">
                                            <i class="bi bi-person-fill me-1"></i>Visitante
                                        </span>
                                        <span class="fw-bold text-muted">Anónimo</span>
                                    <?php endif; ?>

                                    <span class="text-muted small">•</span>
                                    <span class="project-tag">
                                        <i class="bi bi-folder2-open me-1"></i><?php echo $c['nombre_proyecto'] ?? 'General'; ?>
                                    </span>
                                    <span class="text-muted small">•</span>
                                    <span class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($c['fecha_registro'])); ?></span>
                                </div>

                                <p class="mb-0 text-dark" style="font-size: 1.05rem; padding-left: 10px; border-left: 3px solid #000;">
                                    "<?php echo htmlspecialchars($c['texto_comentario']); ?>"
                                </p>
                            </div>
                            <div class="ms-3">
                                <a href="comentarios.php?eliminar=<?php echo $c['id_comentario']; ?>" 
                                   class="btn btn-outline-danger btn-sm rounded-circle shadow-sm" 
                                   onclick="return confirm('¿Seguro que quieres eliminar este comentario?')"
                                   title="Eliminar comentario">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if(mysqli_num_rows($resultado) == 0): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-chat-dots display-1 text-light"></i>
                    <p class="text-muted mt-3">No hay comentarios todavía.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>