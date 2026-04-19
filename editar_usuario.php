<?php
ob_start();
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: usuarios.php?err=noid");
    exit();
}

if (isset($_GET['activar']) && $_GET['activar'] == 1) {
    $pdo->prepare("UPDATE Usuario SET cuenta_activa = 1, token_verificacion = NULL WHERE id_usuario = ?")->execute([$id]);
    header("Location: editar_usuario.php?id=$id&msj=activado");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT u.*, rru.id_rol FROM Usuario u LEFT JOIN R_R_U rru ON u.id_usuario = rru.id_usuario WHERE u.id_usuario = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        header("Location: usuarios.php?err=notfound");
        exit();
    }

    $roles = $pdo->query("SELECT * FROM Roles")->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $nuevo_rol = $_POST['id_rol'];

    try {
        $checkEmail = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE correo = ? AND id_usuario != ?");
        $checkEmail->execute([$correo, $id]);
        
        if ($checkEmail->fetch()) {
            throw new Exception("El correo electrónico ya está registrado por otro usuario.");
        }

        $pdo->beginTransaction();

        $sql_u = "UPDATE Usuario SET nombre_usuario = ?, correo = ? WHERE id_usuario = ?";
        $pdo->prepare($sql_u)->execute([$nombre, $correo, $id]);

        if (!empty($_POST['pass'])) {
            $pass_hash = password_hash($_POST['pass'], PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE Usuario SET password = ? WHERE id_usuario = ?")->execute([$pass_hash, $id]);
        }

        $pdo->prepare("DELETE FROM R_R_U WHERE id_usuario = ?")->execute([$id]);
        $pdo->prepare("INSERT INTO R_R_U (id_usuario, id_rol) VALUES (?, ?)")->execute([$id, $nuevo_rol]);

        $pdo->commit();
        header("Location: usuarios.php?msj=editado");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_msg = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; margin: 0; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        .main-content { flex: 1; padding: 25px; min-height: 100vh; min-width: 0; }
        .top-nav { background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; }
        .form-card { border: none; border-radius: 15px; }
        .input-group-text { cursor: pointer; background: white; }
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
                <h5 class="mb-0 fw-bold">Editar Usuario</h5>
            </div>
            <a href="usuarios.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-8">
                    <div class="card form-card shadow-sm border-0">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-primary">Datos del Perfil: <?php echo htmlspecialchars($user['nombre_usuario']); ?></h6>
                            <div>
                                <?php if($user['cuenta_activa'] == 1): ?>
                                    <span class="badge bg-success rounded-pill">Cuenta Verificada</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill">Pendiente de Verificación</span>
                                    <a href="editar_usuario.php?id=<?php echo $id; ?>&activar=1" class="btn btn-link btn-sm text-decoration-none p-0 ms-2" onclick="return confirm('¿Activar cuenta manualmente?')">Activar manual</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <?php if(isset($error_msg)): ?>
                                <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_msg; ?></div>
                            <?php endif; ?>

                            <?php if(isset($_GET['msj']) && $_GET['msj'] == 'activado'): ?>
                                <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>Cuenta activada manualmente.</div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nombre Completo</label>
                                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($user['nombre_usuario']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Correo Electrónico</label>
                                        <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($user['correo']); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Rol en el Sistema</label>
                                        <select name="id_rol" class="form-select">
                                            <?php foreach($roles as $r): ?>
                                                <option value="<?php echo $r['id_rol']; ?>" <?php echo ($r['id_rol'] == $user['id_rol']) ? 'selected' : ''; ?>>
                                                    <?php echo $r['nombre_rol']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold text-danger">Cambiar Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" name="pass" id="pass" class="form-control" placeholder="Dejar en blanco para no cambiar">
                                            <span class="input-group-text" onclick="togglePassword()">
                                                <i class="bi bi-eye" id="toggleIcon"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top pt-4 mt-2">
                                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                        <i class="bi bi-save me-2"></i>Guardar Cambios
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
    function togglePassword() {
        const passInput = document.getElementById('pass');
        const icon = document.getElementById('toggleIcon');
        if (passInput.type === "password") {
            passInput.type = "text";
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passInput.type = "password";
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
</body>
</html>
<?php ob_end_flush(); ?>