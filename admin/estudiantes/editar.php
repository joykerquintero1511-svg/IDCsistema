<?php
session_start();
if(!isset($_SESSION['rol']) || $_SESSION['rol']!=='admin'  ){
    header("Location: ../../login.php");
exit;// Sin exit(), el script sigue ejecutándose aunque redirija.
}



require_once '../../conexion.php';

$conexion= $conexion ?? null;
$error = "";
$exito = "";

// Verificar si llega un ID por la URL
if (!isset($_GET['id'])) {
    die(" No se especificó qué estudiante editar.");
}

$id = $_GET['id'];

// Obtener los datos actuales del estudiante
$sql = "
    SELECT 
        estudiantes.id_estudiante,
        estudiantes.id_persona,
        estudiantes.id_nivel,
        estudiantes.email,
        estudiantes.nivel_instruccion,
        estudiantes.fecha_registro,
        inscripciones.estado,
        personas.nombre,
        personas.apellido,
        personas.telefono,
        niveles.nivel_academico
    FROM estudiantes
    INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona
    INNER JOIN niveles
        ON estudiantes.id_nivel = niveles.id_nivel
    INNER JOIN inscripciones
    ON estudiantes.id_estudiante = inscripciones.id_estudiante
    WHERE estudiantes.id_estudiante = $id
";

$resultado = ejecutarConsulta($conexion, $sql);
$estudiante = mysqli_fetch_assoc($resultado);// Sirve para extraer una fila de resultados,y convertirla en un formato fácil de usar.

if (!$estudiante) {
    die("Estudiante no encontrado.");
}
// Obtener todos los niveles para llenar el select
$sql_niveles = "SELECT id_nivel, nivel_academico
                FROM niveles
                ORDER BY id_nivel";

$resultado_niveles = ejecutarConsulta($conexion, $sql_niveles);

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $id_nivel = trim($_POST['id_nivel']);
    $estado = trim ($_POST['estado']); 

    if (empty($nombre) || empty($apellido)) {
        $error = " Nombre y Apellido son obligatorios.";
    } else {
        $sql_personas = "UPDATE personas SET 
        nombre = '$nombre',
        apellido = '$apellido',
        telefono = '$telefono'
        WHERE id_persona = " . $estudiante['id_persona'];

    $sql_estudiantes = "UPDATE estudiantes SET 
        email = '$email',
        id_nivel = '$id_nivel'
        WHERE id_estudiante = $id";

    $sql_inscripciones = "UPDATE inscripciones SET 
        estado = '$estado',
        nivel_academico = (
            SELECT nivel_academico 
            FROM niveles 
            WHERE id_nivel = '$id_nivel'
        )
        WHERE id_estudiante = $id";

    if (
    ejecutarConsulta($conexion, $sql_personas) &&
    ejecutarConsulta($conexion, $sql_estudiantes) &&
    ejecutarConsulta($conexion, $sql_inscripciones)
    ) {
            $exito = " Estudiante actualizado correctamente.";
            // Actualizar los datos mostrados
            $estudiante['nombre'] = $nombre;
            $estudiante['apellido'] = $apellido;
            $estudiante['email'] = $email;
            $estudiante['telefono'] = $telefono;
            $estudiante['estado'] = $estado;
            $estudiante['id_nivel'] = $id_nivel;
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
    <link rel="stylesheet" href="../../estilos/style.css">
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

        <select name="id_nivel">
             <?php while ($nivel = mysqli_fetch_assoc($resultado_niveles)) { ?>
        <option value="<?php echo $nivel['id_nivel']; ?>"
             <?php echo ($nivel['id_nivel'] == $estudiante['id_nivel']) ? 'selected' : ''; ?>>
             <?php echo htmlspecialchars($nivel['nivel_academico']); ?>
        </option>
    <?php } ?>
</select><br><br>
    
     <select name="estado">
    <option value="1" <?php echo ($estudiante['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
    <option value="0" <?php echo ($estudiante['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
</select><br><br>

        <button type="submit">Guardar Cambios</button>
        <a href="listar.php">Cancelar</a>
    </form>
</body>
</html>