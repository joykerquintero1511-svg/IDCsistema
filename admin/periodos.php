<?php
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
    <title>Gestión de Períodos Académicos</title>
    <link rel="stylesheet" href="../estilos/style.css"> <!-- Tu CSS general -->
    <style>
        body { background-color: #0c0c0c; color: #ffffff; font-family: 'Segoe UI', sans-serif; margin: 0; }
        .main-content { margin-left: 260px; padding: 2.5rem; box-sizing: border-box; }
        .header-title { font-size: 1.8rem; margin-bottom: 2rem; }
        
        .grid-layout { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; }
        .card { background-color: #111111; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.4); }
        .card h3 { margin-top: 0; margin-bottom: 1.5rem; color: #3a7bc8; }
        
        /* Formulario */
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #aaaaaa; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 0.7rem; background-color: #1a1a1a; border: 1px solid #333; border-radius: 6px; color: #fff; box-sizing: border-box; }
        .btn-submit { background-color: #3a7bc8; color: #fff; border: none; padding: 0.7rem 1.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%; }
        .btn-submit:hover { background-color: #2e62a1; }
        
        /* Tablas */
        .table-custom { width: 100%; border-collapse: collapse; text-align: left; }
        .table-custom th { padding: 0.8rem; background-color: #1a1a1a; color: #888; font-size: 0.85rem; text-transform: uppercase; }
        .table-custom td { padding: 1rem 0.8rem; border-bottom: 1px solid #222; font-size: 0.95rem; }
        
        /* Badges de Estado */
        .badge { padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .badge-active { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
        .badge-inactive { background-color: rgba(108, 117, 125, 0.2); color: #6c757d; }
        
        .btn-action-active { color: #3a7bc8; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .btn-action-active:hover { text-decoration: underline; }
        
        .alert { padding: 0.8rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.95rem; }
        .alert-success { background-color: rgba(40, 167, 69, 0.15); color: #28a745; border: 1px solid #28a745; }
        .alert-danger { background-color: rgba(255, 85, 85, 0.15); color: #ff5555; border: 1px solid #ff5555; }
    </style>
</head>
<body>

<main class="main-content">
    <h1 class="header-title">📅 Configuración de Períodos Académicos</h1>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">✔️ <?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">❌ <?php echo $error; ?></div>
    <?php endif; ?>

    <div class="grid-layout">
        <!-- FORMULARIO DE REGISTRO -->
        <div class="card">
            <h3>Nuevo Período</h3>
            <form action="periodos.php" method="POST">
                <div class="form-group">
                    <label for="nombre_periodo">Nombre del Lapso / Cohorte</label>
                    <input type="text" id="nombre_periodo" name="nombre_periodo" class="form-control" placeholder="Ej: Cohorte 2026-I" required>
                </div>
                <button type="submit" name="guardar_periodo" class="btn-submit">Registrar Período</button>
            </form>
        </div>

        <!-- LISTADO DE PERÍODOS -->
        <div class="card">
            <h3>Historial de Lapsos</h3>
            <table class="table-custom">
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
                            <td><strong><?php echo htmlspecialchars($row['nombre_periodo']); ?></strong></td>
                            <td>
                                <?php if ($row['estado'] == 1): ?>
                                    <span class="badge badge-active">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['estado'] == 0): ?>
                                    <a href="periodos.php?activar_id=<?php echo $row['id_periodo']; ?>" class="btn-action-active" onclick="return confirm('¿Seguro que deseas activar este período? Esto desactivará el actual.');">Marcar como Activo</a>
                                <?php else: ?>
                                    <span style="color: #28a745; font-size: 0.9rem;">En uso ✨</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<a href="cronograma.php" class="boton-volver">Cronograma</a>


<a href="index.php" class="boton-volver">Volver al Inicio</a>

</body>
</html>