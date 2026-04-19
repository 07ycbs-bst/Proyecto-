<?php
// 1. FORZAR UTF-8 para evitar rombos
header('Content-Type: text/html; charset=utf-8');

$host = 'localhost'; 
$db   = 'fluyetyb_proyecto';
$user = 'fluyetyb_fluyetyb-proyecto';
$pass = 'Cum2026*';

$conexion = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conexion, "utf8mb4");

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_servicio = intval($_POST['id_servicio']);
    $id_status = intval($_POST['id_status']);
    $descripcion_falla = mysqli_real_escape_string($conexion, $_POST['descripcion_falla']);
    $prioridad = mysqli_real_escape_string($conexion, $_POST['prioridad']);
    
    // Capturar usuario seleccionado (si existe)
    $id_usuario = !empty($_POST['id_usuario']) ? intval($_POST['id_usuario']) : "NULL";
    
    // Datos de contacto
    $nombre_contacto = mysqli_real_escape_string($conexion, $_POST['nombre_contacto']);
    $telefono_contacto = mysqli_real_escape_string($conexion, $_POST['telefono_contacto']);
    $correo_contacto = mysqli_real_escape_string($conexion, $_POST['correo_contacto']);

    $sql = "INSERT INTO solicitud (id_servicio, id_status, descripcion_falla, prioridad, id_usuario, nombre_contacto, telefono_contacto, correo_contacto) 
            VALUES ($id_servicio, $id_status, '$descripcion_falla', '$prioridad', $id_usuario, '$nombre_contacto', '$telefono_contacto', '$correo_contacto')";

    if (mysqli_query($conexion, $sql)) {
        header("Location: solicitudes.php?msj=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud (Admin) | Fluye T&B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; min-height: 100vh; margin: 0; }
        .main-content { flex: 1; padding: 20px; transition: all 0.3s; min-width: 0; }
        .top-nav { background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; }
        .form-card { background: white; border-radius: 15px; padding: 30px; border: none; }
        .form-label { font-weight: 600; color: #555; }
        .form-control, .form-select { border-radius: 10px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; }
        .section-divider { border-bottom: 2px solid #f0f0f0; margin: 20px 0; padding-bottom: 5px; font-size: 0.85rem; color: #999; text-transform: uppercase; letter-spacing: 1px; }
        /* Estilo para campos bloqueados */
        input:read-only { background-color: #e9ecef !important; opacity: 0.8; cursor: not-allowed; }
        /* Resalte para campo de teléfono si requiere atención */
        .attention-field { border: 1px solid #ffc107 !important; background-color: #fffbe6 !important; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-nav shadow-sm d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear text-primary me-2"></i>Registro Administrativo de Solicitud</h5>
            <a href="solicitudes.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Cancelar</a>
        </div>

        <div class="card form-card shadow-sm">
            <form method="POST" id="formSolicitud">
                
                <div class="section-divider">Asignaci&oacute;n de Usuario</div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Seleccionar Usuario Registrado</label>
                        <select name="id_usuario" id="selectUsuario" class="form-select">
                            <option value="">-- Cliente no registrado (Uso de datos manuales) --</option>
                            <?php 
                            $usuarios = mysqli_query($conexion, "SELECT id_usuario, nombre_usuario, correo FROM Usuario");
                            while($u = mysqli_fetch_assoc($usuarios)) {
                                echo "<option value='{$u['id_usuario']}'>{$u['nombre_usuario']} ({$u['correo']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="section-divider">Datos de Contacto</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nombre del Contacto</label>
                        <input type="text" name="nombre_contacto" id="nombre_contacto" class="form-control" placeholder="Ej: Juan Perez" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tel&eacute;fono</label>
                        <input type="text" name="telefono_contacto" id="telefono_contacto" class="form-control" placeholder="0412xxxxxxx" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Correo Electr&oacute;nico</label>
                        <input type="email" name="correo_contacto" id="correo_contacto" class="form-control" placeholder="cliente@correo.com" required>
                    </div>
                </div>

                <div class="section-divider">Detalles de la Solicitud</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Servicio Solicitado</label>
                        <select name="id_servicio" class="form-select" required>
                            <option value="">Seleccione un servicio...</option>
                            <?php 
                            $servicios = mysqli_query($conexion, "SELECT id_servicio, tipo_servicio FROM servicio");
                            while($s = mysqli_fetch_assoc($servicios)) { echo "<option value='{$s['id_servicio']}'>{$s['tipo_servicio']}</option>"; }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Estatus</label>
                        <select name="id_status" class="form-select" required>
                            <?php 
                            $status = mysqli_query($conexion, "SELECT id_status_solicitud, nombre_status FROM status_solicitud");
                            while($st = mysqli_fetch_assoc($status)) {
                                $selected = ($st['id_status_solicitud'] == 1) ? 'selected' : '';
                                echo "<option value='{$st['id_status_solicitud']}' $selected>{$st['nombre_status']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Prioridad</label>
                        <select name="prioridad" class="form-select" required>
                            <option value="Baja">Baja</option>
                            <option value="Media" selected>Media</option>
                            <option value="Alta">Alta</option>
                            <option value="Urgente">Urgente</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Descripci&oacute;n Detallada</label>
                        <textarea name="descripcion_falla" class="form-control" rows="4" placeholder="Escriba aqu&iacute; el requerimiento del cliente..." required></textarea>
                    </div>
                </div>

                <div class="text-end border-top pt-4 mt-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">
                        <i class="bi bi-save me-2"></i>Guardar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('selectUsuario').addEventListener('change', function() {
    const idUsuario = this.value;
    const nombreInput = document.getElementById('nombre_contacto');
    const telefonoInput = document.getElementById('telefono_contacto');
    const correoInput = document.getElementById('correo_contacto');

    if (idUsuario) {
        fetch('get_usuario.php?id=' + idUsuario)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Llenamos los datos que sí existen
                    nombreInput.value = data.nombre;
                    correoInput.value = data.correo;
                    
                    // Bloqueamos los datos que vienen de la DB
                    nombreInput.readOnly = true;
                    correoInput.readOnly = true;

                    // Como no tenemos teléfono en la tabla Usuario, 
                    // lo dejamos vacío y habilitado para escribirlo
                    telefonoInput.value = '';
                    telefonoInput.readOnly = false;
                    telefonoInput.focus();
                    telefonoInput.placeholder = "Escribe el teléfono aquí...";
                } else {
                    alert("No se encontraron datos para este usuario.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Error crítico: Asegúrate de que el archivo get_usuario.php existe en la misma carpeta.");
            });
    } else {
        // Limpiar todo si selecciona "No registrado"
        [nombreInput, telefonoInput, correoInput].forEach(el => {
            el.value = '';
            el.readOnly = false;
            el.placeholder = '';
        });
    }
});

// Importante: Habilitar antes de enviar para que PHP reciba los valores
document.getElementById('formSolicitud').addEventListener('submit', function() {
    document.getElementById('nombre_contacto').readOnly = false;
    document.getElementById('correo_contacto').readOnly = false;
});
</script>
</body>
</html>