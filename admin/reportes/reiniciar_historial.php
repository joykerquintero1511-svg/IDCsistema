<?php
include '../../session-start.php';
require_once '../../conexion.php';

if (!isset($_GET['id_estudiante'])) {
    die("No se recibió el estudiante.");
}

$id_estudiante = intval($_GET['id_estudiante']);

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

// Buscar únicamente los niveles anteriores al nivel actual
$consulta_niveles = "
SELECT id_nivel, nivel_academico
FROM niveles
WHERE id_nivel < (
    SELECT id_nivel
    FROM estudiantes
    WHERE id_estudiante = '$id_estudiante')
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reiniciar Recorrido Académico</title>

    <link rel="stylesheet" href="../../css/movil.css">
    <link rel="stylesheet" href="../../css/mystyle.css">
    <link rel="icon" href="../../images/EFB.png" type="image/png">
</head>

<body class="admin-body-reinicio">


    <div class="admin-main-content">
        <div class="admin-container">

            <h2 class="admin-titulo">Reiniciar Recorrido Académico</h2>
            <p class="admin-subtitulo">Gestión de Estudiante</p>

            <div class="admin-card">

                <!-- Bloque de Información del Estudiante -->
                <div class="admin-info-box">
                    <p class="admin-info-text">
                        <span>Estudiante:</span>
                        <span class="admin-info-value"><?php echo htmlspecialchars($estudiante['nombre'] . " " . $estudiante['apellido']); ?></span>
                    </p>
                    <p class="admin-info-text">
                        <span>Cédula:</span>
                        <span class="admin-info-value"><?php echo htmlspecialchars($estudiante['cedula']); ?></span>
                    </p>
                    <p class="admin-info-text">
                        <span>Nivel actual:</span>
                        <span class="admin-info-nivel"><?php echo htmlspecialchars($estudiante['nivel_academico']); ?></span>
                    </p>
                </div>

                <!-- Formulario para reiniciar el recorrido académico -->
                <form action="procesar_reinicio.php" method="POST" id="form-reinicio">

                    <input type="hidden" name="id_estudiante" value="<?php echo $id_estudiante; ?>">

                    <div class="admin-form-group">
                        <label for="id_nivel_nuevo" class="admin-label">Nivel para reiniciar el recorrido:</label>
                        <select name="id_nivel_nuevo" id="id_nivel_nuevo" required class="admin-select">
                            <option value="" disabled selected>Seleccione un nivel</option>
                            <?php while ($nivel = $resultado_niveles->fetch_assoc()) { ?>
                                <option value="<?php echo $nivel['id_nivel']; ?>">
                                    <?php
                                    echo htmlspecialchars($nivel['nivel_academico']);
                                    if ($nivel['nivel_academico'] == $estudiante['nivel_academico']) {
                                        echo " (Nivel actual)";
                                    }
                                    ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <p class="admin-aviso">
                        ⚠️ Esta acción conservará el historial académico anterior y creará una nueva inscripción activa en el nivel seleccionado.
                    </p>

                    <button type="submit" class="admin-btn-submit">
                        Reiniciar recorrido
                    </button>
                </form>

            </div>

            <div style="margin-top: 3rem;">
                <a href="detalle_estudiante.php?id_inscripcion=<?php echo $id_inscripcion; ?>" class="admin-enlace-volver">
                    ← Volver al detalle del estudiante
                </a>
            </div>

        </div>
    </div>
    <!-- Librería para mostrar mensajes personalizados -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Buscar el formulario de reinicio académico
        const formularioReinicio = document.getElementById('form-reinicio');

        // Escuchar cuando el administrador intenta reiniciar el recorrido
        formularioReinicio.addEventListener('submit', function(evento) {

            // Evitar que el formulario se envíe sin confirmación
            evento.preventDefault();

            // Mostrar una ventana de confirmación personalizada
            Swal.fire({
                title: '¿Reiniciar recorrido académico?',
                text: 'El estudiante comenzará nuevamente desde el nivel seleccionado. Su historial académico anterior se conservará.',
                icon: 'warning',
                background: '#111827',
                color: '#ffffff',
                showCancelButton: true,
                confirmButtonText: 'Sí, reiniciar recorrido',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#008cff',
                cancelButtonColor: '#374151',
                reverseButtons: true
            }).then(function(resultado) {

                // Enviar el formulario únicamente si el administrador confirma
                if (resultado.isConfirmed) {
                    formularioReinicio.submit();
                }

            });

        });
    </script>

</body>

</html>
</body>

</html>