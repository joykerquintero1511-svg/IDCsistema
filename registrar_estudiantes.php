<?php
require_once 'conexion.php';
global $conexion;// lo coloco para q no se subraye variable $conexion 

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
$nivel_academico = $_POST['nivel_academico'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$fecha_registro = date('Y-m-d');
$genero = $_POST['genero'];
// Pendiente definir si se usará período académico.
// Por ahora se deja fijo para no romper el INSERT de inscripciones.
$periodo = "2026-1";
$estado_inscripcion = 1;
$direccion = $_POST['direccion'];

// Validar campos obligatorios
if (empty($nombre) || empty($apellido) || empty($email) || empty($cedula) || empty($telefono) || empty($direccion)|| empty($nivel_academico) || empty($nacionalidad) || empty($fecha_nacimiento )) {
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

$sql_estudiante = "INSERT INTO estudiantes (id_persona, nivel_instruccion, fecha_registro, email) 
        VALUES ('$id_persona', '$nivel_instruccion', '$fecha_registro', '$email')";

if (!mysqli_query($conexion, $sql_estudiante)) {
    die("Error al guardar estudiante: " . mysqli_error($conexion));
}

// Obtener el ID del estudiante recién insertado para la inscripción
$id_estudiante = mysqli_insert_id($conexion);

  // PASO 3: Insertar en la tabla inscripciones

$sql_inscripcion = "INSERT INTO inscripciones (id_estudiante, nivel_academico, fecha_inscripcion, periodo, estado) 
        VALUES ('$id_estudiante', '$nivel_academico', NOW(), '$periodo', '$estado_inscripcion')";

if (!mysqli_query($conexion, $sql_inscripcion)) {
    die("Error al guardar inscripción: " . mysqli_error($conexion));
}


?>
<!DOCTYPE html>
<html lang="es" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro Exitoso - EFB</title>
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body id="top" style="background-color: #0c0c0c; margin: 0; padding: 0;">
    <main class="s-content">
        <section class="container" style="padding: 10rem 0; text-align: center;">
            <div class="row">
                <div class="column xl-12">
                    <div style="margin-bottom: 3.5rem;">
                        <img src="images/EFB.png" alt="Logo EFB" style="max-width: 160px; width: 100%; height: auto; display: inline-block;">
                    </div>
                    <div style="max-width: 550px; margin: 0 auto; background: #111111; padding: 5rem 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div style="width: 70px; height: 70px; background: rgba(59, 113, 168, 0.1); border: 2px solid var(--color-1-500); color: var(--color-1-400); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2.5rem auto; font-size: 3rem; font-weight: bold;">
                            ✓
                        </div>
                        <h2 style="color: #ffffff; font-size: 3.6rem; margin-bottom: 1.5rem;">¡Registro Exitoso!</h2>
                        <p style="color: rgba(255, 255, 255, 0.5); font-size: 1.8rem; margin-bottom: 4rem;">
                            Gracias <strong style="color: #ffffff;"><?php echo htmlspecialchars($nombre); ?></strong>, tu inscripción ha sido completada.
                        </p>
                        <a href="index.php" class="btn btn--primary u-fullwidth" style="font-size: 1.5rem; text-transform: uppercase; height: 5.5rem; line-height: 5.5rem;">Volver al Inicio</a>
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