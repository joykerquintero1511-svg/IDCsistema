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
    $evaluacion = trim($_POST['evaluacion']);

    // Comprobar que el profesor tenga asignado el nivel donde registrará las notas.
    if ($_SESSION['rol'] === 'profesor') {

        $id_usuario = intval($_SESSION['id_usuario']);

        $sql_permiso = "
        SELECT id_profesor
        FROM profesores
        WHERE id_usuario = '$id_usuario'
        AND id_nivel = '$id_nivel'
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

    // Evitar guardar calificaciones sin un nombre de registro
    if ($evaluacion === "") {
        die("Debe indicar el nombre del registro de calificación.");
    }

    $descripcion_nota_1 = trim($_POST['descripcion_nota_1']);
    $descripcion_nota_2 = trim($_POST['descripcion_nota_2']);

    $notas_1 = $_POST['nota_1'];
    $notas_2 = $_POST['nota_2'];
    $notas_finales = $_POST['nota_final'];
    $observaciones = $_POST['observacion'];

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
            continue; // Pasa al siguiente estudiante sin guardar nada.
        }

        // Validar Nota 1 solamente si fue escrita
        if ($nota_1 !== null) {

            if (!is_numeric($nota_1) || $nota_1 < 0 || $nota_1 > 20) {
                die("La Nota 1 del estudiante con ID $id_estudiante debe estar entre 0 y 20.");
            }
        }

        // Validar Nota 2 solamente si fue escrita
        if ($nota_2 !== null) {

            if (!is_numeric($nota_2) || $nota_2 < 0 || $nota_2 > 20) {
                die("La Nota 2 del estudiante con ID $id_estudiante debe estar entre 0 y 20.");
            }
        }

        // Validar Nota Final solamente si fue escrita
        if ($nota_final !== null) {

            if (!is_numeric($nota_final) || $nota_final < 0 || $nota_final > 20) {
                die("La Nota Final del estudiante con ID $id_estudiante debe estar entre 0 y 20.");
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
        $sql_buscar = "
            SELECT id_calificacion
            FROM calificaciones
            WHERE id_estudiante = '$id_estudiante'
            AND id_nivel = '$id_nivel'
            AND id_periodo = '$id_periodo_activo'
            AND evaluacion = '$evaluacion'
            LIMIT 1
        ";

        $resultado_buscar = mysqli_query($conexion, $sql_buscar);

        if (!$resultado_buscar) {
            die("Error al buscar la calificación: " . mysqli_error($conexion));
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
                '$evaluacion',
                '$descripcion_nota_1',
                '$descripcion_nota_2',
                $valor_nota_1,
                $valor_nota_2,
                $valor_nota_final,
                '$observacion')
            ";
        }

        // Ejecutar INSERT o UPDATE
        $resultado_guardar = mysqli_query($conexion, $sql_guardar);

        if (!$resultado_guardar) {
            die("Error al guardar las notas: " . mysqli_error($conexion));
        }
    } // todo está dentro del foreach.Porque cada vuelta del foreach trabaja con un estudiante diferente.Por eso esas variables no pueden estar fuera del foreach, ya que cambiarán en cada estudiante.

    header("Location: index.php?guardado=1"); // Regresa nuevamente a index.php."
    exit();
