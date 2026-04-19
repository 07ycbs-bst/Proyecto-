<?php
// 1. REPORTE DE ERRORES (Para evitar pantallas en blanco y ver el problema real)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();
require_once 'conexion.php';

// 2. IMPORTACIÓN DE PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Verificación de archivos de la librería para evitar error fatal
$pathEx = 'PHPMailer/src/Exception.php';
$pathPH = 'PHPMailer/src/PHPMailer.php';
$pathSM = 'PHPMailer/src/SMTP.php';

$libreriaOk = (file_exists($pathEx) && file_exists($pathPH) && file_exists($pathSM));

if ($libreriaOk) {
    require $pathEx;
    require $pathPH;
    require $pathSM;
}

$mensaje = "";
$ID_EMPRESA_FIJA = 1;

// 1. DETERMINAR QUÉ ROLES MOSTRAR
$esAdmin = (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1);

try {
    if ($esAdmin) {
        $consultaRoles = $pdo->query("SELECT id_rol, nombre_rol FROM Roles");
    } else {
        $consultaRoles = $pdo->query("SELECT id_rol, nombre_rol FROM Roles WHERE id_rol = 3");
    }
    $roles = $consultaRoles->fetchAll();
} catch (PDOException $e) {
    die("Error al consultar roles: " . $e->getMessage());
}

// 2. PROCESAR EL REGISTRO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_usuario'];
    $cedula = $_POST['cedula'];
    $correo = $_POST['correo'];
    $id_rol = $_POST['id_rol']; 
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Generamos token para activación
    $token = bin2hex(random_bytes(16));

    try {
        // --- VALIDACIÓN: Verificar si el correo ya existe ---
        $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM Usuario WHERE correo = :correo");
        $checkEmail->execute([':correo' => $correo]);
        
        if ($checkEmail->fetchColumn() > 0) {
            $mensaje = "<div class='alert alert-warning shadow-sm border-0 small'><i class='bi bi-exclamation-circle-fill me-2'></i>Este correo ya está registrado. Intenta con otro o inicia sesión.</div>";
        } else {
            // Iniciar Transacción
            $pdo->beginTransaction();

            $sqlUser = "INSERT INTO Usuario (nombre_usuario, cedula, correo, password, id_empresa, token_verificacion, cuenta_activa) 
                        VALUES (:nombre, :cedula, :correo, :pass, :empresa, :token, 0)";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute([
                ':nombre'  => $nombre, ':cedula' => $cedula, ':correo' => $correo,
                ':pass'    => $password, ':empresa' => $ID_EMPRESA_FIJA, ':token' => $token
            ]);

            $nuevo_id_usuario = $pdo->lastInsertId();

            $sqlRel = "INSERT INTO R_R_U (id_rol, id_usuario) VALUES (:rol, :user)";
            $stmtRel = $pdo->prepare($sqlRel);
            $stmtRel->execute([':rol' => $id_rol, ':user' => $nuevo_id_usuario]);

            // ENVIAR CORREO SI LA LIBRERÍA ESTÁ PRESENTE
            if ($libreriaOk) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; 
                    $mail->SMTPAuth   = true;
                    $mail->Username   = '07ycbs@gmail.com'; // EDITAR ESTO
                    $mail->Password   = 'xoia lhbj jraa lpgv';     // EDITAR ESTO
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->setFrom('07ycbs@gmail.com', 'Sistema Fluye T&B');
                    $mail->addAddress($correo, $nombre);
                    $mail->isHTML(true);
                    $mail->Subject = 'Activa tu cuenta - Fluye T&B';
                    
                    $enlace = "https://fluyetyb.soymaracaibo.com/verificar.php?token=$token";
                    $mail->Body = "Hola $nombre, haz clic aquí para activar tu cuenta: <a href='$enlace'>Activar mi cuenta</a>";
                    
                    $mail->send();
                    $mensaje = "<div class='alert alert-success shadow-sm border-0 small'><i class='bi bi-check-circle-fill me-2'></i>¡Registro completado! Revisa tu correo para activar tu cuenta.</div>";
                } catch (Exception $e) {
                    $mensaje = "<div class='alert alert-warning shadow-sm border-0 small'>Usuario registrado, pero falló el envío del correo de activación.</div>";
                }
            } else {
                $mensaje = "<div class='alert alert-success shadow-sm border-0 small'><i class='bi bi-check-circle-fill me-2'></i>¡Usuario registrado con éxito! (Nota: Correo no configurado).</div>";
            }

            $pdo->commit();
        }

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $mensaje = "<div class='alert alert-danger shadow-sm border-0 small'><i class='bi bi-exclamation-triangle-fill me-2'></i>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; min-height: 100vh; display: flex; align-items: center; padding: 20px 0; }
        .card-register { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .btn-brand { background-color: #34aadc; color: white; font-weight: 600; border: none; padding: 12px; transition: 0.3s; }
        .btn-brand:hover { background-color: #2b8cb5; color: white; transform: translateY(-2px); }
        .form-label { font-size: 0.85rem; font-weight: 700; color: #555; }
        .input-group-text { background-color: #f8f9fa; border-right: none; cursor: pointer; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 12px; }
        .badge-admin { background-color: #003366; color: white; padding: 5px 12px; border-radius: 50px; font-size: 0.7rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            
            <div class="card card-register p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <h2 class="fw-bold mb-1">Fluye T&B</h2>
                    <p class="text-muted small">Crea una cuenta en el sistema</p>
                    <?php if($esAdmin): ?>
                        <span class="badge badge-admin"><i class="bi bi-shield-lock me-1"></i>MODO ADMINISTRADOR</span>
                    <?php endif; ?>
                </div>
                
                <?php echo $mensaje; ?>

                <form method="POST" class="needs-validation">
                    <div class="mb-3">
                        <label class="form-label text-uppercase">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="nombre_usuario" class="form-control" placeholder="Ej. Juan Pérez" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label text-uppercase">Cédula / ID</label>
                            <input type="text" name="cedula" class="form-control" placeholder="V-12345678" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label text-uppercase">Rol</label>
                            <select name="id_rol" class="form-select" required>
                                <?php foreach($roles as $rol): ?>
                                    <option value="<?php echo $rol['id_rol']; ?>" <?php echo (!$esAdmin) ? 'selected' : ''; ?>>
                                        <?php echo $rol['nombre_rol']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-uppercase">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="********" required>
                            <span class="input-group-text bg-light" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 shadow-sm mb-3 text-uppercase">
                        <?php echo ($esAdmin) ? 'Registrar Personal' : 'Crear mi cuenta ahora'; ?>
                    </button>
                </form>

                <div class="text-center mt-2">
                    <?php if($esAdmin): ?>
                        <a href="panel.php" class="text-decoration-none small fw-bold text-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Volver al Panel Administrativo
                        </a>
                    <?php else: ?>
                        <span class="text-muted small">¿Ya tienes cuenta?</span> 
                        <a href="login.php" class="text-decoration-none small fw-bold text-primary ms-1">Inicia Sesión</a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>