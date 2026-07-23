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

    $id_calificacion = $_GET['id_calificacion'];

    $sql = "
    SELECT

    calificaciones.id_calificacion,
    calificaciones.id_estudiante,
    calificaciones.id_nivel,
    calificaciones.evaluacion,
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

    $resultado = mysqli_query($conexion,$sql);
    $fila = mysqli_fetch_assoc($resultado);
?>

   <!DOCTYPE html>
   <html lang="es">
   <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Calificacion</title>
   </head>
   <body>
        <h2>Editar Calificacion</h2>
    <form method="POST" action="actualizar_notas.php">

    <input type="hidden" name="id_calificacion" value="<?php echo $fila['id_calificacion']; ?>">
    <input type="hidden" name="id_nivel" value="<?php echo $fila['id_nivel']; ?>">
    <input type="hidden" name="evaluacion" value="<?php echo htmlspecialchars($fila['evaluacion']); ?>">

        <label>Nota 1:</label>

        <input type="number" name="nota_1" value="<?php echo $fila['nota_1']; ?>" min="0" max="20" step="0.01" required>
    
    <br><br>
        
     <label>Nota 2:</label>

        <input type="number" name="nota_2" value="<?php echo $fila['nota_2']; ?>" min="0" max="20" step="0.01">

     <br><br>
        
    <label>Nota Final:</label>

        <input type="number" name="nota_final" value="<?php echo $fila['nota_final']; ?>" min="0" max="20" step="0.01" required>

     <br><br>

     <label>Observación:</label>

         <textarea name="observacion"><?php echo htmlspecialchars($fila['observacion']); ?></textarea>
 <!-- <textarea> porque una observación puede ser más larga.-->
    <br><br>
    
            <button type="submit">Guardar cambios</button>
    <br><br>

<a href="imprimir_notas.php?id_calificacion=<?php echo $fila['id_calificacion']; ?>" target="_blank">Imprimir esta calificación</a>        

</form>

</body>
</html>

    </form>
    
   </body>
   </html>