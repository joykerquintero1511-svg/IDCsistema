<?php
include('../session-start.php');
include("../conexion.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_asignacion = intval($_GET['id']);
    $sql_delete = "DELETE FROM asignacion WHERE id_asignacion = $id_asignacion LIMIT 1";
    mysqli_query($conexion, $sql_delete);
}

header("Location: index.php");
exit();
?>