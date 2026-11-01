<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit; // Sin exit(), el script sigue ejecutándose aunque redirija.
}



require_once '../../conexion.php';

$conexion = $conexion ?? null;
$error = "";
$exito = "";

// Verificar si llega un ID por la URL
if (!isset($_GET['id'])) {
    die(" No se especificó qué estudiante editar.");
}

// Convertir el ID recibido por la URL en un número entero

$id = intval($_GET['id']);

// Obtener únicamente los datos personales del estudiante.
// No consultar inscripciones porque aquí no se modifican.
$sql = "
    SELECT
        estudiantes.id_estudiante,
        estudiantes.id_persona,
        estudiantes.email,
        personas.nombre,
        personas.apellido,
        personas.telefono
    FROM estudiantes
    INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona
    WHERE estudiantes.id_estudiante = $id
";

$resultado = ejecutarConsulta($conexion, $sql);
$estudiante = mysqli_fetch_assoc($resultado); // Sirve para extraer una fila de resultados,y convertirla en un formato fácil de usar.

if (!$estudiante) {
    die("Estudiante no encontrado.");
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);



    if (empty($nombre) || empty($apellido)) {
        $error = " Nombre y Apellido son obligatorios.";
    } else {
        $sql_personas = "UPDATE personas SET 
        nombre = '$nombre',
        apellido = '$apellido',
        telefono = '$telefono'
        WHERE id_persona = " . $estudiante['id_persona'];

        // Actualizar únicamente el correo del estudiante.
        $sql_estudiantes = "UPDATE estudiantes SET
        email = '$email'
        WHERE id_estudiante = $id";

        // Guardar los datos personales sin modificar las inscripciones.
        if (
            ejecutarConsulta($conexion, $sql_personas) &&
            ejecutarConsulta($conexion, $sql_estudiantes)
        ) {
            $exito = "Estudiante actualizado correctamente.";

            // Actualizar los datos mostrados en el formulario.
            $estudiante['nombre'] = $nombre;
            $estudiante['apellido'] = $apellido;
            $estudiante['email'] = $email;
            $estudiante['telefono'] = $telefono;
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
            margin-left: 260px;
            /* Ancho de tu barra lateral */
            width: calc(100% - 260px);
            box-sizing: border-box;
            padding: 2.5rem;
            color: #ffffff;
            min-height: 100vh;
        }

        /* --- HEADER DEL FORMULARIO (IGUAL A AGREGAR.PHP) --- */
        .header-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            /* Espacio limpio antes de la tarjeta */
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
            /* Quitamos márgenes por defecto para que alinee perfecto */
        }

        /* --- TARJETA DEL FORMULARIO --- */
        .form-card {
            background-color: #111111;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            max-width: 550px;
        }

        /* --- DISEÑO DE LOS INPUTS Y SELECTS --- */
        .formulario-input {
            width: 100%;
            background-color: #161616;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            padding: 0.8rem 1rem;
            color: #ffffff;
            font-size: 0.95rem;
            box-sizing: border-box;
            display: block;
            margin-bottom: 1.2rem;
            /* Adiós a los <br> manuales */
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .formulario-input:focus {
            border-color: #3a7bc8;
            outline: none;
            box-shadow: 0 0 0 3px rgba(58, 123, 200, 0.2);
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
            color: #ff5555;
        }

        /* Estilos sutiles para los mensajes de error/éxito */
        .msg-alert {
            padding: 0.8rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
    </style>


</head>

<body>

    <main class="main-content">

        <div class="background-titulo">
            <div class="header-container">
                <img src="../../images/EFB.png" alt="Logo Escuela de Formación Bíblica" class="header-logo">
                <h1 class="header-title">Editar Estudiante</h1>
            </div>
        </div>

        <!-- Bloques de alertas estilizados sutilmente -->
        <?php if ($error): ?>
            <div class="msg-alert" style="background-color: rgba(255, 85, 85, 0.1); color: #ff5555;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="msg-alert" style="background-color: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                <?php echo $exito; ?>
            </div>
        <?php endif; ?>

        <!-- Tarjeta contenedora del formulario -->
        <div class="form-card">
            <form method="POST">

                <!-- Agregamos class="formulario-input" a cada campo y quitamos los <br> intermedios -->
                <input type="text" name="nombre" class="formulario-input" placeholder="Nombres" value="<?php echo htmlspecialchars($estudiante['nombre']); ?>">

                <input type="text" name="apellido" class="formulario-input" placeholder="Apellido" value="<?php echo htmlspecialchars($estudiante['apellido']); ?>">

                <input type="text" name="email" class="formulario-input" placeholder="Correo Electrónico" value="<?php echo htmlspecialchars($estudiante['email']); ?>">

                <input type="text" name="telefono" class="formulario-input" placeholder="Teléfono" value="<?php echo htmlspecialchars($estudiante['telefono']); ?>">

                <!-- Botonera Final -->
                <button type="submit" class="boton-input">Guardar Cambios</button>
                <a href="listar.php" class="boton-volver">Volver</a>

            </form>
        </div>

    </main>

</body>

</html>