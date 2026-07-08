<?php
session_start();
if (!isset ($_SESSION['rol'])|| $_SESSION['rol']!=='admin'){
    header("Location: ../../login.php");
    exit();
} //  Para que solo el administrador pueda agregar estudiantes.



require_once '../../conexion.php';

$conexion = $conexion ?? null; // el xq d esta variable esta en el archivo listar.php

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
    <link rel="icon" type="image/png" href="../../images/EFB.png">
    <style>
/* --- CONFIGURACIÓN BASE DEL FONDO --- */
body {
    background-color: #0c0c0c;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.main-content {
    margin-left: 260px; /* Ancho de tu barra lateral */
    width: calc(100% - 260px);
    box-sizing: border-box;
    padding: 2.5rem;
    color: #ffffff;
    min-height: 100vh;
}

/* --- HEADER DEL FORMULARIO --- */
.header-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.header-logo {
    width: 45px;
    height: auto;
}

.header-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
}

.subtitulos {
    color: #888888;
    font-size: 0.95rem;
    font-weight: 400;
    margin-top: 0;
    margin-bottom: 2rem;
}

/* --- TARJETA DEL FORMULARIO --- */
.form-card {
    background-color: #111111;
    padding: 2.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    max-width: 550px; /* Ancho ideal para que no se estire infinito */
}

/* --- DISEÑO DE LOS INPUTS Y SELECTS --- */
.formulario-input, 
.inscripciones-input {
    width: 100%;
    background-color: #161616;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 6px;
    padding: 0.8rem 1rem;
    color: #ffffff;
    font-size: 0.95rem;
    box-sizing: border-box;
    display: block;
    margin-bottom: 1.2rem; /* Reemplaza el uso de <br> dándole espacio limpio */
    transition: border-color 0.3s, box-shadow 0.3s;
}

/* Estilo especial al hacer clic en un input */
.formulario-input:focus, 
.inscripciones-input:focus {
    border-color: #3a7bc8;
    outline: none;
    box-shadow: 0 0 0 3px rgba(58, 123, 200, 0.2);
}

/* Ajuste del input tipo fecha para modo oscuro */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1); /* Convierte el icono del calendario a color blanco */
    cursor: pointer;
}

/* --- BOTONES DE ACCIÓN --- */
.boton-input {
    background-color: #3a7bc8;
    color: #ffffff;
    border: none;
    padding: 0.8rem 1.8rem;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
    margin-right: 1rem;
}

.boton-input:hover {
    background-color: #2b5f9e;
}

.boton-volver {
    color: #888888;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    transition: color 0.2s;
}

.boton-volver:hover {
    color: #ff5555; /* Color rojo sutil al cancelar */
}
</style>
</head>
<body>

<!-- Justo debajo de tu código PHP de validación, donde empieza el HTML técnico: -->
<main class="main-content">

    <div class="background-titulo">
        <div class="header-container">
            <img src="../../images/EFB.png" alt="Logo Escuela de Formación Bíblica" class="header-logo">
            <h1 class="header-title">Agregar Estudiante</h1>
        </div>
    </div>
    
    <h5 class="subtitulos">¡Bienvenido! Dios te bendiga</h5>

    <!-- Aca se puede abrir y cerrar php varias veces y finalizando el endif -->
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($exito): ?>
        <p style="color: green;"><?php echo $exito; ?></p>
    <?php endif; ?> 

    <!-- AQUÍ EMPIEZA TU FORMULARIO -->
    <div class="form-card">
        <form method="POST">
        <input class="formulario-input" type="text" name="nombre" placeholder="Nombres" value="<?php echo htmlspecialchars($nombre); ?>" required><br><br>
        <input class="formulario-input" type="text" name="apellido" placeholder="Apellido" value="<?php echo htmlspecialchars($apellido); ?>" required ><br><br>
        <input class="formulario-input" type= "text" name="cedula" placeholder="Cedula" value="<?php echo htmlspecialchars($cedula);?>"required><br><br>
        <input class="formulario-input" type="text" name= "direccion" placeholder="Dirección" value="<?php echo htmlspecialchars($direccion);?>"required><br><br>
        <input class="formulario-input" type="text" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>"><br><br> 
        <input class="formulario-input" type="text" name="telefono" placeholder="Telefono" value="<?php echo htmlspecialchars($telefono);?>"required><br><br>
        <input class="formulario-input" type="text" name="nivel_academico" placeholder="Nivel Académico (Ej: 1A, 2B, 3C)" value="<?php echo htmlspecialchars($nivel_academico); ?>" required><br><br>
        <select name="status">
        <option value="Activo" <?php echo ($status == 'Activo') ? 'selected' : ''; ?>>Activo</option>
        <option value="Inactivo" <?php echo ($status == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
        <option value="Egresado" <?php echo ($status == 'Egresado') ? 'selected' : ''; ?>>Egresado</option>
        <option value="Inhabilitado" <?php echo ($status == 'Inhabilitado') ? 'selected' : ''; ?>>Inhabilitado</option>
    </select><br><br>
        <input class="formulario-input" type="date" name="fecha_registro" placeholder="Fecha de Registro" value="<?php echo htmlspecialchars($fecha_registro); ?>"><br><br>       

        <select class="formulario-input" name="nivel_instruccion">
            <option value="">Seleccione</option>
            <option value="Primaria">Primaria</option>
            <option value="Secundaria">Secundaria</option>
            <option value="Bachiller">Bachiller</option> 
            <option value="Técnico">Técnico</option>
            <option value="Postgrado">Postgrado</option>

        </select> <br><br>




        <button type="submit" class="boton-input">Guardar Estudiante</button>
        <a href="listar.php" class="boton-volver">Cancelar</a>
    
            <!-- Tus inputs aquí adentro... -->
        </form>
    </div>

</main>


 



    
</body>
</html>