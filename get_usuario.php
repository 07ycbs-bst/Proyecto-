<?php
// get_usuario.php
header('Content-Type: application/json');

$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Ajustado a tus columnas reales: nombre_usuario y correo
    $res = mysqli_query($conexion, "SELECT nombre_usuario, correo FROM Usuario WHERE id_usuario = $id");
    
    if ($f = mysqli_fetch_assoc($res)) {
        echo json_encode([
            'success' => true,
            'nombre'  => $f['nombre_usuario'],
            'correo'  => $f['correo'],
            'telefono' => '' // Enviamos vacío porque no existe en tu tabla Usuario
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>