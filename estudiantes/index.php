<?php
session_start();
include("../conexion.php"); 

// 1. CONTROL DE SEGURIDAD
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'estudiante' && $_SESSION['rol'] !== 'alumno')) {
    header("Location: ../login.php"); 
    exit();
}

// 2. CAPTURAR DATOS DE LA SESIÓN
$id_estudiante = $_SESSION['id_estudiante'] ?? 0; 
$id_nivel = $_SESSION['id_nivel'] ?? 0;
$nombre_estudiante = $_SESSION['usuario'] ?? 'Estudiante'; 

// 3. CONSULTA 1: Tareas pendientes del nivel actual
$query_tareas = "SELECT id_asignacion, titulo_tarea, descripcion, tema, fecha_limite 
                 FROM asignacion 
                 WHERE id_nivel = '$id_nivel' 
                 ORDER BY fecha_limite ASC";
$result_tareas = mysqli_query($conexion, $query_tareas);

// 4. CONSULTA 2: Historial de Calificaciones
$query_notas = "SELECT a.titulo_tarea, a.tema, c.nota, c.observacion 
                FROM calificaciones c
                INNER JOIN asignacion a ON c.id_asignacion = a.id_asignacion
                WHERE c.id_estudiante = '$id_estudiante'
                ORDER BY c.fecha_calificado DESC";
$result_notas = mysqli_query($conexion, $query_notas);

// 5. CONSULTA 3: Cronograma de clases de su nivel
$query_clases = "SELECT tema_clase, fecha, hora, lugar_modalidad 
                 FROM cronograma_clases 
                 WHERE id_nivel = '$id_nivel' 
                 ORDER BY fecha ASC";
