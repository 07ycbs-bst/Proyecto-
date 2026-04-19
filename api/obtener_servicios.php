<?php
header('Content-Type: application/json; charset=utf-8');
require 'conexion.php';

// Recibimos los parámetros enviados desde el JS
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$cliente_id = isset($_GET['cliente_id']) ? trim($_GET['cliente_id']) : '';

if (empty($tipo) || empty($cliente_id)) {
    echo json_encode([]);
    exit;
}

try {
    // Realizamos la consulta filtrando por ambos criterios
    $stmt = $conn->prepare("SELECT * FROM servicios_solicitados 
                            WHERE TRIM(tipo_servicio) = :tipo 
                            AND cliente_id = :cliente_id 
                            ORDER BY id DESC");
    
    $stmt->execute([
        ':tipo' => $tipo,
        ':cliente_id' => $cliente_id
    ]);
    
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Limpiamos los datos de cualquier salto de línea invisible \r\n
    foreach ($servicios as &$s) {
        $s['descripcion'] = trim(str_replace(["\r", "\n"], '', $s['descripcion']));
        $s['detalle'] = trim(str_replace(["\r", "\n"], '', $s['detalle']));
    }

    echo json_encode($servicios, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}