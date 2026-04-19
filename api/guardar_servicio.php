<?php
header('Content-Type: application/json');
require 'conexion.php'; // Usa la conexión PDO que definimos

// Leer los datos JSON del cuerpo de la petición
$data = json_decode(file_get_contents("php://input"), true);

$tipo        = $data["tipo_servicio"] ?? "";
$descripcion = $data["descripcion"] ?? "";
$detalle     = $data["detalle"] ?? "";
$precio      = $data["precio"] ?? "";

// Validación básica
if (!$tipo || !$descripcion || !$precio) {
    echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
    exit;
}

try {
    // Preparar la consulta usando el objeto $conn (PDO)
    $stmt = $conn->prepare("
        INSERT INTO servicios_solicitados 
        (cliente_id,tipo_servicio, descripcion, detalle, precio)
        VALUES (1,:tipo, :descripcion, :detalle, :precio)
    ");

    // Ejecutar pasando los valores en un arreglo (más limpio que bind_param)
    $resultado = $stmt->execute([
        ':tipo'        => $tipo,
        ':descripcion' => $descripcion,
        ':detalle'     => $detalle,
        ':precio'      => $precio
    ]);

    echo json_encode(["ok" => true, "id_insertado" => $conn->lastInsertId()]);

} catch (PDOException $e) {
    // Manejo de errores en caso de que falle la base de datos
    echo json_encode([
        "ok" => false, 
        "msg" => "Error al guardar", 
        "detalle" => $e->getMessage()
    ]);
}