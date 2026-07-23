<?php
session_start();
include("../conexion.php");

// Seguridad estricta: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ejecutamos la purga: Borra todas las inscripciones que sigan en estatus 'pendiente'
// Opcional: También podrías borrar de la tabla estudiantes/personas si no tienen otra relación, 
// pero limpiar las inscripciones libera los cupos de los "fantasmas".
$sql_purge = "DELETE FROM inscripciones WHERE estatus_presencial = 'pendiente'";

if (mysqli_query($conexion, $sql_purge)) {
    $afectados = mysqli_affected_rows($conexion);
    // Redirigimos al panel con un mensaje de éxito (puedes capturarlo con GET si quieres mostrar una alerta)
    header("Location: index.php?purge=success&count=" . $afectados);
    exit();
} else {
    die("Error al ejecutar la purga del sistema: " . mysqli_error($conexion));
}
?>