<?php
require_once('../conexion.php');

// Verificar si llegó el ID por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se especificó qué inscripción editar.");
}

$id = $_GET['id'];

// Validar que el ID sea un número
if (!is_numeric($id)) {
    die("Error: ID inválido.");
}

// Procesar el formulario cuando se envía (ACTUALIZAR)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nivel_academico = $_POST['nivel_academico'];
    $fecha_inscripcion = $_POST['fecha_inscripcion'];
    $periodo = $_POST['periodo'];
    $estado = $_POST['estado'];

    $sql = "UPDATE inscripciones SET 
            nivel_academico = '$nivel_academico',
            fecha_inscripcion = '$fecha_inscripcion',
            periodo = '$periodo',
            estado = '$estado'
            WHERE id_inscripcion = $id";
    
    if (mysqli_query($conexion, $sql)) {
        header("Location: listar.php");
        exit;
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}

// Cargar los datos actuales de la inscripción (para mostrar en el formulario)
$sql = "SELECT * FROM inscripciones WHERE id_inscripcion = $id";
$resultado = ejecutarConsulta($conexion, $sql);
$fila = mysqli_fetch_assoc($resultado);

if (!$fila) {
    die("Inscripción no encontrada");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Inscripción</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        label { display: inline-block; width: 150px; margin-bottom: 10px; }
        input, select { padding: 5px; width: 200px; margin-bottom: 10px; }
        button { padding: 10px 20px; background: orange; color: white; border: none; cursor: pointer; border-radius: 5px; }
        .cancelar { margin-left: 10px; text-decoration: none; background: gray; color: white; padding: 10px 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>✏️ Editar Inscripción</h1>
    <form method="POST">
        <label>Nivel Académico:</label>
        <input type="text" name="nivel_academico" value="<?php echo htmlspecialchars($fila['nivel_academico']); ?>" required><br>
        
        <label>Fecha Inscripción:</label>
        <input type="date" name="fecha_inscripcion" value="<?php echo $fila['fecha_inscripcion']; ?>" required><br>
        
        <label>Periodo:</label>
        <input type="text" name="periodo" value="<?php echo $fila['periodo']; ?>" required><br>
        
        <label>Estado:</label>
        <select name="estado">
            <option value="1" <?php echo ($fila['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
            <option value="0" <?php echo ($fila['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
        </select><br><br>
        
        <button type="submit">💾 Actualizar</button>
        <a href="listar.php" class="cancelar">Cancelar</a>
    </form>
</body>
</html>