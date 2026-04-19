<?php
session_start();
// --- CONEXIÓN A LA BASE DE DATOS ---
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);
if (!$conexion) { die("Error crítico: " . mysqli_connect_error()); }

// --- VALIDACIÓN DEL ID DEL SERVICIO ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_servicio = mysqli_real_escape_string($conexion, $_GET['id']);
    
    $query_serv = "SELECT s.*, t.nombre_tipo, t.icono_tipo 
                   FROM servicio s
                   LEFT JOIN tipo t ON s.id_tipo = t.id_tipo
                   WHERE s.id_servicio = '$id_servicio' LIMIT 1";
    
    $res_serv = mysqli_query($conexion, $query_serv);
    $datos_serv = mysqli_fetch_assoc($res_serv);

    if (!$datos_serv) { die("Servicio no encontrado."); }
} else {
    header("Location: index.php"); exit();
}

// --- PROCESAR LA SOLICITUD DE SERVICIO ---
$mensaje_exito = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_solicitud'])) {
    $descripcion_falla = mysqli_real_escape_string($conexion, $_POST['descripcion_falla']);
    $prioridad = mysqli_real_escape_string($conexion, $_POST['prioridad']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $id_status = 1; // "Recibida"

    // Lógica para determinar si el usuario está registrado o es invitado
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];
        $nombre = $_SESSION['nombre_usuario']; 
        $correo = $_SESSION['correo'];
    } else {
        $id_usuario = "NULL";
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    }

    $sql_ins = "INSERT INTO solicitud (id_servicio, id_status, descripcion_falla, prioridad, nombre_contacto, telefono_contacto, correo_contacto, id_usuario) 
                VALUES ('$id_servicio', '$id_status', '$descripcion_falla', '$prioridad', '$nombre', '$telefono', '$correo', $id_usuario)";
    
    if (mysqli_query($conexion, $sql_ins)) {
        $mensaje_exito = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $datos_serv['tipo_servicio']; ?> | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --blue-brand: #34aadc; --dark-blue: #003366; }
        .hero-servicio {
            background: linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.85)), url('assets/img/bg-servicios.jpg');
            background-size: cover; color: white; padding: 80px 0; border-bottom: 5px solid var(--blue-brand);
        }
        .info-card {
            background: white; border-radius: 20px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-top: -60px; position: relative; z-index: 10;
        }
        .project-img { height: 250px; object-fit: cover; width: 100%; }
        .shadow-hover:hover { transform: translateY(-5px); transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; }
        .form-label { font-weight: 600; font-size: 0.85rem; color: var(--dark-blue); }
    </style>
