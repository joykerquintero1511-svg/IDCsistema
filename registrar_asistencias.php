<?php
// 1. Conexión a la base de datos
require_once 'conexion.php';

$mensaje = "";
$tipo_mensaje = "";

// 2. Procesar el formulario al guardar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_asistencia'])) {
    $fecha_clase = $_POST['fecha_clase'] ?? '';
    $asistencias = $_POST['asistencia'] ?? []; 
    $observaciones = $_POST['observaciones'] ?? []; 

    if (empty($fecha_clase)) {
        $mensaje = "Seleccione una fecha válida.";
        $tipo_mensaje = "error";
    } elseif (empty($asistencias)) {
        $mensaje = "No hay datos de asistencia.";
        $tipo_mensaje = "error";
    } else {
        mysqli_autocommit($conexion, FALSE);
        $error_flag = false;

        $sql_insert = "INSERT INTO asistencias (id_estudiante, fecha, estado, observaciones) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql_insert);

        if ($stmt) {
            foreach ($asistencias as $id_alumno => $estado) {
                $obs = isset($observaciones[$id_alumno]) ? trim($observaciones[$id_alumno]) : "";
                mysqli_stmt_bind_param($stmt, "isss", $id_alumno, $fecha_clase, $estado, $obs);
                if (!mysqli_stmt_execute($stmt)) { $error_flag = true; break; }
            }
            mysqli_stmt_close($stmt);
        } else { $error_flag = true; }

        if (!$error_flag) {
            mysqli_commit($conexion);
            $mensaje = "Asistencia registrada: " . date('d-m-Y', strtotime($fecha_clase));
            $tipo_mensaje = "success";
        } else {
            mysqli_rollback($conexion);
            $mensaje = "Error al guardar: " . mysqli_error($conexion);
            $tipo_mensaje = "error";
        }
        mysqli_autocommit($conexion, TRUE);
    }
}

// 3. Consulta con INNER JOIN para traer nombres reales
$sql_estudiantes = "SELECT e.id_estudiante, p.nombre, p.apellido, p.cedula 
                    FROM estudiantes e 
                    INNER JOIN personas p ON e.id_persona = p.id_persona 
                    ORDER BY p.apellido ASC";
$resultado_estudiantes = mysqli_query($conexion, $sql_estudiantes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Asistencias</title>
     <link rel="icon" type="image/png" href="images/EFB.png">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-dark: #1e293b;
            --accent: #3b82f6;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --success: #22c55e;
            --error: #ef4444;
        }
        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg-dark); color: var(--text-primary); margin: 0; }
        
        /* Header con Logo */
        header { 
            background-color: #020617; 
            padding: 15px 40px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            border-bottom: 1px solid #334155;
            position: sticky; top: 0; z-index: 100;
        }
        .logo-container { display: flex; align-items: center; gap: 15px; }
        .logo-container img { height: 50px; border-radius: 5px; }
        .logo-container span { font-size: 20px; font-weight: bold; letter-spacing: 1px; }

        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .card { background: var(--card-dark); border-radius: 12px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid #334155; }
        
        h2 { margin-top: 0; color: var(--accent); font-size: 28px; }
        .form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        
        input[type="date"] { background: #0f172a; border: 1px solid #334155; color: white; padding: 10px; border-radius: 6px; font-size: 16px; outline: none; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #0f172a; border-radius: 8px; overflow: hidden; }
        th { background: #334155; color: var(--text-primary); padding: 15px; text-align: left; text-transform: uppercase; font-size: 13px; }
        td { padding: 15px; border-bottom: 1px solid #1e293b; color: var(--text-secondary); }
        tr:hover { background: #1e293b; }
        
        .status-options { display: flex; gap: 12px; }
        .status-options label { font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 5px; color: var(--text-primary); }
        input[type="text"] { background: #1e293b; border: 1px solid #334155; color: white; padding: 8px; border-radius: 4px; width: 100%; }

        .btn-save { background: var(--accent); color: white; border: none; padding: 15px; border-radius: 8px; width: 100%; font-size: 18px; font-weight: bold; cursor: pointer; margin-top: 30px; transition: 0.3s; }
        .btn-save:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4); }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; text-align: center; }
        .alert-success { background: rgba(34, 197, 94, 0.2); border: 1px solid var(--success); color: var(--success); }
        .alert-error { background: rgba(239, 68, 68, 0.2); border: 1px solid var(--error); color: var(--error); }
    </style>
</head>
<body>

<header>
    <div class="logo-container">
        <img src="images/EFB.png" alt="IDC Logo">
        <span>Control de Asistencias</span>
    </div>
</header>

<div class="container">
    <div class="card">
        <form action="registrar_asistencias.php" method="POST">
            <div class="form-header">
                <h2>Asistencia</h2>
                <input type="date" name="fecha_clase" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Cédula</th>
                        <th>Estado de Asistencia</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado_estudiantes)): ?>
                        <tr>
                            <td style="color:white; font-weight:bold;"><?php echo $row['apellido'] . " " . $row['nombre']; ?></td>
                            <td><?php echo $row['cedula']; ?></td>
                            <td>
                                <div class="status-options">
                                    <label><input type="radio" name="asistencia[<?php echo $row['id_estudiante']; ?>]" value="Presente" checked> P</label>
                                    <label><input type="radio" name="asistencia[<?php echo $row['id_estudiante']; ?>]" value="Ausente"> A</label>
                                    <label><input type="radio" name="asistencia[<?php echo $row['id_estudiante']; ?>]" value="Justificado"> J</label>
                                </div>
                            </td>
                            <td><input type="text" name="observaciones[<?php echo $row['id_estudiante']; ?>]" placeholder="..."></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <button type="submit" name="guardar_asistencia" class="btn-save">Guardar</button>
        </form>
    </div>
</div>

</body>
</html>