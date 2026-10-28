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

// Convertir el identificador de la calificación en un número entero.
$id_calificacion = intval($id_calificacion);

// Comprobar que la calificación pertenezca al período académico activo.
$sql_periodo = "
    SELECT calificaciones.id_calificacion
    FROM calificaciones
    INNER JOIN periodos_academicos
        ON calificaciones.id_periodo = periodos_academicos.id_periodo
    WHERE calificaciones.id_calificacion = '$id_calificacion'
    AND periodos_academicos.estado = 1
    LIMIT 1
";

$resultado_periodo = mysqli_query($conexion, $sql_periodo);

if (!$resultado_periodo || mysqli_num_rows($resultado_periodo) == 0) {
    die("Esta calificación no pertenece al período académico activo.");
}

// Comprobar que el profesor tenga asignado el nivel de la calificación.
if ($_SESSION['rol'] === 'profesor') {

    $id_usuario = intval($_SESSION['id_usuario']);

    // Comprobar que el profesor tenga asignado el nivel,en el mismo período académico de la calificación.

    $sql_permiso = "
    SELECT calificaciones.id_calificacion
    FROM calificaciones
    INNER JOIN profesor_nivel
        ON calificaciones.id_nivel = profesor_nivel.id_nivel
        AND calificaciones.id_periodo = profesor_nivel.id_periodo
    INNER JOIN profesores
        ON profesor_nivel.id_profesor = profesores.id_profesor
    WHERE calificaciones.id_calificacion = '$id_calificacion'
    AND profesores.id_usuario = '$id_usuario'
    LIMIT 1
";

    // Ejecutar la consulta para comprobar los permisos del profesor.
    $resultado_permiso = mysqli_query($conexion, $sql_permiso);

    // Comprobar si ocurrió un error al consultar.
    if (!$resultado_permiso) {
        die("Error al verificar los permisos: " . mysqli_error($conexion));
    }

    // Impedir que el profesor modifique notas de niveles no asignados.
    if (mysqli_num_rows($resultado_permiso) == 0) {
        die("No tienes permiso para modificar esta calificación.");
    }
}

// Obtener el nivel y la evaluación reales de la calificación.
$sql_datos = "
    SELECT id_nivel, evaluacion
     FROM calificaciones
     WHERE id_calificacion = '$id_calificacion'
     LIMIT 1
  ";

$resultado_datos = mysqli_query($conexion, $sql_datos);

if (!$resultado_datos || mysqli_num_rows($resultado_datos) == 0) {
    die("No se encontró la calificación solicitada.");
}

$datos_calificacion = mysqli_fetch_assoc($resultado_datos);

$id_nivel = $datos_calificacion['id_nivel'];
$evaluacion = $datos_calificacion['evaluacion'];


// Validar y preparar la Nota 1 para MySQL.
if ($nota_1 === "") {
    $valor_nota_1 = "NULL";
} else {
    if (!is_numeric($nota_1) || $nota_1 < 0 || $nota_1 > 20) {
        die("La Nota 1 debe estar entre 0 y 20.");
    }

    $valor_nota_1 = floatval($nota_1);
}

// Validar y preparar la Nota 2 para MySQL.
if ($nota_2 === "") {
    $valor_nota_2 = "NULL";
} else {
    if (!is_numeric($nota_2) || $nota_2 < 0 || $nota_2 > 20) {
        die("La Nota 2 debe estar entre 0 y 20.");
    }

    $valor_nota_2 = floatval($nota_2);
}

// Validar y preparar la Nota Final para MySQL.
if ($nota_final === "") {
    $valor_nota_final = "NULL";
} else {
    if (!is_numeric($nota_final) || $nota_final < 0 || $nota_final > 20) {
        die("La Nota Final debe estar entre 0 y 20.");
    }

    $valor_nota_final = floatval($nota_final);
}

// Preparar la observación para guardarla correctamente en MySQL.
$observacion = mysqli_real_escape_string($conexion, $observacion);

// Actualizar únicamente la calificación seleccionada
// si pertenece al período académico activo.
$sql = "
    UPDATE calificaciones
    SET
        nota_1 = $valor_nota_1,
        nota_2 = $valor_nota_2,
        nota_final = $valor_nota_final,
        observacion = '$observacion'
    WHERE id_calificacion = '$id_calificacion'
    AND id_periodo IN (
        SELECT id_periodo
        FROM periodos_academicos
        WHERE estado = 1
    )
";
$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error al actualizar la calificación: " . mysqli_error($conexion));
}

header("Location: ver_notas.php?id_nivel=$id_nivel&evaluacion=" . urlencode($evaluacion));
exit();
