<?php
include('../session-start.php');
include '../conexion.php'; // Ajusta la ruta según tu estructura de carpetas

// 1. LÓGICA PARA REGISTRAR UN NUEVO PERÍODO
if (isset($_POST['guardar_periodo'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_periodo']);
    
    // Al crearse, se registra como inactivo (0) por defecto
    $sql_insert = "INSERT INTO periodos_academicos (nombre_periodo, estado) VALUES ('$nombre', 0)";
    if (mysqli_query($conexion, $sql_insert)) {
        header("Location: periodos.php?msg=Periodo creado con éxito");
        exit();
    } else {
        $error = "Error al crear el período: " . mysqli_error($conexion);
    }
}

// 2. LÓGICA PARA ACTIVAR UN PERÍODO (Y desactivar todos los demás)
if (isset($_GET['activar_id'])) {
    $id_activar = intval($_GET['activar_id']);
    
    // Paso A: Apagar todos los períodos existentes (ponerlos en 0)
    mysqli_query($conexion, "UPDATE periodos_academicos SET estado = 0");
    
    // Paso B: Activar únicamente el seleccionado (ponerlo en 1)
    $sql_activar = "UPDATE periodos_academicos SET estado = 1 WHERE id_periodo = $id_activar";
    
    if (mysqli_query($conexion, $sql_activar)) {
        header("Location: periodos.php?msg=Periodo academico activado");
        exit();
    } else {
        $error = "Error al activar el período: " . mysqli_error($conexion);
    }
}

// 3. CONSULTAR TODOS LOS PERÍODOS EXISTENTES
$resultado_periodos = mysqli_query($conexion, "SELECT * FROM periodos_academicos ORDER BY id_periodo DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Períodos Académicos - EFB</title>
        <link rel="icon" type="image/png" href="../images/EFB.png">

    <!-- CONEXIÓN AL CSS GLOBAL -->
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
        <li><a href="periodos.php" class="active">Períodos Académicos</a></li>
        <li><a href="cronograma.php">Cronograma</a></li>
        <li><a href="index.php">Volver al Inicio</a></li>
    </ul>
</div>

<!-- CONTENEDOR PRINCIPAL -->
<main class="main-content">
    
    <!-- CABECERA DE LA SECCIÓN -->
    <div class="welcome-header">
        <h1>🗓️ Configuración de Períodos Académicos</h1>
        <p>Gestión de lapsos activos y cohortes del sistema escolar</p>
    </div>

    <!-- SECCIÓN DE ALERTAS Y MENSAJES PHP -->
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

    <!-- REJILLA DE DOS COLUMNAS (DASHBOARD GRID) -->
    <div class="dashboard-grid">
        
        <!-- COLUMNA IZQUIERDA: FORMULARIO NUEVO PERÍODO -->
        <div class="info-card">
            <h3>Nuevo Período</h3>
            <form action="periodos.php" method="POST" style="margin-top: 1rem;">
                <div class="form-group">
                    <label for="nombre_periodo">Nombre del Lapso / Cohorte</label>
                    <input type="text" id="nombre_periodo" name="nombre_periodo" class="form-control" placeholder="Ej: Cohorte 2026-I" required>
                </div>
                <button type="submit" name="guardar_periodo" class="btn-action" style="width: 100%; text-align: center; margin-top: 1rem; justify-content: center; background-color:transparent;">
                    Registrar Período
                </button>
            </form>
        </div>

        <!-- COLUMNA DERECHA: HISTORIAL DE LAPSOS -->
        <div class="info-card">
            <h3>Historial de Lapsos</h3>
            <div style="overflow-x: auto; margin-top: 1rem;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Período</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($resultado_periodos)): ?>
                            <tr>
                                <td><?php echo $row['id_periodo']; ?></td>
                                <td style="font-weight: 600; color: #ffffff;">
                                    <?php echo htmlspecialchars($row['nombre_periodo']); ?>
                                </td>
                                <td>
                                    <?php if ($row['estado'] == 1): ?>
                                        <span class="badge-nota" style="background-color: rgba(40, 167, 69, 0.2); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.4);">
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-nota" style="background-color: rgba(108, 117, 125, 0.2); color: #94a3b8; border: 1px solid rgba(108, 117, 125, 0.4);">
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['estado'] == 0): ?>
                                        <a href="periodos.php?activar_id=<?php echo $row['id_periodo']; ?>" class="btn-action" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                            Marcar como Activo
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #3b82f6; font-weight: 600; font-size: 0.9rem;">En uso ✨</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

    <?php include '../script-seguridad.php'; ?>

</body>
</html>