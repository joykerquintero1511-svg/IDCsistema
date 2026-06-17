<?php
require 'conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_profesor = $_GET['id'];

// Consulta tradicional con filtro WHERE
$sql = "SELECT * FROM profesores WHERE id_profesor = '$id_profesor'";
$resultado = mysqli_query($conexion, $sql);
$profesor = mysqli_fetch_assoc($resultado);

// Si el profesor no existe en la BD
if (!$profesor) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Profesor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Ficha del Profesor (Tradicional)</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered m-0">
                <tr>
                    <th class="table-secondary" style="width: 35%;">Cédula:</th>
                    <td><?php echo $profesor['cedula']; ?></td>
                </tr>
                <tr>
                    <th class="table-secondary">Nombre:</th>
                    <td><?php echo $profesor['nombre']; ?></td>
                </tr>
                <tr>
                    <th class="table-secondary">Apellido:</th>
                    <td><?php echo $profesor['apellido']; ?></td>
                </tr>
                <tr>
                    <th class="table-secondary">Teléfono:</th>
                    <td><?php echo $profesor['telefono'] ? $profesor['telefono'] : 'No asignado'; ?></td>
                </tr>
                <tr>
                    <th class="table-secondary">Registrado el:</th>
                    <td><?php echo $profesor['creado_en']; ?></td>
                </tr>
            </table>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-secondary">Volver al Listado</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>