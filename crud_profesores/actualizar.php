<?php
require 'conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_profesor = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM profesores WHERE id_profesor = ?");
    $stmt->execute([$id_profesor]);
    $profesor = $stmt->fetch();

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
    <title>Actualizar Profesor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Modificar Datos del Profesor</h4>
        </div>
        <div class="card-body">
            
            <form action="procesar_actualizacion.php" method="POST">
                <input type="hidden" name="id_profesor" value="<?= $profesor['id_profesor'] ?>">

                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula de Identidad</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" value="<?= htmlspecialchars($profesor['cedula']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($profesor['nombre']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($profesor['apellido']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($profesor['telefono']) ?>">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-warning">Actualizar Datos</button>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>