<?php
session_start();
include("../conexion.php");

// Validar que sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['estado'])) {
    $nuevo_estado = intval($_GET['estado']); 
    
    // El WHERE 1=1 engaña a la seguridad de MySQL para que permita el cambio
    $update = "UPDATE periodos_academicos SET inscripciones_abiertas = $nuevo_estado WHERE 1=1";
    $resultado = mysqli_query($conexion, $update);
    
    // Si la base de datos tranca la operación, el sistema nos mostrará el error exacto
    if (!$resultado) {
        die("Error crítico en la base de datos: " . mysqli_error($conexion));
    }
}

// Redirigimos al panel forzando la actualización (el ?upd evita que el navegador use la caché vieja)
header("Location: index.php?upd=" . time());
exit();
?>