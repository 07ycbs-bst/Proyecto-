<?php
ob_start(); 
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';

// LÓGICA DE ELIMINACIÓN
if (isset($_GET['eliminar']) && !empty($_GET['eliminar'])) {
    $id_a_eliminar = intval($_GET['eliminar']); 
    
    try {
        if($id_a_eliminar === intval($_SESSION['id_usuario'])) {
            header("Location: usuarios.php?err=self");
            exit();
        }
        
        $pdo->beginTransaction();
        $stmt1 = $pdo->prepare("DELETE FROM R_R_U WHERE id_usuario = ?");
        $stmt1->execute([$id_a_eliminar]);
        
        $stmt2 = $pdo->prepare("DELETE FROM Usuario WHERE id_usuario = ?");
        $stmt2->execute([$id_a_eliminar]);

        $pdo->commit();
        
        header("Location: usuarios.php?msj=eliminado");
        exit(); 

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error al eliminar: " . $e->getMessage());
    }
}

// CONSULTA DE LISTA - Se agregó u.cuenta_activa
$sql = "SELECT u.id_usuario, u.nombre_usuario, u.correo, u.cuenta_activa, r.nombre_rol 
        FROM Usuario u
        LEFT JOIN R_R_U rru ON u.id_usuario = rru.id_usuario
        LEFT JOIN Roles r ON rru.id_rol = r.id_rol
        ORDER BY u.id_usuario DESC";
$usuarios = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        
        .main-content {
            flex: 1;
            padding: 20px;
            transition: all 0.3s;
            min-width: 0; 
        }
        
        .top-nav {
            background: white; padding: 15px; border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px;
        }
        
        .table-card { border: none; border-radius: 15px; background: white; overflow: hidden; }

        @media (max-width: 991.98px) {
            #sidebar:not(.active) {
                margin-left: -250px;
            }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-nav d-flex justify-content-between align-items-center shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-people-fill text-primary me-2"></i>Gestión de Usuarios
                </h5>
            </div>

            <a href="registro_admin.php" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-person-plus-fill me-1"></i> 
                <span class="d-none d-sm-inline">Nuevo Usuario</span>
            </a>
        </div>

        <?php if(isset($_GET['msj'])): ?>
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
                Acción realizada con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['err']) && $_GET['err'] == 'self'): ?>
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
                No puedes eliminar tu propia cuenta.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card table-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estatus</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td class="ps-4"><strong><?php echo htmlspecialchars($u['nombre_usuario']); ?></strong></td>
                            <td><?php echo htmlspecialchars($u['correo']); ?></td>
                            <td><span class="badge bg-info text-dark rounded-pill"><?php echo $u['nombre_rol'] ?? 'Sin Rol'; ?></span></td>
                            <td>
                                <?php if($u['cuenta_activa'] == 1): ?>
                                    <span class="badge bg-success rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill">
                                        <i class="bi bi-clock-history me-1"></i> Pendiente
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="editar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                    <a href="usuarios.php?eliminar=<?php echo $u['id_usuario']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?');"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($usuarios)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay usuarios registrados.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const btnOpen = document.getElementById('sidebarCollapse');
    const btnClose = document.getElementById('closeSidebar'); 

    if(btnOpen) {
        btnOpen.onclick = (e) => {
            e.stopPropagation();
            sidebar.classList.add('active');
        };
    }

    if(btnClose) {
        btnClose.onclick = () => {
            sidebar.classList.remove('active');
        };
    }

    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickInsideButton = btnOpen.contains(event.target);

        if (!isClickInsideSidebar && !isClickInsideButton && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
</script>

</body>
</html>
<?php ob_end_flush(); ?>