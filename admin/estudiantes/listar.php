<?php
include '../../session-start.php';
require_once '../../conexion.php';

$sql = "
    SELECT 
        estudiantes.id_estudiante,
        personas.nombre,
        personas.apellido,
        estudiantes.email,
        personas.telefono,
        niveles.nivel_academico,
        estudiantes.id_nivel
    FROM estudiantes
    INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona
    INNER JOIN niveles
        ON estudiantes.id_nivel = niveles.id_nivel
    ORDER BY personas.apellido ASC
";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conexion));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Estudiantes - EFB</title>
    <link rel="icon" type="image/png" href="/IDCsistema/images/EFB.png">
    <link rel="stylesheet" href="/IDCsistema/css/mystyle.css">
</head>
<body>

    <!-- Menú lateral unificado -->
    <?php include '../sidebaradmin.php'; ?>

    <!-- Contenido Principal -->
    <main class="main-content">
        
        <div class="estudiantes-header">
            <h1>Lista de Estudiantes</h1>
            <a href="/IDCsistema/inscripcion.php?origen=admin" class="estudiantes-btn-add">
                + Nueva inscripción
            </a>
        </div>

        <div class="estudiantes-table-wrapper">
            <table class="estudiantes-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Nivel Académico</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['id_estudiante']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords(strtolower($fila['nombre']))); ?></td>
                            <td><?php echo htmlspecialchars(ucwords(strtolower($fila['apellido']))); ?></td>
                            <td><?php echo htmlspecialchars($fila['email']); ?></td>
                            <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nivel_academico']); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $fila['id_estudiante']; ?>" class="estudiantes-btn-action estudiantes-btn-edit">
                                    Editar
                                </a>
                                <a href="eliminar.php?id=<?php echo $fila['id_estudiante']; ?>" class="estudiantes-btn-action estudiantes-btn-delete" onclick="return confirm('¿Está seguro de eliminar este estudiante?');">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </main>

    <?php include '../../script-seguridad.php'; ?>
</body>
</html>