<?php
include ("session-start.php");
include("conexion.php");

// Seguridad: Permitir acceso tanto a 'admin' como a 'profesor'
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'profesor')) {
    header("Location: login.php");
    exit();
}

// Lógica de retorno inteligente según el rol del usuario
$url_volver = "index.php";
if ($_SESSION['rol'] === 'admin') {
    $url_volver = "admin/index.php";
} elseif ($_SESSION['rol'] === 'profesor') {
    $url_volver = "profesores/index.php";
}

$mensaje = "";
$tipo_alerta = "";
$estudiante = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['token'])) {
    $busqueda = isset($_POST['busqueda']) ? trim($_POST['busqueda']) : trim($_GET['token']);

    if (!empty($busqueda)) {
        // Cruce de 3 tablas: inscripciones -> estudiantes -> personas
        $sql = "SELECT i.*, per.nombre, per.apellido, per.cedula 
                FROM inscripciones i 
                INNER JOIN estudiantes e ON i.id_estudiante = e.id_estudiante
                INNER JOIN personas per ON e.id_persona = per.id_persona
                WHERE per.cedula = ? OR i.token_qr = ? LIMIT 1";
                
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $busqueda, $busqueda);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($resultado)) {
            $estudiante = $row;
            
            if ($estudiante['estatus_presencial'] === 'asistido') {
                $mensaje = "⚠️ Este estudiante ya había validado su asistencia anteriormente.";
                $tipo_alerta = "warning";
            } else {
                $id_inscripcion = $estudiante['id_inscripcion'];
                $update = mysqli_query($conexion, "UPDATE inscripciones SET estatus_presencial = 'asistido' WHERE id_inscripcion = $id_inscripcion");
                
                if ($update) {
                    $mensaje = "✅ ¡Asistencia validada con éxito!";
                    $tipo_alerta = "success";
                    $estudiante['estatus_presencial'] = 'asistido';
                } else {
                    $mensaje = "❌ Error al actualizar el estatus en la base de datos.";
                    $tipo_alerta = "error";
                }
            }
        } else {
            $mensaje = "❌ No se encontró ningún registro con esa cédula o código QR.";
            $tipo_alerta = "error";
        }
        mysqli_stmt_close($stmt);
    } else {
        $mensaje = "⚠️ Por favor, ingrese una cédula o escanee un código.";
        $tipo_alerta = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación de Asistencia - EFB</title>
    <!-- Enlace a tu hoja de estilos centralizada -->
    <link rel="stylesheet" href="css/mystyle.css">
    <!-- Librería para escanear QR con la cámara -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
   
</head>
<body>

<div class="efb-validar-wrapper">
    <div class="efb-validar-container">
        <h2>Control de Asistencia </h2>
        
        <?php if (!empty($mensaje)): ?>
            <div class="efb-alert <?= $tipo_alerta; ?>"><?= $mensaje; ?></div>
        <?php endif; ?>

        <!-- Contenedor donde se activa la cámara -->
        <div id="reader"></div>

        <form id="form-validacion" action="validar.php" method="POST">
            <div class="efb-form-group">
                <label for="busqueda">O ingrese la Cédula / Token manualmente:</label>
                <input type="text" id="busqueda" name="busqueda" placeholder="Ej. V12345678 o Token QR" autofocus autocomplete="off" required>
            </div>
            <button type="submit">Verificar y Registrar</button>
        </form>

        <?php if ($estudiante): ?>
            <div class="efb-card-result">
                <p><strong>Estudiante:</strong> <?= htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido']); ?></p>
                <p><strong>Cédula:</strong> <?= htmlspecialchars($estudiante['cedula']); ?></p>
                <p><strong>Nivel:</strong> <?= htmlspecialchars($estudiante['nivel_academico']); ?></p>
                <p><strong>Estatus Actual:</strong> <span style="color: <?= $estudiante['estatus_presencial'] === 'asistido' ? '#34d399' : '#fbbf24'; ?>; font-weight: bold; text-transform: uppercase;"><?= htmlspecialchars($estudiante['estatus_presencial']); ?></span></p>
            </div>
        <?php endif; ?>

        <a href="<?= $url_volver; ?>" class="efb-back-link">← Volver al Panel</a>
    </div>
</div>

<script>
    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById('busqueda').value = decodedText;
        document.getElementById('form-validacion').submit();
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250 }, false);
    html5QrcodeScanner.render(onScanSuccess);
</script>
<?php include "script-seguridad.php"; ?>

</body>
</html>