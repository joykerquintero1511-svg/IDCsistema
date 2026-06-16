<?php
require 'conexion.php';

// Verificamos que venga el ID por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_profesor = $_GET['id'];

try {
    // Buscamos al profesor de forma segura
    $stmt = $pdo->prepare("SELECT * FROM profesores WHERE id_profesor = ?");
    $stmt->execute([$id_profesor]);
    $profesor = $stmt->fetch();

    // Si no existe el profesor, redirigimos
    if (!$profesor) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error en el sistema: " . $e->getMessage());
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
            <h4 class="mb-0">Ficha del Profesor</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered m-0">
                <tr>
                    <th class="table-secondary" style="width: 35%;">Cédula:</th>
                    <td><?= htmlspecialchars($profesor['cedula']) ?></td>
                </tr>
                <tr>
                    <th class="table-secondary">Nombre:</th>
                    <td><?= htmlspecialchars($profesor['nombre']) ?></td>
                </tr>
                <tr>
                    <th class="table-secondary">Apellido:</th>
                    <td><?= htmlspecialchars($profesor['apellido']) ?></td>
                </tr>
                <tr>
                    <th class="table-secondary">Teléfono:</th>
                    <td><?= htmlspecialchars($profesor['telefono'] ?? 'No asignado') ?></td>
                </tr>
                <tr>
                    <th class="table-secondary">Registrado el:</th>
                    <td><?= htmlspecialchars($profesor['creado_en']) ?></td>
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