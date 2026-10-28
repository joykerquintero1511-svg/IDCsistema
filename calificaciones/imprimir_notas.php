    <?php
    require_once '../conexion.php';

    session_start();

    if (
        !isset($_SESSION['rol']) ||
        ($_SESSION['rol'] !== 'profesor' && $_SESSION['rol'] !== 'admin')
    ) {
        header("Location: ../login.php");
        exit();
    }

    // Identificar qué calificación o nivel se desea imprimir.
    if (isset($_GET['id_calificacion'])) {

        $id_calificacion = intval($_GET['id_calificacion']);

        // Obtener el nivel real de la calificación.
        $sql_datos = "
            SELECT id_nivel
            FROM calificaciones
            WHERE id_calificacion = '$id_calificacion'
            LIMIT 1
        ";

        $resultado_datos = mysqli_query($conexion, $sql_datos);

        if (!$resultado_datos || mysqli_num_rows($resultado_datos) == 0) {
            die("No se encontró la calificación solicitada.");
        }

        $datos_calificacion = mysqli_fetch_assoc($resultado_datos);
        $id_nivel = intval($datos_calificacion['id_nivel']);
    } else {

        $id_nivel = intval($_GET['id_nivel']);
        // Obtener el nombre de la evaluación seleccionada.
        $evaluacion = trim($_GET['evaluacion']);

        // Eliminar espacios y puntos sobrantes al final.
        $evaluacion = rtrim($evaluacion, " .");

        // Proteger el nombre antes de utilizarlo en SQL.
        $evaluacion_sql = mysqli_real_escape_string($conexion, $evaluacion);
    }

    // Comprobar que el profesor tenga asignado el nivel que desea imprimir.
    //  VALIDACION DE PERMISOS

    if ($_SESSION['rol'] === 'profesor') {

        $id_usuario = intval($_SESSION['id_usuario']);

        // Comprobar que el profesor tenga asignado el nivel durante el período académico activo.

        $sql_permiso = "
            SELECT profesor_nivel.id_nivel
            FROM profesor_nivel
            INNER JOIN profesores
                ON profesor_nivel.id_profesor = profesores.id_profesor
            INNER JOIN periodos_academicos
                ON profesor_nivel.id_periodo = periodos_academicos.id_periodo
            WHERE profesores.id_usuario = '$id_usuario'
            AND profesor_nivel.id_nivel = '$id_nivel'
            AND periodos_academicos.estado = 1
            LIMIT 1
        ";

        $resultado_permiso = mysqli_query($conexion, $sql_permiso);

        // ONSULTA SI ES EL PERIODO ACTIVO
        if (!$resultado_permiso || mysqli_num_rows($resultado_permiso) == 0) {
            die("No tienes permiso para imprimir las calificaciones de este nivel.");
        }
    }

    // Buscar el período académico activo.
    $sql_periodo = "
        SELECT id_periodo
        FROM periodos_academicos
        WHERE estado = 1
        LIMIT 1
    ";

    $resultado_periodo = mysqli_query($conexion, $sql_periodo);

    // Comprobar que exista un período académico activo.
    if (!$resultado_periodo || mysqli_num_rows($resultado_periodo) == 0) {
        die("No hay un período académico activo.");
    }

    // Obtener el identificador del período activo.
    $fila_periodo = mysqli_fetch_assoc($resultado_periodo);
    $id_periodo_activo = $fila_periodo['id_periodo'];

    // Consulta
    if (isset($id_calificacion)) {
        $sql = "
        SELECT
        personas.nombre,
        personas.apellido,
        calificaciones.descripcion_nota_1,
        calificaciones.descripcion_nota_2,
        calificaciones.nota_1,
        calificaciones.nota_2,
        calificaciones.nota_final,
        calificaciones.observacion,
        calificaciones.id_nivel,
        calificaciones.evaluacion
        FROM calificaciones
        INNER JOIN estudiantes
        ON calificaciones.id_estudiante = estudiantes.id_estudiante
          INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona

        WHERE calificaciones.id_calificacion = '$id_calificacion'
        AND calificaciones.id_periodo = '$id_periodo_activo'
    ";
    } else {

        $sql = "
        SELECT
        personas.nombre,
        personas.apellido,
        calificaciones.descripcion_nota_1,
        calificaciones.descripcion_nota_2,
        calificaciones.nota_1,
        calificaciones.nota_2,
        calificaciones.nota_final,
        calificaciones.observacion

        FROM inscripciones

        INNER JOIN estudiantes
        ON inscripciones.id_estudiante = estudiantes.id_estudiante

        INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona

        LEFT JOIN calificaciones
        ON calificaciones.id_estudiante = estudiantes.id_estudiante
        AND calificaciones.id_nivel = inscripciones.id_nivel
        AND calificaciones.id_periodo = '$id_periodo_activo'
        AND TRIM(TRAILING '.' FROM TRIM(calificaciones.evaluacion)) = '$evaluacion_sql'

        WHERE inscripciones.id_nivel = '$id_nivel'
        AND inscripciones.id_periodo = '$id_periodo_activo'
        AND inscripciones.estado = 1

        ORDER BY personas.apellido ASC
    ";
    }

    $resultado = mysqli_query($conexion, $sql);

    if (isset($id_calificacion)) {
        $fila_datos = mysqli_fetch_assoc($resultado);

        if (!$fila_datos) {
            die("No se encontró la calificación.");
        }

        $id_nivel = $fila_datos['id_nivel'];
        $evaluacion = $fila_datos['evaluacion'];
        $resultado = mysqli_query($conexion, $sql);
    }

    $sql_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel'";
    $resultado_nivel = mysqli_query($conexion, $sql_nivel);
    $fila_nivel = mysqli_fetch_assoc($resultado_nivel);
    $nombre_nivel = $fila_nivel['nivel_academico'];
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Imprimir Calificaciones</title>

        <!-- Enlaces a tus hojas de estilo -->
        <link rel="stylesheet" href="../css/mystyle.css">
        <link rel="stylesheet" href="../css/movil.css">
        <link rel="icon" type="image/x-icon" href="../images/EFB.png">
    </head>

    <!-- Quitamos las clases del body para que no pelee con tus estilos globales -->

    <body>

        <!-- Aislamos el diseño en este contenedor -->
        <div class="idc-impresion-contenedor">

            <h2 class="idc-impresion-titulo">Reporte de Calificaciones</h2>

            <p class="idc-impresion-info"><strong>Nivel:</strong> <?php echo htmlspecialchars($nombre_nivel); ?></p>
            <p class="idc-impresion-info"><strong>Evaluación:</strong> <?php echo htmlspecialchars(ucwords(strtolower($evaluacion))); ?></p>

            <hr class="idc-impresion-separador">

            <?php
            // Preparar los nombres de las actividades

            $nombre_actividad_1 = "Nota 1";
            $nombre_actividad_2 = "Nota 2";

            $mostrar_actividad_1 = false;
            $mostrar_actividad_2 = false;

            while ($fila_encabezado = mysqli_fetch_assoc($resultado)) {

                // Obtener el nombre de la Actividad 1, si fue escrito
                if (
                    $fila_encabezado['descripcion_nota_1'] !== null &&
                    $fila_encabezado['descripcion_nota_1'] !== ""
                ) {
                    $nombre_actividad_1 = ucwords(
                        strtolower($fila_encabezado['descripcion_nota_1'])
                    );
                }

                // Mostrar Nota 1 solamente si algún estudiante tiene Nota 1
                if (
                    $fila_encabezado['nota_1'] !== null &&
                    $fila_encabezado['nota_1'] !== ""
                ) {
                    $mostrar_actividad_1 = true;
                }

                // Obtener el nombre de la Actividad 2, si fue escrito
                if (
                    $fila_encabezado['descripcion_nota_2'] !== null &&
                    $fila_encabezado['descripcion_nota_2'] !== ""
                ) {
                    $nombre_actividad_2 = ucwords(
                        strtolower($fila_encabezado['descripcion_nota_2'])
                    );
                }

                // Mostrar Nota 2 solamente si algún estudiante tiene Nota 2
                if (
                    $fila_encabezado['nota_2'] !== null &&
                    $fila_encabezado['nota_2'] !== ""
                ) {
                    $mostrar_actividad_2 = true;
                }
            }

            // Regresar el resultado al primer estudiante
            mysqli_data_seek($resultado, 0);
            ?>

            <table class="idc-impresion-tabla">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <?php if ($mostrar_actividad_1 == true) { ?>

                        <th><?php echo htmlspecialchars($nombre_actividad_1); ?></th>

                    <?php } ?>
                    <?php if ($mostrar_actividad_2 == true) { ?>
                        <th><?php echo htmlspecialchars($nombre_actividad_2); ?></th>
                    <?php } ?>

                    <th>Nota Final</th>
                    <th>Observación</th>
                </tr>

                <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars(ucwords(strtolower($fila['nombre']))); ?></td>
                        <td><?php echo htmlspecialchars(ucwords(strtolower($fila['apellido']))); ?></td>

                        <?php if ($mostrar_actividad_1 == true) { ?>

                            <td>
                                <?php
                                if ($fila['nota_1'] === null || $fila['nota_1'] === "") {
                                    echo "No registrada";
                                } else {
                                    echo htmlspecialchars($fila['nota_1']);
                                }
                                ?>
                            </td>

                        <?php } ?>

                        <?php if ($mostrar_actividad_2 == true) { ?>
                            <td>
                                <?php
                                if ($fila['nota_2'] == null) {
                                    echo "No registrada";
                                } else {
                                    echo htmlspecialchars($fila['nota_2']);
                                }
                                ?>
                            </td>
                        <?php } ?>

                        <td>
                            <?php
                            if ($fila['nota_final'] === null || $fila['nota_final'] === "") {
                                echo "No registrada";
                            } else {
                                echo htmlspecialchars($fila['nota_final']);
                            }
                            ?>
                        </td>


                        <td>
                            <?php
                            if ($fila['observacion'] === null || $fila['observacion'] === "") {
                                echo "Sin observación";
                            } else {
                                echo htmlspecialchars(ucwords(strtolower($fila['observacion'])));
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <br><br>

            <div class="idc-impresion-acciones">
                <button type="button" class="idc-btn-imprimir" onclick="window.print()">
                    Imprimir
                </button>

                <?php if (isset($id_calificacion)) { ?>
                    <a class="idc-btn-volver" href="editar_notas.php?id_calificacion=<?php echo urlencode($id_calificacion); ?>">
                        Volver
                    </a>
                <?php } else { ?>
                    <a class="idc-btn-volver" href="javascript:void(0)" onclick="if (window.history.length > 1) { window.history.back(); } else { window.close(); }">
                        Volver
                    </a>
                <?php } ?>
            </div>

        </div>

    </body>

    </html>