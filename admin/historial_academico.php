<?php
session_start();

// Validar que sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

// 1. OBTENER TODOS LOS PERÍODOS DE LA TABLA 'periodos_academicos'
$query_periodos = "SELECT * FROM periodos_academicos ORDER BY id_periodo DESC";
$res_periodos = mysqli_query($conexion, $query_periodos);

// 2. DETERMINAR EL PERÍODO A CONSULTAR
$periodo_consulta = isset($_GET['id_periodo']) ? intval($_GET['id_periodo']) : 0;
$nombre_periodo_seleccionado = "";

if ($periodo_consulta == 0 && $res_periodos && mysqli_num_rows($res_periodos) > 0) {
    // Tomar el primer período por defecto si no se ha seleccionado ninguno
    mysqli_data_seek($res_periodos, 0);
    $primer_p = mysqli_fetch_assoc($res_periodos);
    $periodo_consulta = intval($primer_p['id_periodo']);
    $nombre_periodo_seleccionado = $primer_p['nombre_periodo'] ?? $primer_p['lapso'] ?? 'Período Actual';
} else {
    // Obtener el nombre del período seleccionado actual
    $q_nom = mysqli_query($conexion, "SELECT * FROM periodos_academicos WHERE id_periodo = $periodo_consulta LIMIT 1");
    if ($dt_nom = mysqli_fetch_assoc($q_nom)) {
        $nombre_periodo_seleccionado = $dt_nom['nombre_periodo'] ?? $dt_nom['lapso'] ?? '';
    }
}

// 3. CONSULTAR PROFESORES ASIGNADOS EN ESTE PERÍODO ESPECÍFICO
$query_hp = "SELECT n.nivel_academico, p.nombre, p.apellido 
             FROM profesor_nivel pn
             INNER JOIN niveles n ON pn.id_nivel = n.id_nivel
             INNER JOIN profesores p ON pn.id_profesor = p.id_profesor
             WHERE pn.id_periodo = $periodo_consulta
             ORDER BY n.id_nivel ASC";
$res_hp = mysqli_query($conexion, $query_hp);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Académico por Período - EFB</title>
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="stylesheet" href="../css/movil.css">
    <link rel='icon' href='../images/EFB.png' type='image/x-icon' sizes="16x16">
</head>
<body>

    <?php include "sidebaradmin.php"; ?>

    <main class="main-content">
        <div class="certificados-header">
            <h1>📜 Historial Académico y Registros por Período</h1>
            <p>Consulta el respaldo histórico de docentes y niveles según la cohorte seleccionada.</p>
        </div>

        <!-- SELECTOR DE PERÍODO -->
        <div class="certificados-card">
            <form method="GET" action="">
                <label style="display: block; color: #cbd5e1; margin-bottom: 8px; font-weight: bold;">📅 Seleccionar Cohorte / Período Histórico:</label>
                <select name="id_periodo" onchange="this.form.submit()" class="promocion-select" style="width: 100%; padding: 10px; background: #0f172a; color: #fff; border: 1px solid #334155; border-radius: 6px;">
                    <?php 
                    // Reiniciamos el puntero del resultado para recorrerlo en el select
                    mysqli_data_seek($res_periodos, 0);
                    while($p = mysqli_fetch_assoc($res_periodos)): 
                        $nombre_p = $p['nombre_periodo'] ?? $p['lapso'] ?? 'Período #' . $p['id_periodo'];
                        $es_activo = (isset($p['estado']) && $p['estado'] == 'Activo');
                    ?>
                        <option value="<?php echo $p['id_periodo']; ?>" <?php if($periodo_consulta == $p['id_periodo']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($nombre_p); ?> 
                            <?php echo $es_activo ? '⭐ (Activo)' : '(Histórico)'; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <!-- RESULTADOS DE PROFESORES POR NIVEL EN ESTE PERÍODO -->
        <div class="certificados-card">
            <h2>Docentes Asignados en el período: <span style="color: #38bdf8;"><?php echo htmlspecialchars($nombre_periodo_seleccionado); ?></span></h2>

            <?php if ($res_hp && mysqli_num_rows($res_hp) > 0): ?>
                <div class="certificados-list">
                    <?php while($row = mysqli_fetch_assoc($res_hp)): ?>
                        <div class="certificado-item" style="display: flex; justify-content: space-between; padding: 12px; background: #0f172a; margin-bottom: 8px; border-radius: 6px;">
                            <span class="nivel-nombre" style="color: #f1f5f9;">
                                📚 <strong><?php echo htmlspecialchars($row['nivel_academico']); ?></strong>
                            </span>
                            <span style="color: #94a3b8;">
                                👨‍🏫 Profesor(a): <strong style="color: #e2e8f0;"><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></strong>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="certificados-empty">No hay registros de profesores asignados para este período seleccionado.</p>
            <?php endif; ?>
        </div>

        <div class="certificados-actions">
            <a href="periodos.php" class="btn-volver">← Volver a Gestión de Períodos</a>
        </div>
    </main>

</body>
</html>