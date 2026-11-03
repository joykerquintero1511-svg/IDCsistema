    <?php

    include '../../session-start.php';
    require_once '../../conexion.php';

    if (!isset($_POST['id_estudiante']) || !isset($_POST['id_nivel_nuevo'])) {
        die("Faltan datos para realizar el reinicio.");
    }

    $id_estudiante = intval($_POST['id_estudiante']);
    $id_nivel_nuevo = intval($_POST['id_nivel_nuevo']);

    if ($id_estudiante <= 0 || $id_nivel_nuevo <= 0) {
        die("Los datos recibidos no son válidos.");
    }

    // Verificar que el nivel seleccionado exista
    $sql_verificar_nivel = "SELECT id_nivel
                        FROM niveles
                        WHERE id_nivel = '$id_nivel_nuevo'";

    $resultado_verificar_nivel = $conexion->query($sql_verificar_nivel);

    if (!$resultado_verificar_nivel || $resultado_verificar_nivel->num_rows == 0) {
        die("El nivel seleccionado no es válido.");
    }

    // Consultar el estudiante y su nivel académico actual
    $sql_verificar_estudiante = "SELECT id_estudiante, id_nivel
                             FROM estudiantes
                             WHERE id_estudiante = '$id_estudiante'";

    $resultado_verificar_estudiante = $conexion->query($sql_verificar_estudiante);

    if (!$resultado_verificar_estudiante || $resultado_verificar_estudiante->num_rows == 0) {
        die("El estudiante no existe.");
    }

    // Obtener el nivel actual del estudiante
    $estudiante = $resultado_verificar_estudiante->fetch_assoc();
    $id_nivel_actual = intval($estudiante['id_nivel']);

    // Verificar que el nivel seleccionado sea anterior al actual
    if ($id_nivel_nuevo >= $id_nivel_actual) {
        die("Solo puede reiniciar el recorrido en un nivel anterior al actual.");
    }

    // Buscar el período académico activo
    $sql_periodo = "SELECT id_periodo
                FROM periodos_academicos
                WHERE estado = 1
                LIMIT 1";

    $resultado_periodo = $conexion->query($sql_periodo);

    // Verificar que la consulta se ejecutó correctamente
    if (!$resultado_periodo) {
        die("Error al consultar el período académico: " . $conexion->error);
    }

    // Verificar que exista un período activo
    if ($resultado_periodo->num_rows == 0) {
        die("No existe un período académico activo.");
    }

    // Obtener el identificador del período activo
    $periodo = $resultado_periodo->fetch_assoc();

    $id_periodo = intval($periodo['id_periodo']);

    // Verificar si el estudiante ya estuvo inscrito en este período
    $sql_verificar_inscripcion = "SELECT id_inscripcion
                             FROM inscripciones
                             WHERE id_estudiante = '$id_estudiante'
                             AND id_periodo = '$id_periodo'
                             LIMIT 1";

    $resultado_verificar_inscripcion = $conexion->query($sql_verificar_inscripcion);

    // Verificar si ocurrió un error en la consulta
    if (!$resultado_verificar_inscripcion) {
        die("Error al verificar la inscripción: " . $conexion->error);
    }

    // Evitar inscripciones duplicadas en el mismo período
    if ($resultado_verificar_inscripcion->num_rows > 0) {
        die("Este estudiante ya estuvo inscrito en el período académico activo. Para reiniciar su recorrido, debe hacerlo en un período nuevo.");
    }


    // Iniciar una transacción para realizar el reinicio de forma segura
    $conexion->begin_transaction();

    // Colocar como inactiva la inscripción actual del estudiante
    $sql_inactivar = "UPDATE inscripciones
                  SET estado = 0
                  WHERE id_estudiante = '$id_estudiante'
                  AND estado = 1";

    $resultado_inactivar = $conexion->query($sql_inactivar);

    // Si ocurre un error, deshacer los cambios realizados en la transacción
    if (!$resultado_inactivar) {
        $conexion->rollback();
        die("Error al desactivar la inscripción actual: " . $conexion->error);
    }

    // Actualizar el nivel actual del estudiante
    $sql_actualizar_nivel = "UPDATE estudiantes
                         SET id_nivel = '$id_nivel_nuevo'
                         WHERE id_estudiante = '$id_estudiante'";

    $resultado_actualizar_nivel = $conexion->query($sql_actualizar_nivel);

    // Si ocurre un error, deshacer los cambios realizados en la transacción
    if (!$resultado_actualizar_nivel) {
        $conexion->rollback();
        die("Error al actualizar el nivel del estudiante: " . $conexion->error);
    }

    // Crear una nueva inscripción activa en el nivel seleccionado y período activo
    $token_qr = md5(uniqid(rand(), true));

    $sql_nueva_inscripcion = "INSERT INTO inscripciones
                      (id_estudiante, id_nivel, id_periodo, fecha_inscripcion, estado, estatus_presencial, token_qr)
                      VALUES
                      ('$id_estudiante', '$id_nivel_nuevo', '$id_periodo', NOW(), 1, 'pendiente', '$token_qr')";

    $resultado_nueva_inscripcion = $conexion->query($sql_nueva_inscripcion);

    // Si ocurre un error, deshacer los cambios realizados en la transacción
    if (!$resultado_nueva_inscripcion) {
        $conexion->rollback();
        die("Error al crear la nueva inscripción: " . $conexion->error);
    }

    // Obtener el id de la nueva inscripción creada
    $id_inscripcion_nueva = $conexion->insert_id;

    // Confirmar todos los cambios realizados en la transacción
    $conexion->commit();

    // Regresar al detalle de la nueva inscripción
    header("Location: detalle_estudiante.php?id_inscripcion=$id_inscripcion_nueva");
    exit;
