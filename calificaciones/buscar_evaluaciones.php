<?php

// Iniciar la sesión del usuario.
include '../session-start.php';

// Conectar con la base de datos.
include '../conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Comprobar que el usuario tenga permiso para consultar evaluaciones.
if (
    !isset($_SESSION['rol']) ||
    ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'profesor')
) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

// Recibir el nivel seleccionado.
// Recibir el nivel enviado desde JavaScript.
$id_nivel = 0;

if (isset($_POST['id_nivel'])) {
    $id_nivel = intval($_POST['id_nivel']);
}

if ($id_nivel <= 0) {
    echo json_encode([]);
    exit();
}


// Obtener el período académico activo.
$sql_periodo = "
    SELECT id_periodo
    FROM periodos_academicos
    WHERE estado = 1
    LIMIT 1
";

$resultado_periodo = mysqli_query($conexion, $sql_periodo);

// Comprobar que exista un período activo.
if (!$resultado_periodo || mysqli_num_rows($resultado_periodo) == 0) {
    echo json_encode([]);
    exit();
}

// Guardar el identificador del período activo.
$fila_periodo = mysqli_fetch_assoc($resultado_periodo);
$id_periodo_activo = intval($fila_periodo['id_periodo']);

// Comprobar que el profesor consulte únicamente sus niveles asignados.
if ($_SESSION['rol'] === 'profesor') {

    $id_usuario = intval($_SESSION['id_usuario']);

    $sql_permiso = "
        SELECT profesor_nivel.id_nivel
        FROM profesor_nivel
        INNER JOIN profesores
            ON profesor_nivel.id_profesor = profesores.id_profesor
        WHERE profesores.id_usuario = '$id_usuario'
        AND profesor_nivel.id_nivel = '$id_nivel'
        AND profesor_nivel.id_periodo = '$id_periodo_activo'
        LIMIT 1
    ";

    $resultado_permiso = mysqli_query($conexion, $sql_permiso);

    if (!$resultado_permiso || mysqli_num_rows($resultado_permiso) == 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Nivel no autorizado']);
        exit();
    }
}

// Buscar únicamente las evaluaciones del nivel y período activo.
$sql_evaluaciones = "
    SELECT DISTINCT evaluacion
    FROM calificaciones
    WHERE id_nivel = '$id_nivel'
    AND id_periodo = '$id_periodo_activo'
    AND TRIM(evaluacion) != ''
    ORDER BY evaluacion ASC
";


$resultado_evaluaciones = mysqli_query($conexion, $sql_evaluaciones);

if (!$resultado_evaluaciones) {
    http_response_code(500);  // Esto indica que ocurrió un error del servidor.
    echo json_encode(['error' => 'No se pudieron consultar las evaluaciones']);
    exit();
}

$evaluaciones = [];

while ($fila = mysqli_fetch_assoc($resultado_evaluaciones)) {
    $evaluaciones[] = $fila['evaluacion'];
}

echo json_encode($evaluaciones);
