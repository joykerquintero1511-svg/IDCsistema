<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";

// PROCESAR FORMULARIO AL HACER POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_nivel = mysqli_real_escape_string($conexion, $_POST['id_nivel']);
    $titulo_tarea = mysqli_real_escape_string($conexion, $_POST['titulo_tarea']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $tema = mysqli_real_escape_string($conexion, $_POST['tema']);
    $fecha_limite = mysqli_real_escape_string($conexion, $_POST['fecha_limite']);

    if (!empty($id_nivel) && !empty($titulo_tarea) && !empty($fecha_limite)) {
        $insert_query = "INSERT INTO asignacion (id_nivel, titulo_tarea, descripcion, tema, fecha_limite) 
                         VALUES ('$id_nivel', '$titulo_tarea', '$descripcion', '$tema', '$fecha_limite')";
        
        if (mysqli_query($conexion, $insert_query)) {
            $mensaje = "<div style='color: #2ea043; background: #161b22; padding: 15px; border: 1px solid #2ea043; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;'>✓ Asignación publicada exitosamente en el nivel correspondiente.</div>";
        } else {
            $mensaje = "<div style='color: #f85149; background: #161b22; padding: 15px; border: 1px solid #f85149; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;'>Error en la base de datos: " . mysqli_error($conexion) . "</div>";
        }
    } else {
        $mensaje = "<div style='color: #ff9e2c; background: #161b22; padding: 15px; border: 1px solid #ff9e2c; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;'>⚠️ Por favor, rellene todos los campos obligatorios.</div>";
    }
}

// Obtener los niveles para el combobox
$query_niveles = "SELECT id_nivel, nombre_nivel FROM niveles ORDER BY id_nivel ASC";
$result_niveles = mysqli_query($conexion, $query_niveles);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Asignación - EFB</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #0b0b0b; color: #e0e0e0; display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background-color: #111111; padding: 30px 20px; display: flex; flex-direction: column; justify-content: space-between; border-right: 1px solid #1a1a1a; position: fixed; height: 100vh; }
        .menu-item { display: block; color: #888888; text-decoration: none; padding: 12px 15px; border-radius: 6px; font-size: 0.95rem; margin-bottom: 8px; }
        .menu-item:hover, .menu-item.active { background-color: #1a1a1a; color: #ffffff; }
        .menu-item.active { background-color: #161b22; color: #58a6ff; font-weight: 600; }

        .main-content { margin-left: 260px; flex-grow: 1; padding: 40px; }
        .card { background-color: #121212; border: 1px solid #1c1c1c; border-radius: 8px; padding: 30px; max-width: 700px; }
        .card h3 { font-size: 1.1rem; color: #ffffff; margin-bottom: 25px; border-bottom: 1px solid #1c1c1c; padding-bottom: 12px; }

        /* FORMULARIOS ESTILO OSCURO PREMIUM */
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.9rem; color: #aaaaaa; font-weight: 500; }
        .form-control { background-color: #161b22; border: 1px solid #30363d; border-radius: 6px; padding: 10px 12px; color: #ffffff; font-size: 0.95rem; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: #58a6ff; }
        textarea.form-control { resize: vertical; min-height: 100px; }
        
        .btn-submit { background-color: #238636; color: #ffffff; padding: 12px 20px; border: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        .btn-submit:hover { background-color: #2ea043; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="menu-group">
            <div class="brand">Profesor EFB</div>
            <a href="index.php" class="menu-item">Inicio</a>
            <a href="crear_asignacion.php" class="menu-item active">Subir Asignación</a>
            <a href="calificar.php" class="menu-item">Calificar Tareas</a>
            <a href="asistencia.php" class="menu-item">Control de Asistencia</a>
        </div>
        <a href="../logout.php" style="color: #ff4444; text-decoration: none; padding: 10px 15px;">Cerrar Sesión</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h3>Publicar Nueva Asignación Académica</h3>
            
            <?php echo $mensaje; ?>

            <form action="crear_asignacion.php" method="POST">
                <div class="form-group">
                    <label for="id_nivel">Nivel Académico (Destinatarios) *</label>
                    <select name="id_nivel" id="id_nivel" class="form-control" required>
                        <option value="">-- Seleccione el nivel --</option>
                        <?php while($row = mysqli_fetch_assoc($result_niveles)): ?>
                            <option value="<?php echo $row['id_nivel']; ?>">
                                <?php echo htmlspecialchars($row['nombre_nivel']); ?>
                            </option>
                        <?php endstyle; ?>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tema">Tema Doctrinal / Unidad</label>
                    <select name="tema" id="tema" class="form-control">
                        <option value="General">General / Introducción</option>
                        <option value="Cristología">Cristología</option>
                        <option value="Bibliología">Bibliología</option>
                        <option value="Teología Doctrinal">Teología Doctrinal</option>
                        <option value="Eclesiología">Eclesiología</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="titulo_tarea">Título de la Asignación *</label>
                    <input type="text" name="titulo_tarea" id="titulo_tarea" class="form-control" placeholder="Ej: Resumen analítico del Capítulo 1" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Instrucciones detalladas</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Escriba las pautas, preguntas o lecturas recomendadas..."></textarea>
                </div>

                <div class="form-group">
                    <label for="fecha_limite">Fecha Límite de Entrega *</label>
                    <input type="date" name="fecha_limite" id="fecha_limite" class="form-control" required>
                </div>

                <button type="submit" class="btn-submit">Publicar Asignación</button>
            </form>
        </div>
    </div>

</body>
</html>