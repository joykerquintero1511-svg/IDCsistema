<?php
session_start();
include("../conexion.php");

// 1. Verificación de seguridad
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit();
}

// 2. Verificar que recibimos un ID para borrar
if (isset($_GET['id'])) {
    $id_asignacion = $_GET['id'];
    
    // 3. Consulta para borrar
    // Asegúrate de que el nombre de la tabla y la columna sean correctos
    $sql = "DELETE FROM asignacion WHERE id_asignacion = '$id_asignacion'";
    
    if (mysqli_query($conexion, $sql)) {
        // Si se borra bien, vuelve a la página principal
        header("Location: index.php");
        exit();
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
} else {
    // Si no llega un ID, redirige al inicio
    header("Location: index.php");
    exit();
}
?>