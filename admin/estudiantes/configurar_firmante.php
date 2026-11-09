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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar firmante - EFB</title>
    <link rel="icon" type="image/png" href="../images/EFB.png">

    <link rel="stylesheet" href="/IDCsistema/css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
</head>

<body style="background-color: #0c0c0c; font-family: sans-serif; color: #fff;">


    <main class="main-content-reporte {
" style="padding: 5rem 0;">
        <div class="main-card" style="max-width: 550px; margin: auto; text-align: center; background: #111111; padding: 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05);">

            <div style="text-align: center; margin-bottom: 2.5rem;">
                <h2 style="color: #fff; margin: 0 0 0.5rem 0; font-size: 2rem;">Configurar firmante</h2>
                <p style="color: #888888; font-size: 1.1rem; margin: 0;">Modifica los datos del responsable que firma los certificados.</p>
            </div>

            <!-- Librería para mostrar mensajes elegantes -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <?php if ($mensaje == "guardado") { ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cambios guardados!',
                        text: 'Los datos del firmante se actualizaron correctamente.',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#245285',
                        background: '#161b22',
                        color: '#ffffff',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((resultado) => {
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
                <div style="background: rgba(255, 77, 77, 0.1); border: 1px solid #ff4d4d; color: #ff4d4d; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <form method="POST">
                <!-- Conservar el estudiante al guardar el formulario -->
                <input type="hidden" name="id_estudiante" value="<?php echo $id_estudiante; ?>">

                <!-- Nombre del firmante -->
                <div class="form-group" style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 0.85rem; color: #888888; font-weight: 500;">Nombre del firmante</label>
                    <input 
                        class="u-fullwidth"
                        type="text" 
                        name="nombre_firmante" 
                        maxlength="100" 
                        required 
                        placeholder="Escribe el nombre del responsable"
                        value="<?php echo isset($configuracion['nombre_firmante']) ? htmlspecialchars($configuracion['nombre_firmante']) : ''; ?>"
                        style="width: 100%; background: #161b22; border: 1px solid #30363d; color: #ffffff; padding: 11px 14px; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">
                </div>

                <!-- Cargo del firmante -->
                <div class="form-group" style="margin-bottom: 25px; display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 0.85rem; color: #888888; font-weight: 500;">Cargo del firmante</label>
                    <input 
                        class="u-fullwidth"
                        type="text" 
                        name="cargo_firmante" 
                        maxlength="100" 
                        required 
                        placeholder="Escribe el cargo del responsable"
                        value="<?php echo isset($configuracion['cargo_firmante']) ? htmlspecialchars($configuracion['cargo_firmante']) : ''; ?>"
                        style="width: 100%; background: #161b22; border: 1px solid #30363d; color: #ffffff; padding: 11px 14px; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">
                </div>

                <button 
                    type="submit" 
                    class="u-fullwidth btn-primary"
                    style="width: 100%; background-color: #245285; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background-color 0.2s;">
                    Guardar cambios
                </button>
            </form>

            <div style="text-align: center; margin-top: 25px;">
                <a href="listar.php" style="color: rgba(255, 255, 255, 0.4); font-size: 1.1rem; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255, 255, 255, 0.4)'">
                    ← Volver a Gestión de Estudiantes
                </a>
            </div>

        </div>
    </main>

</body>

</html>