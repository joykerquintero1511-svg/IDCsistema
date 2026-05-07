<?php
require_once '../conexion.php';

$conexion = $conexion ?? null; // el xq d esta variable esta en la carpeta de listar.php

$nombre = $apellido =$cedula =$direccion = $email = $telefono = $nivel_academico = $status = $nivel_instruccion = $fecha_registro = "";
$error = "";
$exito = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /* $_POST['nombre'] Toma el valor que el usuario escribió en el campo name="nombre"
    trim(...)	Elimina espacios en blanco al inicio y al final del texto */

# Recibir datos 
    $nombre = trim($_POST['nombre']); 
    $apellido = trim($_POST['apellido']);
    $cedula = trim($_POST['cedula']);
    $direccion = trim($_POST ['direccion']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $nivel_academico = trim($_POST['nivel_academico']);
    $status = trim($_POST['status']);
    $nivel_instruccion = trim($_POST['nivel_instruccion']);
    $fecha_registro = trim($_POST['fecha_registro']);

    if (empty($nombre) || empty($apellido)) {   // (empty) esta explicado en registrar_usuario.php
        $error = "Nombre y Apellido son obligatorios.";
    } else {
        $sql = "INSERT INTO estudiantes (nombre, apellido,cedula, direccion, email, telefono, nivel_academico,status,nivel_instruccion, fecha_registro ) 
                VALUES ('$nombre', '$apellido', '$cedula','$direccion','$email', '$telefono', '$nivel_academico', '$status', '$nivel_instruccion','$fecha_registro')";

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
        <input type="text" name="nombre" placeholder="Nombres" value="<?php echo htmlspecialchars($nombre); ?>" required><br><br>
        <input type="text" name="apellido" placeholder="Apellido" value="<?php echo htmlspecialchars($apellido); ?>" required ><br><br>
        <input type= "text" name="cedula" placeholder="Cedula" value="<?php echo htmlspecialchars($cedula);?>"required><br><br>
        <input type="text" name= "direccion" placeholder="Dirección" value="<?php echo htmlspecialchars($direccion);?>"required><br><br>
        <input type="text" name="email" placeholder="Correo Electronico" value="<?php echo htmlspecialchars($email); ?>"><br><br> 
        <input type="text" name="telefono" placeholder="Telefono" value="<?php echo htmlspecialchars($telefono);?>"required><br><br>
       <input type="text" name="nivel_academico" placeholder="Nivel Académico (Ej: 1A, 2B, 3C)" value="<?php echo htmlspecialchars($nivel_academico); ?>" required><br><br>
       
  <select name="status">
    <option value="Activo" <?php echo ($status == 'Activo') ? 'selected' : ''; ?>>Activo</option>
    <option value="Inactivo" <?php echo ($status == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
    <option value="Egresado" <?php echo ($status == 'Egresado') ? 'selected' : ''; ?>>Egresado</option>
    <option value="Inhabilitado" <?php echo ($status == 'Inhabilitado') ? 'selected' : ''; ?>>Inhabilitado</option>
    </select><br><br>
       
        <input type="date" name="fecha_registro" placeholder="Fecha de Registro" value="<?php echo htmlspecialchars($fecha_registro); ?>"><br><br>       

        <select name="nivel_instruccion">
            <option value="">Seleccione</option>
            <option value="Primaria">Primaria</option>
            <option value="Secundaria">Secundaria</option>
            <option value="Bachiller">Bachiller</option> 
            <option value="Técnico">Técnico</option>
            <option value="Postgrado">Postgrado</option>

        </select> <br><br>




        <button type="submit">Guardar Estudiante</button>
        <a href="listar.php">Cancelar</a>
    </form>
</body>
</html>