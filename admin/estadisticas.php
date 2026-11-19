<?php
session_start();
include("../conexion.php");

// Seguridad: Verificar que sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- 1. CONSULTAS PARA EL RESUMEN GLOBAL DETALLADO ---

// Tabla Usuarios (Cuentas de estudiantes creadas y verificadas)
$res_usr = mysqli_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN verificado = 1 THEN 1 ELSE 0 END) as verificados,
    SUM(CASE WHEN verificado = 0 THEN 1 ELSE 0 END) as pendientes
    FROM usuarios WHERE rol = 'estudiante'");
$stats_usr = mysqli_fetch_assoc($res_usr);

// Tabla Inscripciones (Inscritos totales, aprobados y no aprobados)
$res_insc = mysqli_query($conexion, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as aprobadas,
    SUM(CASE WHEN estado = 0 OR estado IS NULL THEN 1 ELSE 0 END) as no_aprobadas
    FROM inscripciones");
$stats_insc = mysqli_fetch_assoc($res_insc);

// Tabla Estudiantes + Personas (Consolidados y desglose por género M / F)
$res_est = mysqli_query($conexion, "SELECT 
    COUNT(e.id_estudiante) as total,
    SUM(CASE WHEN UPPER(TRIM(p.genero)) = 'M' THEN 1 ELSE 0 END) as hombres,
    SUM(CASE WHEN UPPER(TRIM(p.genero)) = 'F' THEN 1 ELSE 0 END) as mujeres,
    SUM(CASE WHEN p.genero IS NULL OR TRIM(p.genero) = '' THEN 1 ELSE 0 END) as sin_genero
    FROM estudiantes e
    LEFT JOIN personas p ON e.id_persona = p.id_persona");
$stats_est = mysqli_fetch_assoc($res_est);

// Estudiantes por cada nivel académico
$res_por_nivel = mysqli_query($conexion, "SELECT 
    n.id_nivel,
    n.nivel_academico,
    COUNT(e.id_estudiante) as cantidad_estudiantes,
    SUM(CASE WHEN UPPER(TRIM(p.genero)) = 'M' THEN 1 ELSE 0 END) as hombres,
    SUM(CASE WHEN UPPER(TRIM(p.genero)) = 'F' THEN 1 ELSE 0 END) as mujeres
    FROM niveles n
    LEFT JOIN estudiantes e ON n.id_nivel = e.id_nivel
    LEFT JOIN personas p ON e.id_persona = p.id_persona
    GROUP BY n.id_nivel, n.nivel_academico
    ORDER BY n.id_nivel ASC");

$estadisticas_niveles = [];
if ($res_por_nivel) {
    while ($fila_niv = mysqli_fetch_assoc($res_por_nivel)) {
        $estadisticas_niveles[] = $fila_niv;
    }
}

// --- 2. VARIABLES Y CONSULTAS PARA LOS FILTROS DE LA TABLA ---
$nivel_filtro = isset($_GET['id_nivel']) && $_GET['id_nivel'] !== '' ? intval($_GET['id_nivel']) : '';
$nota_min = isset($_GET['nota_min']) && $_GET['nota_min'] !== '' ? floatval($_GET['nota_min']) : '';
$nota_max = isset($_GET['nota_max']) && $_GET['nota_max'] !== '' ? floatval($_GET['nota_max']) : '';
$tiene_usuario = isset($_GET['tiene_usuario']) ? $_GET['tiene_usuario'] : '';
$genero_filtro = isset($_GET['genero']) ? strtoupper(trim($_GET['genero'])) : '';

// Cargar niveles para el desplegable del filtro
$niveles_query = mysqli_query($conexion, "SELECT id_nivel, nivel_academico FROM niveles ORDER BY id_nivel ASC");

// Consulta principal vinculando estudiantes, personas, usuarios, niveles y calificaciones
$sql_resultado = "SELECT 
                    e.id_estudiante, 
                    p.nombre, 
                    p.apellido, 
                    p.genero,
                    u.usuario, 
                    u.id_usuario, 
                    u.verificado,
                    n.nivel_academico, 
                    c.nota_final 
                  FROM estudiantes e
                  LEFT JOIN personas p ON e.id_persona = p.id_persona
                  LEFT JOIN usuarios u ON e.email = u.email
                  LEFT JOIN calificaciones c ON e.id_estudiante = c.id_estudiante
                  LEFT JOIN niveles n ON e.id_nivel = n.id_nivel
                  WHERE 1=1";

if ($nivel_filtro !== '') {
    $sql_resultado .= " AND e.id_nivel = $nivel_filtro";
}
if ($nota_min !== '' && $nota_max !== '') {
    $sql_resultado .= " AND c.nota_final BETWEEN $nota_min AND $nota_max";
} elseif ($nota_min !== '') {
    $sql_resultado .= " AND c.nota_final >= $nota_min";
} elseif ($nota_max !== '') {
    $sql_resultado .= " AND c.nota_final <= $nota_max";
}
if ($tiene_usuario === '1') {
    $sql_resultado .= " AND u.id_usuario IS NOT NULL";
} elseif ($tiene_usuario === '0') {
    $sql_resultado .= " AND u.id_usuario IS NULL";
}
if ($genero_filtro === 'M' || $genero_filtro === 'F') {
    $sql_resultado .= " AND UPPER(TRIM(p.genero)) = '$genero_filtro'";
}

$sql_resultado .= " ORDER BY n.id_nivel ASC, p.apellido ASC, p.nombre ASC";
$resultados = mysqli_query($conexion, $sql_resultado);

// --- 3. EXPORTACIÓN A PDF CON DOMPDF ---
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    require_once '../vendor/autoload.php';

    $total_usr = intval($stats_usr['total'] ?? 0);
    $usr_verificados = intval($stats_usr['verificados'] ?? 0);
    $usr_pendientes = intval($stats_usr['pendientes'] ?? 0);

    $total_insc = intval($stats_insc['total'] ?? 0);
    $insc_aprobadas = intval($stats_insc['aprobadas'] ?? 0);
    $insc_no_aprobadas = intval($stats_insc['no_aprobadas'] ?? 0);

    $total_est = intval($stats_est['total'] ?? 0);
    $total_hombres = intval($stats_est['hombres'] ?? 0);
    $total_mujeres = intval($stats_est['mujeres'] ?? 0);

    // Logo institucional resuelto con realpath y codificado en Base64 para Dompdf
    $ruta_logo = realpath(__DIR__ . '/../images/EFB.png');
    $img_logo_html = '';
    if ($ruta_logo && file_exists($ruta_logo)) {
        $datos_img = file_get_contents($ruta_logo);
        if ($datos_img !== false && strlen($datos_img) > 0) {
            $logo_base64 = 'data:image/png;base64,' . base64_encode($datos_img);
            $img_logo_html = "<img src=\"{$logo_base64}\" alt=\"Logo EFB\" style=\"width: 60px; height: auto; display: block;\">";
        }
    }

    $celda_logo_html = !empty($img_logo_html)
        ? "<td style='width: 72px; vertical-align: middle; padding-bottom: 10px;'>{$img_logo_html}</td>"
        : "";

    $filas_niveles_html = '';
    foreach ($estadisticas_niveles as $niv) {
        $filas_niveles_html .= "
            <tr>
                <td>" . htmlspecialchars($niv['nivel_academico']) . "</td>
                <td style='text-align:center;'>" . intval($niv['hombres']) . "</td>
                <td style='text-align:center;'>" . intval($niv['mujeres']) . "</td>
                <td style='text-align:center; font-weight:bold;'>" . intval($niv['cantidad_estudiantes']) . "</td>
            </tr>";
    }

    $filas_detalle_html = '';
    if ($resultados && mysqli_num_rows($resultados) > 0) {
        mysqli_data_seek($resultados, 0);
        while ($row = mysqli_fetch_assoc($resultados)) {
            $nom = trim(($row['nombre'] ?? '') . ' ' . ($row['apellido'] ?? ''));
            if ($nom === '') {
                $nom = "Alumno #" . intval($row['id_estudiante']);
            }
            $gen_txt = ($row['genero'] === 'M') ? 'Masculino (M)' : (($row['genero'] === 'F') ? 'Femenino (F)' : 'No especificado');
            $cuenta_txt = !empty($row['id_usuario'])
                ? (intval($row['verificado']) === 1 ? 'Verificada' : 'Sin verificar')
                : 'Sin cuenta';
            $nota_txt = ($row['nota_final'] !== null) ? number_format(floatval($row['nota_final']), 2) : 'Sin nota';

            $filas_detalle_html .= "
                <tr>
                    <td>" . htmlspecialchars($nom) . "</td>
                    <td style='text-align:center;'>" . htmlspecialchars($gen_txt) . "</td>
                    <td>" . htmlspecialchars($row['nivel_academico'] ?? 'Sin nivel') . "</td>
                    <td style='text-align:center;'>" . htmlspecialchars($cuenta_txt) . "</td>
                    <td style='text-align:center;'>" . htmlspecialchars($nota_txt) . "</td>
                </tr>";
        }
    } else {
        $filas_detalle_html = "<tr><td colspan='5' style='text-align:center; color:#64748b;'>No hay registros para los filtros seleccionados.</td></tr>";
    }

    $fecha_emision = date("d/m/Y H:i");

    $html_pdf = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <link rel='icon' href='../IDCsistema/images/EFB.png' type='image/png'>
        <title>Reporte Estadístico Académico - EFB</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 20px; }
            .header-table { width: 100%; border-collapse: collapse; border-bottom: 2px solid #1e3a8a; margin-bottom: 18px; }
            .header-table h1 { margin: 0; font-size: 16px; color: #0f172a; }
            .header-table p { margin: 4px 0 0; font-size: 10px; color: #475569; }
            h2 { font-size: 13px; color: #1e3a8a; margin: 18px 0 8px; border-left: 4px solid #1e3a8a; padding-left: 8px; }
            .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
            .summary-table td { border: 1px solid #cbd5e1; padding: 8px; width: 33.33%; vertical-align: top; background: #f8fafc; }
            .metric-title { font-size: 10px; font-weight: bold; color: #475569; text-transform: uppercase; }
            .metric-num { font-size: 18px; font-weight: bold; color: #0f172a; margin: 4px 0; }
            .metric-sub { font-size: 10px; color: #334155; line-height: 1.4; }
            .data-table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 10px; }
            .data-table th { background: #1e3a8a; color: #ffffff; padding: 6px 8px; border: 1px solid #1e3a8a; text-align: left; }
            .data-table td { padding: 6px 8px; border: 1px solid #cbd5e1; }
            .data-table tr:nth-child(even) td { background: #f1f5f9; }
            .footer { margin-top: 20px; font-size: 9px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        </style>
    </head>
    <body>
        <table class='header-table'>
            <tr>
                {$celda_logo_html}
                <td style='vertical-align: middle; padding-bottom: 10px;'>
                    <h1>Reporte Estadístico Académico — Escuela de Formación Bíblica (EFB)</h1>
                    <p>Generado el: {$fecha_emision} • Sistema IDCsistema</p>
                </td>
            </tr>
        </table>

        <h2>1. Indicadores Globales</h2>
        <table class='summary-table'>
            <tr>
                <td>
                    <div class='metric-title'>Estudiantes Consolidados y Género</div>
                    <div class='metric-num'>{$total_est}</div>
                    <div class='metric-sub'>
                        Hombres (M): <strong>{$total_hombres}</strong><br>
                        Mujeres (F): <strong>{$total_mujeres}</strong>
                    </div>
                </td>
                <td>
                    <div class='metric-title'>Cuentas de Estudiantes</div>
                    <div class='metric-num'>{$total_usr}</div>
                    <div class='metric-sub'>
                        Cuentas verificadas: <strong>{$usr_verificados}</strong><br>
                        Pendientes de verificar: <strong>{$usr_pendientes}</strong>
                    </div>
                </td>
                <td>
                    <div class='metric-title'>Estado de Inscripciones</div>
                    <div class='metric-num'>{$total_insc}</div>
                    <div class='metric-sub'>
                        Inscritos aprobados: <strong>{$insc_aprobadas}</strong><br>
                        Inscritos no aprobados: <strong>{$insc_no_aprobadas}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <h2>2. Estudiantes por Nivel Académico</h2>
        <table class='data-table'>
            <thead>
                <tr>
                    <th>Nivel Académico</th>
                    <th style='text-align:center; width:15%;'>Hombres</th>
                    <th style='text-align:center; width:15%;'>Mujeres</th>
                    <th style='text-align:center; width:18%;'>Total Alumnos</th>
                </tr>
            </thead>
            <tbody>
                {$filas_niveles_html}
            </tbody>
        </table>

        <h2>3. Listado Filtrado de Estudiantes</h2>
        <table class='data-table'>
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th style='text-align:center;'>Género</th>
                    <th>Nivel</th>
                    <th style='text-align:center;'>Cuenta</th>
                    <th style='text-align:center;'>Nota Final</th>
                </tr>
            </thead>
            <tbody>
                {$filas_detalle_html}
            </tbody>
        </table>

        <div class='footer'>
            Escuela de Formación Bíblica (EFB) • Documento generado automáticamente por el módulo de estadísticas.
        </div>
    </body>
    </html>";

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $root_proyecto = realpath(__DIR__ . '/..');
    if ($root_proyecto) {
        $options->setChroot($root_proyecto);
    }

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html_pdf);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("estadisticas_efb_" . date("Ymd_His") . ".pdf", ["Attachment" => false]);
    exit();
}

// Conserva los filtros actuales al armar la URL de exportación PDF
$params_pdf = $_GET;
$params_pdf['export'] = 'pdf';
$url_pdf = "estadisticas.php?" . http_build_query($params_pdf);
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
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 10px;">
        <a href="index.php" class="btn-volver" style="margin-bottom: 0;">← Volver al Panel Maestro</a>
        <a href="<?php echo htmlspecialchars($url_pdf); ?>" target="_blank" class="btn-action" style="background: #dc2626; color: #fff; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: bold;">
            📄 Exportar Estadísticas a PDF
        </a>
    </div>
    
    <h1>Módulo de Estadísticas y Filtros Avanzados 📊</h1>
    <p style="color: #94a3b8; margin-bottom: 30px;">Análisis global del sistema, distribución por nivel y género, y exportación a PDF.</p>

    <!-- RESUMEN GLOBAL -->
    <div class="stats-grid">
        <!-- Tarjeta 1: Cuentas de Usuario -->
        <div class="stat-box">
            <div class="stat-title">👥 Cuentas Registradas (Estudiantes)</div>
            <div class="stat-main-value"><?php echo $stats_usr['total'] ?? 0; ?></div>
            <div class="stat-detail"><span>Cuentas Verificadas:</span><span class="green"><?php echo $stats_usr['verificados'] ?? 0; ?></span></div>
            <div class="stat-detail"><span>Cuentas Sin Verificar:</span><span class="red"><?php echo $stats_usr['pendientes'] ?? 0; ?></span></div>
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
            <div class="stat-detail"><span>Inscritos Aprobados:</span><span class="green"><?php echo $stats_insc['aprobadas'] ?? 0; ?></span></div>
            <div class="stat-detail"><span>Inscritos No Aprobados:</span><span class="red"><?php echo $stats_insc['no_aprobadas'] ?? 0; ?></span></div>
            <?php $porcentaje_insc = ($stats_insc['total'] > 0) ? round(($stats_insc['aprobadas'] / $stats_insc['total']) * 100) : 0; ?>
            <div class="progress-bar" style="margin-top: 15px;">
                <div style="width: <?php echo $porcentaje_insc; ?>%; background: #3b82f6;"></div>
                <div style="width: <?php echo 100 - $porcentaje_insc; ?>%; background: #ef4444;"></div>
            </div>
        </div>

        <!-- Tarjeta 3: Estudiantes Consolidados y Género -->
        <div class="stat-box" style="display: flex; flex-direction: column; justify-content: center;">
            <div class="stat-title">🎓 Estudiantes Consolidados</div>
            <div class="stat-main-value" style="color: #a855f7; font-size: 42px; margin-bottom: 8px;">
                <?php echo $stats_est['total'] ?? 0; ?>
            </div>
            <div class="stat-detail"><span>👨 Hombres (M):</span><span style="color: #38bdf8; font-weight: bold;"><?php echo $stats_est['hombres'] ?? 0; ?></span></div>
            <div class="stat-detail"><span>👩 Mujeres (F):</span><span style="color: #f472b6; font-weight: bold;"><?php echo $stats_est['mujeres'] ?? 0; ?></span></div>
        </div>
    </div>

    <!-- RESUMEN DE ESTUDIANTES POR NIVEL -->
    <div class="card" style="margin-top: 24px;">
        <h3 style="margin-bottom: 15px;">📚 Estudiantes por Nivel Académico</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nivel Académico</th>
                        <th>Hombres (M)</th>
                        <th>Mujeres (F)</th>
                        <th>Total Estudiantes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estadisticas_niveles as $niv): ?>
                        <tr>
                            <td style="font-weight: bold; color: #fff;"><?php echo htmlspecialchars($niv['nivel_academico']); ?></td>
                            <td><?php echo intval($niv['hombres']); ?></td>
                            <td><?php echo intval($niv['mujeres']); ?></td>
                            <td>
                                <span style="background: #1e293b; padding: 4px 10px; border-radius: 4px; font-weight: bold; color: #38bdf8;">
                                    <?php echo intval($niv['cantidad_estudiantes']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
                    <label>Género:</label>
                    <select name="genero">
                        <option value="">-- Todos --</option>
                        <option value="M" <?php echo ($genero_filtro === 'M') ? 'selected' : ''; ?>>Hombres (M)</option>
                        <option value="F" <?php echo ($genero_filtro === 'F') ? 'selected' : ''; ?>>Mujeres (F)</option>
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
                            <th>Género</th>
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
                            $es_verificada = intval($row['verificado'] ?? 0) === 1;
                            $usuario_alias = htmlspecialchars($row['usuario'] ?? '');
                            $gen_label = ($row['genero'] === 'M') ? 'Hombre (M)' : (($row['genero'] === 'F') ? 'Mujer (F)' : '—');
                        ?>
                            <tr>
                                <td style="font-weight: bold; color: #fff;">
                                    <?php echo htmlspecialchars($nombre_completo); ?>
                                    
                                    <?php if($tiene_cuenta): ?>
                                        <span style="font-weight: normal; color: #9ca3af; font-size: 12px; margin-left: 4px;">
                                            (@<?php echo $usuario_alias; ?>)
                                        </span>
                                        <span class="badge badge-user"><?php echo $es_verificada ? 'Cuenta Verificada' : 'Sin Verificar'; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-nouser">Sin Cuenta</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($gen_label); ?></td>
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