<?php
require 'conexion.php'; // Cargamos la conexión que hicimos antes
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $pass   = $_POST['password'];

    try {
        // Buscamos al usuario por correo
        $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE correo = ?");
        $stmt->execute([$correo]);
        $user = $stmt->fetch();

        // Verificamos si existe y si la clave coincide
        if ($user && $pass === $user['password']) {
            // ¡Login correcto! Guardamos datos en la sesión
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['nombre']  = $user['nombre_usuario'];
            
            echo "Bienvenido " . $_SESSION['nombre'] . ". Has iniciado sesión correctamente.";
            // Aquí podrías redirigir a un panel: header("Location: dashboard.php");
        } else {
            echo "Correo o contraseña incorrectos.";
        }
    } catch (Exception $e) {
        echo "Error en el sistema: " . $e->getMessage();
    }
}