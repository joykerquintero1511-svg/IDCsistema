<?php
require_once('../conexion.php');



$id_estudiante = $_POST['id_estudiante'];
$nivel_academico = $_POST['nivel_academico'];
$fecha_inscripcion = $_POST['fecha_inscripcion'];
$periodo = $_POST['periodo'];
$estado = $_POST['estado'];

$sql = "INSERT INTO inscripciones (id_estudiante, nivel_academico, fecha_inscripcion, periodo, estado) 
        VALUES ('$id_estudiante', '$nivel_academico', '$fecha_inscripcion', '$periodo', '$estado')";

if (mysqli_query($conexion, $sql)) {
    header("Location: listar.php");
} else {
    echo "Error al guardar: " . mysqli_error($conexion);
}
?>