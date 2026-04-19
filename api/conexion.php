<?php
$host = "localhost";
$db   = "fluyetyb_empresa_servicios";
$user = "fluyetyb_admin";
$pass = "Cum2026*";
$charset = "utf8mb4";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db;charset=$charset",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        "error" => "Error de conexión",
        "detalle" => $e->getMessage()
    ]);
    exit;
}

