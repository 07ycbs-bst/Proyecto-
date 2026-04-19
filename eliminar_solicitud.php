<?php
$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

// Obtener el ID de la solicitud desde la URL
$id_solicitud = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_solicitud > 0) {
    
    // Ejecutar la eliminación
    $sql = "DELETE FROM solicitud WHERE id_solicitud = $id_solicitud";
    
    if (mysqli_query($conexion, $sql)) {
        // Redirigir con mensaje de éxito (msj=3 para eliminar)
        header("Location: solicitudes.php?msj=3");
        exit();
    } else {
        // En caso de error (por ejemplo, por integridad referencial)
        echo "Error al eliminar: " . mysqli_error($conexion);
    }

} else {
    // Si no hay ID válido, regresar al listado
    header("Location: solicitudes.php");
    exit();
}
?>