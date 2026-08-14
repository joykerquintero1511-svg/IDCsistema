<?php
session_start();
include("../conexion.php");

// Seguridad: Verificar que sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- 1. CONSULTAS PARA EL RESUMEN GLOBAL DETALLADO ---

// Tabla Usuarios (Cuentas creadas)
$res_usr = mysqli_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN verificado = 1 THEN 1 ELSE 0 END) as verificados,
    SUM(CASE WHEN verificado = 0 THEN 1 ELSE 0 END) as pendientes
    FROM usuarios WHERE rol = 'estudiante'");
$stats_usr = mysqli_fetch_assoc($res_usr);

// Tabla Inscripciones (Proceso de pre-inscripción)
$res_insc = mysqli_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as aprobadas,
    SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) as pendientes
    FROM inscripciones");
$stats_insc = mysqli_fetch_assoc($res_insc);

// Tabla Estudiantes (Alumnos ya consolidados en el sistema)
$res_est = mysqli_query($conexion, "SELECT COUNT(*) as total FROM estudiantes");
$stats_est = mysqli_fetch_assoc($res_est);


// --- 2. VARIABLES Y CONSULTAS PARA LOS FILTROS DE LA TABLA ---
$nivel_filtro = isset($_GET['id_nivel']) ? intval($_GET['id_nivel']) : '';
$nota_min = isset($_GET['nota_min']) ? floatval($_GET['nota_min']) : '';
$nota_max = isset($_GET['nota_max']) ? floatval($_GET['nota_max']) : '';
$tiene_usuario = isset($_GET['tiene_usuario']) ? $_GET['tiene_usuario'] : ''; 

// Cargar niveles para el desplegable del filtro
$niveles_query = mysqli_query($conexion, "SELECT id_nivel, nivel_academico FROM niveles ORDER BY id_nivel ASC");

// Consulta: Vinculación de estudiantes y usuarios por EMAIL
$sql_resultado = "SELECT 
                    e.id_estudiante, 
                    p.nombre, 
                    p.apellido, 
                    u.usuario, 
                    u.id_usuario, 
                    n.nivel_academico, 
                    c.nota_final 
                  FROM estudiantes e
                  LEFT JOIN personas p ON e.id_persona = p.id_persona
                  LEFT JOIN usuarios u ON e.email = u.email
                  LEFT JOIN calificaciones c ON e.id_estudiante = c.id_estudiante
                  LEFT JOIN niveles n ON e.id_nivel = n.id_nivel
                  WHERE 1=1";

// Aplicando los filtros
if ($nivel_filtro != '') {
    $sql_resultado .= " AND e.id_nivel = $nivel_filtro";
}
if ($nota_min !== '' && $nota_max !== '') {
    $sql_resultado .= " AND c.nota_final BETWEEN $nota_min AND $nota_max";
}
if ($tiene_usuario === '1') {
    $sql_resultado .= " AND u.id_usuario IS NOT NULL";
} elseif ($tiene_usuario === '0') {
    $sql_resultado .= " AND u.id_usuario IS NULL";
}

