<?php
require_once '../conexion.php';

if (isset($_GET['id_calificacion'])) {
    $id_calificacion = $_GET['id_calificacion'];
} else {
    $id_nivel = $_GET['id_nivel'];
    $evaluacion = $_GET['evaluacion'];
}

// Consulta
       // Consulta

if (isset($id_calificacion)) {

    $sql = "

    SELECT

    personas.nombre,

    personas.apellido,

    calificaciones.nota_1,

    calificaciones.nota_2,

    calificaciones.nota_final,

    calificaciones.observacion,

    calificaciones.id_nivel,

    calificaciones.evaluacion

    FROM calificaciones

    INNER JOIN estudiantes
    ON calificaciones.id_estudiante = estudiantes.id_estudiante

    INNER JOIN personas
    ON estudiantes.id_persona = personas.id_persona

    WHERE calificaciones.id_calificacion = '$id_calificacion'

    ";

} else {

    $sql = "

    SELECT

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

    WHERE

    calificaciones.id_nivel = '$id_nivel'

    AND

    calificaciones.evaluacion = '$evaluacion'

    ORDER BY personas.apellido ASC

    ";

}
  $resultado = mysqli_query($conexion,$sql);

    if (isset($id_calificacion)) {

    $fila_datos = mysqli_fetch_assoc($resultado);

    if (!$fila_datos) {
    die("No se encontró la calificación.");
}

    $id_nivel = $fila_datos['id_nivel'];

    $evaluacion = $fila_datos['evaluacion'];

    $resultado = mysqli_query($conexion, $sql);
}
  
        $sql_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel'";
        $resultado_nivel = mysqli_query($conexion, $sql_nivel);
        $fila_nivel = mysqli_fetch_assoc($resultado_nivel);
        $nombre_nivel = $fila_nivel['nivel_academico'];
?>

  <!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 

    <title>Imprimir Calificaciones</title> <!-- @media print:  oculta el botón en la hoja impresa. --> 
        <style > 
    @media print { 
        button {
            display: none;
        } 
    }
    
    </style>
</head>

<body>

        <h2>Reporte de Calificaciones</h2>

        <p><strong>Nivel:</strong> <?php echo htmlspecialchars($nombre_nivel); ?></p>

        <p><strong>Evaluación:</strong> <?php echo htmlspecialchars(ucwords(strtolower($evaluacion))); ?></p>

<hr>

    <table border="1">

    <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Nota 1</th>
        <th>Nota 2</th>
        <th>Nota Final</th>
        <th>Observación</th>
    </tr>

        <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

    <tr>

        <td><?php echo htmlspecialchars(ucwords(strtolower($fila['nombre']))); ?></td>

        <td><?php echo htmlspecialchars(ucwords(strtolower($fila['apellido']))); ?></td>

        <td><?php echo htmlspecialchars($fila['nota_1']); ?></td>

        <td>

    <?php

    if ($fila['nota_2'] === null) {
        echo "No registrada";
    } else {
        echo htmlspecialchars($fila['nota_2']);
    }

    ?>

</td>
        <td><?php echo htmlspecialchars($fila['nota_final']); ?></td>

<td>

    <?php

    if ($fila['observacion'] === null || $fila['observacion'] === "") {
        echo "Sin observación";
    } else {
        echo htmlspecialchars(ucwords(strtolower($fila['observacion'])));
    }

    ?>

</td>
        </tr>

    <?php } ?>

</table>

    <br><br>

        <button onclick="window.print()">
            Imprimir
</button>

</body>
</html>
