<?php
include("conexion.php");

$mensaje = "";
$token_valido = false;

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conexion, $_GET['token']);
    
    $query = "SELECT id_usuario, token_expiracion FROM usuarios WHERE token = '$token'";
    $resultado = mysqli_query($conexion, $query);

    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $fecha_actual = date("Y-m-d H:i:s");

        if ($fecha_actual <= $row['token_expiracion']) {
            $token_valido = true;
            $id_usuario = $row['id_usuario'];
        } else {
            $mensaje = "<h2 style='color: #d9534f; text-align: center;'>El enlace de recuperación ha expirado.</h2>";
        }
    } else {
        $mensaje = "<h2 style='color: #d9534f; text-align: center;'>Token de recuperación inválido.</h2>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token_oculto'])) {
    $token_oculto = mysqli_real_escape_string($conexion, $_POST['token_oculto']);
    $nueva_pass = $_POST['nueva_password'];
    
    // Encriptamos la nueva contraseña
    $password_hash = password_hash($nueva_pass, PASSWORD_BCRYPT);

    // Actualizamos la clave y limpiamos el token
    $update = "UPDATE usuarios SET contraseña = '$password_hash', token = NULL, token_expiracion = NULL WHERE token = '$token_oculto'";
    
    if (mysqli_query($conexion, $update)) {
        echo "<div style='font-family: Arial; text-align: center; margin-top: 80px;'>
                <h2 style='color: #2b9348;'>¡Contraseña restablecida con éxito!</h2>
                <p>Ya puedes iniciar sesión con tu nueva contraseña.</p>
                <p><a href='login.php' style='background: #0b2545; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Ir al Login</a></p>
              </div>";
        exit;
    } else {
        $mensaje = "<p style='color: #d9534f; text-align: center;'>Error al actualizar la contraseña: " . mysqli_error($conexion) . "</p>";
        $token_valido = true; // para mantener el formulario activo si falla
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña - Escuela Bíblica</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">
    <div style="width: 100%; max-width: 400px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1);">
        
        <?php if ($token_valido): ?>
            <h2 style="color: #0b2545; text-align: center; margin-bottom: 20px;">Restablecer Contraseña</h2>
            <?php echo $mensaje; ?>
            <form action="" method="POST">
                <input type="hidden" name="token_oculto" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #555;">Nueva contraseña:</label>
                    <input type="password" name="nueva_password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>
                <button type="submit" style="width: 100%; background: #0b2545; color: white; padding: 10px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Guardar nueva contraseña</button>
            </form>
        <?php else: ?>
            <?php echo $mensaje; ?>
            <p style="text-align: center; margin-top: 20px;"><a href="login.php" style="color: #0b2545; text-decoration: none;">Ir al Login</a></p>
        <?php endif; ?>

    </div>
</body>
</html>