
<?php
include '../session-start.php';
include '../conexion.php';

// Comprobar que el usuario sea profesor o administrador.
if (
    !isset($_SESSION['rol']) ||
    ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'admin')
) {
    header("Location: ../login.php");
    exit();
}

// Permitir que el cambio se haga solamente desde el formulario.
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Acceso no permitido.");
}

// Recibir los datos de la evaluación.
$id_nivel = intval($_POST['id_nivel'] ?? 0);
$nombre_anterior = trim($_POST['nombre_anterior'] ?? '');
$nombre_nuevo = trim($_POST['nombre_nuevo'] ?? '');

// Quitar los puntos y espacios sobrantes al final.
$nombre_anterior = rtrim($nombre_anterior, " .");
$nombre_nuevo = rtrim($nombre_nuevo, " .");


// Verificar que el nivel académico se haya recibido.
if ($id_nivel <= 0) {
    die("Error: no se recibió el nivel académico.");
}

// Verificar que se haya recibido el nombre original.
if ($nombre_anterior == '') {
    die("Error: no se recibió el nombre de la evaluación original.");
}

// Verificar que se haya escrito el nombre nuevo.
if ($nombre_nuevo == '') {
    die("Error: no se recibió el nuevo nombre de la evaluación.");
}

// Buscar el período académico activo.
$sql_periodo = "
    SELECT id_periodo
    FROM periodos_academicos
    WHERE estado = 1
    LIMIT 1
";

$resultado_periodo = mysqli_query($conexion, $sql_periodo);

if (!$resultado_periodo || mysqli_num_rows($resultado_periodo) == 0) {
    die("No hay un período académico activo.");
}

$periodo = mysqli_fetch_assoc($resultado_periodo);
$id_periodo = $periodo['id_periodo'];

// Comprobar que el profesor tenga asignado el nivel seleccionado.
if ($_SESSION['rol'] == 'profesor') {

    $id_usuario = intval($_SESSION['id_usuario']);

    // Buscar si el nivel pertenece al profesor.
    $sql_permiso = "
        SELECT profesor_nivel.id_nivel
        FROM profesor_nivel
        INNER JOIN profesores
        ON profesor_nivel.id_profesor = profesores.id_profesor
        WHERE profesores.id_usuario = '$id_usuario'
        AND profesor_nivel.id_nivel = '$id_nivel'
        LIMIT 1
    ";

    $resultado_permiso = mysqli_query($conexion, $sql_permiso);

    // Impedir que modifique un nivel que no tiene asignado.
    if (!$resultado_permiso || mysqli_num_rows($resultado_permiso) == 0) {

        die("No tienes permiso para modificar este nivel.");
    }
}

// Comprobar que exista la evaluación original.
$sql_original = "
        SELECT id_calificacion
        FROM calificaciones
        WHERE id_nivel = ?
        AND id_periodo = ?
        AND TRIM(TRAILING '.' FROM TRIM(evaluacion)) = ?
        LIMIT 1
    ";

$consulta_original = mysqli_prepare($conexion, $sql_original);
mysqli_stmt_bind_param(
    $consulta_original,
    "iis",
    $id_nivel,
    $id_periodo,
    $nombre_anterior
);
mysqli_stmt_execute($consulta_original);

$resultado_original = mysqli_stmt_get_result($consulta_original);

if (mysqli_num_rows($resultado_original) == 0) {
    die("La evaluación original no existe en el período activo.");
}

// Evitar que el nuevo nombre coincida con otra evaluación.
if (strcasecmp($nombre_anterior, $nombre_nuevo) != 0) {

    $sql_existe = "
        SELECT id_calificacion
        FROM calificaciones
        WHERE id_nivel = ?
        AND id_periodo = ?
        AND TRIM(TRAILING '.' FROM TRIM(evaluacion)) = ?
        LIMIT 1
    ";

    $consulta_existe = mysqli_prepare($conexion, $sql_existe);
    mysqli_stmt_bind_param(
        $consulta_existe,
        "iis",
        $id_nivel,
        $id_periodo,
        $nombre_nuevo
    );
    mysqli_stmt_execute($consulta_existe);

    $resultado_existe = mysqli_stmt_get_result($consulta_existe);

    if (mysqli_num_rows($resultado_existe) > 0) {
        die("Ya existe una evaluación con ese nombre.");
    }
}

// Cambiar solamente el nombre de la evaluación.
// os ? son lugares reservados para valores que PHP enviará de manera segura.
$sql_actualizar = "
    UPDATE calificaciones
    SET evaluacion = ? 
    WHERE id_nivel = ?
    AND id_periodo = ?
    AND TRIM(TRAILING '.' FROM TRIM(evaluacion)) = ?
";

$consulta_actualizar = mysqli_prepare($conexion, $sql_actualizar);

mysqli_stmt_bind_param( // sirve para colocar de forma segura los valores en los signos ? de una consulta preparada
    $consulta_actualizar,
    // "siis" indica los tipos de datos: texto, entero, entero y texto.
    "siis",
    $nombre_nuevo,
    $id_nivel,
    $id_periodo,
    $nombre_anterior
);

if (mysqli_stmt_execute($consulta_actualizar)) {

    // Regresar al nivel y a la evaluación con su nuevo nombre.
    header("Location: index.php?id_nivel=$id_nivel&evaluacion=" . urlencode($nombre_nuevo) . "&renombrada=1");
    exit();
} else {

    die("No se pudo cambiar el nombre de la evaluación.");
}
?>
