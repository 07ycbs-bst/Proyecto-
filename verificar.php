<?php
require_once 'conexion.php';

$titulo = "Verificando cuenta...";
$contenido = "";
$tipo_alerta = "info"; // success, danger, info

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Buscar al usuario con ese token que aún no esté activo
        $stmt = $pdo->prepare("SELECT id_usuario, nombre_usuario FROM Usuario WHERE token_verificacion = :token AND cuenta_activa = 0");
        $stmt->execute([':token' => $token]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Activar la cuenta y limpiar el token
            $update = $pdo->prepare("UPDATE Usuario SET cuenta_activa = 1, token_verificacion = NULL WHERE id_usuario = :id");
            $update->execute([':id' => $usuario['id_usuario']]);

            $tipo_alerta = "success";
            $titulo = "¡Cuenta Activada!";
            $contenido = "Hola <strong>" . htmlspecialchars($usuario['nombre_usuario']) . "</strong>, tu cuenta ha sido verificada con éxito. Ya puedes disfrutar de todos nuestros servicios.";
        } else {
            $tipo_alerta = "danger";
            $titulo = "Enlace no válido";
            $contenido = "El token es inválido o la cuenta ya ha sido activada anteriormente.";
        }
    } catch (PDOException $e) {
        $tipo_alerta = "danger";
        $titulo = "Error del sistema";
        $contenido = "Hubo un problema al procesar tu solicitud. Por favor, intenta más tarde.";
    }
} else {
    $tipo_alerta = "warning";
    $titulo = "Acceso denegado";
    $contenido = "No se ha proporcionado un token de seguridad para validar la cuenta.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-activation {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            max-width: 450px;
            width: 100%;
        }
        .icon-box {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .btn-custom {
            background-color: #34aadc;
            color: white;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #2b8cb5;
            color: white;
            transform: translateY(-2px);
        }
        /* Animación suave para el icono */
        .zoom-in {
            animation: zoomIn 0.5s ease-out;
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="card card-activation p-4 p-md-5 text-center bg-white mx-auto">
        
        <?php if ($tipo_alerta == "success"): ?>
            <div class="icon-box text-success zoom-in">
                <i class="bi bi-patch-check-fill"></i>
            </div>
        <?php elseif ($tipo_alerta == "danger" || $tipo_alerta == "warning"): ?>
            <div class="icon-box text-danger">
                <i class="bi bi-exclamation-octagon"></i>
            </div>
        <?php endif; ?>

        <h2 class="fw-bold text-dark mb-3"><?php echo $titulo; ?></h2>
        <p class="text-secondary mb-4">
            <?php echo $contenido; ?>
        </p>

        <div class="d-grid gap-2">
            <?php if ($tipo_alerta == "success"): ?>
                <a href="login.php" class="btn btn-custom text-uppercase">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                </a>
            <?php else: ?>
                <a href="registro.php" class="btn btn-outline-secondary text-uppercase">
                    Volver al Registro
                </a>
            <?php endif; ?>
        </div>

        <div class="mt-4 pt-3 border-top">
            <small class="text-muted">© <?php echo date('Y'); ?> Fluye T&B - Maracaibo, Zulia</small>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>