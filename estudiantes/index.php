<?php
include("../session-start.php");
include("../conexion.php"); 

// 1. CONTROL DE SEGURIDAD
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'estudiante' && $_SESSION['rol'] !== 'alumno')) {
    header("Location: ../login.php"); 
    exit();
}

// 2. CAPTURAR DATOS DE LA SESIÓN
$id_estudiante = $_SESSION['id_estudiante'] ?? 0; 
$nombre_estudiante = $_SESSION['usuario'] ?? 'Estudiante';
$id_nivel = $_SESSION['id_nivel'] ?? 0; 


/// 2. Consulta filtrada: Tareas vigentes
$query_tareas = "SELECT * FROM asignacion WHERE id_nivel = '$id_nivel' ORDER BY fecha_limite ASC";
$result_tareas = mysqli_query($conexion, $query_tareas);

// 4. CONSULTA 2: Historial de Calificaciones (Unido con asignaciones para traer el nombre del tema/tarea)
$query_notas = "SELECT c.nota, c.observacion, a.titulo_tarea 
                FROM calificaciones c 
                INNER JOIN asignacion a ON c.id_asignacion = a.id_asignacion 
                WHERE c.id_estudiante = '$id_estudiante' 
                ORDER BY c.fecha_calificado DESC";
$result_notas = mysqli_query($conexion, $query_notas);
// 5. CONSULTA 3: Cronograma de clases corregido con tus campos reales
$query_clases = "SELECT materia_tema, fecha, hora_inicio, hora_fin 
                 FROM cronograma_clases 
                 ORDER BY fecha ASC";
$result_clases = mysqli_query($conexion, $query_clases);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Estudiante - EFB</title>
    <!-- CONEXIÓN AL ARCHIVO DE ESTILOS GLOBAL -->
    <link rel="stylesheet" href="../css/mystyle.css"> 
    <link rel="icon" type="image/png" href="../images/EFB.png">
</head>
<body>

<!-- BARRA LATERAL (SIDEBAR) -->
<div class="sidebar">
    <div class="sidebar-brand">
        <img src="../images/EFB.png" alt="EFB Logo" onerror="this.style.display='none'">
        <h2>Estudiante</h2>
    </div>
    <ul class="menu-links">
        <li><a href="index.php" class="active">Inicio</a></li>
        <li><a href="mis_notas.php">Mis Calificaciones</a></li>
        <li><a href="cronograma.php">Horarios de Clase</a></li>
    </ul>
    <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
</div>

<!-- CONTENEDOR PRINCIPAL -->
<main class="main-content">
    
    <!-- CABECERA DE BIENVENIDA -->
    <div class="welcome-header">
        <h1>Bienvenido, <?php echo htmlspecialchars($nombre_estudiante); ?></h1>
        <p>Escuela de Formación Bíblica • Tu espacio de crecimiento espiritual y académico</p>
    </div>

    <!-- REJILLA DE CONTENIDO (DASHBOARD GRID) -->
    <div class="dashboard-grid">
        
        <!-- COLUMNA IZQUIERDA: ASIGNACIONES Y NOTAS -->
        <div style="display: flex; flex-direction: column; gap: 2.5rem;">
            
            <!-- TARJETA DE TAREAS PENDIENTES -->
            <div class="info-card">
                <h3>Tareas y Asignaciones Pendientes</h3>
                <?php if ($result_tareas && mysqli_num_rows($result_tareas) > 0): ?>
                    <ul class="task-list">
                        <?php while ($tarea = mysqli_fetch_assoc($result_tareas)): ?>
                            <li class="task-item" style="border-left-color: #eab308;"> <!-- Detalle dorado para tareas -->
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($tarea['titulo_tarea']); ?></h4>
                                    <p>Tema: <span><?php echo htmlspecialchars($tarea['tema']); ?></span></p>
                                    <p style="margin-top: 0.3rem; font-size: 0.85rem; color: #64748b;">
                                        📅 Entrega límite: <?php echo date("d/m/Y", strtotime($tarea['fecha_limite'])); ?>
                                    </p>
                                </div>
                                <a href="subir_tarea.php?id=<?php echo $tarea['id_asignacion']; ?>" class="btn-action">Subir Tarea</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="no-data">¡Al día! No tienes asignaciones pendientes por ahora.</div>
                <?php endif; ?>
            </div>

            <!-- TARJETA DE CALIFICACIONES (RECIENTES) -->
            <div class="info-card">
                <h3>Mis Calificaciones Recientes</h3>
                <?php if ($result_notas && mysqli_num_rows($result_notas) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nota 1</th>
                                <th>Nota 2</th>
                                <th>Nota Final</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($nota = mysqli_fetch_assoc($result_notas)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($nota['nota_1']); ?></td>
                                    <td><?php echo htmlspecialchars($nota['nota_2']); ?></td>
                                    <td><span class="badge-nota"><?php echo htmlspecialchars($nota['nota_final']); ?></span></td>
                                    <td style="color: #94a3b8; font-size: 0.9rem;"><?php echo htmlspecialchars($nota['observacion']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">Aún no se han cargado calificaciones en este período.</div>
                <?php endif; ?>
            </div>

        </div>

        <!-- COLUMNA DERECHA: CRONOGRAMA DE CLASES -->
        <div class="info-card">
            <h3>Próximas Clases y Encuentros</h3>
            <?php if ($result_clases && mysqli_num_rows($result_clases) > 0): ?>
                <ul class="class-list">
                    <?php while ($clase = mysqli_fetch_assoc($result_clases)): ?>
                        <li class="class-item">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($clase['materia_tema']); ?></h4>
                                <p style="margin-top: 0.3rem; font-size: 0.85rem; color: #64748b;">
                                    📅 Fecha: <?php echo date("d/m/Y", strtotime($clase['fecha'])); ?>
                                </p>
                                <p style="font-size: 0.85rem; color: #3b82f6; font-weight: 600;">
                                    🕒 Horario: <?php echo date("g:i A", strtotime($clase['hora_inicio'])) . " - " . date("g:i A", strtotime($clase['hora_fin'])); ?>
                                </p>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div class="no-data">No hay clases agendadas en tu cronograma.</div>
            <?php endif; ?>
        </div>

    </div>
</main>

</body>
</html>