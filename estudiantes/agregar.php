<?php
require_once 'conexion.php';

$conexion = $conexion ?? null; // el xq d esta variable esta en la carpeta de listar.php

$nombre = $apellido = $email = $telefono = $nivel = "";
$error = "";
$exito = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /* $_POST['nombre'] Toma el valor que el usuario escribió en el campo name="nombre"
    trim(...)	Elimina espacios en blanco al inicio y al final del texto */

    $nombre = trim($_POST['nombre']); 
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $nivel = trim($_POST['nivel_academico']);

    if (empty($nombre) || empty($apellido)) {   // (empty) esta explicado en registrar_usuario.php
        $error = "Nombre y Apellido son obligatorios.";
    } else {
        $sql = "INSERT INTO estudiantes (nombre, apellido, email, telefono, nivel_academico) 
                VALUES ('$nombre', '$apellido', '$email', '$telefono', '$nivel')";

    # Esta es la q cree en conexion.php 
        if (ejecutarConsulta($conexion, $sql)) {
            $exito = "Estudiante agregado correctamente.";
            $nombre = $apellido = $email = $telefono = $nivel = "";
        } else {
            $error = "Error al agregar estudiante.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Estudiante</title>
    <link rel="stylesheet" href="../estilos/style.css">
</head>
<body>
    <h1> Agregar Nuevo Estudiante</h1>

 <!-- Aca se puede abrir y cerrar php varias veces y finalizando el endif -->
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($exito): ?>
        <p style="color: green;"><?php echo $exito; ?></p>
    <?php endif; ?> 



    <form method="POST"> <!-- El formulario envía los datos ocultos (seguro) con POST-->
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required><br><br>

        <label>Apellido:</label><br>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>"><br><br>

        <label>Nivel Académico:</label><br>
        <input type="text" name="nivel_academico" value="<?php echo htmlspecialchars($nivel); ?>"><br><br>

        <button type="submit">Guardar Estudiante</button>
        <a href="listar.php">Cancelar</a>
    </form>
</body>
</html>