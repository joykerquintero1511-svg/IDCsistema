<?php
session_start();

require_once '../../conexion.php';

// Obtener el ID del estudiante para regresar a sus certificados.
$id_estudiante = 0;

if (isset($_GET['id_estudiante'])) {
    $id_estudiante = intval($_GET['id_estudiante']);
}

if (isset($_POST['id_estudiante'])) {
    $id_estudiante = intval($_POST['id_estudiante']);
}


// Permitir el acceso solo al administrador.
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$mensaje = "";
$error = "";

// Obtener la configuración actual.
$sql = "SELECT id_configuracion, nombre_firmante, cargo_firmante
        FROM configuracion_certificados
        WHERE id_configuracion = 1";

$resultado = mysqli_query($conexion, $sql);
$configuracion = mysqli_fetch_assoc($resultado);

if (!$configuracion) {
    die("No existe la configuración del firmante.");
}

// Guardar los cambios.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre_firmante']);
    $cargo = trim($_POST['cargo_firmante']);

    // Verificar que ambos campos estén completos.
    if ($nombre == "" || $cargo == "") {
        $error = "Debes completar ambos campos.";

        // Permitir letras, espacios, tildes y la letra ñ.
    } elseif (
        !preg_match('/^[\p{L}\s]+$/u', $nombre) ||
        !preg_match('/^[\p{L}\s]+$/u', $cargo)
    ) {

        $error = "El nombre y el cargo solo pueden contener letras y espacios.";
    } else {

        $sql_actualizar = "UPDATE configuracion_certificados
                           SET nombre_firmante = ?,
                               cargo_firmante = ?
                           WHERE id_configuracion = 1";

        $consulta = mysqli_prepare($conexion, $sql_actualizar);

        mysqli_stmt_bind_param($consulta, "ss", $nombre, $cargo);

        if (mysqli_stmt_execute($consulta)) {

            // Indicar que los datos se guardaron correctamente.
            $mensaje = "guardado";

            $configuracion['nombre_firmante'] = $nombre;
            $configuracion['cargo_firmante'] = $cargo;
        } else {
            $error = "No se pudieron guardar los cambios.";
        }

        mysqli_stmt_close($consulta);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Configurar firmante - EFB</title>

    <link rel="stylesheet" href="/IDCsistema/css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
</head>

<body>

    <?php include '../sidebaradmin.php'; ?>

    <main class="main-content">

        <div style="max-width: 550px; margin: 40px auto; color: white;">

            <h1>Configurar firmante</h1>

            <p>Modifica los datos del responsable que firma los certificados.</p>

            <!-- Librería para mostrar mensajes elegantes -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <?php if ($mensaje == "guardado") { ?>

                <script>
                    // Mostrar mensaje cuando se guarden los datos.
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cambios guardados!',
                        text: 'Los datos del firmante se actualizaron correctamente.',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#006da8',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((resultado) => {

                        // Regresar a los certificados al presionar Aceptar.
                        if (resultado.isConfirmed) {
                            window.location.href = <?php
                                                    if ($id_estudiante > 0) {
                                                        echo json_encode('certificados_estudiante.php?id_estudiante=' . $id_estudiante);
                                                    } else {
                                                        echo json_encode('listar.php');
                                                    }
                                                    ?>;
                        }
                    });
                </script>

            <?php } ?>

            <?php if ($error != "") { ?>
                <p style="color: #ff5555;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php } ?>

            <form method="POST">
                <!-- Conservar el estudiante al guardar el formulario -->
                <input type="hidden"
                    name="id_estudiante"
                    value="<?php echo $id_estudiante; ?>">

                <label>Nombre del firmante</label>

                <!-- Nombre del firmante -->
                <label>Nombre del firmante</label>

                <input
                    type="text"
                    name="nombre_firmante"
                    maxlength="100"
                    required
                    placeholder="Escribe el nombre del responsable"
                    value=""
                    style="display:block; width:100%; padding:12px; margin:10px 0 20px;">

                <!-- Cargo del firmante -->
                <label>Cargo del firmante</label>

                <input
                    type="text"
                    name="cargo_firmante"
                    maxlength="100"
                    required
                    value="<?php echo htmlspecialchars($configuracion['cargo_firmante']); ?>"
                    style="display:block; width:100%; padding:12px; margin:10px 0 20px;">

                <button
                    type="submit"
                    style="background:#006da8; color:white; border:0; padding:12px 20px; cursor:pointer;">
                    Guardar cambios
                </button>

            </form>

            <p style="margin-top:25px;">
                <a href="listar.php" style="color:#60a5fa;">
                    ← Volver a Gestión de Estudiantes
                </a>
            </p>

        </div>

    </main>

</body>

</html>