<?php
ob_start();
session_start();
require_once 'conexion.php';

// 1. SEGURIDAD: Solo Admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: login.php");
    exit();
}

// 2. CARGAR ROLES Y EMPRESAS
try {
    $roles = $pdo->query("SELECT * FROM Roles")->fetchAll();
    $empresas = $pdo->query("SELECT * FROM Empresa ORDER BY nombre_empresa ASC")->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

// 3. PROCESAR EL REGISTRO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $pass = $_POST['pass'];
    $id_rol = $_POST['id_rol'];
    $id_empresa = $_POST['id_empresa'];
    $cedula = trim($_POST['cedula']);

    if (!empty($nombre) && !empty($correo) && !empty($pass) && !empty($id_empresa)) {
        try {
            $pdo->beginTransaction();

            $pass_hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO Usuario (nombre_usuario, correo, password, id_empresa, cedula, cuenta_activa) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$nombre, $correo, $pass_hash, $id_empresa, $cedula]);
            
            $nuevo_id = $pdo->lastInsertId();

            $stmt_rol = $pdo->prepare("INSERT INTO R_R_U (id_usuario, id_rol) VALUES (?, ?)");
            $stmt_rol->execute([$nuevo_id, $id_rol]);

            $pdo->commit();
            header("Location: usuarios.php?msj=registrado");
            exit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_msg = "Error: El correo o la identificación ya podrían estar registrados.";
        }
    } else {
        $error_msg = "Todos los campos marcados son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; margin: 0; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        .main-content { flex: 1; padding: 25px; min-height: 100vh; min-width: 0; }
        .top-nav { background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; }
        .form-card { border: none; border-radius: 15px; }
        /* Estilo para el botón de ver contraseña */
        .password-container { position: relative; }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
      <div class="top-nav d-flex justify-content-between align-items-center shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill fs-4 text-primary"></i> 
                Agregar Nuevo Usuario
            </h5>
        </div>
        <a href="usuarios.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Volver a la lista
        </a>
      </div>

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-8">
                    <div class="card form-card shadow-sm border-0">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="m-0 fw-bold text-success">Crear Nueva Cuenta</h6>
                        </div>
                        <div class="card-body p-4">
                            
                            <?php if(isset($error_msg)): ?>
                                <div class="alert alert-danger border-0 shadow-sm"><?php echo $error_msg; ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Asignar Empresa</label>
                                        <select name="id_empresa" class="form-select" required>
                                            <option value="" selected disabled>Selecciona la empresa...</option>
                                            <?php foreach($empresas as $e): ?>
                                                <option value="<?php echo $e['id_empresa']; ?>">
                                                    <?php echo htmlspecialchars($e['nombre_empresa']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nombre Completo</label>
                                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Pérez" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Identificación (Cédula)</label>
                                        <input type="text" name="cedula" class="form-control" placeholder="Ej. 12345678">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Correo Electrónico</label>
                                        <input type="email" name="correo" class="form-control" placeholder="nombre@ejemplo.com" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Contraseña</label>
                                        <div class="password-container">
                                            <input type="password" name="pass" id="pass" class="form-control" required>
                                            <i class="bi bi-eye toggle-password" id="togglePassword"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Asignar Rol</label>
                                        <select name="id_rol" class="form-select" required>
                                            <option value="" selected disabled>Selecciona un rol...</option>
                                            <?php foreach($roles as $r): ?>
                                                <option value="<?php echo $r['id_rol']; ?>">
                                                    <?php echo $r['nombre_rol']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="border-top pt-4 mt-2">
                                    <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                                        <i class="bi bi-person-plus-fill me-2"></i>Registrar Usuario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Lógica para ver/ocultar contraseña
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#pass');

    togglePassword.addEventListener('click', function (e) {
        // Alternar el tipo de input
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Alternar el icono
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });

    // Lógica Sidebar
    const sidebar = document.getElementById('sidebar');
    const btnOpen = document.getElementById('sidebarCollapse');
    const btnClose = document.getElementById('closeSidebar');

    if(btnOpen) btnOpen.onclick = () => sidebar.classList.add('active');
    if(btnClose) btnClose.onclick = () => sidebar.classList.remove('active');
</script>

</body>
</html>
<?php ob_end_flush(); ?>