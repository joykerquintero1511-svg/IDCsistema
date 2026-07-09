<?php


require_once '../conexion.php';

$sql =" SELECT id_nivel,nivel_academico 
        FROM niveles";

$resultado = mysqli_query($conexion,$sql); // mysqli_query "Ejecuta la consulta que está en $sql usando la conexión $conexion y guarda el resultado en $resultado."

    if(isset($_GET['id_nivel'])){

        $id_nivel = $_GET['id_nivel'];
        $evaluacion = $_GET['evaluacion'];

        $sql_nivel_seleccionado = "
        SELECT nivel_academico
        FROM niveles
        WHERE id_nivel = $id_nivel
";
$resultado_nivel = mysqli_query($conexion, $sql_nivel_seleccionado);
   $fila_nivel = mysqli_fetch_assoc($resultado_nivel);    
$nombre_nivel = $fila_nivel['nivel_academico'];


        $sql_estudiantes = "
            SELECT 
                estudiantes.id_estudiante,
                personas.nombre,
                personas.apellido
            FROM estudiantes
            INNER JOIN personas
                ON estudiantes.id_persona = personas.id_persona
            WHERE estudiantes.id_nivel = $id_nivel
            ORDER BY personas.apellido ASC
";

$resultado_estudiantes = mysqli_query($conexion, $sql_estudiantes);

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Calificaciones</title>
</head>
<body>
    <h2>Registrar Calificaciones</h2>
    <form method="GET">

        <label>Nivel:</label>
        <select name="id_nivel" required>

            <option value="">Seleccione un nivel</option>

    <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

    <option value="<?php echo $fila['id_nivel']; ?>"
             <?php
                if (isset($id_nivel) && $id_nivel == $fila['id_nivel']) {
                 echo "selected";
             }
    ?>
>
    <?php echo $fila['nivel_academico']; ?>
</option>
<?php } ?>

        </select>
        
<br><br>

    <label>Nombre de la evaluacion</label>
    <?php 
    $textoEvaluacion ="";

    if(isset($evaluacion)){
        $textoEvaluacion = htmlspecialchars(ucwords(strtolower($evaluacion)));

    }
?>
      <input type="text" name="evaluacion" value="<?php echo $textoEvaluacion;?>"required>
       

<br><br>
    <button type="submit">Buscar estudiantes</button>

    </form>
<?php if (isset($resultado_estudiantes)) { ?>

    <h3>Estudiantes encontrados</h3>
    <p>
    <strong>Nivel:</strong>
    <?php echo htmlspecialchars($nombre_nivel); ?>
</p>

<p>
    <strong>Evaluación:</strong>
    <?php echo htmlspecialchars(ucwords(strtolower($evaluacion))); ?>
</p>

    <form method="POST" action="guardar_notas.php">

        <input type="hidden" name="id_nivel" value="<?php echo $id_nivel; ?>">
        <input type="hidden" name="evaluacion" value="<?php echo htmlspecialchars($evaluacion); ?>">

        <table border="1">
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Nota 1</th>
                <th>Nota 2</th>
                <th>Nota Final</th>
                <th>Observación</th>
            </tr>

            <?php while($estudiante = mysqli_fetch_assoc($resultado_estudiantes)) { ?>

                <tr>
                    <td><?php echo htmlspecialchars($estudiante['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($estudiante['apellido']); ?></td>

                    <td>
                        <input type="number" name="nota_1[<?php echo $estudiante['id_estudiante']; ?>]" min="0" max="20" step="0.01" required>
                    </td>

                    <td>
                        <input type="number" name="nota_2[<?php echo $estudiante['id_estudiante']; ?>]" min="0" max="20" step="0.01">
                    </td>

                    <td>
                        <input type="number" name="nota_final[<?php echo $estudiante['id_estudiante']; ?>]" min="0" max="20" step="0.01" required>
                    </td>

                    <td>
                        <input type="text" name="observacion[<?php echo $estudiante['id_estudiante']; ?>]">
                    </td>
                </tr>

            <?php } ?>

        </table>

        <br>

        <button type="submit">Guardar notas</button>

    </form>

<?php } ?>
    
</body>
</html>

