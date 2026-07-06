<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

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
    <title>Lista de Estudiantes</title>
</head>
<body>

<h1>Lista de Estudiantes</h1>

<table border="1">
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
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['apellido']); ?></td>
                <td><?php echo htmlspecialchars($fila['email']); ?></td>
                <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                <td><?php echo htmlspecialchars($fila['nivel_academico']); ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $fila['id_estudiante']; ?>">Editar</a> |
                    <a href="eliminar.php?id=<?php echo $fila['id_estudiante']; ?>">Eliminar</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<br>
<a href="agregar.php">Agregar nuevo estudiante</a>

</body>
</html>