$resultados = mysqli_query($conexion, $sql_resultado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas Avanzadas - EFB</title>
    <link rel="icon" href="../images/EFB.png" type="image/png">
    
    <!-- HOJAS DE ESTILO EXTERNAS -->
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
</head>
<body>

<!-- BARRA LATERAL ADMINISTRATIVA -->
<?php include 'sidebaradmin.php'; ?>

<!-- CONTENEDOR PRINCIPAL -->
<main class="main-content">
    <a href="index.php" class="btn-volver">← Volver al Panel Maestro</a>
    
    <h1>Módulo de Estadísticas y Filtros Avanzados 📊</h1>
    <p style="color: #94a3b8; margin-bottom: 30px;">Análisis global del sistema y filtrado específico de alumnos según rendimiento.</p>

    <!-- RESUMEN GLOBAL -->
    <div class="stats-grid">
        <!-- Tarjeta 1: Cuentas de Usuario -->
        <div class="stat-box">
            <div class="stat-title">👥 Cuentas Registradas (Usuarios)</div>
            <div class="stat-main-value"><?php echo $stats_usr['total'] ?? 0; ?></div>
            <div class="stat-detail"><span>Cuentas Verificadas:</span><span class="green"><?php echo $stats_usr['verificados'] ?? 0; ?></span></div>
            <div class="stat-detail"><span>Cuentas Pendientes:</span><span class="red"><?php echo $stats_usr['pendientes'] ?? 0; ?></span></div>
            <?php $porcentaje_usr = ($stats_usr['total'] > 0) ? round(($stats_usr['verificados'] / $stats_usr['total']) * 100) : 0; ?>
            <div class="progress-bar" style="margin-top: 15px;">
                <div style="width: <?php echo $porcentaje_usr; ?>%; background: #10b981;"></div>
                <div style="width: <?php echo 100 - $porcentaje_usr; ?>%; background: #ef4444;"></div>
            </div>
        </div>

        <!-- Tarjeta 2: Inscripciones -->
        <div class="stat-box">
            <div class="stat-title">📝 Proceso de Inscripciones</div>
            <div class="stat-main-value"><?php echo $stats_insc['total'] ?? 0; ?></div>
            <div class="stat-detail"><span>Inscripciones Aprobadas:</span><span class="green"><?php echo $stats_insc['aprobadas'] ?? 0; ?></span></div>
            <div class="stat-detail"><span>Inscripciones Pendientes:</span><span class="red"><?php echo $stats_insc['pendientes'] ?? 0; ?></span></div>
            <?php $porcentaje_insc = ($stats_insc['total'] > 0) ? round(($stats_insc['aprobadas'] / $stats_insc['total']) * 100) : 0; ?>
            <div class="progress-bar" style="margin-top: 15px;">
                <div style="width: <?php echo $porcentaje_insc; ?>%; background: #3b82f6;"></div>
                <div style="width: <?php echo 100 - $porcentaje_insc; ?>%; background: #ef4444;"></div>
            </div>
        </div>

        <!-- Tarjeta 3: Estudiantes Consolidados -->
        <div class="stat-box" style="display: flex; flex-direction: column; justify-content: center;">
            <div class="stat-title">🎓 Estudiantes Consolidados</div>
            <div class="stat-main-value" style="color: #a855f7; font-size: 42px; margin-bottom: 5px;">
                <?php echo $stats_est['total'] ?? 0; ?>
            </div>
            <p style="color: #9ca3af; font-size: 13px; margin: 0;">
                Alumnos activos que ya pasaron los filtros de verificación y aprobación de inscripción.
            </p>
        </div>
    </div>

    <!-- FORMULARIO DE FILTROS PERSONALIZADOS -->
    <div class="card">
        <h3 style="margin-bottom: 15px;">Filtro Personalizado de Alumnos</h3>
        <form method="GET" action="">
            <div class="form-grid">
                <div>
                    <label>Nivel Académico:</label>
                    <select name="id_nivel">
                        <option value="">-- Todos los Niveles --</option>
                        <?php 
                        if ($niveles_query) {
                            while($n = mysqli_fetch_assoc($niveles_query)) {
                                $selected = ($nivel_filtro == $n['id_nivel']) ? 'selected' : '';
                                echo "<option value='{$n['id_nivel']}' $selected>" . htmlspecialchars($n['nivel_academico']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label>Estado de Cuenta:</label>
                    <select name="tiene_usuario">
                        <option value="">-- Todos los Alumnos --</option>
                        <option value="1" <?php echo ($tiene_usuario === '1') ? 'selected' : ''; ?>>Con usuario registrado</option>
                        <option value="0" <?php echo ($tiene_usuario === '0') ? 'selected' : ''; ?>>Sin usuario registrado</option>
                    </select>
                </div>

                <div>
                    <label>Nota Mínima / Máxima:</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="number" step="0.1" name="nota_min" placeholder="Min" value="<?php echo isset($_GET['nota_min']) ? htmlspecialchars($_GET['nota_min']) : ''; ?>">
                        <input type="number" step="0.1" name="nota_max" placeholder="Max" value="<?php echo isset($_GET['nota_max']) ? htmlspecialchars($_GET['nota_max']) : ''; ?>">
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; justify-content: flex-end; align-items: center;">
                    <button type="submit">Filtrar</button>
                    <a href="estadisticas.php" style="color: #9ca3af; margin-top: 10px; text-decoration: none; font-size: 13px; text-align: center; display: block;">Restablecer Filtros</a>
                </div>
            </div>
        </form>
    </div>

    <!-- TABLA DE RESULTADOS OBTENIDOS -->
    <div class="card">
        <h3>Resultados de la Búsqueda (<?php echo $resultados ? mysqli_num_rows($resultados) : 0; ?> Alumnos encontrados)</h3>
        
        <?php if($resultados && mysqli_num_rows($resultados) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Nivel Académico</th>
                            <th>Nota Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($resultados)): 
                            $nombre_completo = trim(($row['nombre'] ?? '') . ' ' . ($row['apellido'] ?? ''));
                            if (empty($nombre_completo)) {
                                $nombre_completo = "Alumno #" . $row['id_estudiante'];
                            }
                            
                            $tiene_cuenta = !empty($row['id_usuario']);
                            $usuario_alias = htmlspecialchars($row['usuario'] ?? '');
                        ?>
                            <tr>
                                <td style="font-weight: bold; color: #fff;">
                                    <?php echo htmlspecialchars($nombre_completo); ?>
                                    
                                    <?php if($tiene_cuenta): ?>
                                        <span style="font-weight: normal; color: #9ca3af; font-size: 12px; margin-left: 4px;">
                                            (@<?php echo $usuario_alias; ?>)
                                        </span>
                                        <span class="badge badge-user">Cuenta Activa</span>
                                    <?php else: ?>
                                        <span class="badge badge-nouser">Sin Cuenta</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['nivel_academico'] ?? 'Sin Nivel Asignado'); ?></td>
                                <td>
                                    <?php if ($row['nota_final'] !== null): ?>
                                        <span style="background: #1e293b; padding: 4px 10px; border-radius: 4px; font-weight: bold; color: <?php echo ($row['nota_final'] >= 10) ? '#10b981' : '#ef4444'; ?>;">
                                            <?php echo number_format($row['nota_final'], 2); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #6b7280; font-size: 13px;">Sin nota registrada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: #94a3b8; margin-top: 20px;">No se encontraron registros que coincidan con los criterios seleccionados.</p>
        <?php endif; ?>
    </div>
</main>

<?php include "../script-seguridad.php"; ?>

</body>
</html>