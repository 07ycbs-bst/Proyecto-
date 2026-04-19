<?php
session_start();
// --- CONEXIÓN A LA BASE DE DATOS ---
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);
if (!$conexion) { die("Error de conexión: " . mysqli_connect_error()); }

// --- 1. PROCESAR ENVÍO DE SOLICITUD DE INFORMACIÓN ---
$mensaje_exito = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_solicitud'])) {
    $id_serv_input = mysqli_real_escape_string($conexion, $_POST['id_servicio']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion_falla']);
    $prioridad = mysqli_real_escape_string($conexion, $_POST['prioridad']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $id_status = 1; // 'Recibida'

    // Lógica de usuario (Registrado vs Invitado)
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];
        $nombre = $_SESSION['nombre_usuario']; 
        $correo = $_SESSION['correo'];
    } else {
        $id_usuario = "NULL";
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    }
    
    $sql_sol = "INSERT INTO solicitud (id_servicio, id_status, descripcion_falla, prioridad, nombre_contacto, telefono_contacto, correo_contacto, id_usuario) 
                VALUES ('$id_serv_input', '$id_status', '$descripcion', '$prioridad', '$nombre', '$telefono', '$correo', $id_usuario)";
    
    if (mysqli_query($conexion, $sql_sol)) {
        $mensaje_exito = "¡Solicitud enviada con éxito! Nos contactaremos pronto.";
    }
}

// --- VALIDACIÓN Y CONSULTA ---
$datos_proy = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_proyecto = mysqli_real_escape_string($conexion, $_GET['id']);
    
    // --- PROCESAR ENVÍO DE COMENTARIO ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_comentario'])) {
        $texto = mysqli_real_escape_string($conexion, $_POST['texto_comentario']);
        $id_usuario_com = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 'NULL';

        if (!empty($texto)) {
            $sql_ins = "INSERT INTO comentarios (id_proyecto, id_usuario, texto_comentario) 
                        VALUES ('$id_proyecto', $id_usuario_com, '$texto')";
            mysqli_query($conexion, $sql_ins);
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id_proyecto . "#comentarios-section");
            exit();
        }
    }

    // Consulta extendida
    $query_proy = "SELECT p.*, s.tipo_servicio, s.id_tipo, s.id_servicio 
                   FROM proyecto p
                   LEFT JOIN servicio s ON p.id_servicio = s.id_servicio
                   WHERE p.id_proyecto = '$id_proyecto' LIMIT 1";
    
    $res_proy = mysqli_query($conexion, $query_proy);
    $datos_proy = mysqli_fetch_assoc($res_proy);

    if (!$datos_proy) { die("El proyecto solicitado no existe."); }
    
    $id_retorno_categoria = $datos_proy['id_tipo'];
} else {
    header("Location: index.php"); exit();
}

