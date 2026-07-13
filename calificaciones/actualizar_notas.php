<?php
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Acceso no permitido.");
}

$id_calificacion = $_POST['id_calificacion'];
$id_nivel = $_POST['id_nivel'];
$evaluacion = $_POST['evaluacion'];

$nota_1 = $_POST['nota_1'];

$nota_2 = $_POST['nota_2'];

$nota_final = $_POST['nota_final'];

$observacion = trim($_POST['observacion']);

    if ($nota_2 == "") {

    $valor_nota_2 = "NULL";

} else {

    $valor_nota_2 = "'$nota_2'";

}

    $sql = "
UPDATE calificaciones

SET

nota_1 = '$nota_1',

nota_2 = $valor_nota_2,

nota_final = '$nota_final',

observacion = '$observacion'

WHERE id_calificacion = '$id_calificacion'
";

    $resultado = mysqli_query($conexion,$sql);
    
    if (!$resultado) {
    die("Error al actualizar la calificación: " . mysqli_error($conexion));
    }

    header("Location: ver_notas.php?id_nivel=$id_nivel&evaluacion=" . urlencode($evaluacion));
        exit();
