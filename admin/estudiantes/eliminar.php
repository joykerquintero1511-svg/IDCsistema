<?php
session_start();
if (!isset ($_SESSION['rol'])|| $_SESSION['rol']!=='admin'){
    header("Location: ../../login.php");
    exit();
} //  Para que solo el administrador pueda agregar estudiantes.




require_once '../../conexion.php';

$conexion=$conexion ?? null;

// Verificar si llega un ID por la URL
if (!isset($_GET['id'])) {
    die("No se especificó qué estudiante eliminar.");
}

$id = $_GET['id'];

// Obtener el nombre del estudiante para mostrarlo en el mensaje
$sql = "SELECT nombre, apellido FROM estudiantes WHERE id_estudiante = $id";
$resultado = ejecutarConsulta($conexion, $sql);
$estudiante = mysqli_fetch_assoc($resultado);

if (!$estudiante) {
    die("Estudiante no encontrado.");
}

$nombreCompleto = $estudiante['nombre'] . " " . $estudiante['apellido'];

// Procesar la eliminación cuando se confirma
if (isset($_GET['confirmar']) && $_GET['confirmar'] == 'si') {
    $sql = "DELETE FROM estudiantes WHERE id_estudiante = $id";
    
    if (ejecutarConsulta($conexion, $sql)) {
        // Redirigir a listar.php con mensaje de éxito
        header("Location: listar.php?mensaje=eliminado");
        exit;
    } else {
        $error = "Error al eliminar estudiante.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Estudiante</title>
    <link rel="stylesheet" href="../estilos/style.css">
</head>
<body>
    <h1>🗑️ Eliminar Estudiante</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <p>¿Estás seguro de que deseas eliminar al estudiante:</p>
    <p><strong><?php echo htmlspecialchars($nombreCompleto); ?></strong>?</p>

    <p>Esta acción no se puede deshacer.</p>

    <a href="eliminar.php?id=<?php echo $id; ?>&confirmar=si" style="color: red;">✅ Sí, eliminar</a> |
    <a href="listar.php"> No, cancelar</a>
</body>
</html>