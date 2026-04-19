<?php
// 1. Forzar codificaci車n UTF-8 en la salida del servidor
header('Content-Type: text/html; charset=utf-8');

$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

// 2. IMPORTANTE: Establecer el juego de caracteres en la conexi車n para evitar rombos
mysqli_set_charset($conexion, "utf8mb4");

$id_solicitud = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_solicitud === 0) {
    header("Location: solicitudes.php");
    exit();
}

// Obtener datos con el nombre del servicio
$query = "SELECT s.*, ser.tipo_servicio 
          FROM solicitud s 
          LEFT JOIN servicio ser ON s.id_servicio = ser.id_servicio 
          WHERE s.id_solicitud = $id_solicitud";
$res = mysqli_query($conexion, $query);
$s_actual = mysqli_fetch_assoc($res);

if (!$s_actual) { header("Location: solicitudes.php"); exit(); }

// Procesar actualizaci車n de Estatus
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_status = intval($_POST['id_status']);
    $sql = "UPDATE solicitud SET id_status = $id_status WHERE id_solicitud = $id_solicitud";
    if (mysqli_query($conexion, $sql)) {
        header("Location: solicitudes.php?msj=2"); 
        exit();
    }
}

// Funci車n para limpiar datos de la BD y asegurar UTF-8
function limpiar($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles Solicitud #<?php echo $id_solicitud; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .main-content { padding: 30px; }
        .detail-card { background: white; border-radius: 20px; padding: 40px; border: none; }
        
        /* Estilo de Ficha Informativa */
        .info-label { color: #888; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
        .info-value { color: #333; font-size: 1.05rem; margin-bottom: 20px; border-left: 3px solid #eee; padding-left: 12px; }
        .desc-box { background: #f8f9fa; border-radius: 12px; padding: 20px; border: 1px solid #eee; min-height: 100px; color: #444; line-height: 1.6; }
        
        .section-header { border-bottom: 1px solid #f0f0f0; margin-bottom: 25px; padding-bottom: 10px; color: #2c3e50; font-weight: 700; }
        .status-box { background: #eef2ff; border-radius: 15px; padding: 25px; border: 1px dashed #6366f1; }
        .btn-save { border-radius: 50px; padding: 12px 35px; font-weight: 600; background: #6366f1; border: none; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content w-100">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Detalles de Solicitud <span class="text-muted">#<?php echo $id_solicitud; ?></span></h4>
            <a href="solicitudes.php" class="btn btn-light rounded-pill border shadow-sm px-4">Volver</a>
        </div>

        <div class="card detail-card shadow-sm">
            <form method="POST">
                
                <div class="row">
                    <div class="col-md-5">
                        <h6 class="section-header"><i class="bi bi-person-circle me-2 text-primary"></i>Datos del Cliente</h6>
                        
                        <div class="info-label">Nombre de Contacto</div>
                        <div class="info-value"><?php echo limpiar($s_actual['nombre_contacto']); ?></div>

                        <div class="info-label">Tel&eacute;fono</div>
                        <div class="info-value"><?php echo limpiar($s_actual['telefono_contacto']); ?></div>

                        <div class="info-label">Correo Electr&oacute;nico</div>
                        <div class="info-value"><?php echo limpiar($s_actual['correo_contacto']); ?></div>
                    </div>

                    <div class="col-md-7">
                        <h6 class="section-header"><i class="bi bi-info-square me-2 text-primary"></i>Detalles del Servicio</h6>
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="info-label">Servicio Solicitado</div>
                                <div class="info-value"><?php echo limpiar($s_actual['tipo_servicio']); ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-label">Prioridad</div>
                                <div class="info-value">
                                    <span class="badge <?php echo ($s_actual['prioridad'] == 'Urgente') ? 'bg-danger' : 'bg-secondary'; ?> rounded-pill">
                                        <?php echo limpiar($s_actual['prioridad']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="info-label">Descripci&oacute;n de la Falla / Requerimiento</div>
                        <div class="desc-box mb-4">
                            <?php echo nl2br(limpiar($s_actual['descripcion_falla'])); ?>
                        </div>
                    </div>
                </div>

                <div class="status-box mt-4 shadow-sm">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h6 class="fw-bold mb-1"><i class="bi bi-arrow-repeat me-2"></i>Actualizar Estado de la Solicitud</h6>
                            <p class="text-muted small mb-0">Seleccione el nuevo estado para notificar al cliente sobre el progreso.</p>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex gap-2">
                                <select name="id_status" class="form-select form-select-lg border-primary" style="font-weight: 500;">
                                    <?php 
                                    $status_list = mysqli_query($conexion, "SELECT id_status_solicitud, nombre_status FROM status_solicitud");
                                    while($st = mysqli_fetch_assoc($status_list)) {
                                        $sel = ($st['id_status_solicitud'] == $s_actual['id_status']) ? 'selected' : '';
                                        echo "<option value='{$st['id_status_solicitud']}' $sel>" . limpiar($st['nombre_status']) . "</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-save shadow">
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>