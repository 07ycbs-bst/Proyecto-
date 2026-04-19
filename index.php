<?php
session_start(); 

// --- SECCIÓN: CONEXIÓN A BASE DE DATOS ---
$conexion = mysqli_connect("localhost", "fluyetyb_fluyetyb-proyecto", "Cum2026*", "fluyetyb_proyecto");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// --- CONSULTA DE PROYECTOS PARA EL SLIDER ---
$query_slider = "SELECT p.id_proyecto, p.imagen_principal, s.tipo_servicio 
                 FROM proyecto p 
                 LEFT JOIN servicio s ON p.id_servicio = s.id_servicio 
                 ORDER BY p.id_proyecto DESC LIMIT 10";
$res_slider = mysqli_query($conexion, $query_slider);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLUYE T&B - Soluciones Integrales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        :root {
            --blue-brand: #34aadc;
            --dark-blue: #003366;
        }
        .bg-brand { background-color: var(--blue-brand); }
        .text-brand { color: var(--blue-brand); }
        .btn-brand { background-color: var(--blue-brand); color: white; border: none; }
        .btn-brand:hover { background-color: #2b8cb5; color: white; }
        
        /* Estilos del Slider Principal */
        .hero-section { height: 75vh; min-height: 500px; }
        .hero-img { object-fit: cover; height: 75vh; filter: brightness(0.5); }
        .carousel-caption { bottom: 25% !important; z-index: 10; }
        
        .services-icon { font-size: 3rem; color: #333; transition: transform 0.3s; }
        .service-link { text-decoration: none; color: inherit; display: block; }
        .service-link:hover .services-icon { transform: scale(1.1); color: var(--blue-brand); }
        
        .proyecto-card {
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            height: 350px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .proyecto-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .proyecto-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(0,51,102,0.9), transparent);
            display: flex; flex-direction: column; justify-content: flex-end;
            padding: 20px; color: white;
        }
        .proyecto-card:hover .proyecto-img { transform: scale(1.1); }
        
        .swiper-button-next, .swiper-button-prev { color: var(--dark-blue); background: white; width: 40px; height: 40px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }

        .contact-banner {
            background: linear-gradient(rgba(0, 51, 102, 0.8), rgba(0, 51, 102, 0.8)), url('https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1200');
            background-size: cover; background-position: center; color: white;
        }
    </style>
</head>
<body>

    <?php include 'menu.php'; ?>

    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active hero-section">
                <img src="https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?auto=format&fit=crop&w=1600&q=80" class="d-block w-100 hero-img" alt="Soldadura">
                <div class="carousel-caption d-block text-start">
                    <h5 class="display-6 fw-light text-uppercase">Precisión y Calidad</h5>
                    <h1 class="display-2 fw-bold text-white">CONSTRUCCIÓN TÉCNICA</h1>
                    <p class="lead d-none d-md-block">Garantizamos la durabilidad en cada unión y estructura.</p>
                </div>
            </div>

            <div class="carousel-item hero-section">
                <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1600&q=80" class="d-block w-100 hero-img" alt="Obra de construcción">
                <div class="carousel-caption d-block text-start">
                    <h5 class="display-6 fw-light text-uppercase">Infraestructura y Desarrollo</h5>
                    <h1 class="display-2 fw-bold text-white">OBRAS INTEGRALES</h1>
                    <p class="lead d-none d-md-block">Ejecutamos proyectos civiles e industriales con excelencia.</p>
                </div>
            </div>

            <div class="carousel-item hero-section">
                <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1600" class="d-block w-100 hero-img" alt="Electricidad">
                <div class="carousel-caption d-block text-start">
                    <h5 class="display-6 fw-light text-uppercase">Ingeniería Eléctrica</h5>
                    <h1 class="display-2 fw-bold text-white">ENERGÍA SEGURA</h1>
                    <p class="lead d-none d-md-block">Instalaciones de alta potencia y tableros industriales.</p>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <section id="nosotros" class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="img/about.jpg" class="img-fluid rounded shadow" alt="Técnico" onerror="this.src='https://via.placeholder.com/600x400'">
                </div>
                <div class="col-md-6 ps-md-5">
                    <h6 class="text-muted text-uppercase fw-bold">Sobre nosotros</h6>
                    <h2 class="fw-bold mb-3 text-brand">Expertos en Soluciones Integrales</h2>
                    <p class="text-secondary">
                        En <strong>FLUYE T&B, C.A.</strong>, nos dedicamos a mantener la eficiencia operativa de su empresa y hogar. Somos especialistas en la reparación y mantenimiento de sistemas hídricos, industriales y eléctricos con un enfoque en la excelencia técnica.
                    </p>
                    <p class="text-secondary">
                        Nuestra misión es proporcionar tranquilidad a nuestros clientes a través de intervenciones precisas, infraestructura sólida y un servicio al cliente excepcional, garantizando que sus procesos nunca se detengan.
                    </p>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <ul class="list-unstyled small text-secondary">
                                <li><i class="bi bi-check-circle-fill text-brand me-2"></i>Calidad Certificada</li>
                                <li><i class="bi bi-check-circle-fill text-brand me-2"></i>Personal Experto</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled small text-secondary">
                                <li><i class="bi bi-check-circle-fill text-brand me-2"></i>Atención Inmediata</li>
                                <li><i class="bi bi-check-circle-fill text-brand me-2"></i>Garantía de Servicio</li>
                            </ul>
                        </div>
                    </div>
                    
                    <a href="#contacto" class="btn btn-brand px-4 py-2 rounded-pill shadow-sm">Contactanos</a>
                </div>
            </div>
        </div>
    </section>

    <section id="servicios" class="py-5 bg-light text-center">
        <div class="container">
            <h2 class="fw-bold">NUESTROS SERVICIOS</h2>
            <div class="row g-4 mt-4 justify-content-center">
                <?php
                $query = "SELECT id_tipo, nombre_tipo, icono_tipo FROM tipo";
                $resultado = mysqli_query($conexion, $query);
                while ($row = mysqli_fetch_assoc($resultado)): ?>
                    <div class="col-6 col-md-3">
                        <a href="categoria_detalle.php?id=<?php echo $row['id_tipo']; ?>" class="service-link">
                            <i class="bi <?php echo $row['icono_tipo']; ?> services-icon"></i>
                            <p class="mt-2 fw-bold text-uppercase"><?php echo htmlspecialchars($row['nombre_tipo']); ?></p>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section id="proyectos" class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">PROYECTOS RECIENTES</h2>
                <div class="mx-auto" style="width: 50px; height: 3px; background: var(--blue-brand);"></div>
            </div>
            <div class="swiper swiperProyectos">
                <div class="swiper-wrapper">
                    <?php while($reg = mysqli_fetch_assoc($res_slider)): ?>
                    <div class="swiper-slide">
                        <a href="proyecto_detalle.php?id=<?php echo $reg['id_proyecto']; ?>" class="text-decoration-none">
                            <div class="proyecto-card">
                                <img src="uploads/proyectos/<?php echo $reg['imagen_principal']; ?>" class="proyecto-img" alt="Proyecto" onerror="this.src='https://via.placeholder.com/400x350'">
                                <div class="proyecto-overlay">
                                    <h5 class="fw-bold m-0 text-white"><?php echo htmlspecialchars($reg['tipo_servicio']); ?></h5>
                                    <small class="text-white-50">Ver detalles <i class="bi bi-arrow-right"></i></small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <section class="py-5 contact-banner text-center">
        <div class="container">
            <h2 class="display-6 fw-bold my-4">“MANTENEMOS EN MOVIMIENTO LO QUE HACE QUE TU EMPRESA FUNCIONE”</h2>
            <div class="d-flex justify-content-center align-items-center">
                <i class="bi bi-whatsapp me-2 fs-3"></i>
                <span class="fs-4 fw-bold">0414 5928886 / 0414 5928388</span>
            </div>
        </div>
    </section>

    <section id="contacto" class="py-5 bg-light">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-md-6">
                    <img src="img/contact.jpg" class="img-fluid rounded shadow-lg" alt="Contacto" onerror="this.src='https://images.unsplash.com/photo-1523966211575-eb4a01e7dd51?auto=format&fit=crop&w=800'">
                </div>
                <div class="col-md-6">
                    <h2 class="fw-bold mb-4">CONTÁCTANOS</h2>
                    <p class="text-muted">¿Tienes alguna pregunta? ¡Estamos aquí para escucharte!</p>
                    <form action="procesar_contacto.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="nombre" class="form-control p-3 border-0 shadow-sm" placeholder="Nombre completo" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="correo" class="form-control p-3 border-0 shadow-sm" placeholder="Correo electrónico" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="asunto" class="form-control p-3 border-0 shadow-sm" placeholder="Asunto">
                        </div>
                        <div class="mb-3">
                            <textarea name="mensaje" class="form-control p-3 border-0 shadow-sm" rows="4" placeholder="¿En qué podemos ayudarte?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill shadow fw-bold">ENVIAR MENSAJE</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0 small opacity-50">&copy; <?php echo date('Y'); ?> FLUYE T&B, C.A.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiperProyectos', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: { delay: 3500 },
            pagination: { el: '.swiper-pagination', clickable: true },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
                1200: { slidesPerView: 4 }
            }
        });
    </script>
</body>
</html>