<?php
// 1. Conexión a la base de datos
include("conexion.php");

// 2. Verificar que el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Captura y limpieza de datos (importante para evitar errores)
    // Estos nombres deben coincidir EXACTAMENTE con el atributo 'name' en tu HTML
    $id_nivel    = mysqli_real_escape_string($conexion, $_POST['id_nivel']);
    $tema        = mysqli_real_escape_string($conexion, $_POST['tema']);
    $titulo      = mysqli_real_escape_string($conexion, $_POST['titulo_tarea']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

    // 4. Inserción en la base de datos
    // Asegúrate de que tu tabla se llame 'asignaciones' (ajusta si es necesario)
    $sql = "INSERT INTO asignaciones (id_nivel, tema, titulo, descripcion) 
            VALUES ('$id_nivel', '$tema', '$titulo', '$descripcion')";

    if (mysqli_query($conexion, $sql)) {
        echo "<script>alert('Asignación creada exitosamente'); window.location='crear_asignacion.php';</script>";
    } else {
        echo "Error al guardar: " . mysqli_error($conexion);
    }
}
?>