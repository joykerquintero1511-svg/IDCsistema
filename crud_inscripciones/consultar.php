<?php
require_once('../conexion.php');

// Verificar si llegó el ID por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se especificó qué inscripción consultar.");
}

$id = $_GET['id'];

// Validar que el ID sea un número
if (!is_numeric($id)) {
    die("Error: ID inválido.");
}

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
    <title>Consultar Inscripción</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .card { border: 1px solid #ddd; padding: 20px; border-radius: 10px; max-width: 500px; background: #f9f9f9; }
        .campo { margin-bottom: 15px; }
        .label { font-weight: bold; display: inline-block; width: 150px; }
        .btn { padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px; }
        .btn-volver { background: gray; color: white; }
        .btn-editar { background: orange; color: white; }
    </style>
</head>
<body>
    <h1>🔍 Detalle de Inscripción</h1>
    <div class="card">
        <div class="campo">
            <span class="label">ID:</span>
            <span><?php echo $fila['id_inscripcion']; ?></span>
        </div>
        <div class="campo">
            <span class="label">ID Estudiante:</span>
            <span><?php echo $fila['id_estudiante']; ?></span>
        </div>
        <div class="campo">
            <span class="label">Nivel Académico:</span>
            <span><?php echo htmlspecialchars($fila['nivel_academico']); ?></span>
        </div>
        <div class="campo">
            <span class="label">Fecha Inscripción:</span>
            <span><?php echo $fila['fecha_inscripcion']; ?></span>
        </div>
        <div class="campo">
            <span class="label">Periodo:</span>
            <span><?php echo $fila['periodo']; ?></span>
        </div>
        <div class="campo">
            <span class="label">Estado:</span>
            <span><?php echo $fila['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></span>
        </div>
    </div>
    <br>
    <a href="listar.php" class="btn btn-volver">← Volver al listado</a>
    <a href="editar.php?id=<?php echo $fila['id_inscripcion']; ?>" class="btn btn-editar">✏️ Editar</a>
</body>
</html>