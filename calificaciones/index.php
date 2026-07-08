<?php
require_once '../conexion.php';

$sql =" SELECT id_nivel,nivel_academico 
        FROM niveles";

$resultado = mysqli_query($conexion,$sql); // mysqli_query "Ejecuta la consulta que está en $sql usando la conexión $conexion y guarda el resultado en $resultado."

    if(isset($_GET['id_nivel'])){
        $id_nivel = $_GET['id_nivel'];
        $evaluacion = $_GET['evaluacion'];
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
    <h2>Registrar Califiicaciones</h2>
    <form method="GET">

        <label>Nivel:</label>
        <select name="id_nivel" required>

            <option value="">Seleccione un nivel</option>

    <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

    <option value="<?php echo $fila['id_nivel']; ?>">
            <?php echo $fila['nivel_academico'] ?>
     </option>

<?php } ?>

        </select>
        
<br><br>

    <label>Nombre de la evaluacion</label>

    <input type= "text" name="evaluacion" required>

<br><br>
    <button type="submit">Buscar estudiantes</button>

    </form>

    
</body>
</html>

