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

    <div style="margin-bottom:20px;"><a href="index.php" style="background:#2563eb;color:white;padding:10px 18px;text-decoration:none;border-radius:8px;font-weight:bold;">← Volver a Registrar Calificaciones</a></div>

    <form method="GET">

        <label>Nivel:</label>

        <select name="id_nivel" required onchange="this.form.submit();">

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

<select name="evaluacion">

    <option value="">Seleccione una evaluación</option>

    <?php

    if (isset($_GET['id_nivel']) && $_GET['id_nivel'] != "") {

        $id_nivel_seleccionado = $_GET['id_nivel'];

        $sql_evaluaciones = "
            SELECT DISTINCT evaluacion
            FROM calificaciones
            WHERE id_nivel = '$id_nivel_seleccionado'
            AND evaluacion != ''
            ORDER BY evaluacion ASC
        ";

        $resultado_evaluaciones = mysqli_query($conexion, $sql_evaluaciones);

        while ($fila_evaluacion = mysqli_fetch_assoc($resultado_evaluaciones)) {

            $evaluacion_seleccionada = "";

            if (isset($_GET['evaluacion']) && $_GET['evaluacion'] == $fila_evaluacion['evaluacion']) {
                $evaluacion_seleccionada = "selected";
            }

            echo '<option value="' . htmlspecialchars($fila_evaluacion['evaluacion']) . '" ' . $evaluacion_seleccionada . '>';
            echo htmlspecialchars(ucwords(strtolower($fila_evaluacion['evaluacion'])));
            echo '</option>';
        }
    }

    ?>

</select>

        <br><br>

                <label>Buscar estudiante:</label>

                 <input type="text" name="buscar" value="<?php if(isset($_GET['buscar'])){ echo htmlspecialchars($_GET['buscar']); } ?>">

            <br><br>

            <button type="submit">Buscar</button>

            <a href="ver_notas.php" style="margin-left:10px;padding:6px 12px;text-decoration:none;border:1px solid #999;border-radius:5px;background:#f2f2f2;color:#000;">Limpiar</a>

            </form>

    </form>

    <?php

    // Este bloque solo se ejecuta cuando el usuario pulsa Buscar
    if (isset($_GET['id_nivel']) && isset($_GET['evaluacion']) && $_GET['evaluacion'] != "") {

        $id_nivel = $_GET['id_nivel'];
        $evaluacion = trim($_GET['evaluacion']);

        $buscar = "";

            if (isset($_GET['buscar'])) {
                $buscar = trim($_GET['buscar']);
            }
        $sql_calificaciones = "
            SELECT
                calificaciones.id_calificacion,
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
        ";

        if ($evaluacion !== "") {
            $sql_calificaciones .= " AND calificaciones.evaluacion = '$evaluacion'";
        }
        if ($buscar != "") {
            $sql_calificaciones .= " AND (personas.nombre LIKE '%$buscar%' OR personas.apellido LIKE '%$buscar%')";
        }
        
        $sql_calificaciones .= " ORDER BY personas.apellido ASC";
    
        $resultado_calificaciones = mysqli_query($conexion,$sql_calificaciones);

        if (!$resultado_calificaciones) {
            die(
                "Error al consultar las calificaciones: " .
                mysqli_error($conexion)
            );
        }

    ?>

    <?php if (mysqli_num_rows($resultado_calificaciones) > 0) { ?>

        <h3>Calificaciones encontradas</h3>

         <?php
        if ($evaluacion != "") {
            echo "<p><strong>Evaluación:</strong> " . htmlspecialchars(ucwords(strtolower($evaluacion))) . "</p>";
        }
        ?>

        

        <?php

        $fila_encabezado = mysqli_fetch_assoc($resultado_calificaciones);

        $nombre_actividad_1 = "Nota 1";
        $nombre_actividad_2 = "Nota 2";

        if ($fila_encabezado['descripcion_nota_1'] != "") {
            $nombre_actividad_1 = ucwords(strtolower($fila_encabezado['descripcion_nota_1']));
        }

        if ($fila_encabezado['descripcion_nota_2'] != "") {
            $nombre_actividad_2 = ucwords(strtolower($fila_encabezado['descripcion_nota_2']));
        }

        mysqli_data_seek($resultado_calificaciones, 0);

        ?>

            <table border="1">

                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th><?php echo htmlspecialchars($nombre_actividad_1); ?></th>
                    <th><?php echo htmlspecialchars($nombre_actividad_2); ?></th>
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

    <div style="display:flex;justify-content:center;gap:15px;margin-top:20px;"><a href="imprimir_notas.php?id_nivel=<?php echo $id_nivel; ?>&evaluacion=<?php echo urlencode($evaluacion); ?>" target="_blank" style="background:#2563eb;color:white;padding:10px 18px;text-decoration:none;border-radius:8px;font-weight:bold;">🖨 Imprimir</a><a href="pdf_notas.php?id_nivel=<?php echo $id_nivel; ?>&evaluacion=<?php echo urlencode($evaluacion); ?>" target="_blank" style="background:#dc2626;color:white;padding:10px 18px;text-decoration:none;border-radius:8px;font-weight:bold;">Exportar PDF</a><a href="excel_notas.php?id_nivel=<?php echo $id_nivel; ?>&evaluacion=<?php echo urlencode($evaluacion); ?>" style="background:#16a34a;color:white;padding:10px 18px;text-decoration:none;border-radius:8px;font-weight:bold;">Exportar Excel</a></div>

        <?php } else { ?>

            <p>No se encontraron calificaciones para ese nivel y evaluación.</p>

        <?php } ?>

    <?php } ?>

</body>

</html>