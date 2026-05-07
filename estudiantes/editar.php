<?php
require_once '../conexion.php';
require_once '../nav.php';
$conexion= $conexion ?? null;
$error = "";
$exito = "";

// Verificar si llega un ID por la URL
if (!isset($_GET['id'])) {
    die(" No se especificó qué estudiante editar.");
}

$id = $_GET['id'];

// Obtener los datos actuales del estudiante
$sql = "SELECT * FROM estudiantes WHERE id_estudiante = $id";
$resultado = ejecutarConsulta($conexion, $sql);
$estudiante = mysqli_fetch_assoc($resultado);

if (!$estudiante) {
    die("Estudiante no encontrado.");
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $nivel = trim($_POST['nivel_academico']);

    if (empty($nombre) || empty($apellido)) {
        $error = " Nombre y Apellido son obligatorios.";
    } else {
        $sql = "UPDATE estudiantes SET 
                nombre = '$nombre',
                apellido = '$apellido',
                email = '$email',
                telefono = '$telefono',
                nivel_academico = '$nivel'
                WHERE id_estudiante = $id";

        if (ejecutarConsulta($conexion, $sql)) {
            $exito = " Estudiante actualizado correctamente.";
            // Actualizar los datos mostrados
            $estudiante['nombre'] = $nombre;
            $estudiante['apellido'] = $apellido;
            $estudiante['email'] = $email;
            $estudiante['telefono'] = $telefono;
            $estudiante['nivel_academico'] = $nivel;
        } else {
            $error = " Error al actualizar estudiante.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Estudiante</title>
    <link rel="stylesheet" href="../estilos/style.css">
</head>
<body>
    <h1> Editar Estudiante</h1>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($exito): ?>
        <p style="color: green;"><?php echo $exito; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($estudiante['nombre']); ?>" required><br><br>

        <label>Apellido:</label><br>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($estudiante['apellido']); ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($estudiante['email']); ?>"><br><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($estudiante['telefono']); ?>"><br><br>

        <label>Nivel Académico:</label><br>
        <input type="text" name="nivel_academico" value="<?php echo htmlspecialchars($estudiante['nivel_academico']); ?>"><br><br>

        <button type="submit">💾 Guardar Cambios</button>
        <a href="listar.php">Cancelar</a>
    </form>
</body>
</html>