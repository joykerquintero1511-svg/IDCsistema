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

// Comprobar que el profesor consulte únicamente su nivel.
if ($_SESSION['rol'] === 'profesor') {
    $id_usuario = intval($_SESSION['id_usuario']);

    $sql_permiso = "
        SELECT id_profesor
        FROM profesores
        WHERE id_usuario = '$id_usuario'
        AND id_nivel = '$id_nivel'
        LIMIT 1
    ";

    $resultado_permiso = mysqli_query($conexion, $sql_permiso);

    if (!$resultado_permiso || mysqli_num_rows($resultado_permiso) == 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Nivel no autorizado']);
        exit();
    }
}

// Buscar las evaluaciones guardadas en el período activo.
$sql_evaluaciones = "
    SELECT DISTINCT c.evaluacion
    FROM calificaciones c
    INNER JOIN periodos_academicos p
        ON c.id_periodo = p.id_periodo
    WHERE c.id_nivel = '$id_nivel'
    AND p.estado = 1
    AND TRIM(c.evaluacion) != ''
    ORDER BY c.evaluacion ASC
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
