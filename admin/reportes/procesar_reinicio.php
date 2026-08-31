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

    // Verificar que el estudiante exista
    $sql_verificar_estudiante = "SELECT id_estudiante
                             FROM estudiantes
                             WHERE id_estudiante = '$id_estudiante'";

    $resultado_verificar_estudiante = $conexion->query($sql_verificar_estudiante);

    if (!$resultado_verificar_estudiante || $resultado_verificar_estudiante->num_rows == 0) {
        die("El estudiante no existe.");
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

    // Crear una nueva inscripción activa en el nivel seleccionado
    $token_qr = md5(uniqid(rand(), true));

    $sql_nueva_inscripcion = "INSERT INTO inscripciones
                          (id_estudiante, id_nivel, fecha_inscripcion, estado, estatus_presencial, token_qr)
                          VALUES
                          ('$id_estudiante', '$id_nivel_nuevo', NOW(), 1, 'pendiente', '$token_qr')";

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
