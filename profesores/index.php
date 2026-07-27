<?php
include('../session-start.php');
include("../conexion.php");

// 1. CONTROL DE SEGURIDAD: Solo profesores
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit();
}

// 2. CAPTURAR DATOS DE LA SESIÓN
$id_usuario = $_SESSION['id_usuario'];
$nombre_profesor = $_SESSION['usuario'] ?? 'Profesor';

// 3. CONSULTA: Obtener el ID real del profesor y su nivel académico asignado
$query_prof = "SELECT p.id_profesor, p.id_nivel, n.nivel_academico 
               FROM profesores p 
               LEFT JOIN niveles n ON p.id_nivel = n.id_nivel 
               WHERE p.id_usuario = '$id_usuario'";
$res_prof = mysqli_query($conexion, $query_prof);
$id_profesor = 0;
$nivel_asignado = "Sin nivel asignado";

if ($res_prof && $row_prof = mysqli_fetch_assoc($res_prof)) {
    $id_profesor = $row_prof['id_profesor'];
    if (!empty($row_prof['nivel_academico'])) {
        $nivel_asignado = $row_prof['nivel_academico'];
    }
}

// 4. CONSULTA 1: Últimas asignaciones creadas por este profesor
$query_tareas = "SELECT a.id_asignacion, a.titulo_tarea, a.tema, a.fecha_limite, n.nivel_academico 
                 FROM asignacion a
                 INNER JOIN niveles n ON a.id_nivel = n.id_nivel
                 ORDER BY a.id_asignacion DESC LIMIT 5"; 
$result_tareas = mysqli_query($conexion, $query_tareas);

// 5. CONSULTAR 2: Clases/Encuentros en el cronograma (incluyendo modalidad)
$query_clases = "SELECT c.materia_tema, c.fecha, c.hora_inicio, c.hora_fin, n.nivel_academico 
                 FROM cronograma_clases c
                 INNER JOIN niveles n ON c.id_nivel = n.id_nivel
                 ORDER BY c.fecha ASC";
$result_clases = mysqli_query($conexion, $query_clases);

// Si entra al panel de profesor
$_SESSION['panel_regreso'] = 'index.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Profesor - EFB</title>
    <link rel="icon" type="image/png" href="../images/EFB.png">
    <link rel="stylesheet" href="../css/mystyle.css">
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <img src="../images/EFB.png" alt="Logo">
                <h2>Profesor</h2>
            </div>
            <ul class="menu-links">
                <li><a href="index.php" class="active">Inicio</a></li>
                <li><a href="../validar.php">Validar Asistencia y QR</a></li>
                <li><a href="registrar_asistencias.php">Registrar Asistencias</a></li>
                <li><a href="crear_asignacion.php">Nueva Asignación</a></li>
                <li><a href="../calificaciones/index.php">Calificaciones</a></li>
            </ul>
        </div>
        <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
    </aside>

    <main class="main-content">
        <header class="welcome-header">
            <h1>Bienvenido Profesor, <?php echo htmlspecialchars($nombre_profesor); ?></h1>
            <!-- AQUÍ SE REFLEJA AUTOMÁTICAMENTE EL NIVEL QUE LE ASIGNÓ EL ADMIN -->
            <p>Escuela de Formación Bíblica • Nivel Asignado: <span style="color: #38bdf8; font-weight: bold;"><?php echo htmlspecialchars($nivel_asignado); ?></span></p>
        </header>

        <div class="dashboard-grid">
        
        <div style="display: flex; flex-direction: column; gap: 2.0rem;">
            
            <section class="info-card">
                <h3>Asignaciones Publicadas Recientemente</h3>
                <ul class="task-list">
                    <?php if ($result_tareas && mysqli_num_rows($result_tareas) > 0): ?>
                        <?php while($tarea = mysqli_fetch_assoc($result_tareas)): ?>
                            <li class="task-item">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($tarea['titulo_tarea']); ?></h4>
                                    <p>Dirigido a: <span><?php echo htmlspecialchars($tarea['nivel_academico']); ?></span> • Tema: <span><?php echo htmlspecialchars($tarea['tema']); ?></span></p>
                                    <p>Fecha límite: <?php echo date('d/m/Y', strtotime($tarea['fecha_limite'])); ?></p>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No has publicado ninguna asignación académica todavía.</p>
                    <?php endif; ?>
                </ul>
            </section>

        </div>

        <div>
            <section class="info-card">
                <h3>Tus Clases y Cronograma</h3>
                <ul class="class-list">
                    <?php if ($result_clases && mysqli_num_rows($result_clases) > 0): ?>
                        <?php while($clase = mysqli_fetch_assoc($result_clases)): ?>
                            <li class="class-item">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($clase['materia_tema']); ?></h4>
                                    <p>Grupo: <span><?php echo htmlspecialchars($clase['nivel_academico']); ?></span></p>
                                    <p>Fecha: <?php echo date('d/m/Y', strtotime($clase['fecha'])); ?></p>
                                    <p>Hora: <?php echo date('h:i A', strtotime($clase['hora_inicio'])); ?> - <?php echo date('h:i A', strtotime($clase['hora_fin'])); ?></p>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No tienes encuentros programados en tu agenda.</p>
                    <?php endif; ?>
                </ul>
            </section>
        </div>

    </div>
    </main>
    <?php include '../script-seguridad.php'; ?>

</body>
</html>