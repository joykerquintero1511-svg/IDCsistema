<?php
include '../session-start.php';
include '../conexion.php';

// Buscar el período académico activo definido por el administrador.
$sql_periodo = "
    SELECT id_periodo, nombre_periodo
    FROM periodos_academicos
    WHERE estado = 1
    LIMIT 1
";

// Ejecutar la consulta para obtener el período activo.
$resultado_periodo = mysqli_query($conexion, $sql_periodo);

// Comprobar que la consulta se ejecutó correctamente.
if (!$resultado_periodo) {
    die("Error al consultar el período académico.");
}

// Comprobar que exista un período académico activo.
if (mysqli_num_rows($resultado_periodo) == 0) {
    die("No hay un período académico activo.");
}

// Guardar los datos del período encontrado.
$periodo_activo = mysqli_fetch_assoc($resultado_periodo);

// Guardar el identificador y el nombre del período.
$id_periodo_activo = $periodo_activo['id_periodo'];
$nombre_periodo_activo = $periodo_activo['nombre_periodo'];


// Para que el prof vea unicamente su nivel

$id_usuario = $_SESSION['id_usuario'];

$id_nivel_profesor = null;

if ($_SESSION['rol'] === 'profesor') {

    $sql_profesor = "

    SELECT id_nivel

    FROM profesores

    WHERE id_usuario = '$id_usuario'

    LIMIT 1

    ";

    $resultado_profesor = mysqli_query($conexion, $sql_profesor);

    if ($resultado_profesor && mysqli_num_rows($resultado_profesor) > 0) {

        $fila_profesor = mysqli_fetch_assoc($resultado_profesor);

        $id_nivel_profesor = $fila_profesor['id_nivel'];
    }
}


if (
    !isset($_SESSION['rol']) ||
    ($_SESSION['rol'] !== 'profesor' && $_SESSION['rol'] !== 'admin')
) {
    header("Location: ../login.php");
    exit();
}

$descripcion_nota_1 = "";
$descripcion_nota_2 = "";
$evaluacion = "";
$registro_existente = false;

# Esta consulta muestra todos los niveles al administrador
# Y solamente el nivel asignado al profesor

if ($_SESSION['rol'] === 'admin') {

    $sql = "
        SELECT id_nivel, nivel_academico
        FROM niveles
    ";
} else {

    $sql = "
        SELECT id_nivel, nivel_academico
        FROM niveles
        WHERE id_nivel = '$id_nivel_profesor'
    ";
}

