<nav id="sidebar" class="sidebar shadow">
    <div class="d-lg-none text-end p-3">
        <button type="button" id="closeSidebar" class="btn text-white fs-3">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="p-4 text-center">
        <h5 class="fw-bold text-white mb-0"><a href="index.php" class="text-white text-decoration-none">FLUYE T&B</a></h5>
        <small class="text-muted text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">Administración</small>
    </div>
    
    <div class="nav flex-column">
        <?php $archivo = basename($_SERVER['PHP_SELF']); ?>
        <a class="nav-link <?php echo ($archivo == 'panel.php') ? 'active' : ''; ?>" href="panel.php"><i class="bi bi-house-door"></i> Inicio</a>
        <a class="nav-link <?php echo ($archivo == 'usuarios.php' || $archivo == 'editar_usuario.php') ? 'active' : ''; ?>" href="usuarios.php"><i class="bi bi-people"></i> Usuarios</a>
        <a class="nav-link <?php echo ($archivo == 'servicios.php') ? 'active' : ''; ?>" href="servicios.php"><i class="bi bi-gear"></i> Servicios</a>
        <a class="nav-link <?php echo ($archivo == 'categorias.php') ? 'active' : ''; ?>" href="categorias.php"><i class="bi bi-tags"></i> Categorías</a>
        <a class="nav-link <?php echo ($archivo == 'proyectos.php') ? 'active' : ''; ?>" href="proyectos.php"><i class="bi bi-folder"></i> Proyectos</a>
        <a class="nav-link <?php echo ($archivo == 'solicitudes.php') ? 'active' : ''; ?>" href="solicitudes.php"><i class="bi bi-file-earmark-plus"></i> Solicitudes</a>
        <a class="nav-link <?php echo ($archivo == 'comentarios.php') ? 'active' : ''; ?>" href="comentarios.php"><i class="bi bi-chat-left-text"></i> Comentarios</a>
        <hr class="text-secondary mx-3">
        <a class="nav-link text-danger mt-3" href="logout.php"><i class="bi bi-door-open"></i> Salir</a>
    </div>
</nav>

<style>
    .sidebar { min-width: 260px; max-width: 260px; background: #212529; min-height: 100vh; transition: all 0.3s; }
    .nav-link { color: #adb5bd; padding: 12px 20px; display: flex; align-items: center; gap: 10px; border-left: 4px solid transparent; text-decoration: none; }
    .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.05); border-left: 4px solid #34aadc; }
    
    @media (max-width: 991.98px) {
        .sidebar { 
            margin-left: -260px; 
            position: fixed; 
            height: 100%; 
            z-index: 1050; 
            top: 0;
            left: 0;
        }
        .sidebar.active { margin-left: 0; }
    }
</style>