<?php
require_once '../conexion.php';

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
    $nivel_academico = trim($_POST['nivel_academico']);
    $status = trim ($_POST['status']); 

    if (empty($nombre) || empty($apellido)) {
        $error = " Nombre y Apellido son obligatorios.";
    } else {
        $sql = "UPDATE estudiantes SET 
                nombre = '$nombre',
                apellido = '$apellido',
                email = '$email',
                telefono = '$telefono',
                nivel_academico = '$nivel_academico',
                status = '$status'
                WHERE id_estudiante = $id";

        if (ejecutarConsulta($conexion, $sql)) {
            $exito = " Estudiante actualizado correctamente.";
            // Actualizar los datos mostrados
            $estudiante['nombre'] = $nombre;
            $estudiante['apellido'] = $apellido;
            $estudiante['email'] = $email;
            $estudiante['telefono'] = $telefono;
            $estudiante['nivel_academico'] = $nivel_academico;
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

        <input type="text" name="nombre" placeholder="Nombres" value="<?php echo htmlspecialchars($estudiante['nombre']); ?>" required><br><br>

        <input type="text" name="apellido" placeholder="Apellido" value="<?php echo htmlspecialchars($estudiante['apellido']); ?>" required><br><br>

        <input type="text" name="email" placeholder="Correo Electrónico" value="<?php echo htmlspecialchars($estudiante['email']); ?>"><br><br>

        <input type="text" name="telefono" placeholder="Telefono" value="<?php echo htmlspecialchars($estudiante['telefono']); ?>"><br><br>

       <input type="text" name="nivel_academico" placeholder="nivel_academico" value="<?php echo htmlspecialchars($estudiante['nivel_academico']); ?>"><br><br>
    
       <select name="status">
    <option value="Activo" <?php echo ($estudiante['status'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
    <option value="Inactivo" <?php echo ($estudiante['status'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
    <option value="Egresado" <?php echo ($estudiante['status'] == 'Egresado') ? 'selected' : ''; ?>>Egresado</option>
    <option value="Inhabilitado" <?php echo ($estudiante['status'] == 'Inhabilitado') ? 'selected' : ''; ?>>Inhabilitado</option>
</select><br><br>

        <button type="submit">Guardar Cambios</button>
        <a href="listar.php">Cancelar</a>
    </form>
</body>
</html>