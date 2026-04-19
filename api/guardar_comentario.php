<?php
error_reporting(0); // Evita que errores de texto rompan el JSON
header('Content-Type: application/json; charset=utf-8');
require 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

$servicio_id = isset($data['servicio_id']) ? intval($data['servicio_id']) : 0;
$cliente_id  = isset($data['cliente_id']) ? intval($data['cliente_id']) : 0;
$texto       = isset($data['comentario']) ? trim($data['comentario']) : '';

if ($servicio_id <= 0 || $cliente_id <= 0 || empty($texto)) {
    echo json_encode(['ok' => false, 'msg' => 'Datos incompletos']);
    exit;
}

try {
    // Insertamos en la nueva tabla comentarios que vincula con servicios_solicitados
    $stmt = $conn->prepare("INSERT INTO comentarios (servicio_id, cliente_id, comentario) 
                            VALUES (:sid, :cid, :txt)");
    $resultado = $stmt->execute([
        ':sid' => $servicio_id,
        ':cid' => $cliente_id,
        ':txt' => $texto
    ]);

    echo json_encode(['ok' => $resultado]);
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}