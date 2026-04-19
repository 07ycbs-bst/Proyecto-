<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';
$charset = 'utf8mb4';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Fallo la conexión: " . mysqli_connect_error());
}

// Lógica para eliminar un servicio
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    // Al eliminar un servicio, eliminamos su relación en R_E_S también
    mysqli_query($conexion, "DELETE FROM R_E_S WHERE id_servicio = $id_eliminar");
    $sql_delete = "DELETE FROM servicio WHERE id_servicio = $id_eliminar";
    if(mysqli_query($conexion, $sql_delete)){
        header("Location: servicios.php?msj=1"); 
        exit();
    }
}

// --- LÓGICA DE FILTROS Y BÚSQUEDA ---
$where = " WHERE 1=1 ";

if (!empty($_GET['buscar'])) {
    $buscar = mysqli_real_escape_string($conexion, $_GET['buscar']);
    $where .= " AND s.tipo_servicio LIKE '%$buscar%' ";
}

if (!empty($_GET['f_empresa'])) {
    $f_emp_id = intval($_GET['f_empresa']);
    $where .= " AND r.id_empresa = $f_emp_id ";
}

if (!empty($_GET['f_tipo'])) {
    $f_tipo_id = intval($_GET['f_tipo']);
    $where .= " AND s.id_tipo = $f_tipo_id ";
}

if (!empty($_GET['f_status'])) {
    $f_status_id = intval($_GET['f_status']);
    $where .= " AND s.id_status = $f_status_id ";
}

// Consulta optimizada incluyendo la Empresa
$query = "SELECT s.*, st.nombre_status, t.nombre_tipo, e.nombre_empresa 
          FROM servicio s
          LEFT JOIN status_servicios st ON s.id_status = st.id_status
          LEFT JOIN tipo t ON s.id_tipo = t.id_tipo
          LEFT JOIN R_E_S r ON s.id_servicio = r.id_servicio
          LEFT JOIN Empresa e ON r.id_empresa = e.id_empresa
          $where
          ORDER BY s.id_servicio DESC";

$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .table-card { border: none; border-radius: 15px; background: white; overflow: hidden; }
        .filter-card { border: none; border-radius: 15px; background: white; padding: 20px; margin-bottom: 20px; }
        
        .badge-activo { background-color: #d1e7dd; color: #0f5132; }
        .badge-inactivo { background-color: #f8d7da; color: #842029; }
        .badge-pendiente { background-color: #fff3cd; color: #664d03; }
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
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-tools text-primary me-2"></i>Gestión de Servicios
                </h5>
            </div>

            <a href="nuevo_servicio.php" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-plus-circle-fill me-1"></i> 
                <span class="d-none d-sm-inline">Nuevo Servicio</span>
            </a>
        </div>

        <?php if(isset($_GET['msj'])): ?>
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i> Acción realizada con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card filter-card shadow-sm">
            <form method="GET" action="servicios.php" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control bg-light border-0" placeholder="Buscar..." value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="f_empresa" class="form-select bg-light border-0">
                        <option value="">Todas las Empresas</option>
                        <?php 
                        $res_e = mysqli_query($conexion, "SELECT id_empresa, nombre_empresa FROM Empresa");
                        while($e = mysqli_fetch_assoc($res_e)): ?>
                            <option value="<?php echo $e['id_empresa']; ?>" <?php echo (isset($_GET['f_empresa']) && $_GET['f_empresa'] == $e['id_empresa']) ? 'selected' : ''; ?>>
                                <?php echo $e['nombre_empresa']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="f_tipo" class="form-select bg-light border-0">
                        <option value="">Categorías</option>
                        <?php 
                        $res_t = mysqli_query($conexion, "SELECT id_tipo, nombre_tipo FROM tipo");
                        while($t = mysqli_fetch_assoc($res_t)): ?>
                            <option value="<?php echo $t['id_tipo']; ?>" <?php echo (isset($_GET['f_tipo']) && $_GET['f_tipo'] == $t['id_tipo']) ? 'selected' : ''; ?>>
                                <?php echo $t['nombre_tipo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="f_status" class="form-select bg-light border-0">
                        <option value="">Estatus</option>
                        <?php 
                        $res_st = mysqli_query($conexion, "SELECT id_status, nombre_status FROM status_servicios");
                        while($st = mysqli_fetch_assoc($res_st)): ?>
                            <option value="<?php echo $st['id_status']; ?>" <?php echo (isset($_GET['f_status']) && $_GET['f_status'] == $st['id_status']) ? 'selected' : ''; ?>>
                                <?php echo $st['nombre_status']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill">Filtrar</button>
                </div>
            </form>
        </div>

        <div class="card table-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Descripción del Servicio</th>
                            <th>Empresa</th>
                            <th>Precio (Ref)</th>
                            <th>Unidad</th>
                            <th>Estatus</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s = mysqli_fetch_assoc($resultado)): 
                            $nombre_st = $s['nombre_status'] ?? 'Desconocido';
                            $nombre_emp = $s['nombre_empresa'] ?? '<span class="text-danger">Sin Empresa</span>';

                            $clase_status = 'badge-pendiente';
                            if($s['id_status'] == 1) $clase_status = 'badge-activo';
                            if($s['id_status'] == 2) $clase_status = 'badge-inactivo';
                        ?>
                        <tr>
                            <td class="ps-4 text-muted">#<?php echo $s['id_servicio']; ?></td>
                            <td>
                                <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($s['tipo_servicio']); ?></span>
                                <small class="text-muted"><?php echo htmlspecialchars($s['nombre_tipo'] ?? 'S/N'); ?></small>
                            </td>
                            <td><span class="badge bg-white text-dark border"><?php echo $nombre_emp; ?></span></td>
                            <td><span class="text-primary fw-semibold">$<?php echo number_format($s['precio_referencial'], 2); ?></span></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($s['unidad_medida']); ?></small></td>
                            <td>
                                <span class="badge rounded-pill <?php echo $clase_status; ?>">
                                    <?php echo htmlspecialchars($nombre_st); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group gap-2">
                                    <a href="editar_servicio.php?id=<?php echo $s['id_servicio']; ?>" 
                                       class="btn btn-sm btn-outline-warning rounded-circle" title="Editar">
                                         <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="servicios.php?eliminar=<?php echo $s['id_servicio']; ?>" 
                                       class="btn btn-sm btn-outline-danger rounded-circle" 
                                       onclick="return confirm('¿Seguro que deseas eliminar este servicio?');" title="Eliminar">
                                         <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($resultado) == 0): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron servicios con esos filtros.</td></tr>
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
        btnOpen.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.add('active');
        });
    }

    if(btnClose) {
        btnClose.addEventListener('click', function() {
            sidebar.classList.remove('active');
        });
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