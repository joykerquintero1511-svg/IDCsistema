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
$sql = "SELECT p.nombre, p.apellido 
        FROM estudiantes e 
        INNER JOIN personas p ON e.id_persona = p.id_persona 
        WHERE e.id_estudiante = $id";
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
    <link rel="icon" type="image/png" href="../../images/EFB.png">
    <style>
/* --- CONFIGURACIÓN BASE --- */
body {
    background-color: #00051b92;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.main-content {
    margin-left: 260px; /* Alínea con tu barra lateral */
    width: calc(100% - 260px);
    box-sizing: border-box;
    padding: 2.5rem;
    color: #ffffff;
    min-height: 100vh;
}

.header-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #030303;
    margin-top: 0;
    margin-bottom: 2rem;
}

/* --- TARJETA DE CONFIRMACIÓN DE ELIMINACIÓN --- */
.delete-card {
    background-color: #111111;
    padding: 2.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    border-top: 4px solid #ff5555; /* Línea roja que denota advertencia/peligro */
}

.delete-warning {
    color: #cccccc;
    font-size: 1.1rem;
    margin-top: 0;
    margin-bottom: 0.5rem;
}

.student-name {
    color: #ff5555; /* Resalta el nombre en rojo */
    font-size: 1.4rem;
    font-weight: 600;
    margin-top: 0;
    margin-bottom: 1.5rem;
}

.danger-text {
    color: #777777;
    font-size: 0.9rem;
    margin-bottom: 2.5rem;
}

/* --- ACCIONES / BOTONES --- */
.btn-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.btn-danger {
    background-color: #ff5555;
    color: #ffffff;
    text-decoration: none;
    padding: 0.8rem 1.8rem;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 600;
    transition: background-color 0.2s;
    display: inline-block;
}

.btn-danger:hover {
    background-color: #d43f3f;
}

.btn-cancel {
    color: #888888;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    transition: color 0.2s;
}

.btn-cancel:hover {
    color: #ffffff;
}

/* Alerta de error técnica */
.msg-alert {
    padding: 0.8rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-size: 0.95rem;
    background-color: rgba(255, 85, 85, 0.1);
    color: #ff5555;
    max-width: 500px;
}
</style>


</head>
<body>

<main class="main-content">

    <h1 class="header-title">🗑️ Eliminar Estudiante</h1>

    <!-- Bloque de errores en caso de fallar algo en la base de datos -->
    <?php if (isset($error)): ?>
        <div class="msg-alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Tarjeta de confirmación -->
    <div class="delete-card">
        
        <p class="delete-warning">¿Estás seguro de que deseas eliminar al estudiante?</p>
        <h2 class="student-name"><?php echo htmlspecialchars($nombreCompleto); ?></h2>
        
        <p class="danger-text">⚠️ Esta acción es permanente y no se puede deshacer.</p>
        
        <!-- Botonera integrada -->
        <div class="btn-actions">
            <a href="eliminar.php?id=<?php echo $id; ?>&confirmar=si" class="btn-danger">Sí, eliminar</a>
            <a href="listar.php" class="btn-cancel">No, cancelar</a>
        </div>

    </div>

</main>

</body>