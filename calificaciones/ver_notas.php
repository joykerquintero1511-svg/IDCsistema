<?php

require_once '../conexion.php';

// Consulta para llenar el select de niveles
$sql = "
    SELECT id_nivel, nivel_academico
    FROM niveles
    ORDER BY nivel_academico ASC
";

$resultado = mysqli_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Calificaciones</title>
</head>

<body>

    <h1>Ver Calificaciones</h1>

    <form method="GET">

        <label>Nivel:</label>

        <select name="id_nivel" required>

            <option value="">Seleccione un nivel</option>

            <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

                <option
                    value="<?php echo $fila['id_nivel']; ?>"

                    <?php
                    if (isset($_GET['id_nivel']) && $_GET['id_nivel'] == $fila['id_nivel']){
                        echo "selected";
                    }
                    ?>
                >
            <?php echo htmlspecialchars($fila['nivel_academico']); ?>

             </option>

            <?php } ?>

        </select>

        <br><br>

        <label>Evaluación:</label>

        <?php

        $textoEvaluacion = "";

        if (isset($_GET['evaluacion'])) {

            $textoEvaluacion = htmlspecialchars(ucwords(strtolower($_GET['evaluacion'])));
        }
     ?>

        <input
            type="text"
            name="evaluacion"
            value="<?php echo $textoEvaluacion; ?>"required>

        <br><br>

        <button type="submit">Buscar</button>

    </form>

    <?php

    // Este bloque solo se ejecuta cuando el usuario pulsa Buscar
    if (isset($_GET['id_nivel'])) {

        $id_nivel = $_GET['id_nivel'];
        $evaluacion = trim($_GET['evaluacion']);

        $sql_calificaciones = "
            SELECT
                calificaciones.id_calificacion,
                personas.nombre,
                personas.apellido,
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

        $resultado_calificaciones = mysqli_query($conexion,$sql_calificaciones);

        if (!$resultado_calificaciones) {
            die(
                "Error al consultar las calificaciones: " .
                mysqli_error($conexion)
            );
        }

    ?>

        <h3>Calificaciones encontradas</h3>

        <?php if (mysqli_num_rows($resultado_calificaciones) > 0) { ?>

            <table border="1">

                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Nota 1</th>
                    <th>Nota 2</th>
                    <th>Nota Final</th>
                    <th>Observación</th>
                    <th>Acción</th>
                </tr>

                <?php
                while ($fila_calificacion = mysqli_fetch_assoc($resultado_calificaciones)) {
                ?>

                    <tr>

                        <td>
                            <?php
                   echo htmlspecialchars(ucwords(strtolower($fila_calificacion['nombre']))); ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(ucwords(strtolower($fila_calificacion['apellido']))); ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($fila_calificacion['nota_1']);?>
                        </td>

                        <td>
                            <?php

                            if ($fila_calificacion['nota_2'] === null) {
                                echo "No registrada";
                            } else {
                                echo htmlspecialchars(
                                    $fila_calificacion['nota_2']
                                );
                            }

                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($fila_calificacion['nota_final']);
                            ?>
                        </td>

                        <td>
                            <?php

                            if ($fila_calificacion['observacion'] === null || $fila_calificacion['observacion'] === "") {
                                echo "Sin observación";
                            } else {
                               echo htmlspecialchars(ucwords(strtolower($fila_calificacion['observacion'])));
                            }

                            ?>
                        </td>

                    <td>
                     <a href="editar_notas.php?id_calificacion=<?php echo $fila_calificacion['id_calificacion']; ?>">Editar </a>
                </td>

                    </tr>

                <?php } ?>

            </table>
            <br><br>

        <a href="imprimir_notas.php?id_nivel=<?php echo $id_nivel; ?>&evaluacion=<?php echo urlencode($evaluacion); ?>" target="_blank">
            Imprimir Calificaciones
        </a>

        <?php } else { ?>

            <p>No se encontraron calificaciones para ese nivel y evaluación.</p>

        <?php } ?>

    <?php } ?>

</body>

</html>