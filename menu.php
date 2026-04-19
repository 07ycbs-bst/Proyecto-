<?php
// No olvides que session_start() debe ir al inicio de index.php para que esto funcione
?>
<style>
    /* Estilos para que se vea igual a tu imagen */
    .navbar-public { padding: 15px 0; background: #fff; }
    .nav-link-custom { 
        font-weight: 600; 
        color: #444 !important; 
        text-transform: uppercase; 
        font-size: 0.85rem;
        padding: 0.5rem 1rem !important;
    }
    .nav-link-custom:hover { color: #34aadc !important; }
    
    /* El botón azul de tu imagen */
    .btn-login-blue {
        background-color: #34aadc;
        color: white !important;
        border-radius: 50px;
        padding: 8px 25px !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        border: none;
    }

    /* Estilo para el nombre de usuario cuando aparece */
    .user-name-dropdown {
        font-weight: 700;
        color: #34aadc !important;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light navbar-public sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="bi bi-lightning-charge-fill text-primary fs-3 me-2"></i>
            <span class="fw-bold text-dark">FLUYE T&B</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link nav-link-custom" href="index.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="https://fluyetyb.soymaracaibo.com/index.php#nosotros">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="https://fluyetyb.soymaracaibo.com/index.php#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="https://fluyetyb.soymaracaibo.com/index.php#proyectos">Proyectos</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="https://fluyetyb.soymaracaibo.com/index.php#contacto">Contáctenos</a></li>
            </ul>

            <div class="d-flex">
                <?php if(isset($_SESSION['nombre_usuario'])): ?>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle user-name-dropdown" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            
                            <?php if(isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1): ?>
                                <li><a class="dropdown-item fw-bold text-primary" href="panel.php">Panel de Administrador</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>

                            <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-login-blue shadow-sm">
                        <i class="bi bi-person-fill me-2"></i> Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>