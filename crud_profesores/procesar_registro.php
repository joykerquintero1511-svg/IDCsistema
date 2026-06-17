<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Captura tradicional de datos limpios de espacios
    $cedula   = trim($_POST['cedula']);
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);

    // Validación básica obligatoria
    if (empty($cedula) || empty($nombre) || empty($apellido)) {
        die("Por favor, rellene todos los campos obligatorios.");
    }

    // Consulta INSERT básica y tradicional de MySQL
    $sql = "INSERT INTO profesores (cedula, nombre, apellido, telefono) 
            VALUES ('$cedula', '$nombre', '$apellido', '$telefono')";
    
    // Ejecución de la consulta
    $ejecutar = mysqli_query($conexion, $sql);

    if ($ejecutar) {
        // Redirige al listado mandando el estado por la URL
        header("Location: index.php?mensaje=registrado");
        exit();
    } else {
        // Si hay error (como por ejemplo cédula duplicada)
        if (mysqli_errno($conexion) == 1062) {
            echo "<script>
                    alert('Error: La cédula ya se encuentra registrada.');
                    window.history.back();
                  </script>";
        } else {
            echo "Error al registrar en la base de datos: " . mysqli_error($conexion);
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>