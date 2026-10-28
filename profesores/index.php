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

// ==========================================
// 3. OBTENER EL PERÍODO ACTIVO ACTUAL
// ==========================================
$query_pa = "SELECT id_periodo, nombre_periodo FROM periodos_academicos WHERE estado = 1 LIMIT 1";
$res_pa = mysqli_query($conexion, $query_pa);
$id_periodo_activo = 0;
$nombre_periodo_activo = "Ninguno";

if ($res_pa && $row_pa = mysqli_fetch_assoc($res_pa)) {
    $id_periodo_activo = intval($row_pa['id_periodo']);
    $nombre_periodo_activo = $row_pa['nombre_periodo'] ?? $row_pa['lapso'] ?? 'Período Actual';
}

// ==========================================
// 4. CONSULTA: Obtener ID del profesor y sus niveles FILTRADOS POR EL PERÍODO ACTIVO
// ==========================================
$nombre_limpio = strtolower(str_replace(' ', '', $nombre_profesor));

$query_prof = "SELECT p.id_profesor, 
               GROUP_CONCAT(n.id_nivel SEPARATOR '||') AS ids_niveles,
               GROUP_CONCAT(n.nivel_academico SEPARATOR '||') AS nombres_niveles
               FROM profesores p 
               LEFT JOIN profesor_nivel pn ON p.id_profesor = pn.id_profesor AND pn.id_periodo = $id_periodo_activo
               LEFT JOIN niveles n ON pn.id_nivel = n.id_nivel 
               WHERE p.id_usuario = '$id_usuario' 
                  OR REPLACE(LOWER(CONCAT(p.nombre, IFNULL(p.apellido, ''))), ' ', '') = '$nombre_limpio'
                  OR REPLACE(LOWER(p.nombre), ' ', '') = '$nombre_limpio'
               GROUP BY p.id_profesor
               LIMIT 1";

$res_prof = mysqli_query($conexion, $query_prof);
$id_profesor = 0;
$nombres_niveles_array = [];

if ($res_prof && $row_prof = mysqli_fetch_assoc($res_prof)) {
    $id_profesor = intval($row_prof['id_profesor']);
    if (!empty($row_prof['nombres_niveles'])) {
        $nombres_niveles_array = explode('||', $row_prof['nombres_niveles']);
    }
}

// Texto formateado para mostrar en la cabecera
$nivel_asignado = !empty($nombres_niveles_array) ? implode(', ', $nombres_niveles_array) : "Sin niveles asignados";

// 5. CONSULTA 1: Últimas asignaciones creadas por este profesor (puedes filtrar por período si tu tabla asignación tiene id_periodo)
$query_tareas = "SELECT a.id_asignacion, a.titulo_tarea, a.tema, a.fecha_limite, n.nivel_academico 
                 FROM asignacion a
                 INNER JOIN niveles n ON a.id_nivel = n.id_nivel
                 ORDER BY a.id_asignacion DESC LIMIT 5"; 
$result_tareas = mysqli_query($conexion, $query_tareas);

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
    <link rel="stylesheet" href="../css/movil.css">
</head>
<body>

    <?php include 'sidebarprof.php'; ?>

    <main class="main-content">
        <header class="welcome-header">
            <h1>Bienvenido Profesor, <?php echo htmlspecialchars($nombre_profesor); ?></h1>
            <!-- INFORMACIÓN CLARA DEL PERÍODO ACTIVO Y NIVELES ASIGNADOS -->
            <p>
                Escuela de Formación Bíblica • 
                Período: <strong style="color: #38bdf8;"><?php echo htmlspecialchars($nombre_periodo_activo); ?></strong>  <br> 
                Nivel Asignado: <span style="color: #f8fafc; font-weight: bold;"><?php echo htmlspecialchars($nivel_asignado); ?></span>
            </p>
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

        
    </div>
    </main>
    <?php include '../script-seguridad.php'; ?>

</body>
</html>