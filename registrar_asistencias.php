<?php
require_once 'conexion.php';
$mensaje = "";
$tipo_mensaje = "";

// PROCESAR EL FORMULARIO CUANDO SE ENVÍA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_clase = $_POST['fecha_clase'];
    $asistencias = $_POST['asistencia']; // Esto será un array con los estados
    $observaciones = $_POST['observaciones']; // Array con las notas

    // Empezamos una transacción para guardar todo de golpe
    $conn->begin_transaction();

    try {
        $sql_insert = "INSERT INTO asistencias (id_estudiante, fecha, estado, observaciones) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);

        // Recorremos el array de asistencias enviado por el formulario
        foreach ($asistencias as $id_estudiante => $estado) {
            $obs = $observaciones[$id_estudiante] ?? "";
            $stmt->bind_param("isss", $id_estudiante, $fecha_clase, $estado, $obs);
            $stmt->execute();
        }

        $conn->commit();
        $mensaje = "Asistencia registrada correctamente para la fecha: " . $fecha_clase;
        $tipo_mensaje = "success";

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $mensaje = "Error al guardar la asistencia: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
}

// OBTENER LA LISTA DE ESTUDIANTES PARA MOSTRARLA
$sql_estudiantes = "SELECT id_estudiante, nombre, apellido, cedula FROM estudiantes ORDER BY apellido ASC";
$resultado_estudiantes = $conn->query($sql_estudiantes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tomar Asistencia - Escuela Bíblica</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f9; padding: 20px; }
        .container { max-width: 800px; background: #fff; margin: 0 auto; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #1B365D; text-align: center; }
        .form-group { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #1B365D; color: white; }
        .radio-group label { margin-right: 15px; cursor: pointer; }
        input[type="date"], input[type="text"] { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 12px 25px; background-color: #1B365D; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 20px; width: 100%; font-weight: bold; }
        button:hover { background-color: #2C5E8A; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="container">
    <h2>Registro de Asistencia</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form action="registrar_asistencia.php" method="POST">
        <div class="form-group">
            <label for="fecha_clase"><strong>Fecha de la Clase:</strong></label>
            <input type="date" id="fecha_clase" name="fecha_clase" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Estudiante</th>
                    <th>Estado de Asistencia</th>
                    <th>Observación (Opcional)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_estudiantes && $resultado_estudiantes->num_rows > 0): ?>
                    <?php while ($row = $resultado_estudiantes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                            <td><?php echo htmlspecialchars($row['apellido'] . ", " . $row['nombre']); ?></td>
                            <td class="radio-group">
                                <label><input type="radio" name="asistencia[<?php echo $row['id_estudiante']; ?>]" value="Presente" checked> Presente</label>
                                <label><input type="radio" name="asistencia[<?php echo $row['id_estudiante']; ?>]" value="Ausente"> Ausente</label>
                                <label><input type="radio" name="asistencia[<?php echo $row['id_estudiante']; ?>]" value="Justificado"> Just.</label>
                            </td>
                            <td>
                                <input type="text" name="observaciones[<?php echo $row['id_estudiante']; ?>]" placeholder="Motivo...">
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay estudiantes registrados para mostrar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <button type="submit">Guardar Asistencias</button>
    </form>
</div>

</body>
</html>