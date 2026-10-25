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
// Identificar la calificación que se desea editar.
$id_calificacion = intval($_GET['id_calificacion']);

// Comprobar que la calificación pertenezca al período activo.
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

// Verificar que el profesor tenga asignado el nivel de la calificación.
if ($_SESSION['rol'] === 'profesor') {

    $id_usuario = intval($_SESSION['id_usuario']);

    $sql_permiso = "
        SELECT calificaciones.id_calificacion
        FROM calificaciones
        INNER JOIN profesor_nivel
            ON calificaciones.id_nivel = profesor_nivel.id_nivel
        INNER JOIN profesores
            ON profesor_nivel.id_profesor = profesores.id_profesor
        WHERE calificaciones.id_calificacion = '$id_calificacion'
        AND profesores.id_usuario = '$id_usuario'
        LIMIT 1
    ";

    $resultado_permiso = mysqli_query($conexion, $sql_permiso);

    if (!$resultado_permiso || mysqli_num_rows($resultado_permiso) == 0) {
        die("No tienes permiso para editar esta calificación.");
    }
}

$sql = "


    SELECT

    calificaciones.id_calificacion,
    calificaciones.id_estudiante,
    calificaciones.id_nivel,
    calificaciones.id_periodo,
    calificaciones.evaluacion,
    calificaciones.descripcion_nota_1,
    calificaciones.descripcion_nota_2,
    calificaciones.nota_1,
    calificaciones.nota_2,
    calificaciones.nota_final,
    calificaciones.observacion,

    personas.nombre,
    personas.apellido

    FROM calificaciones

    INNER JOIN estudiantes
    ON calificaciones.id_estudiante = estudiantes.id_estudiante

    INNER JOIN personas
    ON estudiantes.id_persona = personas.id_persona

    WHERE calificaciones.id_calificacion = '$id_calificacion'
    ";

// Comprobar que la calificación exista antes de mostrar el formulario.
$resultado = mysqli_query($conexion, $sql);

if (!$resultado || mysqli_num_rows($resultado) == 0) {
    die("No se encontró la calificación solicitada.");
}

$fila = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Calificación</title>
    <link rel="stylesheet" href="../css/mystyle.css">
</head>

<body>

    <div class="edit-notas-wrapper">
        <div class="edit-notas-card">

            <div class="edit-notas-header">
                <h2>Editar Calificación</h2>
                <p>Modificación de notas y observaciones</p>

                <!-- Mostrar el identificador del período de la calificación. -->
                <p>
                    Período académico:
                    <?php
                    if ($fila['id_periodo'] === null) {
                        echo "Registro anterior sin período asignado";
                    } else {
                        echo htmlspecialchars($fila['id_periodo']);
                    }
                    ?>
                </p>
            </div>

            <form method="POST" action="actualizar_notas.php">

                <input type="hidden" name="id_calificacion" value="<?php echo $fila['id_calificacion']; ?>">
                <input type="hidden" name="id_nivel" value="<?php echo $fila['id_nivel']; ?>">
                <input type="hidden" name="evaluacion" value="<?php echo htmlspecialchars($fila['evaluacion']); ?>">

                <div class="edit-notas-grid">
                    <div class="edit-notas-group">

                        <label for="nota_1">
                            <?php
                            if ($fila['descripcion_nota_1'] != "") {
                                echo htmlspecialchars(ucwords(strtolower($fila['descripcion_nota_1'])));
                            } else {
                                echo "Nota 1";
                            }
                            ?>:
                        </label>

                        <input type="number" id="nota_1" name="nota_1" class="edit-notas-input" value="<?php echo $fila['nota_1']; ?>" min="0" max="20" step="0.01">
                    </div>

                    <div class="edit-notas-group">
                        <label for="nota_2">
                            <?php
                            if ($fila['descripcion_nota_2'] != "") {
                                echo htmlspecialchars(ucwords(strtolower($fila['descripcion_nota_2'])));
                            } else {
                                echo "Nota 2";
                            }
                            ?>:
                        </label>

                        <input type="number" id="nota_2" name="nota_2" class="edit-notas-input" value="<?php echo $fila['nota_2']; ?>" min="0" max="20" step="0.01">
                    </div>
                </div>

                <div class="edit-notas-group">
                    <label for="nota_final">Nota Final:</label>
                    <input type="number" id="nota_final" name="nota_final" class="edit-notas-input" value="<?php echo $fila['nota_final']; ?>" min="0" max="20" step="0.01">
                </div>

                <div class="edit-notas-group">
                    <label for="observacion">Observación:</label>
                    <textarea id="observacion" name="observacion" class="edit-notas-input edit-notas-textarea" placeholder="Escriba una observación opcional..."><?php echo htmlspecialchars($fila['observacion']); ?></textarea>
                </div>

                <div class="edit-notas-actions">
                    <button type="submit" class="edit-notas-btn-save">Guardar cambios</button>

                    <a href="ver_notas.php?id_nivel=<?php echo $fila['id_nivel']; ?>&evaluacion=<?php echo urlencode($fila['evaluacion']); ?>" class="edit-notas-btn-print">Volver</a>

                    <a href="imprimir_notas.php?id_calificacion=<?php echo $fila['id_calificacion']; ?>" target="_blank" class="edit-notas-btn-print">🖨️ Imprimir esta calificación</a>
                </div>

            </form>

        </div>
    </div>

</body>

</html>