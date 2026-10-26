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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificados del Estudiante - EFB</title>
    <link rel="icon" type="image/png" href="/IDCsistema/images/EFB.png">
    <link rel="stylesheet" href="/IDCsistema/css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
</head>
<body>

    <!-- Menú lateral unificado de administrador -->
    <?php include '../sidebaradmin.php'; // Ajusta la ruta relativa según la ubicación exacta del archivo ?>

    <!-- Contenedor Principal con el margen correcto -->
    <main class="main-content">
        
        <div class="certificados-header">
            <h1>Certificados de <?php echo htmlspecialchars(ucwords(strtolower($estudiante['nombre'] . ' ' . $estudiante['apellido']))); ?> 🎓</h1>
            <p>Visualiza el historial académico y accede a los certificados de los niveles cursados.</p>
        </div>

        <div class="certificados-card">
            <h2>Niveles Cursados</h2>

            <?php if (mysqli_num_rows($resultado_historial) > 0) { ?>
                <div class="certificados-list">
                    <?php while ($nivel = mysqli_fetch_assoc($resultado_historial)) { ?>
                        <div class="certificado-item">
                            <span class="nivel-nombre">
                                📜 <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
                            </span>
                            <a href="../../certificados/certificado.php?id_inscripcion=<?php echo $nivel['id_inscripcion']; ?>" class="btn-ver-certificado" target="_blank">
                                Ver certificado
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="certificados-empty">Este estudiante no tiene niveles registrados.</p>
            <?php } ?>
        </div>

        <div class="certificados-actions">
            <a href="listar.php" class="btn-volver">← Volver a la lista de estudiantes</a>
        </div>

    </main>

</body>
</html>