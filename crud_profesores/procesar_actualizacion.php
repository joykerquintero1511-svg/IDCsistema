<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_profesor = trim($_POST['id_profesor']);
    $cedula      = trim($_POST['cedula']);
    $nombre      = trim($_POST['nombre']);
    $apellido    = trim($_POST['apellido']);
    $telefono    = trim($_POST['telefono']);

    if (empty($id_profesor) || empty($cedula) || empty($nombre) || empty($apellido)) {
        die("Campos requeridos faltantes.");
    }

    // Sentencia UPDATE estructurada clásica
    $sql = "UPDATE profesores SET 
            cedula = '$cedula', 
            nombre = '$nombre', 
            apellido = '$apellido', 
            telefono = '$telefono' 
            WHERE id_profesor = '$id_profesor'";
    
    $ejecutar = mysqli_query($conexion, $sql);

    if ($ejecutar) {
        header("Location: index.php?mensaje=actualizado");
        exit();
    } else {
        if (mysqli_errno($conexion) == 1062) {
            echo "<script>
                    alert('Error: La cédula ya le pertenece a otro profesor.');
                    window.history.back();
                  </script>";
        } else {
            echo "Error al actualizar: " . mysqli_error($conexion);
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>