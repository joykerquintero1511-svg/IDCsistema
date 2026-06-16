<?php
require 'conexion.php';

// Operación: LISTAR (Ajustado a tu nueva tabla limpia)
$stmt = $pdo->query("SELECT * FROM profesores ORDER BY id_profesor DESC");
$profesores = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo de Profesores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Profesores</h2>
        <a href="registrar.php" class="btn btn-primary">Registrar Nuevo Profesor</a>
    </div>

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'registrado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ¡Profesor registrado exitosamente!
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-hover align-middle m-0">
                <thead class="table-dark">
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Teléfono</th>
                        <th class="text-center" style="width: 200px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($profesores) > 0): ?>
                        <?php foreach ($profesores as $profesor): ?>
                            <tr>
                                <td><?= htmlspecialchars($profesor['cedula']) ?></td>
                                <td><?= htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido']) ?></td>
                                <td><?= htmlspecialchars($profesor['telefono'] ?? 'No asignado') ?></td>
                                <td class="text-center">
                                    <a href="consultar.php?id=<?= $profesor['id_profesor'] ?>" class="btn btn-info btn-sm text-white">Consultar</a>
                                    <a href="actualizar.php?id=<?= $profesor['id_profesor'] ?>" class="btn btn-warning btn-sm">Actualizar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No hay profesores registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>