<?php
include '../conexion.php'; // Ajusta la ruta según tu estructura de archivos

// 1. OBTENER EL PERÍODO ACADÉMICO ACTIVO ACTUALMENTE
$sql_periodo = "SELECT * FROM periodos_academicos WHERE estado = 1 LIMIT 1";
$res_periodo = mysqli_query($conexion, $sql_periodo);
$periodo_activo = mysqli_fetch_assoc($res_periodo);

// 2. LÓGICA PARA REGISTRAR UNA NUEVA CLASE
if (isset($_POST['guardar_clase'])) {
    if (!$periodo_activo) {
        $error = "No puedes programar clases si no hay un Período Académico activo. Ve al gestor de períodos.";
    } else {
        $id_periodo = $periodo_activo['id_periodo'];
        $id_nivel = intval($_POST['id_nivel']);
        $materia_tema = mysqli_real_escape_string($conexion, $_POST['materia_tema']);
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];

        $sql_insert = "INSERT INTO cronograma_clases (id_periodo, id_nivel, materia_tema, fecha, hora_inicio, hora_fin) 
                       VALUES ($id_periodo, $id_nivel, '$materia_tema', '$fecha', '$hora_inicio', '$hora_fin')";
        
        if (mysqli_query($conexion, $sql_insert)) {
            header("Location: cronograma.php?msg=Clase programada con éxito");
            exit();
        } else {
            $error = "Error al registrar la clase: " . mysqli_error($conexion);
        }
    }
}

// 3. CONSULTAR LOS NIVELES PARA EL MENÚ DESPLEGABLE
$res_niveles = mysqli_query($conexion, "SELECT * FROM niveles ORDER BY id_nivel ASC");

// 4. CONSULTAR LAS CLASES YA PROGRAMADAS DEL PERÍODO ACTIVO
$clases_programadas = null;
if ($periodo_activo) {
    $id_p_activo = $periodo_activo['id_periodo'];
    // Ajustado con 'nivel_academico' según la estructura de tu base de datos
    $sql_clases = "SELECT c.*, n.nivel_academico 
                   FROM cronograma_clases c
                   INNER JOIN niveles n ON c.id_nivel = n.id_nivel
                   WHERE c.id_periodo = $id_p_activo
                   ORDER BY c.fecha ASC, c.hora_inicio ASC";
    $clases_programadas = mysqli_query($conexion, $sql_clases);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cronograma de Clases - Administrador</title>
    <link rel="stylesheet" href="../estilos/style.css">
    <style>
        body { background-color: #0c0c0c; color: #ffffff; font-family: 'Segoe UI', sans-serif; margin: 0; }
        .main-content { margin-left: 260px; padding: 2.5rem; box-sizing: border-box; }
        .header-title { font-size: 1.8rem; margin-bottom: 0.5rem; }
        .subtitle { color: #888888; margin-top: 0; margin-bottom: 2rem; font-size: 1rem; }
        .periodo-tag { background-color: rgba(40, 167, 69, 0.2); color: #28a745; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: bold; }
        
        .grid-layout { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; }
        .card { background-color: #111111; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.4); height: fit-content; }
        .card h3 { margin-top: 0; margin-bottom: 1.5rem; color: #3a7bc8; }
        
        /* Formulario */
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #aaaaaa; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 0.7rem; background-color: #1a1a1a; border: 1px solid #333; border-radius: 6px; color: #fff; box-sizing: border-box; font-family: inherit; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .btn-submit { background-color: #3a7bc8; color: #fff; border: none; padding: 0.7rem 1.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%; margin-top: 0.5rem; }
        .btn-submit:hover { background-color: #2e62a1; }
        
        /* Tablas */
        .table-custom { width: 100%; border-collapse: collapse; text-align: left; }
        .table-custom th { padding: 0.8rem; background-color: #1a1a1a; color: #888; font-size: 0.85rem; text-transform: uppercase; }
        .table-custom td { padding: 1rem 0.8rem; border-bottom: 1px solid #222; font-size: 0.95rem; }
        
        .alert { padding: 0.8rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.95rem; }
        .alert-success { background-color: rgba(40, 167, 69, 0.15); color: #28a745; border: 1px solid #28a745; }
        .alert-danger { background-color: rgba(255, 85, 85, 0.15); color: #ff5555; border: 1px solid #ff5555; }
        .alert-warning { background-color: rgba(255, 193, 7, 0.15); color: #ffc107; border: 1px solid #ffc107; }
    </style>
</head>
<body>

<main class="main-content">
    <h1 class="header-title">📅 Cronograma Global de Clases</h1>
    <p class="subtitle">
        Período del Sistema: 
        <?php if ($periodo_activo): ?>
            <span class="periodo-tag">✨ <?php echo htmlspecialchars($periodo_activo['nombre_periodo']); ?></span>
        <?php else: ?>
            <span class="alert-danger" style="padding: 0.2rem 0.5rem; border-radius:4px;">⚠️ NINGUNO ACTIVO</span>
        <?php endif; ?>
    </p>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">✔️ <?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <div class="grid-layout">
        <!-- FORMULARIO PARA REGISTRAR CLASE -->
        <div class="card">
            <h3>Programar Nueva Clase</h3>
            <form action="cronograma.php" method="POST">
                
                <div class="form-group">
                    <label for="id_nivel">Seleccionar Nivel Académico</label>
                    <select id="id_nivel" name="id_nivel" class="form-control" required>
                        <option value="">-- Seleccione un nivel --</option>
                        <?php while ($nivel = mysqli_fetch_assoc($res_niveles)): ?>
                            <option value="<?php echo $nivel['id_nivel']; ?>">
                                <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="materia_tema">Materia o Tema de la Clase</label>
                    <input type="text" id="materia_tema" name="materia_tema" class="form-control" placeholder="Ej: Introducción al Evangelismo" required>
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha de la Clase</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="hora_inicio">Hora de Entrada</label>
                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="hora_fin">Hora de Salida</label>
                        <input type="time" id="hora_fin" name="hora_fin" class="form-control" required>
                    </div>
                </div>

                <button type="submit" name="guardar_clase" class="btn-submit">Publicar en Cronograma</button>
            </form>
        </div>

        <!-- TABLA CON LA AGENDA ACTUAL -->
        <div class="card">
            <h3>Clases Programadas en este Lapso</h3>
            <?php if ($clases_programadas && mysqli_num_rows($clases_programadas) > 0): ?>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nivel</th>
                            <th>Materia / Tema</th>
                            <th>Horario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($clase = mysqli_fetch_assoc($clases_programadas)): ?>
                            <tr>
                                <td><strong><?php echo date("d/m/Y", strtotime($clase['fecha'])); ?></strong></td>
                                <td><span style="color: #3a7bc8; font-weight:600;"><?php echo htmlspecialchars($clase['nivel_academico']); ?></span></td>
                                <td><?php echo htmlspecialchars($clase['materia_tema']); ?></td>
                                <td>
                                    <?php 
                                        echo date("g:i A", strtotime($clase['hora_inicio'])) . " - " . date("g:i A", strtotime($clase['hora_fin'])); 
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning" style="margin-bottom:0;">Formulario listo. Aún no hay clases agendadas para este período.</div>
            <?php endif; ?>
        </div>
    </div>
</main>

</body>
</html>