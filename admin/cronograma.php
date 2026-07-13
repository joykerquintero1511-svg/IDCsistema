<?php
include('../session-start.php');
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
    <link rel="icon" type="image/png" href="../images/EFB.png">
    <!-- UNICA LLAMADA A TUS ESTILOS -->
    <link rel="stylesheet" href="../css/mystyle.css">
</head>
<body>

<!-- BARRA LATERAL ADMINISTRATIVA (SIDEBAR) -->
<div class="sidebar">
    <div class="sidebar-brand">
        <img src="../images/EFB.png" alt="EFB Logo" onerror="this.style.display='none'">
        <h2>Administrador</h2>
    </div>
    <ul class="menu-links">
        <li><a href="cronograma.php" class="active">Cronograma</a></li>
        <li><a href="periodos.php">Períodos Académicos</a></li>
        <li><a href="index.php">Volver al Inicio</a></li>
    </ul>
</div>

<!-- CONTENEDOR PRINCIPAL -->
<main class="main-content">
    
    <!-- CABECERA -->
    <div class="welcome-header">
        <h1>🗓️ Cronograma Global de Clases</h1>
        <p>
            Período del Sistema: 
            <?php if ($periodo_activo): ?>
                <span class="badge-nota badge-activo">
                    ✨ <?php echo htmlspecialchars($periodo_activo['nombre_periodo']); ?>
                </span>
            <?php else: ?>
                <span class="badge-nota badge-inactivo">
                    ⚠️ NINGUNO ACTIVO
                </span>
            <?php endif; ?>
        </p>
    </div>

    <!-- SECCIÓN DE ALERTAS PHP -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            ✔️ <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            ❌ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- REJILLA ASIMÉTRICA LIMPIA -->
    <div class="cronograma-grid">
        
        <!-- CUADRO 1 (IZQUIERDO): FORMULARIO VERTICAL COMPACTO -->
        <div class="info-card">
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

                <!-- FILA DOBLE ENTRADA Y SALIDA CON CLASE PROPIA -->
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

                <button type="submit" name="guardar_clase" class="btn-action btn-submit-full">
                    Publicar en Cronograma
                </button>
            </form>
        </div>

        <!-- CUADRO 2 (DERECHO): HISTORIAL AMPLIO -->
        <div class="info-card">
            <h3>Clases Programadas en este Lapso</h3>
            <div class="table-responsive">
                <?php if (isset($clases_programadas) && mysqli_num_rows($clases_programadas) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>NIVEL</th>
                                <th>MATERIA / TEMA</th>
                                <th>HORARIO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($clase = mysqli_fetch_assoc($clases_programadas)): ?>
                                <tr>
                                    <td><strong><?php echo date("d/m/Y", strtotime($clase['fecha'])); ?></strong></td>
                                    <td>
                                        <span class="text-highlight">
                                            <?php echo htmlspecialchars($clase['nivel_academico']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($clase['materia_tema']); ?></td>
                                    <td class="text-muted-dark">
                                        <?php 
                                            echo date("g:i A", strtotime($clase['hora_inicio'])) . " - " . date("g:i A", strtotime($clase['hora_fin'])); 
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-danger alert-no-margin">
                        ⚠️ Formulario listo. Aún no hay clases agendadas para este período.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

    <?php include '../script-seguridad.php'; ?>

</body>
</html>