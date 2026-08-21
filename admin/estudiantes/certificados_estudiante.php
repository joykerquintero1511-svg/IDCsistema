<?php

    session_start();

    require_once '../../conexion.php';

    // Permitir el acceso únicamente al administrador
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        header("Location: ../../login.php");
        exit();
    }

    // Verificar que llegue el ID del estudiante
    if (!isset($_GET['id_estudiante'])) {
        die("No se especificó el estudiante.");
    }

    // Convertir el ID recibido en un número entero
    $id_estudiante = intval($_GET['id_estudiante']);
    // Buscar los datos del estudiante seleccionado

    $sql_estudiante = "
        SELECT personas.nombre, personas.apellido
        FROM estudiantes
        INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona
        WHERE estudiantes.id_estudiante = '$id_estudiante'
    ";

    $resultado_estudiante = mysqli_query($conexion, $sql_estudiante);

$estudiante = mysqli_fetch_assoc($resultado_estudiante);

    // Verificar que el estudiante exista

        if (!$estudiante) {
            die("Estudiante no encontrado.");
    }

    // Buscar el historial de niveles del estudiante

    $sql_historial = "
        SELECT inscripciones.id_inscripcion,
        niveles.nivel_academico
        FROM inscripciones
        INNER JOIN niveles
        ON inscripciones.id_nivel = niveles.id_nivel
        WHERE inscripciones.id_estudiante = '$id_estudiante'
        ORDER BY inscripciones.fecha_inscripcion ASC
    ";

    $resultado_historial = mysqli_query($conexion, $sql_historial);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificados del Estudiante</title>
</head>
<body>

    <h1>
        Certificados de
        <?php echo htmlspecialchars(ucwords(strtolower($estudiante['nombre'] . ' ' . $estudiante['apellido']))); ?>
    </h1>
    <h2>Niveles cursados</h2>

<?php if (mysqli_num_rows($resultado_historial) > 0) { ?>

    <?php while ($nivel = mysqli_fetch_assoc($resultado_historial)) { ?>

        <p>
            <?php echo htmlspecialchars($nivel['nivel_academico']); ?>

            <a href="../../certificados/certificado.php?id_inscripcion=<?php echo $nivel['id_inscripcion']; ?>">
                Ver certificado
            </a>
        </p>

    <?php } ?>

<?php } else { ?>

    <p>Este estudiante no tiene niveles registrados.</p>

<?php } ?>

    <a href="listar.php">← Volver a la lista de estudiantes</a>

</body>
</html>