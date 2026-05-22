<?php
 // Incluye el contenido del archivo conexion.php dentro de este archivo. 
  require_once 'conexion.php';

   // Verificar que el formulario fue enviado por POST
  if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Acceso no permitido. Por favor, envía el formulario desde la página de inscripción.");
}

// Recibir los datos del formulario

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
    $fecha_registro = date('Y-m-d H:i:s'); // Fecha y hora actual para el registro
    $genero = $_POST['genero'];




/* (empty) es una función de PHP que verifica si una variable está vacía,
 es decir, si no tiene un valor válido como texto, número o si es nula.
 */
    if (empty($nombre) || empty($apellido) || empty($email) || empty($cedula) || empty($telefono) || empty($contacto_emergencia) || empty($nivel_academico) || empty($nacionalidad) || empty($fecha_nacimiento) || empty($nivel_instruccion) || empty($genero) || empty($fecha_registro)) {
        die("Error: Todos los campos son obligatorios.");
}

/* ($sql) Crea una consulta SQL para insertar los datos en la base de datos.
 "INSERT INTO es la orden para agregar una fila nueva. Entre paréntesis
  va qué columnas voy a llenar. En VALUES van los datos que voy a 
  poner en esas columnas, en el mismo orden."      */





    $sql = "INSERT INTO estudiantes (nombre, apellido, email, cedula, telefono, contacto_emergencia, nivel_academico, nacionalidad, fecha_nacimiento, nivel_instruccion, genero, fecha_registro) 
        VALUES ('$nombre', '$apellido', '$email', '$cedula', '$telefono', '$contacto_emergencia', '$nivel_academico', '$nacionalidad', '$fecha_nacimiento', '$nivel_instruccion', '$genero', '$fecha_registro')";

    if (mysqli_query($conexion, $sql)) {
        // Cerramos PHP temporalmente para inyectar la interfaz limpia
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

                            <div style="max-width: 550px; margin: 0 auto; background: #111111; padding: 5rem 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.7);">
                                
                                <div style="width: 70px; height: 70px; background: rgba(59, 113, 168, 0.1); border: 2px solid var(--color-1-500); color: var(--color-1-400); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2.5rem auto; font-size: 3rem; font-weight: bold;">
                                    ✓
                                </div>

                                <h2 class="text-display-title" style="color: #ffffff; font-size: 3.6rem; margin-bottom: 1.5rem; margin-top: 0;">
                                    ¡Registro Exitoso!
                                </h2>
                                
                                <p style="color: rgba(255, 255, 255, 0.5); font-size: 1.8rem; line-height: 1.6; margin-bottom: 4rem;">
                                    Gracias <strong style="color: #ffffff; text-transform: capitalize;"><?php echo htmlspecialchars($nombre); ?></strong>, tu registro ha sido completado.
                                </p>

                                <a href="index.php" class="btn btn--primary u-fullwidth" style="font-size: 1.5rem; letter-spacing: 2px; text-transform: uppercase; height: 5.5rem; line-height: 5.5rem; display: block; text-align: center; margin: 0;">
                                    Volver al Inicio
                                </a>
                            </div>

                        </div>
                    </div>
                </section>
            </main>

        </body>
        </html>
        <?php // Volvemos a abrir PHP para mantener el flujo del else nativo
    } else {
        echo "Error al registrar: " . mysqli_error($conexion);
    }

// Cerrar la conexión
    mysqli_close($conexion);
?>