$ruta_base = "uploads/proyectos/";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($datos_proy['tipo_servicio']); ?> | Fluye T&B</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    
    <style>
        :root { --dark-blue: #003366; --brand-blue: #34aadc; }
        body { background-color: #f8f9fa; }
        .hero-section { background: var(--dark-blue); color: white; padding: 60px 0; border-bottom: 5px solid var(--brand-blue); }
        .text-small-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.8; margin-bottom: 8px; display: block; }
        .text-main-title { font-size: 2.8rem; font-weight: 800; text-transform: uppercase; line-height: 1.2; }
        .img-principal-wrapper { position: relative; display: block; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .img-principal { width: 100%; object-fit: cover; max-height: 500px; transition: transform 0.4s; }
        .img-principal-wrapper:hover .img-principal { transform: scale(1.03); }
        .zoom-icon { position: absolute; bottom: 20px; right: 20px; background: rgba(255,255,255,0.7); color: #333; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; opacity: 0; transition: 0.3s; }
        .img-principal-wrapper:hover .zoom-icon { opacity: 1; }
        .thumb-galeria-wrapper { display: block; border-radius: 10px; overflow: hidden; border: 2px solid transparent; transition: 0.3s; }
        .thumb-galeria { height: 110px; object-fit: cover; transition: 0.3s; }
        .thumb-galeria-wrapper:hover { border-color: var(--brand-blue); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(52, 170, 220, 0.2); }
        .card-info { border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); background: #fff; }
        .comment-bubble { background: #fff; border-radius: 15px; padding: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .avatar-user { background: var(--brand-blue) !important; }
        .avatar-guest { background: #6c757d !important; }
    </style>
</head>
<body>

    <?php if(file_exists('menu.php')) include 'menu.php'; ?>

    <?php if(isset($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-lg" style="z-index: 1060;">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $mensaje_exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <header class="hero-section text-center mb-5">
        <div class="container">
            <span class="text-small-label fw-bold">Detalle del Proyecto</span>
            <h1 class="text-main-title"><?php echo htmlspecialchars($datos_proy['tipo_servicio']); ?></h1>
        </div>
    </header>

    <div class="container mb-5">
        <div class="row g-5">
            <div class="col-lg-7">
                <a href="<?php echo $ruta_base . $datos_proy['imagen_principal']; ?>" class="glightbox img-principal-wrapper mb-4" data-gallery="gallery1">
                    <img src="<?php echo $ruta_base . $datos_proy['imagen_principal']; ?>" class="img-principal" onerror="this.src='https://via.placeholder.com/800x500'">
                    <div class="zoom-icon"><i class="bi bi-zoom-in"></i></div>
                </a>

                <h4 class="fw-bold mb-3"><i class="bi bi-images me-2 text-primary"></i>Galería del Proyecto</h4>
                <div class="row g-2 mb-5">
                    <?php
                    $sql_gal = "SELECT ruta_imagen FROM proyecto_imagenes WHERE id_proyecto = '$id_proyecto'";
                    $res_gal = mysqli_query($conexion, $sql_gal);
                    if($res_gal && mysqli_num_rows($res_gal) > 0) {
                        while($foto = mysqli_fetch_assoc($res_gal)) {
                            $path = $ruta_base . trim($foto['ruta_imagen']); ?>
                            <div class="col-4 col-md-3">
                                <a href="<?php echo $path; ?>" class="glightbox thumb-galeria-wrapper shadow-sm" data-gallery="gallery1">
                                    <img src="<?php echo $path; ?>" class="thumb-galeria w-100" onerror="this.style.display='none'">
                                </a>
                            </div>
                        <?php }
                    } else { echo '<p class="text-muted small ms-2">No hay fotos adicionales.</p>'; }
                    ?>
                </div>

                <div id="comentarios-section" class="pt-4 border-top">
                    <h4 class="fw-bold mb-4"><i class="bi bi-chat-left-text me-2 text-primary"></i>Comentarios</h4>
                    <form action="" method="POST" class="mb-4">
                        <div class="card shadow-sm border-0 p-3">
                            <textarea name="texto_comentario" class="form-control border-0 bg-light" rows="3" placeholder="Escribe tu opinión..." required></textarea>
                            <div class="text-end mt-2">
                                <button type="submit" name="enviar_comentario" class="btn btn-primary px-4 rounded-pill fw-bold">Publicar</button>
                            </div>
                        </div>
                    </form>
                    <div class="lista-comentarios">
                        <?php
                        $sql_com = "SELECT c.*, u.nombre_usuario FROM comentarios c LEFT JOIN Usuario u ON c.id_usuario = u.id_usuario WHERE c.id_proyecto = '$id_proyecto' ORDER BY c.fecha_registro DESC";
                        $res_com = mysqli_query($conexion, $sql_com);
                        if ($res_com && mysqli_num_rows($res_com) > 0) {
                            while ($com = mysqli_fetch_assoc($res_com)) { 
                                $es_reg = !empty($com['id_usuario']); ?>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center <?php echo $es_reg ? 'avatar-user' : 'avatar-guest'; ?>" style="width: 40px; height: 40px;">
                                            <i class="bi <?php echo $es_reg ? 'bi-person-check-fill' : 'bi-person'; ?>"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="comment-bubble">
                                            <div class="d-flex justify-content-between">
                                                <small class="fw-bold"><?php echo htmlspecialchars($es_reg ? $com['nombre_usuario'] : 'Visitante'); ?></small>
                                                <small class="text-muted"><?php echo date("d/m/Y H:i", strtotime($com['fecha_registro'])); ?></small>
                                            </div>
                                            <p class="mb-0 text-secondary mt-1"><?php echo nl2br(htmlspecialchars($com['texto_comentario'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else { echo '<p class="text-center text-muted py-4">Aún no hay comentarios.</p>'; }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-info p-4">
                    <h4 class="fw-bold text-dark mb-4 border-bottom pb-3">Descripción</h4>
                    <p class="text-muted fs-5" style="text-align: justify;"><?php echo nl2br(htmlspecialchars($datos_proy['descripcion'])); ?></p>
                    <div class="mt-4 pt-3 border-top">
                        <div class="mb-3">
                            <small class="text-muted d-block fw-bold text-uppercase small">Presupuesto Ejecutado</small>
                            <h2 class="fw-bold text-primary">$<?php echo number_format($datos_proy['presupuesto_estimado'], 2); ?></h2>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block fw-bold text-uppercase small">Inicio</small>
                                <p class="fw-bold"><i class="bi bi-calendar-event me-1"></i> <?php echo date("d/m/Y", strtotime($datos_proy['fecha_inicio'])); ?></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block fw-bold text-uppercase small">Fin</small>
                                <p class="fw-bold text-success"><i class="bi bi-calendar-check me-1"></i> <?php echo date("d/m/Y", strtotime($datos_proy['fecha_fin'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success w-100 rounded-pill py-3 fw-bold mt-4 shadow" data-bs-toggle="modal" data-bs-target="#modalSolicitud">
                        <i class="bi bi-send-fill me-2"></i>Solicitar Información
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSolicitud" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Nueva Solicitud</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_servicio" value="<?php echo $datos_proy['id_servicio']; ?>">
                        
                        <div class="p-3 bg-success bg-opacity-10 rounded-3 mb-4 border border-success border-opacity-25">
                            <small class="text-success fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Servicio relacionado:</small>
                            <span class="fw-bold text-dark fs-5"><?php echo $datos_proy['tipo_servicio']; ?></span>
                        </div>

                        <?php if(isset($_SESSION['id_usuario'])): ?>
                            <div class="p-3 bg-light rounded-3 mb-3 border">
                                <small class="text-muted d-block">Solicitante:</small>
                                <span class="fw-bold text-primary"><?php echo $_SESSION['nombre_usuario']; ?></span>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Tu Nombre:</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Nombre completo" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Correo Electrónico:</label>
                                <input type="email" name="correo" class="form-control" placeholder="ejemplo@correo.com" required>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Teléfono (WhatsApp):</label>
                            <input type="tel" name="telefono" class="form-control" placeholder="Ej: +58 414 1234567" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Descripción del requerimiento:</label>
                            <textarea name="descripcion_falla" class="form-control" rows="3" placeholder="¿En qué podemos ayudarte?" required></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small">Prioridad:</label>
                            <select name="prioridad" class="form-select">
                                <option value="Baja">Baja</option>
                                <option value="Media" selected>Media</option>
                                <option value="Alta">Alta</option>
                                <option value="Urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="submit" name="enviar_solicitud" class="btn btn-success w-100 rounded-pill fw-bold py-2">ENVIAR SOLICITUD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="py-4 bg-dark text-white text-center">
        <div class="container"><p class="mb-0 small opacity-50">&copy; <?php echo date('Y'); ?> FLUYE T&B, C.A.</p></div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lightbox = GLightbox({ selector: '.glightbox', loop: true });
        });
    </script>
</body>
</html>