$result_clases = mysqli_query($conexion, $query_clases);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estudiante - EFB</title>
    <link rel="icon" type="image/png" href="../images/EFB.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #0c0c0c; color: #ffffff; display: flex; min-height: 100vh; }

        /* BARRA LATERAL */
        .sidebar { width: 260px; background-color: #111111; border-right: 1px solid rgba(255, 255, 255, 0.05); padding: 2.5rem 1.5rem; display: flex; flex-direction: column; justify-content: space-between; position: fixed; height: 100vh; z-index: 10; }
        .sidebar-brand { display: flex; align-items: center; gap: 1rem; margin-bottom: 3rem; }
        .sidebar-brand img { max-width: 35px; height: auto; }
        .sidebar-brand h2 { font-size: 1.2rem; font-weight: bold; color: #fff; letter-spacing: 1px; }
        
        .menu-links { list-style: none; display: flex; flex-direction: column; gap: 0.8rem; }
        .menu-links a { color: #a0a0a0; text-decoration: none; padding: 0.8rem 1.2rem; border-radius: 6px; display: block; font-size: 1rem; font-weight: 500; transition: all 0.3s ease; }
        .menu-links a:hover, .menu-links a.active { background: rgba(36, 82, 133, 0.15); color: #3a7bc8; font-weight: bold; }
        
        .btn-logout { color: #ff5555; text-decoration: none; padding: 0.8rem 1.2rem; font-weight: bold; border-radius: 6px; transition: background 0.3s; }
        .btn-logout:hover { background: rgba(255, 85, 85, 0.1); }

        /* CONTENEDOR PRINCIPAL REALINEADO */
        .main-content { 
            margin-left: 260px; 
            width: calc(100% - 260px); 
            padding: 3.5rem; 
            flex-grow: 1;
        }
        
        .welcome-header { margin-bottom: 3rem; }
        .welcome-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        .welcome-header p { color: #666; font-size: 1rem; }

        /* REJILLA TOTALMENTE EXPANDIDA */
        .dashboard-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 2.5rem; 
            width: 100%;
        }

        @media (min-width: 1024px) {
            .dashboard-grid { 
                grid-template-columns: 2fr 1fr; /* Columna izquierda grande, derecha agenda */
            }
        }

        .info-card { background: #111111; border: 1px solid rgba(255, 255, 255, 0.05); padding: 2rem; border-radius: 8px; width: 100%; }
        .info-card h3 { font-size: 1.1rem; color: #a0a0a0; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px; }

        .task-list, .class-list { list-style: none; display: flex; flex-direction: column; gap: 1rem; }
        .task-item, .class-item { display: flex; justify-content: space-between; align-items: center; padding: 1.2rem; background: rgba(255,255,255,0.02); border-radius: 6px; border-left: 4px solid #245285; }
        .class-item { border-left-color: #3a7bc8; }
        
        .item-info h4 { font-size: 1.05rem; margin-bottom: 0.3rem; color: #fff; }
        .item-info p { color: #666; font-size: 0.9rem; }
        .item-info span { color: #a0a0a0; font-weight: 500; }
        
        .btn-action { color: #3a7bc8; text-decoration: none; font-size: 0.95rem; font-weight: bold; }
        .btn-action:hover { text-decoration: underline; }

        .data-table { width: 100%; border-collapse: collapse; text-align: left; }
        .data-table th { color: #666; font-size: 0.85rem; text-transform: uppercase; padding: 1rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .data-table td { padding: 1rem 0; border-bottom: 1px solid rgba(255, 255, 255, 0.03); color: #e0e0e0; font-size: 0.95rem; }
        .badge-nota { background: rgba(36, 82, 133, 0.2); color: #3a7bc8; padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: bold; }
        .no-data { color: #555; font-style: italic; text-align: center; padding: 2rem 0; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <img src="../images/EFB.png" alt="Logo">
                <h2>Estudiante</h2>
            </div>
            <ul class="menu-links">
                <li><a href="#" class="active">Inicio</a></li>
                <li><a href="#">Actividades</a></li>
                <li><a href="#">Historial de Notas</a></li>
                <li><a href="#">Horarios de Clase</a></li>
            </ul>
        </div>
        <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
    </aside>

    <main class="main-content">
        <header class="welcome-header">
            <h1>Bienvenido, <?php echo htmlspecialchars($nombre_estudiante); ?></h1>
            <p>Escuela de Formación Bíblica • Estado de cuenta académico</p>
        </header>

        <div class="dashboard-grid">
            
            <div style="display: flex; flex-direction: column; gap: 2.5rem;">
                
                <section class="info-card">
                    <h3>Asignaciones y Tareas Pendientes</h3>
                    <ul class="task-list">
                        <?php if (mysqli_num_rows($result_tareas) > 0): ?>
                            <?php while($tarea = mysqli_fetch_assoc($result_tareas)): ?>
                                <li class="task-item">
                                    <div class="item-info">
                                        <h4><?php echo htmlspecialchars($tarea['titulo_tarea']); ?></h4>
                                        <p>Tema Doctrinal: <span><?php echo htmlspecialchars($tarea['tema']); ?></span></p>
                                        <p>Entrega límite: <?php echo date('d/m/Y', strtotime($tarea['fecha_limite'])); ?></p>
                                    </div>
                                    <a href="ver_tarea.php?id=<?php echo $tarea['id_asignacion']; ?>" class="btn-action">Ver Detalles</a>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="no-data">No tienes asignaciones pendientes para tu nivel en este momento.</p>
                        <?php endif; ?>
                    </ul>
                </section>

                <section class="info-card">
                    <h3>Registro de Calificaciones</h3>
                    <?php if (mysqli_num_rows($result_notas) > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Asignación / Tema</th>
                                    <th>Feedback del Profesor</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($nota = mysqli_fetch_assoc($result_notas)): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($nota['titulo_tarea']); ?></strong><br>
                                            <span style="font-size: 0.85rem; color: #555;"><?php echo htmlspecialchars($nota['tema']); ?></span>
                                        </td>
                                        <td style="color: #aaa; font-size: 0.9rem;"><?php echo htmlspecialchars($nota['observacion'] ?? 'Sin observaciones'); ?></td>
                                        <td><span class="badge-nota"><?php echo $nota['nota']; ?> / 20</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="no-data">Aún no registras tareas calificadas.</p>
                    <?php endif; ?>
                </section>
            </div>

            <div>
                <section class="info-card">
                    <h3>Próximas Clases y Fechas</h3>
                    <ul class="class-list">
                        <?php if (mysqli_num_rows($result_clases) > 0): ?>
                            <?php while($clase = mysqli_fetch_assoc($result_clases)): ?>
                                <li class="class-item">
                                    <div class="item-info">
                                        <h4><?php echo htmlspecialchars($clase['tema_clase']); ?></h4>
                                        <p>Fecha: <span><?php echo date('d/m/Y', strtotime($clase['fecha'])); ?></span></p>
                                        <p>Hora: <?php echo date('h:i A', strtotime($clase['hora'])); ?></p>
                                        <p style="margin-top: 0.3rem; font-size: 0.85rem; color: #3a7bc8;">📍 <?php echo htmlspecialchars($clase['lugar_modalidad']); ?></p>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="no-data">No hay encuentros programados en el cronograma.</p>
                        <?php endif; ?>
                    </ul>
                </section>
            </div>

        </div>
    </main>
</body>
</html>