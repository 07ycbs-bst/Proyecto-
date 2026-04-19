<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';
$id_usuario = $_SESSION['id_usuario'];
$mensaje = "";

// --- LÓGICA PARA ACTUALIZAR DATOS ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_actualizar'])) {
    $nuevo_nombre = $_POST['nombre_usuario'];
    $nuevo_correo = $_POST['correo'];

    try {
        $sqlUpd = "UPDATE Usuario SET nombre_usuario = :nom, correo = :cor WHERE id_usuario = :id";
        $stmtUpd = $pdo->prepare($sqlUpd);
        $stmtUpd->execute([
            ':nom' => $nuevo_nombre,
            ':cor' => $nuevo_correo,
            ':id'  => $id_usuario
        ]);
        
        $_SESSION['nombre'] = $nuevo_nombre;
        $mensaje = "<div class='alert alert-success shadow-sm'>Datos actualizados correctamente.</div>";
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger shadow-sm'>Error al actualizar: " . $e->getMessage() . "</div>";
    }
}

// --- CONSULTA DE DATOS ACTUALES (Incluye Roles y R_R_U) ---
try {
    // Consulta que une Usuario con R_R_U y Roles para obtener el nombre del rol
    $sql = "SELECT u.nombre_usuario, u.cedula, u.correo, r.nombre_rol, e.nombre_empresa 
            FROM Usuario u
            LEFT JOIN R_R_U rru ON u.id_usuario = rru.id_usuario
            LEFT JOIN Roles r ON rru.id_rol = r.id_rol
            LEFT JOIN Empresa e ON u.id_empresa = e.id_empresa
            WHERE u.id_usuario = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $u = $stmt->fetch();
} catch (PDOException $e) {
    die("Error crítico: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($u['nombre_usuario']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .card-profile { border: none; border-radius: 15px; overflow: hidden; }
        .header-profile { background: #34aadc; color: white; padding: 2rem; text-align: center; }
        .role-badge { background: rgba(255,255,255,0.2); border: 1px solid white; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; }
        .form-label { font-weight: 600; color: #555; font-size: 0.85rem; }
        .read-only-box { background-color: #e9ecef; border-radius: 8px; padding: 10px; color: #666; font-size: 0.95rem; }
        /* Estilo para campos deshabilitados que no parezcan demasiado oscuros */
        .form-control:disabled { background-color: #fdfdfd; color: #888; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            
            <?php echo $mensaje; ?>

            <div class="card card-profile shadow-sm">
                <div class="header-profile">
                    <h2 class="fw-bold mb-1" id="display-name"><?php echo htmlspecialchars($u['nombre_usuario']); ?></h2>
                    <span class="role-badge">ROL: <?php echo strtoupper(htmlspecialchars($u['nombre_rol'] ?? 'Sin Rol')); ?></span>
                </div>

                <div class="card-body p-4">
                    <form method="POST" id="perfil-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre de Usuario</label>
                                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" 
                                       value="<?php echo htmlspecialchars($u['nombre_usuario']); ?>" required disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="correo" id="correo" class="form-control" 
                                       value="<?php echo htmlspecialchars($u['correo']); ?>" required disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cédula / ID (No editable)</label>
                                <div class="read-only-box"><?php echo htmlspecialchars($u['cedula']); ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Empresa Asignada</label>
                                <div class="read-only-box"><?php echo htmlspecialchars($u['nombre_empresa'] ?? 'Particular'); ?></div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="button" id="btn-editar" class="btn btn-warning px-4 shadow-sm" onclick="habilitarEdicion()">
                                Editar Datos
                            </button>

                            <button type="submit" name="btn_actualizar" id="btn-actualizar" class="btn btn-primary px-4 shadow-sm" 
                                    style="background-color: #34aadc; border: none; display: none;">
                                Guardar Cambios
                            </button>

                            <a href="index.php" class="btn btn-outline-secondary px-4">Volver</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="logout.php" class="text-danger small text-decoration-none fw-bold">CERRAR SESIÓN SEGURA</a>
            </div>
        </div>
    </div>
</div>

<script>
function habilitarEdicion() {
    // Habilitar campos de texto
    document.getElementById('nombre_usuario').disabled = false;
    document.getElementById('correo').disabled = false;

    // Mostrar botón de actualizar y ocultar el de editar
    document.getElementById('btn-actualizar').style.display = 'block';
    document.getElementById('btn-editar').style.display = 'none';

    // Poner el foco en el primer campo
    document.getElementById('nombre_usuario').focus();
}
</script>

</body>
</html>