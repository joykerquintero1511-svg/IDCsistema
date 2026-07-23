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

$nombre = $_POST['nombre'];
$apellido = $_POST['apellidos'];
$email = $_POST['email'];
$nacionalidad = $_POST['nacionalidad'];
$cedula = $_POST['cedula'];
$telefono = $_POST['telefono'];
$contacto_emergencia = $_POST['contacto_emergencia'];
$nivel_instruccion = $_POST['nivel_instruccion'];
$id_nivel = $_POST['nivel_academico'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$fecha_registro = date('Y-m-d');
$genero = $_POST['genero'];
$estado_inscripcion = 1;
$direccion = $_POST['direccion'];

$consulta_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel'";
$resultado_nivel = mysqli_query($conexion, $consulta_nivel);

if (!$resultado_nivel || mysqli_num_rows($resultado_nivel) == 0) {
    die("Error: El nivel seleccionado no existe.");
}

$fila_nivel = mysqli_fetch_assoc($resultado_nivel);
$nivel_academico = $fila_nivel['nivel_academico'];

// Validar campos obligatorios
if (empty($nombre) || empty($apellido) || empty($email) || empty($cedula) || empty($telefono) || empty($direccion)|| empty($id_nivel) || empty($nacionalidad) || empty($fecha_nacimiento )) {
    die("Error: Todos los campos son obligatorios.");
}

// PASO 1: Insertar en la tabla personas
$sql_persona = "INSERT INTO personas (tipo_documento, cedula, nacionalidad, nombre, apellido, fecha_nacimiento, genero, telefono, contacto_emergencia, direccion) 
                VALUES ('Cédula', '$cedula', '$nacionalidad', '$nombre', '$apellido', '$fecha_nacimiento', '$genero', '$telefono', '$contacto_emergencia', '$direccion')";

if (!mysqli_query($conexion, $sql_persona)) {
    die("Error al guardar datos personales: " . mysqli_error($conexion));
}

// Obtener el ID del estudiante recién insertado
$id_persona = mysqli_insert_id($conexion);


// PASO 2: Insertar en la tabla estudiantes
$sql_estudiante = "INSERT INTO estudiantes (id_persona, id_nivel, nivel_instruccion, fecha_registro, email) 
        VALUES ('$id_persona', '$id_nivel' , '$nivel_instruccion', '$fecha_registro', '$email')";

if (!mysqli_query($conexion, $sql_estudiante)) {
    die("Error al guardar estudiante: " . mysqli_error($conexion));
}

// Obtener el ID del estudiante recién insertado para la inscripción
$id_estudiante = mysqli_insert_id($conexion);


// PASO 3: Insertar en la tabla inscripciones (AQUÍ AGREGAMOS LA MAGIA DEL QR)

$token_qr = md5(uniqid(rand(), true)); // Genera un código loco único

$sql_inscripcion = "INSERT INTO inscripciones (id_estudiante, nivel_academico, fecha_inscripcion, estado, estatus_presencial, token_qr) 
        VALUES ('$id_estudiante', '$nivel_academico', NOW(), '$estado_inscripcion', 'pendiente', '$token_qr')";

if (!mysqli_query($conexion, $sql_inscripcion)) {
    die("Error al guardar inscripción: " . mysqli_error($conexion));
}

// Generamos el enlace que leerá el QR (Ajusta 'localhost/IDCsistema' si tu proyecto se llama distinto)
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
            .no-print { display: none !important; }
            body { background-color: white !important; }
            .ticket-caja { border: 2px solid black !important; background: white !important; }
            h2, p, strong { color: black !important; }
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
                            <strong style="color: #ffffff;"><?php echo htmlspecialchars($nombre . " " . $apellido); ?></strong>, has asegurado tu cupo.
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