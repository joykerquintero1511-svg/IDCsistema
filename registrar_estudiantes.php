<?php
require_once 'conexion.php';
global $conexion;

// =========================================================================
// BLOQUEO DE SEGURIDAD: Verificar si las inscripciones están abiertas
// =========================================================================
$q_inscrip = mysqli_query($conexion, "SELECT id_periodo, inscripciones_abiertas FROM periodos_academicos ORDER BY id_periodo DESC LIMIT 1");
$periodo = mysqli_fetch_assoc($q_inscrip);
if (!$periodo || $periodo['inscripciones_abiertas'] == 0) {
    die("
    <body style='background-color: #0c0c0c; margin: 0; display:flex; justify-content:center; align-items:center; height:100vh; font-family:sans-serif;'>
        <div style='background: #111111; padding: 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08); text-align:center;'>
            <h2 style='color: #ef4444; font-size: 2.5rem; margin-top:0;'>Inscripciones Cerradas 🔒</h2>
            <p style='color: rgba(255, 255, 255, 0.5); font-size: 1.5rem; margin-bottom: 2rem;'>El proceso web de pre-inscripciones no está activo en este momento.</p>
            <a href='index.php' style='display:inline-block; padding: 1rem 2rem; background: #3b82f6; color:white; text-decoration:none; border-radius:4px; font-size: 1.4rem;'>Volver al Inicio</a>
        </div>
    </body>
    ");
}
// =========================================================================

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Acceso no permitido. Por favor, envía el formulario desde la página de inscripción.");
}

$nombre = mysqli_real_escape_string($conexion, mb_convert_case(mb_strtolower(trim($_POST['nombre'])), MB_CASE_TITLE, 'UTF-8'));
$apellido = mysqli_real_escape_string($conexion, mb_convert_case(mb_strtolower(trim($_POST['apellidos'])), MB_CASE_TITLE, 'UTF-8'));
$email = mysqli_real_escape_string($conexion, $_POST['email']);
$nacionalidad = mysqli_real_escape_string($conexion, $_POST['nacionalidad']);
$cedula = mysqli_real_escape_string($conexion, $_POST['cedula']);
$telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
$contacto_emergencia = mysqli_real_escape_string($conexion, $_POST['contacto_emergencia']);
$nivel_instruccion = mysqli_real_escape_string($conexion, $_POST['nivel_instruccion']);

// Capturar y validar el nivel académico
$id_nivel = intval($_POST['id_nivel']);

$fecha_nacimiento = mysqli_real_escape_string($conexion, $_POST['fecha_nacimiento']);
$fecha_registro = date('Y-m-d');
$genero = mysqli_real_escape_string($conexion, $_POST['genero']);
$estado_inscripcion = 1;

// Preparar la dirección con mayúsculas y minúsculas ordenadas

// Eliminar espacios al inicio y al final
$direccion = trim($_POST['direccion']);

// Reemplazar múltiples espacios por uno solo
$direccion = preg_replace('/\s+/', ' ', $direccion);

// Convertir toda la dirección a minúsculas
$direccion = mb_strtolower($direccion, 'UTF-8');

// Colocar la primera letra de cada palabra en mayúscula
$direccion = mb_convert_case($direccion, MB_CASE_TITLE, 'UTF-8');

// Preparar la dirección para guardarla de forma segura en la base de datos
$direccion = mysqli_real_escape_string($conexion, $direccion);

// Validar campos obligatorios
if (empty($nombre) || empty($apellido) || empty($email) || empty($cedula) || empty($telefono) || empty($direccion) || empty($id_nivel) || empty($nacionalidad) || empty($fecha_nacimiento)) {
    die("Error: Todos los campos son obligatorios.");
}

// =========================================================================
// PASO 1: LÓGICA INTELIGENTE PARA LA TABLA PERSONAS
// =========================================================================
$sql_buscar_persona = "SELECT id_persona FROM personas WHERE cedula = '$cedula' LIMIT 1";
$res_persona = mysqli_query($conexion, $sql_buscar_persona);

if (mysqli_num_rows($res_persona) > 0) {
    // La persona ya existe, tomamos su ID y actualizamos sus datos de contacto por si cambiaron
    $fila = mysqli_fetch_assoc($res_persona);
    $id_persona = $fila['id_persona'];

    $sql_update_persona = "UPDATE personas SET telefono='$telefono', contacto_emergencia='$contacto_emergencia', direccion='$direccion' WHERE id_persona='$id_persona'";
    mysqli_query($conexion, $sql_update_persona);
} else {
    // Es una persona nueva, hacemos el INSERT
    $sql_persona = "INSERT INTO personas (tipo_documento, cedula, nacionalidad, nombre, apellido, fecha_nacimiento, genero, telefono, contacto_emergencia, direccion) 
                    VALUES ('Cédula', '$cedula', '$nacionalidad', '$nombre', '$apellido', '$fecha_nacimiento', '$genero', '$telefono', '$contacto_emergencia', '$direccion')";
    if (!mysqli_query($conexion, $sql_persona)) {
        die("Error al guardar datos personales: " . mysqli_error($conexion));
    }
    $id_persona = mysqli_insert_id($conexion);
}

// =========================================================================
// PASO 2: LÓGICA INTELIGENTE PARA LA TABLA ESTUDIANTES
// =========================================================================
$sql_buscar_est = "SELECT id_estudiante FROM estudiantes WHERE id_persona = '$id_persona' LIMIT 1";
$res_est = mysqli_query($conexion, $sql_buscar_est);