$resultado = mysqli_query($conexion, $sql);
// // Bloquea el acceso a otros niveles si el profesor cambia el id_nivel en la URL
if (isset($_GET['id_nivel'])) {

    $id_nivel = intval($_GET['id_nivel']);

    if ($_SESSION['rol'] === 'profesor') {

        if ($id_nivel != $id_nivel_profesor) {

            header("Location: index.php");
            exit();
        }
    }

    // Evita que un profesor acceda a otro nivel modificando la URL
    if ($_SESSION['rol'] === 'profesor') {

        if ($id_nivel !== intval($id_nivel_profesor)) { // Intval : Es una función de PHP que convierte cualquier dato en un número entero.

            header("Location: index.php");
            exit();
        }
    }

    // Limpiar el nombre de la evaluación antes de buscar las notas.
    if (isset($_GET['evaluacion'])) {
        $evaluacion = rtrim(trim($_GET['evaluacion']), " ."); // PHP elimina los puntos finales
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


    // Mostrar los nombres de las evaluaciones sin repetir variantes por puntos finales.
    $sql_registros = "
    SELECT
    TRIM(TRAILING '.' FROM TRIM(evaluacion)) AS evaluacion
    FROM calificaciones
    WHERE id_nivel = '$id_nivel'
    AND id_periodo = '$id_periodo_activo'
    AND TRIM(evaluacion) != ''
    GROUP BY TRIM(TRAILING '.' FROM TRIM(evaluacion))
    ORDER BY evaluacion ASC
";

    $resultado_registros = mysqli_query($conexion, $sql_registros);

    if (!$resultado_registros) {
        die("Error al buscar los registros de calificación: " . mysqli_error($conexion));
    }

    // Buscar los nombres de las actividades del registro seleccionado

    $sql_descripciones = "
    SELECT descripcion_nota_1, descripcion_nota_2
    FROM calificaciones
    WHERE id_nivel = '$id_nivel'
    AND id_periodo = '$id_periodo_activo'
    AND evaluacion = '$evaluacion'
    LIMIT 1
";

    $resultado_descripciones = mysqli_query(
        $conexion,
        $sql_descripciones
    );

    if (
        $resultado_descripciones &&
        mysqli_num_rows($resultado_descripciones) > 0
    ) {
        $fila_descripciones = mysqli_fetch_assoc(
            $resultado_descripciones
        );

        $descripcion_nota_1 =
            $fila_descripciones['descripcion_nota_1'];

        $descripcion_nota_2 =
            $fila_descripciones['descripcion_nota_2'];

        $registro_existente = true;
    }


    $sql_nivel_seleccionado = "
        SELECT nivel_academico
        FROM niveles
        WHERE id_nivel = $id_nivel
";
    $resultado_nivel = mysqli_query($conexion, $sql_nivel_seleccionado);
    $fila_nivel = mysqli_fetch_assoc($resultado_nivel);
    $nombre_nivel = $fila_nivel['nivel_academico'];

    // Consultar los estudiantes y sus calificaciones del período activo.
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

        INNER JOIN inscripciones
            ON inscripciones.id_estudiante = estudiantes.id_estudiante
            AND inscripciones.id_nivel = '$id_nivel'
            AND inscripciones.id_periodo = '$id_periodo_activo'
            AND inscripciones.estado = 1

        LEFT JOIN calificaciones
            ON estudiantes.id_estudiante = calificaciones.id_estudiante
            AND calificaciones.id_nivel = '$id_nivel'
            AND calificaciones.id_periodo = '$id_periodo_activo'
            AND TRIM(TRAILING '.' FROM TRIM(calificaciones.evaluacion)) = '$evaluacion'
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

            <?php
            // Mostrar un mensaje de confirmación después de guardar las calificaciones.
            if (isset($_GET['guardado']) && $_GET['guardado'] == 1) {
            ?>
                <div style="background-color:#e8f7ee; color:#216e42; padding:15px;
                border:1px solid #b7e4c7; border-radius:8px;
                margin-bottom:20px; text-align:center; font-weight:600;">

                    ✓ Las calificaciones se guardaron correctamente.

                </div>
            <?php
            }
            ?>

            <!-- CABECERA PRINCIPAL CON BOTÓN VOLVER -->
            <div class="welcome-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <h1 style="margin: 0; font-size: 1.8rem;">Registrar Calificaciones</h1>

                <!-- Mostrar el período académico activo definido por el administrador -->
                <p style="color: #3b82f6; font-weight: bold;">
                    Período activo:
                    <?php echo htmlspecialchars($nombre_periodo_activo); ?>
                </p>
                <?php // Para q el boton lo rediriga depende de su rol

                if ($_SESSION['rol'] === 'admin') { ?>
                    <a class="btn-action" href="../admin/index.php">Volver</a>

                <?php

                } else { ?>
                    <a class="btn-action" href="../profesores/index.php">Volver</a>

                <?php

                }
                ?>

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
                            <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                                <option value="<?php echo $fila['id_nivel']; ?>" <?php if (isset($id_nivel) && $id_nivel == $fila['id_nivel']) {
                                                                                        echo "selected";
                                                                                    } ?>>
                                    <?php echo htmlspecialchars($fila['nivel_academico']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="evaluacion">Nombre del Registro de Calificación:</label>
                        <?php
                        $textoEvaluacion = "";

                        if (isset($evaluacion)) {
                            $textoEvaluacion = htmlspecialchars(ucwords(strtolower($evaluacion)));
                        }
                        ?>

                        <!-- Permitir escribir una evaluación o seleccionar una existente. -->
                        <input
                            type="text"
                            name="evaluacion"
                            id="evaluacion"
                            class="form-control"
                            autocomplete="off"
                            list="lista_evaluaciones"
                            value="<?php echo $textoEvaluacion; ?>"
                            placeholder="Escriba o seleccione una evaluación"
                            required>


                        <datalist id="lista_evaluaciones">
                            <?php
                            if (isset($resultado_registros)) {
                                mysqli_data_seek($resultado_registros, 0);

                                while ($registro = mysqli_fetch_assoc($resultado_registros)) {
                                    echo '<option value="' . htmlspecialchars($registro['evaluacion'], ENT_QUOTES, 'UTF-8') . '">';
                                }

                                mysqli_data_seek($resultado_registros, 0);
                            }
                            ?>
                        </datalist>

                        <br><br>

                        <label>Actividad 1:</label>
                        <input type="text" name="descripcion_nota_1" id="descripcion_nota_1_visible" class="form-control" value="<?php echo htmlspecialchars(ucwords(strtolower($descripcion_nota_1))); ?>" placeholder="Ej: Participación" <?php if ($registro_existente) {
                                                                                                                                                                                                                                                echo "readonly";
                                                                                                                                                                                                                                            } ?>>
                        <br><br>


                        <label>Actividad 2:</label>

                        <input type="text" name="descripcion_nota_2" id="descripcion_nota_2_visible" class="form-control" value="<?php echo htmlspecialchars(ucwords(strtolower($descripcion_nota_2))); ?>" placeholder="Ej: Participación" <?php if ($registro_existente) {
                                                                                                                                                                                                                                                echo "readonly";
                                                                                                                                                                                                                                            } ?>>

                    </div>
                    <?php if ($registro_existente) { ?>
                        <br>

                        <button type="button" id="btnEditarRegistro" class="btn-block">
                            Editar registro
                        </button>

                    <?php } ?>

                    <button type="submit" class="btn-block">Buscar estudiantes</button>

                    <!-- Limpiar la búsqueda y comenzar un nuevo registro. -->
                    <a href="index.php" class="btn-block" style="display:inline-block; text-decoration:none; margin-left:10px;">
                        Limpiar
                    </a>

                    <?php
                    if (isset($resultado_registros) && $evaluacion == "" && mysqli_num_rows($resultado_registros) > 0) {
                    ?>

                        <br>

                        <div class="info-resumen">

                            <p><strong>Registros de calificación existentes:</strong></p>

                            <?php
                            while ($registro = mysqli_fetch_assoc($resultado_registros)) {
                                echo '<p style="margin: 0.4rem 0; color: #3d8cfb; text-decoration: underline;">' . '<a href="?id_nivel=' . $id_nivel . '&evaluacion=' . urlencode($registro['evaluacion']) . '">' . htmlspecialchars(ucwords(strtolower($registro['evaluacion']))) . '</a></p>';
                            }
                            ?>

                        </div>

                    <?php
                    }
                    ?>

                </form>

                <!-- Cambiar el nombre de una evaluación existente. -->
                <?php if ($registro_existente) { ?>

                    <form method="POST" action="renombrar_evaluacion.php"
                        style="margin-top:20px;">

                        <!-- Identificar el nivel y la evaluación original. -->
                        <input type="hidden" name="id_nivel"
                            value="<?php echo htmlspecialchars((string) ($id_nivel ?? '')); ?>">

                        <input type="hidden" name="nombre_anterior" id="nombre_anterior">

                        <script>
                            document.getElementById('nombre_anterior').value =
                                document.getElementById('evaluacion').value;
                        </script>


                        <label for="nombre_nuevo">Nuevo nombre de la evaluación:</label>

                        <input type="text"
                            name="nombre_nuevo"
                            id="nombre_nuevo"
                            class="form-control"
                            placeholder="Escriba el nuevo nombre"
                            required>

                        <button type="submit" class="btn-block"
                            style="margin-top:10px;">
                            Cambiar nombre
                        </button>

                    </form>

                <?php } ?>

            </div>

            <!-- TARJETA 2: LISTADO DE ESTUDIANTES (MISMA CUADRATURA Y BORDES) -->

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

                        <!-- Enviar el identificador del período académico activo -->
                        <input type="hidden" name="id_periodo" value="<?php echo $id_periodo_activo; ?>">

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
                                    <?php while ($estudiante = mysqli_fetch_assoc($resultado_estudiantes)) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($estudiante['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($estudiante['apellido']); ?></td>

                                            <td>
                                                <input type="number" name="nota_1[<?php echo $estudiante['id_estudiante']; ?>]" value="<?php if (isset($estudiante['nota_1'])) {
                                                                                                                                            echo htmlspecialchars($estudiante['nota_1']);
                                                                                                                                        } ?>" class="form-control table-input" min="0" max="20" step="0.1">
                                            </td>
                                            <td>
                                                <input type="number" name="nota_2[<?php echo $estudiante['id_estudiante']; ?>]" value="<?php if (isset($estudiante['nota_2'])) {
                                                                                                                                            echo htmlspecialchars($estudiante['nota_2']);
                                                                                                                                        } ?>" class="form-control table-input" min="0" max="20" step="0.1">
                                            </td>
                                            <td>
                                                <input type="number" name="nota_final[<?php echo $estudiante['id_estudiante']; ?>]" value="<?php if (isset($estudiante['nota_final'])) {
                                                                                                                                                echo htmlspecialchars($estudiante['nota_final']);
                                                                                                                                            } ?>" class="form-control table-input" min="0" max="20" step="0.1">
                                            </td>
                                            <td>
                                                <input type="text" name="observacion[<?php echo $estudiante['id_estudiante']; ?>]" value="<?php if (isset($estudiante['observacion'])) {
                                                                                                                                                echo htmlspecialchars($estudiante['observacion']);
                                                                                                                                            } ?>" class="form-control" style="padding: 0.4rem 0.6rem;" placeholder="Ej: Ninguna">
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="display:flex;justify-content:center;gap:15px;margin-top:20px;">
                            <!-- Guardar las calificaciones sin mostrar una ventana de confirmación. -->
                            <button type="submit" class="btn-block" style="width:auto;padding:12px 22px;">Guardar notas</button>
                            <a href="ver_notas.php?id_nivel=<?php echo $id_nivel; ?>&evaluacion=<?php echo urlencode($evaluacion); ?>" class="btn-block" style="display:inline-block;width:auto;padding:12px 22px;text-decoration:none;">Ver Calificaciones</a>
                        </div>
                    </form>
                </div>
            <?php } ?>

        </div> <!-- FIN DEL WRAPPER -->
    </main>
    <script>
        document.getElementById('id_nivel').addEventListener('change', function() {
            document.getElementById('evaluacion').value = '';
            document.getElementById('descripcion_nota_1_visible').value = '';
            document.getElementById('descripcion_nota_2_visible').value = '';

            // Buscar las evaluaciones del nivel seleccionado.
            cargarEvaluaciones(); // Aquí estamos llamando a la función
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

        // Consultar las evaluaciones del nivel seleccionado.
        function cargarEvaluaciones() {

            let nivel = document.getElementById('id_nivel').value;
            let lista = document.getElementById('lista_evaluaciones');

            // Limpiar las opciones anteriores.
            lista.innerHTML = '';

            if (nivel === '') {
                return;
            }

            // Enviar el nivel al archivo PHP.
            let formData = new FormData();
            formData.append('id_nivel', nivel);

            fetch('buscar_evaluaciones.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {

                    // Mostrar las evaluaciones recibidas.
                    data.forEach(function(evaluacion) { // Recorre las evaluaciones para colocarlas en la lista. Este es el único concepto nuevo importante de este fragmento.
                        let opcion = document.createElement('option');
                        opcion.value = evaluacion;
                        lista.appendChild(opcion);
                    });

                })
                .catch(error => {
                    console.error('Error al buscar evaluaciones:', error);
                });
        }
    </script>


    <?php include '../script-seguridad.php'; ?>

</body>

</html>