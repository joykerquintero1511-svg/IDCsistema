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
    $telefono = $_POST['telefono'];
<<<<<<< HEAD
    $nivel_academico = $_POST['nivel_academico']; 
    $contraseña = $_POST['contraseña']; // cree esta columna la base d datos q faltaba
=======
    $nivel_academico = $_POST['nivel_academico'];
     // cree esta columna la base d datos q faltaba
>>>>>>> 610430851cd8030ef81907da62e30224e75183ab


/* (empty) es una función de PHP que verifica si una variable está vacía,
 es decir, si no tiene un valor válido como texto, número o si es nula.
 */
    if (empty($nombre) || empty($apellido) || empty($email) || empty($nivel_academico) || empty($contraseña)) {
        die("Error: Todos los campos son obligatorios.");
}

/* ($sql) Crea una consulta SQL para insertar los datos en la base de datos.
 "INSERT INTO es la orden para agregar una fila nueva. Entre paréntesis
  va qué columnas voy a llenar. En VALUES van los datos que voy a 
  poner en esas columnas, en el mismo orden."      */

    $sql = "INSERT INTO estudiantes (nombre, apellido, email, telefono, nivel_academico, contraseña) 
        VALUES ('$nombre', '$apellido', '$email', '$telefono','$nivel_academico', '$contraseña')";

    if (mysqli_query($conexion, $sql)) {
    echo "<h2> Registro exitoso</h2>"; // se puede colcocar etiqueta HTML ya q php trabaja con html sin problema

    echo "<p>Gracias $nombre, tu registro ha sido completado.</p>";
    
    echo "<a href='InscripcionEFB.php'>Volver al formulario</a>";
    } else {
    echo "Error al registrar: " . mysqli_error($conexion);
}

// Cerrar la conexión
    mysqli_close($conexion);
?>