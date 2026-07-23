<?php
include("session-start.php");
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
        $sql = "SELECT i.*, e.nombre, e.apellido, e.cedula, p.nombre_periodo 
                FROM inscripciones i 
                INNER JOIN estudiantes e ON i.id_estudiante = e.id_estudiante
                INNER JOIN periodos_academicos p ON i.id_periodo = p.id_periodo
                WHERE e.cedula = ? OR i.token_qr = ? LIMIT 1";
                
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
    <!-- Librería para escanear QR con la cámara -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>/* ==========================================
   ESTILOS DEL MÓDULO DE VALIDACIÓN (EFB)
   ================================---------- */
.efb-validar-wrapper { 
    background-color: #0b0f19; 
    color: white; 
    font-family: sans-serif; 
    margin: 0; 
    padding: 20px; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    min-height: 100vh; 
    box-sizing: border-box;
}
.efb-validar-container { 
    background: #1e293b; 
    padding: 30px; 
    border-radius: 12px; 
    width: 100%; 
    max-width: 500px; 
    box-shadow: 0 10px 25px rgba(0,0,0,0.3); 
}
.efb-validar-container h2 { 
    text-align: center; 
    color: #38bdf8; 
    margin-bottom: 20px; 
}
#reader { 
    background: #0f172a; 
    border: 2px dashed #334155; 
    border-radius: 8px; 
    margin-bottom: 20px; 
    overflow: hidden; 
}
#reader video { 
    width: 100% !important; 
    border-radius: 6px; 
}
.efb-form-group { 
    margin-bottom: 20px; 
}
.efb-validar-container label { 
    display: block; 
    margin-bottom: 8px; 
    color: #94a3b8; 
    font-size: 14px; 
}
.efb-validar-container input[type="text"] { 
    width: 100%; 
    padding: 12px; 
    background: #0f172a; 
    border: 1px solid #334155; 
    border-radius: 8px; 
    color: white; 
    font-size: 16px; 
    box-sizing: border-box; 
}
.efb-validar-container input[type="text"]:focus { 
    border-color: #38bdf8; 
    outline: none; 
}
.efb-validar-container button { 
    width: 100%; 
    padding: 12px; 
    background: #3b82f6; 
    border: none; 
    border-radius: 8px; 
    color: white; 
    font-weight: bold; 
    font-size: 16px; 
    cursor: pointer; 
    transition: background 0.2s; 
}
.efb-validar-container button:hover { 
    background: #2563eb; 
}
.efb-alert { 
    padding: 12px; 
    border-radius: 8px; 
    margin-bottom: 20px; 
    text-align: center; 
    font-weight: bold; 
    font-size: 14px; 
}
.efb-alert.success { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid #10b981; }
.efb-alert.warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid #f59e0b; }
.efb-alert.error { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444; }
.efb-card-result { 
    background: #0f172a; 
    padding: 20px; 
    border-radius: 8px; 
    margin-top: 20px; 
    border-left: 4px solid #38bdf8; 
}
.efb-card-result p { margin: 8px 0; font-size: 14px; color: #cbd5e1; }
.efb-card-result strong { color: white; }
.efb-back-link { display: block; text-align: center; margin-top: 20px; color: #94a3b8; text-decoration: none; font-size: 14px; }
.efb-back-link:hover { color: white; }
</style>
</head>
<body>

<div class="efb-validar-wrapper">
    <div class="efb-validar-container">
        <h2>Control de Asistencia 🎟️</h2>
        
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
                <p><strong>Período:</strong> <?= htmlspecialchars($estudiante['nombre_periodo']); ?></p>
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