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

// ==========================================
// 3. OBTENER EL PERÍODO ACTIVO ACTUAL
// ==========================================
$query_pa = "SELECT id_periodo FROM periodos_academicos WHERE estado = 1 LIMIT 1";
$res_pa = mysqli_query($conexion, $query_pa);
$id_periodo_activo = 0;
if ($res_pa && $row_pa = mysqli_fetch_assoc($res_pa)) {
    $id_periodo_activo = intval($row_pa['id_periodo']);
}

// Buscar el nombre descriptivo del nivel académico
$nombre_nivel = "Tu Nivel"; 
if ($id_nivel > 0) {
    $query_nombre_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel'";
    $res_nombre_nivel = mysqli_query($conexion, $query_nombre_nivel);
    
    if ($res_nombre_nivel && $row_nivel = mysqli_fetch_assoc($res_nombre_nivel)) {
        $nombre_nivel = $row_nivel['nivel_academico'];
    }
}

// ==========================================
// 4. CONSULTA FILTRADA: Tareas vigentes del nivel y del período activo
// ==========================================
$query_tareas = "SELECT * FROM asignacion 
                 WHERE id_nivel = '$id_nivel' 
                   AND id_periodo = $id_periodo_activo 
                   AND fecha_limite >= CURDATE() 
                 ORDER BY fecha_limite ASC";
$result_tareas = mysqli_query($conexion, $query_tareas);

// ==========================================
// 5. CONSULTA: Historial de Calificaciones
// ==========================================
$query_notas = "SELECT c.evaluacion, c.descripcion_nota_1, c.descripcion_nota_2, c.nota_1, c.nota_2, c.nota_final, c.observacion, c.fecha_calificado
                FROM calificaciones c
                WHERE c.id_estudiante = '$id_estudiante'
                ORDER BY c.fecha_calificado DESC";
$result_notas = mysqli_query($conexion, $query_notas);

$nombre_nota_1 = "Nota 1";
$nombre_nota_2 = "Nota 2";

if ($result_notas && mysqli_num_rows($result_notas) > 0) {
    $primera_nota = mysqli_fetch_assoc($result_notas);

    if (!empty($primera_nota['descripcion_nota_1'])) {
        $nombre_nota_1 = ucwords(strtolower($primera_nota['descripcion_nota_1']));
    }

    if (!empty($primera_nota['descripcion_nota_2'])) {
        $nombre_nota_2 = ucwords(strtolower($primera_nota['descripcion_nota_2']));
    }

    mysqli_data_seek($result_notas, 0);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Estudiante - EFB</title>
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
    </ul>
    <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
</div>

<!-- CONTENEDOR PRINCIPAL -->
<main class="main-content">
    
    <!-- CABECERA DE BIENVENIDA -->
    <div class="welcome-header">
        <h1>Bienvenido a <?php echo htmlspecialchars($nombre_nivel); ?>, <?php echo htmlspecialchars($nombre_estudiante); ?></h1>
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
                            <li class="task-item" style="border-left-color: #eab308; display: flex; justify-content: space-between; align-items: center;">
    <div class="item-info">
        <h4><?php echo htmlspecialchars($tarea['titulo_tarea']); ?></h4>
        <p>Tema: <span><?php echo htmlspecialchars($tarea['tema']); ?></span></p>
        <p style="margin-top: 0.3rem; font-size: 0.85rem; color: #64748b;">
             Entrega límite: <?php echo date("d/m/Y", strtotime($tarea['fecha_limite'])); ?>
        </p>
    </div>
    <!-- Botón que activa el modal pasando la descripción de esta tarea -->
    <div>
        <button type="button" 
                onclick="abrirModal('<?php echo htmlspecialchars(addslashes($tarea['titulo_tarea'])); ?>', '<?php echo htmlspecialchars(addslashes($tarea['descripcion'] ?? 'Sin descripción proporcionada.')); ?>')"
                style="background-color: transparent; color: #fffffe; border:  2px solid #1414fe; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 0.85rem;">
            📖 Ver Descripción
        </button>
    </div>
</li>
                            
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="no-data">¡Al día! No tienes asignaciones pendientes por ahora en este período.</div>
                <?php endif; ?>
                
            </div>

            <!-- TARJETA DE CALIFICACIONES (RECIENTES) -->
            <div class="info-card">
                <h3>Mis Calificaciones Recientes</h3>
                <?php if ($result_notas && mysqli_num_rows($result_notas) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Evaluación</th>
                                <th><?php echo htmlspecialchars($nombre_nota_1); ?></th>
                                <th><?php echo htmlspecialchars($nombre_nota_2); ?></th>
                                <th>Nota Final</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($nota = mysqli_fetch_assoc($result_notas)): ?>
                                <tr>
                                    <td>
                                    <?php
                                    if ($nota['evaluacion'] === null || $nota['evaluacion'] === "") {
                                        echo "No especificada";
                                    } else {
                                        echo htmlspecialchars(ucwords(strtolower($nota['evaluacion'])));
                                    }
                                    ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($nota['nota_1']); ?></td>
                                    <td>
                                        <?php
                                        if ($nota['nota_2'] === null || $nota['nota_2'] === "") {
                                            echo "No registrada";
                                        } else {
                                            echo htmlspecialchars($nota['nota_2']);
                                        }
                                        ?>
                                    </td>
                                    <td><span class="badge-nota"><?php echo htmlspecialchars($nota['nota_final']); ?></span></td>
                                    <td style="color: #94a3b8; font-size: 0.9rem;">
                                       <?php
                                        if ($nota['observacion'] === null || $nota['observacion'] === "") {
                                            echo "Sin observación";
                                        } else {
                                            echo htmlspecialchars(ucwords(strtolower($nota['observacion'])));
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">Aún no se han cargado calificaciones.</div>
                <?php endif; ?>
            </div>

        </div>

    </div>
    <!-- VENTANA MODAL FLOTANTE -->
<div id="modal-descripcion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: #161b22; border: 1px solid #30363d; padding: 25px; border-radius: 10px; width: 90%; max-width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.5); color: #fff; position: relative;">
        <h3 id="modal-titulo" style="color: #38bdf8; margin-top: 0; margin-bottom: 15px; font-size: 1.2rem;">Título de la Tarea</h3>
        <hr style="border: 0; border-top: 1px solid #30363d; margin-bottom: 15px;">
        <p id="modal-texto" style="color: #c9d1d9; font-size: 0.95rem; line-height: 1.5; white-space: pre-wrap; word-break: break-word; max-height: 250px; overflow-y: auto;"></p>
        <div style="text-align: right; margin-top: 20px;">
            <button type="button" onclick="cerrarModal()" style="background-color: #238636; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
    function abrirModal(titulo, descripcion) {
        document.getElementById('modal-titulo').innerText = titulo;
        document.getElementById('modal-texto').innerText = descripcion;
        document.getElementById('modal-descripcion').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modal-descripcion').style.display = 'none';
    }

    // Cerrar también si hacen clic fuera del cuadro de la modal
    window.onclick = function(event) {
        let modal = document.getElementById('modal-descripcion');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
</main>
<?php include("../script-seguridad.php"); ?>

</body>
</html>