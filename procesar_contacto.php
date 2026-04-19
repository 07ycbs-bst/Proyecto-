<?php
// 1. Configuración de conexión
$host = "localhost";
$user = "fluyetyb_fluyetyb-proyecto";
$pass = "Cum2026*";
$db   = "fluyetyb_proyecto";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// 2. Captura y limpieza de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre  = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo  = mysqli_real_escape_string($conexion, $_POST['correo']);
    $asunto  = mysqli_real_escape_string($conexion, $_POST['asunto']);
    $mensaje = mysqli_real_escape_string($conexion, $_POST['mensaje']);
    $fecha   = date('Y-m-d H:i:s');

    // 3. Definir el ID de la empresa (Fluye T&B es el ID 1 según tus capturas)
    $id_empresa = 1; 

    // 4. Insertar en la base de datos (Añadido id_empresa)
    $sql = "INSERT INTO contacto (id_empresa, nombre, correo, asunto, mensaje, fecha) 
            VALUES ('$id_empresa', '$nombre', '$correo', '$asunto', '$mensaje', '$fecha')";

    if (mysqli_query($conexion, $sql)) {
        
        // 5. Enviar notificación por correo electrónico
        $para = "ycbs07@hotmail.com"; 
        $titulo = "Nuevo contacto desde FLUYE T&B: " . $asunto;
        
        $cuerpo = "Has recibido un nuevo mensaje de contacto:\n\n";
        $cuerpo .= "Nombre: $nombre\n";
        $cuerpo .= "Correo: $correo\n";
        $cuerpo .= "Asunto: $asunto\n";
        $cuerpo .= "Mensaje: $mensaje\n";
        
        // Cabeceras para mejorar la entregabilidad
        $cabeceras = "From: no-reply@fluyetyb.com" . "\r\n" .
                     "Reply-To: $correo" . "\r\n" .
                     "Content-Type: text/plain; charset=UTF-8" . "\r\n" .
                     "X-Mailer: PHP/" . phpversion();

        // El uso de @ oculta errores si el servidor de correo no está configurado
        @mail($para, $titulo, $cuerpo, $cabeceras);

        // 6. Redirigir con éxito usando JavaScript
        echo "<script>
                alert('¡Mensaje enviado con éxito! Nos pondremos en contacto pronto.');
                window.location.href='index.php'; 
              </script>";
    } else {
        echo "Error al guardar el mensaje: " . mysqli_error($conexion);
    }
}

mysqli_close($conexion);
?>