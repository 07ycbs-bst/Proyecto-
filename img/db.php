<?php
$host     = "localhost";
$db_name  = "fluyetyb_empresa_servicios";
$username = "fluyetyb_admin"; // El que creaste en el paso 1
$password = "Cum2026*"; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>