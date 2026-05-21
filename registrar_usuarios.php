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
    $cedula = $_POST['cedula'];
    $telefono = $_POST['telefono'];
    $nivel_academico = $_POST['nivel_academico']; 
    $contraseña = $_POST['contraseña']; // cree esta columna la base d datos q faltaba


/* (empty) es una función de PHP que verifica si una variable está vacía,
 es decir, si no tiene un valor válido como texto, número o si es nula.
 */
    if (empty($nombre) || empty($apellido) || empty($email) || empty($cedula) || empty($telefono) || empty($nivel_academico) || empty($contraseña)) {
        die("Error: Todos los campos son obligatorios.");
}

/* ($sql) Crea una consulta SQL para insertar los datos en la base de datos.
 "INSERT INTO es la orden para agregar una fila nueva. Entre paréntesis
  va qué columnas voy a llenar. En VALUES van los datos que voy a 
  poner en esas columnas, en el mismo orden."      */

  // **LA VALIDACIÓN DE LONGITUD DE LA CONTRASEÑA VA AQUÍ** 
if (strlen($contraseña) < 6) {
    die("Error: La contraseña debe tener al menos 6 caracteres.");
}

// Encriptar la contraseña
$contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

    $sql = "INSERT INTO estudiantes (nombre, apellido, email, cedula, telefono, nivel_academico, contraseña) 
        VALUES ('$nombre', '$apellido', '$email', '$cedula', '$telefono','$nivel_academico', '$contraseña_encriptada')";

    if (mysqli_query($conexion, $sql)) {
    echo "<h2> Registro exitoso</h2>"; // se puede colcocar etiqueta HTML ya q php trabaja con html sin problema

    echo "<p>Gracias $nombre, tu registro ha sido completado.</p>";
    
    echo "<a href='inscripcion.php'>Volver al formulario</a>";;
    } else {
    echo "Error al registrar: " . mysqli_error($conexion);
}

// Cerrar la conexión
    mysqli_close($conexion);
?>