<?php
include '../session-start.php';
include '../conexion.php';

$descripcion_nota_1 = "";
$descripcion_nota_2 = "";
$evaluacion = "";
$registro_existente = false;

$sql =" SELECT id_nivel,nivel_academico 
        FROM niveles";

$resultado = mysqli_query($conexion,$sql); // mysqli_query "Ejecuta la consulta que está en $sql usando la conexión $conexion y guarda el resultado en $resultado."

           if (isset($_GET['id_nivel'])) {

    $id_nivel = $_GET['id_nivel'];

    if (isset($_GET['evaluacion'])) {
        $evaluacion = trim($_GET['evaluacion']);
    } else {
        $evaluacion = "";
}

    if (isset($_GET['descripcion_nota_1'])) {
        $descripcion_nota_1 = trim($_GET['descripcion_nota_1']);
    } else {
        $descripcion_nota_1 = "";
    }

    if (isset($_GET['descripcion_nota_2'])) {
        $descripcion_nota_2 = trim($_GET['descripcion_nota_2']);
    } else {
        $descripcion_nota_2 = "";
    }
    
  $sql_registros = "
    SELECT DISTINCT evaluacion
    FROM calificaciones
    WHERE id_nivel = '$id_nivel'
    AND evaluacion != ''
    ORDER BY evaluacion ASC
";

$resultado_registros = mysqli_query($conexion, $sql_registros);

if (!$resultado_registros) {
    die("Error al buscar los registros de calificación: " . mysqli_error($conexion));
}

if ($evaluacion != "") {
    $sql_descripciones = "
        SELECT descripcion_nota_1, descripcion_nota_2
        FROM calificaciones
        WHERE id_nivel = '$id_nivel'
        AND evaluacion = '$evaluacion'
        LIMIT 1
    ";

    $resultado_descripciones = mysqli_query($conexion, $sql_descripciones);

    if ($resultado_descripciones && mysqli_num_rows($resultado_descripciones) > 0) {
        $fila_descripciones = mysqli_fetch_assoc($resultado_descripciones);
        $descripcion_nota_1 = $fila_descripciones['descripcion_nota_1'];
        $descripcion_nota_2 = $fila_descripciones['descripcion_nota_2'];
        $registro_existente = true;
    }
}
    
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
        personas.apellido,
        calificaciones.nota_1,
        calificaciones.nota_2,
        calificaciones.nota_final,
        calificaciones.observacion

    FROM estudiantes

    INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona

    LEFT JOIN calificaciones
        ON estudiantes.id_estudiante = calificaciones.id_estudiante
        AND calificaciones.id_nivel = '$id_nivel'
        AND calificaciones.evaluacion = '$evaluacion'

    WHERE estudiantes.id_nivel = '$id_nivel'

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

    <?php
        if (isset($_GET['guardado']) && $_GET['guardado'] == 1) {
            echo "<p>Las notas fueron guardadas correctamente.</p>";
        }
        ?>
   

    <main class="main-content-class">
    
    <!-- CONTENEDOR MAESTRO QUE CENTRA TODO -->
    <div class="calificaciones-wrapper">

        <!-- CABECERA PRINCIPAL CON BOTÓN VOLVER -->
        <div class="welcome-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <h1 style="margin: 0; font-size: 1.8rem;">Registrar Calificaciones</h1>
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
                    <label for="evaluacion">Nombre del Registro de Calificación:</label>
                    <?php
                    $textoEvaluacion = "";

                    if(isset($evaluacion)){
                        $textoEvaluacion = htmlspecialchars(ucwords(strtolower($evaluacion)));
                    }
                    ?>

                    <input type="text" name="evaluacion" id="evaluacion" class="form-control" value="<?php echo $textoEvaluacion; ?>" placeholder="Ej: Primer trimestre">
                   <br><br>

                    <label>Actividad 1:</label>
            <input type="text" name="descripcion_nota_1" id="descripcion_nota_1_visible" class="form-control" value="<?php echo htmlspecialchars(ucwords(strtolower($descripcion_nota_1))); ?>" placeholder="Ej: Participación" <?php if ($registro_existente) { echo "readonly"; } ?>>
               <br><br>

                
                <label>Actividad 2:</label>

            <input type="text" name="descripcion_nota_2" id="descripcion_nota_2_visible" class="form-control" value="<?php echo htmlspecialchars(ucwords(strtolower($descripcion_nota_2))); ?>" placeholder="Ej: Participación" <?php if ($registro_existente) { echo "readonly"; } ?>>

                </div>
            <?php if ($registro_existente) { ?>
            <br>

            <button type="button" id="btnEditarRegistro" class="btn-block">
                Editar registro
            </button>

            <?php } ?>
                 <button type="submit" class="btn-block">Buscar estudiantes</button>
            <?php
                if (isset($resultado_registros) && $evaluacion == "" && mysqli_num_rows($resultado_registros) > 0 ) {
                ?>

                    <br>

                    <div class="info-resumen">

                        <p><strong>Registros de calificación existentes:</strong></p>

            <?php
          while ($registro = mysqli_fetch_assoc($resultado_registros)) {
            echo '<p><a href="?id_nivel=' . $id_nivel . '&evaluacion=' . urlencode($registro['evaluacion']) . '">' . htmlspecialchars(ucwords(strtolower($registro['evaluacion']))) . '</a></p>';
          } 
              ?>

                    </div>

                <?php
                }
            ?>
            </form>
        </div>

        <!-- TARJETA 2: LISTADO DE ESTUDIANTES (MISMA CUADRATURA Y BORDES) -->
        <?php if (isset($resultado_estudiantes)) { ?>
            <div class="info-card">
                <h3>Estudiantes Encontrados</h3>
                
                <!-- Resumen Informativo Superior -->
                <div class="info-resumen">
                    <p><strong>Nivel:</strong> <span class="text-highlight"><?php echo htmlspecialchars($nombre_nivel); ?></span></p>
                   <p><strong>Registro de Calificación:</strong>
                    <span class="text-highlight"> <?php echo htmlspecialchars(ucwords(strtolower($evaluacion))); ?>
                    </span>
                </p>
                
                </div>

                <!-- Formulario de Carga de Notas -->
                <form method="POST" action="guardar_notas.php">
                    <input type="hidden" name="id_nivel" value="<?php echo $id_nivel; ?>">
                    <input type="hidden" name="evaluacion" value="<?php echo htmlspecialchars($evaluacion); ?>">

            <input type="hidden" name="descripcion_nota_1" id="descripcion_nota_1_oculta" value="<?php echo htmlspecialchars($descripcion_nota_1); ?>">
            <input type="hidden" name="descripcion_nota_2" id="descripcion_nota_2_oculta" value="<?php echo htmlspecialchars($descripcion_nota_2); ?>">

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>
                                    <?php
                                    if ($descripcion_nota_1 != "") {
                                        echo htmlspecialchars(ucwords(strtolower($descripcion_nota_1)));
                                    } else {
                                        echo "Nota 1";
                                    }
                                    ?>
                                    </th>
                                    <th>
                                    <?php
                                        if ($descripcion_nota_2 != "") {
                                            echo htmlspecialchars(ucwords(strtolower($descripcion_nota_2)));
                                        } else {
                                            echo "Nota 2";
                                        }
                                        ?>
                                    </th>
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
                                            <input type="number" name="nota_1[<?php echo $estudiante['id_estudiante']; ?>]" value="<?php if (isset($estudiante['nota_1'])) { echo htmlspecialchars($estudiante['nota_1']); } ?>" class="form-control table-input" min="0" max="20" step="0.1">
                                        </td>
                                        <td>
                                            <input type="number" name="nota_2[<?php echo $estudiante['id_estudiante']; ?>]" value="<?php if (isset($estudiante['nota_2'])) { echo htmlspecialchars($estudiante['nota_2']); } ?>" class="form-control table-input" min="0" max="20" step="0.1">
                                        </td>
                                        <td>
                                            <input type="number" name="nota_final[<?php echo $estudiante['id_estudiante']; ?>]" value="<?php if (isset($estudiante['nota_final'])) { echo htmlspecialchars($estudiante['nota_final']); } ?>"class="form-control table-input" min="0" max="20" step="0.1">
                                        </td>
                                        <td>
                                            <input type="text" name="observacion[<?php echo $estudiante['id_estudiante']; ?>]" value="<?php if (isset($estudiante['observacion'])) { echo htmlspecialchars($estudiante['observacion']); } ?>"class="form-control" style="padding: 0.4rem 0.6rem;" placeholder="Ej: Ninguna">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

             <button type="submit" class="btn-block" style="margin-top: 1.5rem;" onclick="return confirmarGuardado();">Guardar notas</button>
                </form>
            </div>
        <?php } ?>

    </div> <!-- FIN DEL WRAPPER -->
</main>
      <script> 
         function confirmarGuardado() {
            return confirm("¿Está seguro de que desea guardar las notas?");
        }

         document.getElementById('id_nivel').addEventListener('change', function() {
         document.getElementById('evaluacion').value = '';
         document.getElementById('descripcion_nota_1_visible').value = '';
         document.getElementById('descripcion_nota_2_visible').value = '';
    });

            document.getElementById('descripcion_nota_1_visible').addEventListener('input', function() {
                if (document.getElementById('descripcion_nota_1_oculta')) {
                    document.getElementById('descripcion_nota_1_oculta').value = this.value;
                }
            });

            document.getElementById('descripcion_nota_2_visible').addEventListener('input', function() {
                if (document.getElementById('descripcion_nota_2_oculta')) {
                    document.getElementById('descripcion_nota_2_oculta').value = this.value;
                }
            });

        const botonEditarRegistro = document.getElementById('btnEditarRegistro');

        if (botonEditarRegistro) {
         botonEditarRegistro.addEventListener('click', function() {
         document.getElementById('descripcion_nota_1_visible').readOnly = false;
         document.getElementById('descripcion_nota_2_visible').readOnly = false;

        document.getElementById('descripcion_nota_1_visible').focus();

        botonEditarRegistro.textContent = 'Edición habilitada';
        botonEditarRegistro.disabled = true;
    });
}

 </script>
    

    <?php include '../script-seguridad.php'; ?>

</body>
</html>