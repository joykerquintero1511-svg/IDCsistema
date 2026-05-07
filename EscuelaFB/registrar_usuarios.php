<?php
 // Incluye el contenido del archivo conexion.php dentro de este archivo. 
    include ("../conexion.php");

// Recibir los datos del formulario

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $red = $_POST['red'];


/* (empty) es una función de PHP que verifica si una variable está vacía,
 es decir, si no tiene un valor válido como texto, número o si es nula.
 */
    if (empty($nombre) || empty($apellido) || empty($email) || empty($red)) {
        die("Error: Todos los campos son obligatorios.");
}

/* ($sql) Crea una consulta SQL para insertar los datos en la base de datos.
 "INSERT INTO es la orden para agregar una fila nueva. Entre paréntesis
  va qué columnas voy a llenar. En VALUES van los datos que voy a 
  poner en esas columnas, en el mismo orden."      */

    $sql = "INSERT INTO estudiantes (nombre, apellido, email, telefono,red) 
        VALUES ('$nombre', '$apellido', '$email', '$telefono','$red')";

    if (mysqli_query($conexion, $sql)) {
        echo "<h2> Registro exitoso</h2>"; // se puede colcoar etiqueta HTML

    echo "<p>Gracias $nombre, tu registro ha sido completado.</p>";
    
    echo "<a href='Registro_de_miembros.php'>Volver al formulario</a>";
    } else {
    echo "Error al registrar: " . mysqli_error($conexion);
}

// Cerrar la conexión
    mysqli_close($conexion);
?>