</head>
<body class="bg-light">

    <?php if(file_exists('menu.php')) include 'menu.php'; ?>

    <header class="hero-servicio text-center">
        <div class="container">
            <span class="badge bg-info rounded-pill mb-3 text-uppercase p-2 px-3">
                <i class="bi <?php echo $datos_serv['icono_tipo']; ?> me-2"></i>
                <?php echo $datos_serv['nombre_tipo']; ?>
            </span>
            <h1 class="display-4 fw-bold text-uppercase"><?php echo $datos_serv['tipo_servicio']; ?></h1>
        </div>
    </header>

    <main class="container mb-5">


        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="info-card">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <h4 class="fw-bold text-dark mb-3">Descripción del Servicio</h4>
                            <p class="text-muted fs-5" style="white-space: pre-line;">
                                <?php echo !empty($datos_serv['descripcion_servicio']) ? $datos_serv['descripcion_servicio'] : "Especialistas en " . $datos_serv['tipo_servicio'] . "."; ?>
                            </p>
                        </div>
                        <div class="col-md-4 border-start">
                            <div class="ps-md-4">
                                <h5 class="fw-bold mb-4">Ficha Técnica</h5>
                                <div class="mb-3">
                                    <small class="text-muted d-block text-uppercase small fw-bold">Unidad:</small>
                                    <span class="text-dark fw-medium"><?php echo $datos_serv['unidad_medida']; ?></span>
                                </div>
                                <div class="mb-4">
                                    <small class="text-muted d-block text-uppercase small fw-bold">Precio Ref:</small>
                                    <span class="text-primary fs-3 fw-bold">$<?php echo number_format($datos_serv['precio_referencial'], 2); ?></span>
                                </div>
                                <button type="button" class="btn btn-success w-100 rounded-pill py-2 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalServicio">
                                    <i class="bi bi-tools me-2"></i>Solicitar Servicio
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                        <?php if($mensaje_exito): ?>
            <div class="alert alert-success alert-dismissible fade show mt-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> 
                <strong>¡Solicitud enviada!</strong> Hemos recibido su interés en <b><?php echo $datos_serv['tipo_servicio']; ?></b>. Pronto nos comunicaremos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

                <div class="mt-5 pt-4">
                    <h3 class="fw-bold text-dark mb-4 border-bottom pb-2">Proyectos Realizados</h3>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        <?php
                        $query_proy = "SELECT * FROM proyecto WHERE id_servicio = '$id_servicio' ORDER BY id_proyecto DESC";
                        $res_proy = mysqli_query($conexion, $query_proy);
                        if (mysqli_num_rows($res_proy) > 0) {
                            while ($proy = mysqli_fetch_assoc($res_proy)) {
                                $ruta_img = "uploads/proyectos/" . $proy['imagen_principal']; ?>
                                <div class="col">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden shadow-hover">
                                        <img src="<?php echo $ruta_img; ?>" class="card-img-top project-img" onerror="this.src='https://via.placeholder.com/600x400'">
                                        <div class="card-body">
                                            <p class="card-text text-dark fw-medium">
                                                <?php echo (strlen($proy['descripcion']) > 100) ? substr($proy['descripcion'], 0, 100) . "..." : $proy['descripcion']; ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-white border-0 pb-4 px-3">
                                            <a href="proyecto_detalle.php?id=<?php echo $proy['id_proyecto']; ?>" class="btn btn-outline-primary btn-sm rounded-pill w-100">Ver Detalles</a>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else {
                            echo '<div class="col-12"><div class="alert alert-light text-center py-5 border">No hay proyectos registrados.</div></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalServicio" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Nueva Solicitud</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="POST">
              <div class="modal-body p-4">
                
                <div class="p-3 bg-primary bg-opacity-10 rounded-3 mb-4 border border-primary border-opacity-25">
                    <small class="text-primary fw-bold d-block text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">Servicio solicitado:</small>
                    <span class="fw-bold text-dark fs-5"><?php echo $datos_serv['tipo_servicio']; ?></span>
                </div>

                <?php if(isset($_SESSION['id_usuario'])): ?>
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <small class="text-muted d-block">Solicitante:</small>
                        <span class="fw-bold text-primary"><?php echo $_SESSION['nombre_usuario']; ?></span>
                        <small class="d-block text-muted"><?php echo $_SESSION['correo']; ?></small>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">Tu Nombre:</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre y Apellido" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico:</label>
                        <input type="email" name="correo" class="form-control" placeholder="ejemplo@correo.com" required>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Teléfono de contacto (WhatsApp):</label>
                    <input type="tel" name="telefono" class="form-control" placeholder="Ej: +58 414 1234567" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción del requerimiento:</label>
                    <textarea name="descripcion_falla" class="form-control" rows="3" placeholder="Detalla brevemente lo que necesitas..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Prioridad:</label>
                    <select name="prioridad" class="form-select">
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Urgente">Urgente</option>
                    </select>
                </div>
              </div>
              <div class="modal-footer border-0 bg-light">
                <button type="submit" name="enviar_solicitud" class="btn btn-primary w-100 rounded-pill fw-bold py-2">ENVIAR SOLICITUD DE SERVICIO</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <footer class="py-4 bg-dark text-white text-center">
        <div class="container"><p class="mb-0 small opacity-50">&copy; <?php echo date('Y'); ?> FLUYE T&B, C.A.</p></div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>