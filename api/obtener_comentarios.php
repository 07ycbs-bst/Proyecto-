<?php
header('Content-Type: application/json; charset=utf-8');
require 'conexion.php';

$servicio_id = isset($_GET['servicio_id']) ? intval($_GET['servicio_id']) : 0;

if ($servicio_id <= 0) {
    echo json_encode([]);
    exit;
}

try {
    // Ajustado: cli.nombres (con 's') y cli.cliente_id para el JOIN
    $stmt = $conn->prepare("SELECT 
                                c.id, 
                                c.comentario, 
                                c.fecha, 
                                cli.nombres as nombre_usuario 
                            FROM comentarios c 
                            INNER JOIN cliente cli ON c.cliente_id = cli.cliente_id 
                            WHERE c.servicio_id = :servicio_id 
                            ORDER BY c.fecha ASC");
    
    $stmt->execute([':servicio_id' => $servicio_id]);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Limpieza de datos para el JSON
    foreach ($comentarios as &$com) {
        $com['comentario'] = trim(str_replace(["\r", "\n"], ' ', $com['comentario']));
        $com['nombre_usuario'] = trim($com['nombre_usuario']);
    }

    echo json_encode($comentarios, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}