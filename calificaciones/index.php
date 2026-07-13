<?php
include '../session-start.php';
include '../conexion.php';

$sql =" SELECT id_nivel,nivel_academico 
        FROM niveles";

$resultado = mysqli_query($conexion,$sql); // mysqli_query "Ejecuta la consulta que está en $sql usando la conexión $conexion y guarda el resultado en $resultado."

    if(isset($_GET['id_nivel'])){

        $id_nivel = $_GET['id_nivel'];
        $evaluacion = $_GET['evaluacion'];

        $sql_nivel_seleccionado = "
        SELECT nivel_academico
        FROM niveles
        WHERE id_nivel = $id_nivel
";
$resultado_nivel = mysqli_query($conexion, $sql_nivel_seleccionado);
   $fila_nivel = mysqli_fetch_assoc($resultado_nivel);    
$nombre_nivel = $fila_nivel['nivel_academico'];


        $sql_estudiantes = "
            SELECT 
                estudiantes.id_estudiante,
                personas.nombre,
                personas.apellido
            FROM estudiantes
            INNER JOIN personas
                ON estudiantes.id_persona = personas.id_persona
            WHERE estudiantes.id_nivel = $id_nivel
            ORDER BY personas.apellido ASC
";

$resultado_estudiantes = mysqli_query($conexion, $sql_estudiantes);

    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Calificaciones</title>
    <link rel="icon" href="../images/EFB.png" type="image/png">
    <!-- TU CSS GLOBAL -->
    <link rel="stylesheet" href="../css/mystyle.css">
</head>
<body>

<main class="main-content-class">
    
    <!-- CONTENEDOR MAESTRO QUE CENTRA TODO -->
    <div class="calificaciones-wrapper">

        <!-- CABECERA PRINCIPAL CON BOTÓN VOLVER -->
        <div class="welcome-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <h1 style="margin: 0; font-size: 1.8rem;">📝 Registrar Calificaciones</h1>
        <a href="../admin/index.php" style="color: var(--accent); text-decoration: none; font-weight: bold;">&#8592; Volver al Panel</a>
        </div>
        <!-- TARJETA 1: FILTRO DE BÚSQUEDA (AHORA ALINEADA AL ANCHO TOTAL) -->
        <div class="info-card">
            <h3>Buscar Grupo y Evaluación</h3>
            <br>
            <form method="GET" action="">
                <div class="form-group">
                    <label for="id_nivel">Nivel Académico:</label>
                    <select name="id_nivel" id="id_nivel" class="form-control" required>
                        <option value="">-- Seleccione un nivel --</option>
                        <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>
                            <option value="<?php echo $fila['id_nivel']; ?>" <?php if (isset($id_nivel) && $id_nivel == $fila['id_nivel']) { echo "selected"; } ?>>
                                <?php echo htmlspecialchars($fila['nivel_academico']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="evaluacion">Nombre de la Evaluación:</label>
                    <?php
                    $textoEvaluacion = "";
                    if(isset($evaluacion)){
                        $textoEvaluacion = htmlspecialchars(ucwords(strtolower($evaluacion)));
                    }
                    ?>
                    <input type="text" name="evaluacion" id="evaluacion" class="form-control" value="<?php echo $textoEvaluacion; ?>" placeholder="Ej: Primer Parcial" required>
                </div>

                <button type="submit" class="btn-block">Buscar estudiantes</button>
            </form>
        </div>

        <!-- TARJETA 2: LISTADO DE ESTUDIANTES (MISMA CUADRATURA Y BORDES) -->
        <?php if (isset($resultado_estudiantes)) { ?>
            <div class="info-card">
                <h3>Estudiantes Encontrados</h3>
                
                <!-- Resumen Informativo Superior -->
                <div class="info-resumen">
                    <p><strong>Nivel:</strong> <span class="text-highlight"><?php echo htmlspecialchars($nombre_nivel); ?></span></p>
                    <p><strong>Evaluación:</strong> <span class="text-highlight"><?php echo htmlspecialchars(ucwords(strtolower($evaluacion))); ?></span></p>
                </div>

                <!-- Formulario de Carga de Notas -->
                <form method="POST" action="guardar_notas.php">
                    <input type="hidden" name="id_nivel" value="<?php echo $id_nivel; ?>">
                    <input type="hidden" name="evaluacion" value="<?php echo htmlspecialchars($evaluacion); ?>">

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Nota 1</th>
                                    <th>Nota 2</th>
                                    <th>Nota Final</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($estudiante = mysqli_fetch_assoc($resultado_estudiantes)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($estudiante['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($estudiante['apellido']); ?></td>
                                        
                                        <td>
                                            <input type="number" name="nota_1[<?php echo $estudiante['id_estudiante']; ?>]" class="form-control table-input" min="0" max="20" step="0.1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="nota_2[<?php echo $estudiante['id_estudiante']; ?>]" class="form-control table-input" min="0" max="20" step="0.1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="nota_final[<?php echo $estudiante['id_estudiante']; ?>]" class="form-control table-input" min="0" max="20" step="0.1" required>
                                        </td>
                                        <td>
                                            <input type="text" name="observacion[<?php echo $estudiante['id_estudiante']; ?>]" class="form-control" style="padding: 0.4rem 0.6rem;" placeholder="Ej: Ninguna">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn-block" style="margin-top: 1.5rem;">Guardar notas</button>
                </form>
            </div>
        <?php } ?>

    </div> <!-- FIN DEL WRAPPER -->
</main>
<?php include '../script-seguridad.php'; ?>

</body>
</html>