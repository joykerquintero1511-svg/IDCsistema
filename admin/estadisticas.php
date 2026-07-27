<?php
session_start();
include("../conexion.php");

// Seguridad: Verificar que sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Variables para los filtros
$nivel_filtro = isset($_GET['id_nivel']) ? intval($_GET['id_nivel']) : '';
$nota_min = isset($_GET['nota_min']) ? floatval($_GET['nota_min']) : '';
$nota_max = isset($_GET['nota_max']) ? floatval($_GET['nota_max']) : '';

// Cargar niveles para el desplegable del filtro
$niveles_query = mysqli_query($conexion, "SELECT id_nivel, nivel_academico FROM niveles ORDER BY id_nivel ASC");

// Consulta ajustada a las columnas reales: nivel_academico y nota_final
$sql_resultado = "SELECT e.id_estudiante, u.usuario, n.nivel_academico, c.nota_final 
                  FROM estudiantes e
                  INNER JOIN usuarios u ON e.id_persona = u.id_usuario
                  LEFT JOIN calificaciones c ON e.id_estudiante = c.id_estudiante
                  LEFT JOIN niveles n ON e.id_nivel = n.id_nivel
                  WHERE 1=1";

if ($nivel_filtro != '') {
    $sql_resultado .= " AND e.id_nivel = $nivel_filtro";
}
if ($nota_min !== '' && $nota_max !== '') {
    $sql_resultado .= " AND c.nota_final BETWEEN $nota_min AND $nota_max";
}

$resultados = mysqli_query($conexion, $sql_resultado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas Avanzadas - EFB</title>
    <link rel="icon" href="./images/EFB.png" type="image/png">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0b0f19; color: #e2e8f0; margin: 0; padding: 30px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .card { background: #111827; border: 1px solid #1f2937; border-radius: 10px; padding: 25px; margin-bottom: 25px; }
        h1, h2, h3 { color: #f8fafc; margin-top: 0; }
        .btn-volver { color: #60a5fa; text-decoration: none; font-weight: bold; display: inline-block; margin-bottom: 20px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end; }
        label { display: block; color: #9ca3af; font-size: 13px; margin-bottom: 5px; }
        select, input { width: 100%; padding: 10px; background: #1f2937; border: 1px solid #374151; color: #fff; border-radius: 6px; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #1f2937; }
        th { background: #1f2937; color: #9ca3af; font-size: 13px; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="btn-volver">← Volver al Panel Maestro</a>
    
    <h1>Módulo de Estadísticas y Filtros Avanzados 📊</h1>
    <p style="color: #94a3b8;">Filtra información de alumnos según niveles académicos y rendimientos específicos.</p>

    <!-- FORMULARIO DE FILTROS PERSONALIZADOS -->
    <div class="card">
        <h3>Filtro Personalizado de Alumnos</h3>
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
                    <label>Nota Mínima:</label>
                    <input type="number" step="0.1" name="nota_min" placeholder="Ej: 10" value="<?php echo isset($_GET['nota_min']) ? $_GET['nota_min'] : ''; ?>">
                </div>

                <div>
                    <label>Nota Máxima:</label>
                    <input type="number" step="0.1" name="nota_max" placeholder="Ej: 20" value="<?php echo isset($_GET['nota_max']) ? $_GET['nota_max'] : ''; ?>">
                </div>

                <div>
                    <button type="submit">Buscar Consulta</button>
                    <a href="estadisticas.php" style="color: #9ca3af; margin-left: 10px; text-decoration: none; font-size: 13px;">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <!-- TABLA DE RESULTADOS OBTENIDOS -->
    <div class="card">
        <h3>Resultados de la Búsqueda (<?php echo $resultados ? mysqli_num_rows($resultados) : 0; ?> Alumnos encontrados)</h3>
        
        <?php if($resultados && mysqli_num_rows($resultados) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Nivel Académico</th>
                        <th>Nota Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($resultados)): ?>
                        <tr>
                            <td style="font-weight: bold;"><?php echo htmlspecialchars($row['usuario']); ?></td>
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
        <?php else: ?>
            <p style="color: #94a3b8; margin-top: 20px;">No se encontraron registros que coincidan con los criterios seleccionados.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>