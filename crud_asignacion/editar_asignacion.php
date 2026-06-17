<?php
// 1. Conectar a la base de datos
include("../conexion.php");

// 2. Verificar que llegue el ID por la URL
if (isset($_GET['id'])) {
    $id_asignacion = $_GET['id'];

    // 3. Consultar los datos (Cambiado a 'asignacion' en singular)
    $query = "SELECT * FROM asignacion WHERE id_asignacion = $id_asignacion"; 
    $resultado = mysqli_query($conexion, $query);

    if (!$resultado) {
        die("Error en la base de datos: " . mysqli_error($conexion));
    }

    if (mysqli_num_rows($resultado) == 1) {
        $tarea = mysqli_fetch_assoc($resultado);
    } else {
        echo "La asignación con ID " . htmlspecialchars($id_asignacion) . " no existe.";
        exit;
    }
} else {
    echo "ID no especificado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Asignación</title>
    <link rel="stylesheet" href="css/estilos.css"> 
</head>
<body>

<div class="contenedor-formulario">
    <h2>Modificar Asignación</h2>
    
    <form action="procesar_editar.php" method="POST">
        
        <input type="hidden" name="id_asignacion" value="<?php echo $tarea['id_asignacion']; ?>">

        <div class="grupo-campo">
            <label>Título de la Asignación:</label>
            <input type="text" name="titulo_tarea" value="<?php echo htmlspecialchars($tarea['titulo_tarea']); ?>" required>
        </div>

        <div class="grupo-campo">
            <label>Tema:</label>
            <input type="text" name="tema" value="<?php echo htmlspecialchars($tarea['tema']); ?>" required>
        </div>

        <div class="grupo-campo">
            <label>Fecha Límite:</label>
            <input type="date" name="fecha_limite" value="<?php echo $tarea['fecha_limite']; ?>" required>
        </div>

        <div class="botones-acciones">
            <button type="submit" class="btn-guardar">Guardar Cambios</button>
            <a href="index.php" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>