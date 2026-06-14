<?php
require_once('../conexion.php');

 $conexion = $conexion ?? null; /* Significa:

“si $conexion no existe, créala como null”

 Para PHP → no cambia nada
 Para VS Code → deja de marcar error,xq se tildaba
 $conexion(linea 56) x la extension intelephense q pensaba no
 estaba declarada la variable igual corria el sistema
 pero no m gustaba cm se veia.y tambien xq se usa
 require_once,la variable viene de otro archivo y
 el codigo funciona bien.
*/


$sql = "SELECT * FROM inscripciones ORDER BY id_inscripcion DESC";
$resultado = ejecutarConsulta($conexion, $sql);



?>

<!DOCTYPE html>
<html>
<head>
    <title>Listado de Inscripciones</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-nuevo { background: blue; color: white; padding: 10px; text-decoration: none; display: inline-block; margin-bottom: 20px; border-radius: 5px; }
        .btn-ver { background: green; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .btn-editar { background: orange; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Listado de Inscripciones</h1>
    <a href="registrar.php" class="btn-nuevo">Nueva Inscripción</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Estudiante</th>
                <th>Nivel Académico</th>
                <th>Fecha Inscripción</th>
                <th>Periodo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($fila = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo $fila['id_inscripcion']; ?></td>
                <td><?php echo $fila['id_estudiante']; ?></td>
                <td><?php echo htmlspecialchars($fila['nivel_academico']); ?></td>
                <td><?php echo $fila['fecha_inscripcion']; ?></td>
                <td><?php echo $fila['periodo']; ?></td>
                <td><?php echo $fila['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                <td>
                    <a href="consultar.php?id=<?php echo $fila['id_inscripcion']; ?>" class="btn-ver">Ver</a>
                    <a href="editar.php?id=<?php echo $fila['id_inscripcion']; ?>" class="btn-editar">Editar</a>
                </a>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>