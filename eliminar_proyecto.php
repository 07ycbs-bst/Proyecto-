<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

// Obtener el ID del proyecto
$id_proyecto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_proyecto > 0) {
    
    // 1. PRIMERO: Buscar nombres de imágenes para borrarlas del servidor
    // Imagen Principal
    $res_p = mysqli_query($conexion, "SELECT imagen_principal FROM proyecto WHERE id_proyecto = $id_proyecto");
    $img_p = mysqli_fetch_assoc($res_p);
    
    // Imágenes de la Galería
    $res_g = mysqli_query($conexion, "SELECT ruta_imagen FROM proyecto_imagenes WHERE id_proyecto = $id_proyecto");

    // 2. BORRAR ARCHIVOS FÍSICOS
    $ruta = "uploads/proyectos/";

    // Borrar principal (si no es la default)
    if ($img_p && $img_p['imagen_principal'] != 'default.jpg') {
        if (file_exists($ruta . $img_p['imagen_principal'])) {
            unlink($ruta . $img_p['imagen_principal']);
        }
    }

    // Borrar galería
    while ($img_g = mysqli_fetch_assoc($res_g)) {
        if (file_exists($ruta . $img_g['ruta_imagen'])) {
            unlink($ruta . $img_g['ruta_imagen']);
        }
    }

    // 3. BORRAR DE LA BASE DE DATOS
    // Al usar DELETE en proyecto, si configuraste la FK con "ON DELETE CASCADE", 
    // se borrarán solas las fotos en 'proyecto_imagenes'. 
    // Por seguridad, lo hacemos manual si no estás seguro:
    mysqli_query($conexion, "DELETE FROM proyecto_imagenes WHERE id_proyecto = $id_proyecto");
    mysqli_query($conexion, "DELETE FROM proyecto WHERE id_proyecto = $id_proyecto");

    // Redireccionar con mensaje de éxito
    header("Location: proyectos.php?msj=3");
    exit();

} else {
    // Si no hay ID válido, volver al listado
    header("Location: proyectos.php");
    exit();
}
?>