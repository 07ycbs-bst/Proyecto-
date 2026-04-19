<?php
// 1. Forzamos la visualización de errores solo para esta prueba
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// 2. Verificamos si el archivo de conexión existe antes de pedirlo
$ruta_conexion = 'conexion.php';
if (!file_exists($ruta_conexion)) {
    echo json_encode(['ok' => false, 'msg' => 'Error: No se encuentra el archivo conexion.php en la ruta ' . $ruta_conexion]);
    exit;
}

require $ruta_conexion;

$nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';

if (empty($nombre)) {
    echo json_encode(['ok' => false, 'msg' => 'Nombre no proporcionado']);
    exit;
}

try {
    // Consulta a la tabla 'servicio'
   $stmt = $conn->prepare("SELECT * FROM servicio WHERE nombre LIKE :nombre LIMIT 1");
    $stmt->execute([':nombre' => "%" . $nombre . "%"]);
    
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
   

    if ($servicio) {
       echo json_encode(['ok' => true, 'datos' => $servicio], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'No se encontro el servicio en la BD: ' . $nombre]);
    }
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'msg' => 'Error de Base de Datos: ' . $e->getMessage()]);
}
exit;