if (mysqli_num_rows($res_est) > 0) {
    // Ya es estudiante de la escuela, tomamos su ID y actualizamos el nivel e email
    $fila_est = mysqli_fetch_assoc($res_est);
    $id_estudiante = $fila_est['id_estudiante'];

    // Colocar como inactiva la inscripción anterior del estudiante
    $sql_inactivar = "UPDATE inscripciones SET estado = 0 WHERE id_estudiante = '$id_estudiante' AND estado = 1";
    mysqli_query($conexion, $sql_inactivar);

    $sql_update_est = "UPDATE estudiantes SET id_nivel='$id_nivel', nivel_instruccion='$nivel_instruccion', email='$email' WHERE id_estudiante='$id_estudiante'";
    mysqli_query($conexion, $sql_update_est);
} else {
    // Es un estudiante nuevo, hacemos el INSERT
    $sql_estudiante = "INSERT INTO estudiantes (id_persona, id_nivel, nivel_instruccion, fecha_registro, email) 
            VALUES ('$id_persona', '$id_nivel' , '$nivel_instruccion', '$fecha_registro', '$email')";
    if (!mysqli_query($conexion, $sql_estudiante)) {
        die("Error al guardar estudiante: " . mysqli_error($conexion));
    }
    $id_estudiante = mysqli_insert_id($conexion);
}

// =========================================================================
// PASO 3: INSERTAR EN INSCRIPCIONES Y GENERAR QR (SIEMPRE SE HACE)
// =========================================================================
$token_qr = md5(uniqid(rand(), true));

// <-- Ahora insertamos el id_nivel en lugar del texto
$sql_inscripcion = "INSERT INTO inscripciones (id_estudiante, id_nivel, fecha_inscripcion, estado, estatus_presencial, token_qr) 
        VALUES ('$id_estudiante', '$id_nivel', NOW(), '$estado_inscripcion', 'pendiente', '$token_qr')";

if (!mysqli_query($conexion, $sql_inscripcion)) {
    die("Error al guardar inscripción: " . mysqli_error($conexion));
}

$url_validacion = "http://localhost/IDCsistema/admin/validar_qr.php?token=" . $token_qr;
$url_qr_codificada = urlencode($url_validacion);
?>

<!DOCTYPE html>
<html lang="es" class="no-js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro Exitoso - Pase QR</title>
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="images/EFB.png" type="image/png">

    <!-- CSS adicional para ocultar botones a la hora de imprimir -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: white !important;
            }

            .ticket-caja {
                border: 2px solid black !important;
                background: white !important;
            }

            h2,
            p,
            strong {
                color: black !important;
            }
        }
    </style>
</head>

<body id="top" style="background-color: #0c0c0c; margin: 0; padding: 0;">
    <main class="s-content">
        <section class="container" style="padding: 6rem 0; text-align: center;">
            <div class="row">
                <div class="column xl-12">
                    <div style="margin-bottom: 2rem;" class="no-print">
                        <img src="images/EFB.png" alt="Logo EFB" style="max-width: 130px; width: 100%; height: auto; display: inline-block;">
                    </div>

                    <div class="ticket-caja" style="max-width: 550px; margin: 0 auto; background: #111111; padding: 4rem 3rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08);">

                        <div class="no-print" style="width: 60px; height: 60px; background: rgba(59, 113, 168, 0.1); border: 2px solid #3b71a8; color: #3b71a8; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; font-size: 2.5rem; font-weight: bold;">
                            ✓
                        </div>

                        <h2 style="color: #ffffff; font-size: 3rem; margin-bottom: 1rem;">¡Pre-inscripción Exitosa!</h2>
                        <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.6rem; margin-bottom: 2.5rem;">
                            <strong style="color: #ffffff;"><?php echo htmlspecialchars(stripslashes($nombre) . " " . stripslashes($apellido)); ?></strong>, has asegurado tu cupo.
                        </p>

                        <!-- CAJA DEL CÓDIGO QR -->
                        <div style="background: rgba(255,255,255,0.03); border: 2px dashed #3b71a8; padding: 2rem; border-radius: 8px; margin-bottom: 2.5rem;">
                            <p style="color: #3b71a8; font-size: 1.4rem; font-weight: bold; margin-bottom: 1rem; text-transform: uppercase;">Pase de Entrada Oficial</p>

                            <div style="background: white; padding: 1.5rem; display: inline-block; border-radius: 8px; margin-bottom: 1.5rem;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?php echo $url_qr_codificada; ?>" alt="Código QR EFB" style="display: block;">
                            </div>

                            <p style="color: rgba(255, 255, 255, 0.5); font-size: 1.3rem; margin-bottom: 0; line-height: 1.4;">
                                ⚠️ <b>OBLIGATORIO:</b> Toma una captura de pantalla (screenshot) o imprime este código. Deberás presentarlo en la puerta el primer día de clases para confirmar tu asistencia.
                            </p>
                        </div>

                        <div class="no-print">
                            <button onclick="window.print()" style="background-color: #3b71a8; color: white; border: none; padding: 1.2rem 2rem; font-size: 1.4rem; border-radius: 4px; cursor: pointer; font-weight: bold; margin-bottom: 1.5rem; width: 100%;">🖨️ Imprimir / Guardar PDF</button>

                            <a href="index.php" class="btn btn--primary u-fullwidth" style="font-size: 1.3rem; text-transform: uppercase; height: 5rem; line-height: 5rem; background: transparent; border: 1px solid rgba(255,255,255,0.2);">Volver al Inicio</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
<?php
mysqli_close($conexion);
?>