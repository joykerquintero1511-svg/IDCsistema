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

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        die("Acceso no permitido");
    }

    $id_nivel = intval($_POST['id_nivel']);

    // Quitar espacios y puntos finales del nombre de la evaluación.
    $evaluacion = trim($_POST['evaluacion']);
    $evaluacion = rtrim($evaluacion, " .");

    // Proteger el nombre de la evaluación para las consultas SQL.
    $evaluacion_sql = mysqli_real_escape_string($conexion, $evaluacion);

    // Comprobar que el profesor tenga asignado el nivel donde registrará las notas.
    if ($_SESSION['rol'] === 'profesor') {

        $id_usuario = intval($_SESSION['id_usuario']);

        // Comprobar que el profesor tenga asignado el nivel
        // específicamente durante el período académico activo.
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

        if (!$resultado_permiso || mysqli_num_rows($resultado_permiso) == 0) {
            die("No tienes permiso para registrar notas en este nivel.");
        }
    }

    // Buscar el período académico activo definido por el administrador.
    $sql_periodo = "
    SELECT id_periodo
    FROM periodos_academicos
    WHERE estado = 1
    LIMIT 1
";

    // Ejecutar la consulta.
    $resultado_periodo = mysqli_query($conexion, $sql_periodo);

    // Comprobar si ocurrió un error al consultar.
    if (!$resultado_periodo) {
        die("Error al consultar el período académico.");
    }

    // Impedir guardar notas si no existe un período activo.
    if (mysqli_num_rows($resultado_periodo) == 0) {
        die("No hay un período académico activo.");
    }

    // Obtener el identificador del período activo.
    $fila_periodo = mysqli_fetch_assoc($resultado_periodo);
    $id_periodo_activo = $fila_periodo['id_periodo'];

    // Preparar la dirección para regresar al formulario.
    $url_regreso = "index.php?id_nivel=" . $id_nivel .
        "&evaluacion=" . urlencode($evaluacion);

    // Comprobar si el administrador cambió el período académico.
    if (
        !isset($_POST['id_periodo']) ||
        intval($_POST['id_periodo']) != intval($id_periodo_activo)
    ) {

        header("Location: " . $url_regreso . "&error=periodo_cambiado");
        exit();
    }

    // Evitar guardar calificaciones sin un nombre de registro
    if ($evaluacion === "") {
        die("Debe indicar el nombre del registro de calificación.");
    }

    // Capturar las descripciones de las actividades.
    $descripcion_nota_1 = trim($_POST['descripcion_nota_1']);
    $descripcion_nota_2 = trim($_POST['descripcion_nota_2']);

    // Proteger las descripciones antes de guardarlas en MySQL.
    $descripcion_nota_1 = mysqli_real_escape_string($conexion, $descripcion_nota_1);
    $descripcion_nota_2 = mysqli_real_escape_string($conexion, $descripcion_nota_2);

    // Obtener las notas enviadas desde el formulario.
    if (isset($_POST['nota_1'])) {
        $notas_1 = $_POST['nota_1'];
    } else {
        $notas_1 = [];
    }

    if (isset($_POST['nota_2'])) {
        $notas_2 = $_POST['nota_2'];
    } else {
        $notas_2 = [];
    }

    if (isset($_POST['nota_final'])) {
        $notas_finales = $_POST['nota_final'];
    } else {
        $notas_finales = [];
    }

    if (isset($_POST['observacion'])) {
        $observaciones = $_POST['observacion'];
    } else {
        $observaciones = [];
    }


    // Impedir guardar si no se recibieron estudiantes.
    if (!is_array($notas_1) || count($notas_1) == 0) {

        header("Location: " . $url_regreso . "&error=sin_estudiantes");
        exit();
    }
    // Contar cuántas calificaciones se guardaron.
    $calificaciones_guardadas = 0;

    // Iniciar una transacción para proteger todas las calificaciones.
    mysqli_begin_transaction($conexion);

    try {

        foreach ($notas_1 as $id_estudiante => $nota_1) {

            // Obtener Nota 1
            if ($nota_1 === "") {
                $nota_1 = null;
            }

            // Obtener Nota 2
            if (isset($notas_2[$id_estudiante])) {

                if ($notas_2[$id_estudiante] === "") {
                    $nota_2 = null;
                } else {
                    $nota_2 = $notas_2[$id_estudiante];
                }
            } else {
                $nota_2 = null;
            }

            // Obtener Nota Final
            if (isset($notas_finales[$id_estudiante])) {

                if ($notas_finales[$id_estudiante] === "") {
                    $nota_final = null;
                } else {
                    $nota_final = $notas_finales[$id_estudiante];
                }
            } else {
                $nota_final = null;
            }

            // Obtener Observación
            if (isset($observaciones[$id_estudiante])) {
                $observacion = trim($observaciones[$id_estudiante]);
            } else {
                $observacion = "";
            }

            // Si toda la fila está vacía, ignorar este estudiante
            if ($nota_1 === null && $nota_2 === null && $nota_final === null && $observacion === "") {
                continue;
            }

            // Comprobar que el estudiante esté inscrito en el nivel y período activo.
            $id_estudiante = intval($id_estudiante);

            $sql_inscripcion = "
            SELECT id_inscripcion
            FROM inscripciones
            WHERE id_estudiante = '$id_estudiante'
            AND id_nivel = '$id_nivel'
            AND id_periodo = '$id_periodo_activo'
            AND estado = 1
            LIMIT 1
        ";

            $resultado_inscripcion = mysqli_query($conexion, $sql_inscripcion);

            // Comprobar que la consulta de inscripción se ejecutó correctamente.
            if (!$resultado_inscripcion) {
                throw new Exception("Error al verificar la inscripción.");
            }

            // Impedir guardar notas si el estudiante no tiene
            // una inscripción activa en el nivel y período correspondiente.

            if (mysqli_num_rows($resultado_inscripcion) == 0) {

                throw new Exception("inscripcion_inactiva");
            }

            // Proteger la observación antes de guardarla en MySQL.
            $observacion = mysqli_real_escape_string($conexion, $observacion);

            // Validar Nota 1 solamente si fue escrita.
            if ($nota_1 !== null) {

                if (!is_numeric($nota_1) || $nota_1 < 0 || $nota_1 > 20) {
                    throw new Exception("error_guardado");
                }
            }

            // Validar Nota 2 solamente si fue escrita.
            if ($nota_2 !== null) {

                if (!is_numeric($nota_2) || $nota_2 < 0 || $nota_2 > 20) {
                    throw new Exception("error_guardado");
                }
            }


            // Validar Nota Final solamente si fue escrita.
            if ($nota_final !== null) {
                if (!is_numeric($nota_final) || $nota_final < 0 || $nota_final > 20) {
                    throw new Exception("error_guardado");
                }
            }

            // Preparar Nota 1 para MySQL
            if ($nota_1 === null) {
                $valor_nota_1 = "NULL";
            } else {
                $valor_nota_1 = "'$nota_1'";
            }

            // Preparar Nota 2 para MySQL
            if ($nota_2 === null) {
                $valor_nota_2 = "NULL";
            } else {
                $valor_nota_2 = "'$nota_2'";
            }

            // Preparar Nota Final para MySQL
            if ($nota_final === null) {
                $valor_nota_final = "NULL";
            } else {
                $valor_nota_final = "'$nota_final'";
            }

            // Buscar si el estudiante ya tiene una calificación
            // en el mismo nivel, período y registro de evaluación.
            // Ignorar los puntos y espacios sobrantes al final del nombre.
            $sql_buscar = "
            SELECT id_calificacion
            FROM calificaciones
            WHERE id_estudiante = '$id_estudiante'
            AND id_nivel = '$id_nivel'
            AND id_periodo = '$id_periodo_activo'
            AND TRIM(TRAILING '.' FROM TRIM(evaluacion)) = '$evaluacion_sql'
            LIMIT 1
        ";

            $resultado_buscar = mysqli_query($conexion, $sql_buscar);

            // Comprobar que la búsqueda de la calificación fue correcta.
            if (!$resultado_buscar) {
                throw new Exception("error_guardado");
            }

            // Si ya existe, actualizarla
            if (mysqli_num_rows($resultado_buscar) > 0) {

                $fila_calificacion = mysqli_fetch_assoc($resultado_buscar);
                $id_calificacion = $fila_calificacion['id_calificacion'];

                $sql_guardar = "
                    UPDATE calificaciones

                    SET descripcion_nota_1 = '$descripcion_nota_1',
                        descripcion_nota_2 = '$descripcion_nota_2',
                        nota_1 = $valor_nota_1,
                        nota_2 = $valor_nota_2,
                        nota_final = $valor_nota_final,
                        observacion = '$observacion'

                    WHERE id_calificacion = '$id_calificacion'
                ";
            } else {


                // Crear una nueva calificación asociada al período académico activo.
                $sql_guardar = "
                INSERT INTO calificaciones
                (id_estudiante, id_nivel, id_periodo, evaluacion, descripcion_nota_1, descripcion_nota_2, nota_1, nota_2, nota_final, observacion)

            VALUES
                ('$id_estudiante',
                '$id_nivel',
                '$id_periodo_activo',
                '$evaluacion_sql',
                '$descripcion_nota_1',
                '$descripcion_nota_2',
                $valor_nota_1,
                $valor_nota_2,
                $valor_nota_final,
                '$observacion')
            ";
            }

            // Ejecutar INSERT o UPDATE.
            $resultado_guardar = mysqli_query($conexion, $sql_guardar);

            // Comprobar que la calificación se guardó correctamente.
            if (!$resultado_guardar) {
                throw new Exception("error_guardado");
            }

            // Contar esta calificación porque se procesó correctamente.
            $calificaciones_guardadas++;
        } // Cerrar el foreach de los estudiantes.

        // Comprobar si se guardó alguna calificación.
        if ($calificaciones_guardadas == 0) {
            throw new Exception("sin_calificaciones");
        }

        // Confirmar todas las calificaciones procesadas.
        mysqli_commit($conexion);

        // Regresar al formulario con el mensaje de éxito.
        header("Location: index.php?guardado=1");
        exit();
    } catch (Throwable $error) {

        // Deshacer los cambios realizados durante este intento.
        mysqli_rollback($conexion);

        // Identificar el error ocurrido.
        $mensaje_error = $error->getMessage();

        // Avisar cuando una inscripción no esté activa.
        if ($mensaje_error == "inscripcion_inactiva") {
            header("Location: " . $url_regreso . "&error=inscripcion_inactiva");
            exit();
        }

        // Avisar cuando no se ingresaron calificaciones.
        if ($mensaje_error == "sin_calificaciones") {
            header("Location: " . $url_regreso . "&error=sin_calificaciones");
            exit();
        }

        // Mostrar un mensaje general para los demás errores.
        header("Location: " . $url_regreso . "&error=error_guardado");
        exit();
    }
