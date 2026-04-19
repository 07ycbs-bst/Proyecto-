<?php
// 1. Iniciar el manejo de sesiones para poder manipular la sesión actual
session_start();

// 2. Limpiar todas las variables de sesión de la memoria
$_SESSION = array();

// 3. Si se desea destruir la sesión por completo, también se debe borrar la cookie de sesión.
// Nota: Esto es opcional pero recomendado por seguridad.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finalmente, destruir la sesión en el servidor
session_destroy();

// 5. Redirigir al usuario a la página de login
header("Location: login.php");
exit();
?>