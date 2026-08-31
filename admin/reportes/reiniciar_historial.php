    <?php
    include '../../session-start.php';
    require_once '../../conexion.php';

    if (!isset($_GET['id_estudiante'])) {
        die("No se recibio el estudiante.");
    }

    $id_estudiante = intval($_GET['id_estudiante']);

    // Recibir y validar la inscripción para poder regresar al detalle del estudiante
    if (!isset($_GET['id_inscripcion'])) {
        die("No se recibió la inscripción.");
    }

    $id_inscripcion = intval($_GET['id_inscripcion']);

    // Buscar los datos del estudiante
    $consulta_estudiante = "
    SELECT
    estudiantes.id_estudiante,
    personas.cedula,
    personas.nombre,
    personas.apellido,
    niveles.nivel_academico
FROM estudiantes
INNER JOIN personas
    ON estudiantes.id_persona = personas.id_persona
INNER JOIN niveles
    ON estudiantes.id_nivel = niveles.id_nivel
WHERE estudiantes.id_estudiante = $id_estudiante
";

    $resultado_estudiante = $conexion->query($consulta_estudiante);

    if (!$resultado_estudiante) {
        die("Error al buscar estudiante: " . $conexion->error);
    }

    $estudiante = $resultado_estudiante->fetch_assoc();
    if (!$estudiante) {
        die("No se encontró el estudiante.");
    }

    // Buscar todos los niveles disponibles
    $consulta_niveles = "
    SELECT id_nivel, nivel_academico
    FROM niveles
    ORDER BY id_nivel ASC
";

    $resultado_niveles = $conexion->query($consulta_niveles);

    if (!$resultado_niveles) {
        die("Error al buscar niveles: " . $conexion->error);
    }

    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Reiniciar recorrido académico</title>
    </head>

    <body>

        <h1>Reiniciar recorrido académico</h1>

        <p>
            Estudiante:
            <strong>
                <?php echo htmlspecialchars($estudiante['nombre'] . " " . $estudiante['apellido']); ?>
            </strong>
        </p>

        <p>
            Cédula:
            <strong>
                <?php echo htmlspecialchars($estudiante['cedula']); ?>
            </strong>
        </p>

        <p>
            Nivel actual:
            <strong>
                <?php echo htmlspecialchars($estudiante['nivel_academico']); ?>
            </strong>
        </p>

        <form action="procesar_reinicio.php" method="POST" onsubmit="return confirm('¿Está seguro de reiniciar el recorrido académico de este estudiante?');">

            <input type="hidden" name="id_estudiante" value="<?php echo $id_estudiante; ?>">

            <label for="id_nivel_nuevo">Nivel para reiniciar el recorrido:</label>

            <select name="id_nivel_nuevo" id="id_nivel_nuevo" required>
                <option value="" disabled selected>Seleccione un nivel</option>

                <?php while ($nivel = $resultado_niveles->fetch_assoc()) { ?>
                    <option value="<?php echo $nivel['id_nivel']; ?>">
                        <?php
                        echo htmlspecialchars($nivel['nivel_academico']);

                        if ($nivel['nivel_academico'] == $estudiante['nivel_academico']) {
                            echo " - Nivel actual";
                        }
                        ?>
                    </option>
                <?php } ?>
            </select>
            <p>
                Esta acción conservará el historial académico anterior y creará una nueva inscripción activa en el nivel seleccionado.
            </p>

            <button type="submit">
                Reiniciar recorrido
            </button>
        </form>
        <br>

        <a href="detalle_estudiante.php?id_inscripcion=<?php echo $id_inscripcion; ?>">
            ← Volver
        </a>

    </body>

    </html>