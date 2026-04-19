<?php
// 1. Iniciar el buffer de salida y la sesión antes de cualquier texto
ob_start();
session_start();

// 2. Configuración de cabeceras y conexión
header('Content-Type: text/html; charset=utf-8');
require_once 'conexion.php';

$mensaje = "";

// 3. Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        // Consulta SQL con Joins para obtener el Rol y el estado de la cuenta (cuenta_activa)
        $sql = "SELECT u.*, r.id_rol FROM Usuario u 
                LEFT JOIN R_R_U rru ON u.id_usuario = rru.id_usuario 
                LEFT JOIN Roles r ON rru.id_rol = r.id_rol 
                WHERE u.correo = :correo";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        // 4. Verificación de contraseña
        if ($usuario && password_verify($password, $usuario['password'])) {
            
            // --- NUEVO: VALIDACIÓN DE CUENTA ACTIVA ---
            if ($usuario['cuenta_activa'] == 0) {
                $mensaje = "<div class='alert alert-warning border-0 shadow-sm small animated fadeIn'>
                                <i class='bi bi-envelope-exclamation-fill me-2'></i>Tu cuenta no ha sido activada aún. Por favor, revisa tu correo electrónico.
                            </div>";
            } else {
                // Si está activa, guardar datos en la sesión
                $_SESSION['id_usuario']     = $usuario['id_usuario'];
                $_SESSION['id_rol']         = $usuario['id_rol'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario']; 
                
                header("Location: index.php"); 
                exit();
            }
            // ------------------------------------------
            
        } else {
            $mensaje = "<div class='alert alert-danger border-0 shadow-sm small animated fadeIn'>
                            <i class='bi bi-exclamation-circle-fill me-2'></i>Credenciales incorrectas
                        </div>";
        }
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-warning border-0 shadow-sm small'>
                        Error de sistema: " . htmlspecialchars($e->getMessage()) . "
                    </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Fluye T&B</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --brand-color: #34aadc;
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        body { 
            background: var(--bg-gradient);
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card-login { 
            width: 100%; 
            max-width: 400px; 
            border: none; 
            border-radius: 20px; 
            background: #ffffff; 
            padding: 2.5rem 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .brand-logo {
            color: var(--brand-color);
            font-size: 1.8rem;
            letter-spacing: -1px;
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: #adb5bd;
        }

        .form-control {
            border-left: none;
            padding: 12px;
            font-size: 0.95rem;
            border-radius: 0 10px 10px 0;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        .btn-brand { 
            background-color: var(--brand-color); 
            color: white; 
            border: none; 
            padding: 14px; 
            font-weight: 700; 
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-brand:hover { 
            background-color: #2b8cb5; 
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(52, 170, 220, 0.3);
            color: white;
        }

        .toggle-password {
            cursor: pointer;
            border-left: none;
            border-radius: 0 10px 10px 0 !important;
        }
        
        .password-field {
            border-right: none !important;
            border-radius: 0 !important;
        }

        .animated { animation-duration: 0.5s; animation-fill-mode: both; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .fadeIn { animation-name: fadeIn; }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center">
        <div class="card-login text-center">
            <div class="mb-4">
                <div class="brand-logo fw-bold mb-1">FLUYE T&B</div>
                <p class="text-muted small">Sistema de Gestión Profesional</p>
            </div>

            <?php if($mensaje) echo $mensaje; ?>

            <form method="POST" autocomplete="off" class="text-start">
                <div class="mb-3">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="correo" class="form-control" placeholder="nombre@ejemplo.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control password-field" placeholder="••••••••" required>
                        <span class="input-group-text toggle-password" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-brand w-100 shadow-sm">
                    INGRESAR <i class="bi bi-arrow-right-short ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="small text-muted mb-0">¿Eres nuevo aquí?</p>
                <a href="registro.php" class="small text-decoration-none fw-bold" style="color: var(--brand-color);">Regístrate</a><br>
                
                <br><a href="index.php" class="small text-decoration-none" style="color: var(--brand-color);">Volver a la página principal</a>
            </div>
            
        </div>
          
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
// 5. Enviar el buffer al navegador
ob_end_flush(); 
?>