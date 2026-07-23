<?php

require_once '../conexion.php';

session_start();

if (
    !isset($_SESSION['rol']) ||
    ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'admin')
) {
    header("Location: ../login.php");
    exit();
}

$id_nivel = $_GET['id_nivel'];
$evaluacion = $_GET['evaluacion'];

$sql = "
SELECT
personas.nombre,
personas.apellido,
calificaciones.descripcion_nota_1,
calificaciones.descripcion_nota_2,
calificaciones.nota_1,
calificaciones.nota_2,
calificaciones.nota_final,
calificaciones.observacion

FROM calificaciones

INNER JOIN estudiantes
ON calificaciones.id_estudiante = estudiantes.id_estudiante

INNER JOIN personas
ON estudiantes.id_persona = personas.id_persona

WHERE calificaciones.id_nivel = '$id_nivel'
AND calificaciones.evaluacion = '$evaluacion'

ORDER BY personas.apellido ASC
";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado || mysqli_num_rows($resultado) == 0) {
    die("No se encontraron calificaciones.");
}

$sql_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel'";
$resultado_nivel = mysqli_query($conexion, $sql_nivel);
$fila_nivel = mysqli_fetch_assoc($resultado_nivel);
$nombre_nivel = $fila_nivel['nivel_academico'];

$fila_encabezado = mysqli_fetch_assoc($resultado);

$nombre_actividad_1 = "Nota 1";
$nombre_actividad_2 = "Nota 2";

if ($fila_encabezado['descripcion_nota_1'] != "") {
    $nombre_actividad_1 = ucwords(strtolower($fila_encabezado['descripcion_nota_1']));
}

if ($fila_encabezado['descripcion_nota_2'] != "") {
    $nombre_actividad_2 = ucwords(strtolower($fila_encabezado['descripcion_nota_2']));
}

$mostrar_actividad_2 = false;

if ($fila_encabezado['descripcion_nota_2'] != "") {
    $mostrar_actividad_2 = true;
}

mysqli_data_seek($resultado, 0);

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=calificaciones.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<meta charset="UTF-8">

<table border="1">

    <tr>
        <th colspan="6">Reporte de Calificaciones</th>
    </tr>

    <tr>
        <td><strong>Nivel</strong></td>
        <td><?php echo htmlspecialchars($nombre_nivel); ?></td>
    </tr>

    <tr>
        <td><strong>Evaluación</strong></td>
        <td><?php echo htmlspecialchars(ucwords(strtolower($evaluacion))); ?></td>
    </tr>

    <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th><?php echo htmlspecialchars($nombre_actividad_1); ?></th>

        <?php if ($mostrar_actividad_2 == true) { ?>
            <th><?php echo htmlspecialchars($nombre_actividad_2); ?></th>
        <?php } ?>

        <th>Nota Final</th>
        <th>Observación</th>
    </tr>

    <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

        <tr>
            <td><?php echo htmlspecialchars(ucwords(strtolower($fila['nombre']))); ?></td>

            <td><?php echo htmlspecialchars(ucwords(strtolower($fila['apellido']))); ?></td>

            <td><?php echo htmlspecialchars($fila['nota_1']); ?></td>

            <?php if ($mostrar_actividad_2 == true) { ?>

                <td>
                    <?php
                    if ($fila['nota_2'] == null || $fila['nota_2'] == "") {
                        echo "No registrada";
                    } else {
                        echo htmlspecialchars($fila['nota_2']);
                    }
                    ?>
                </td>

            <?php } ?>

            <td><?php echo htmlspecialchars($fila['nota_final']); ?></td>

            <td>
                <?php
                if ($fila['observacion'] == null || $fila['observacion'] == "") {
                    echo "Sin observación";
                } else {
                    echo htmlspecialchars(ucwords(strtolower($fila['observacion'])));
                }
                ?>
            </td>
        </tr>

    <?php } ?>

</table>