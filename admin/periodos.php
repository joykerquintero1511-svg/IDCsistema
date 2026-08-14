<?php
include('../session-start.php');
include '../conexion.php'; 

// 1. LÓGICA PARA REGISTRAR UN NUEVO PERÍODO
if (isset($_POST['guardar_periodo'])) {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre_periodo']));
    
    // Al crearse, se registra como inactivo (0) por defecto
    $sql_insert = "INSERT INTO periodos_academicos (nombre_periodo, estado) VALUES ('$nombre', 0)";
    if (mysqli_query($conexion, $sql_insert)) {
        header("Location: periodos.php?msg=Período creado con éxito");
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
        header("Location: periodos.php?msg=Período académico activado correctamente");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Períodos Académicos - EFB</title>
    <link rel="icon" type="image/png" href="../images/EFB.png">

    <!-- HOJAS DE ESTILO EXTERNAS -->
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
</head>
<body>

<!-- BARRA LATERAL ADMINISTRATIVA -->
<?php include 'sidebaradmin.php'; ?>

<!-- CONTENEDOR PRINCIPAL -->
<main class="main-content">
    
    <!-- TÍTULO PRINCIPAL (Sin clases extrañas para que mantenga el tamaño grande de tu sistema) -->
    <h1 style="margin-bottom: 5px;">Configuración de Períodos Académicos</h1>
    <p style="color: #94a3b8; margin-bottom: 30px;">Gestión de lapsos activos y cohortes del sistema escolar.</p>

    <!-- MENSAJES DE ALERTA -->
    <?php if (isset($_GET['msg'])): ?>
        <div style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ✔️ <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ❌ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- TARJETA: NUEVO PERÍODO (Usando el mismo estilo de "Reporte de Estudiantes") -->
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #fff;">NUEVO PERÍODO</h3>
        <form action="periodos.php" method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #e2e8f0; font-size: 14px; margin-bottom: 8px; font-weight: 500;">Nombre del Lapso / Cohorte:</label>
                <input type="text" name="nombre_periodo" placeholder="Ej: Cohorte 2026-I" required autocomplete="off" 
                       style="width: 100%; padding: 12px; background: #1f2937; border: 1px solid #374151; color: #fff; border-radius: 6px; box-sizing: border-box; font-size: 14px; outline: none;">
            </div>
            <button type="submit" name="guardar_periodo" 
                    style="width: 100%; background: #2563eb; color: white; border: none; padding: 14px; border-radius: 6px; font-weight: bold; font-size: 15px; cursor: pointer; transition: 0.2s;">
                Registrar Período
            </button>
        </form>
    </div>

    <!-- TARJETA: HISTORIAL DE LAPSOS -->
    <div class="card">
        <h3 style="margin-bottom: 20px; color: #fff;">HISTORIAL DE LAPSOS</h3>
        <div style="width: 100%; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr>
                        <th style="padding: 12px; border-bottom: 1px solid #1f2937; color: #9ca3af; font-size: 13px; text-transform: uppercase;">ID</th>
                        <th style="padding: 12px; border-bottom: 1px solid #1f2937; color: #9ca3af; font-size: 13px; text-transform: uppercase;">Nombre del Período</th>
                        <th style="padding: 12px; border-bottom: 1px solid #1f2937; color: #9ca3af; font-size: 13px; text-transform: uppercase;">Estado</th>
                        <th style="padding: 12px; border-bottom: 1px solid #1f2937; color: #9ca3af; font-size: 13px; text-transform: uppercase;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado_periodos && mysqli_num_rows($resultado_periodos) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($resultado_periodos)): ?>
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #1f2937; color: #cbd5e1;"><?php echo $row['id_periodo']; ?></td>
                                <td style="padding: 12px; border-bottom: 1px solid #1f2937; font-weight: 600; color: #ffffff;">
                                    <?php echo htmlspecialchars($row['nombre_periodo']); ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #1f2937;">
                                    <?php if ($row['estado'] == 1): ?>
                                        <span style="background-color: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span style="background-color: rgba(107, 114, 128, 0.2); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.3); padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #1f2937;">
                                    <?php if ($row['estado'] == 0): ?>
                                        <a href="periodos.php?activar_id=<?php echo $row['id_periodo']; ?>" 
                                           style="background: #2563eb; color: #fff; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: bold; display: inline-block;">
                                            Marcar como Activo
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #60a5fa; font-weight: bold; font-size: 13px;">En uso ✨</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="padding: 15px; text-align: center; color: #9ca3af; border-bottom: 1px solid #1f2937;">No hay períodos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<?php include '../script-seguridad.php'; ?>

</body>
</html>