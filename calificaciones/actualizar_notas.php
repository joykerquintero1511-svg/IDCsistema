<?php
require_once '../conexion.php';

session_start();

if (
    !isset($_SESSION['rol']) ||
    ($_SESSION['rol'] !== 'profesor' && $_SESSION['rol'] !== 'admin')
) {
    header("Location: ../login.php");
    exit();
}

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

// Verificar que el profesor tenga permiso para editar el nivel de esta calificación.
if ($_SESSION['rol'] === 'profesor') {

    $id_usuario = intval($_SESSION['id_usuario']);
    $id_calificacion = intval($id_calificacion);

    $sql_permiso = "
        SELECT calificaciones.id_calificacion
        FROM calificaciones
        INNER JOIN profesores
        ON calificaciones.id_nivel = profesores.id_nivel
        INNER JOIN periodos_academicos
        ON calificaciones.id_periodo = periodos_academicos.id_periodo
        WHERE calificaciones.id_calificacion = '$id_calificacion'
        AND profesores.id_usuario = '$id_usuario'
        AND periodos_academicos.estado = 1
        LIMIT 1
    ";

    $resultado_permiso = mysqli_query($conexion, $sql_permiso);

    if (!$resultado_permiso || mysqli_num_rows($resultado_permiso) == 0) {
        die("No tienes permiso para modificar esta calificación.");
    }
}

// Preparar Nota 1 para MySQL
if ($nota_1 === "") {
    $valor_nota_1 = "NULL";
} else {
    $valor_nota_1 = "'$nota_1'";
}

// Preparar Nota 2 para MySQL
if ($nota_2 === "") {
    $valor_nota_2 = "NULL";
} else {
    $valor_nota_2 = "'$nota_2'";
}

// Preparar Nota Final para MySQL
if ($nota_final === "") {
    $valor_nota_final = "NULL";
} else {
    $valor_nota_final = "'$nota_final'";
}

$sql = "
UPDATE calificaciones

SET

nota_1 = $valor_nota_1,

nota_2 = $valor_nota_2,

nota_final = $valor_nota_final,

observacion = '$observacion'

WHERE id_calificacion = '$id_calificacion'
";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error al actualizar la calificación: " . mysqli_error($conexion));
}

header("Location: ver_notas.php?id_nivel=$id_nivel&evaluacion=" . urlencode($evaluacion));
exit();
