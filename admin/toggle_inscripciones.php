<?php
session_start();
include("../conexion.php");

// Validar que sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['estado'])) {
    $nuevo_estado = intval($_GET['estado']); // 1 para abrir, 0 para cerrar
    
    // Actualizamos el período académico activo (o el más reciente)
    $update = "UPDATE periodos_academicos SET inscripciones_abiertas = $nuevo_estado WHERE estado = 'activo' OR 1=1 LIMIT 1";
    mysqli_query($conexion, $update);
}

// Redirigir de vuelta al index del Admin
header("Location: index.php");
exit();
?>