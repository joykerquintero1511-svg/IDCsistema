<?php
include("conexion.php");
include("mailer.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conexion, $_POST['email']);

    $sql = "SELECT id_usuario, usuario FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $id_usuario = $row['id_usuario'];
        $nombre = $row['usuario'];

        // Generar token de recuperación (válido por 1 hora)
        $token = bin2hex(random_bytes(32));
        $token_expiracion = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $update = "UPDATE usuarios SET token = '$token', token_expiracion = '$token_expiracion' WHERE id_usuario = $id_usuario";
        
        if (mysqli_query($conexion, $update)) {
            $enlace = "http://localhost/IDCsistema/restablecer.php?token=" . $token;
            
            $asunto = "Recuperación de Contraseña - Escuela de Formación Bíblica";
            $cuerpo = "
                <div style='font-family: Arial, sans-serif; color: #333; padding: 20px;'>
                    <h2 style='color: #0b2545;'>Hola, $nombre</h2>
                    <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente botón para crear una nueva:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='$enlace' style='background-color: #0b2545; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Restablecer Contraseña</a>
                    </p>
                    <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                    <p><a href='$enlace'>$enlace</a></p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #777;'>Este enlace expirará en 1 hora por seguridad. Si tú no solicitaste esto, puedes ignorar este mensaje.</p>
                </div>
            ";

            if (enviarCorreo($email, $asunto, $cuerpo)) {
                $mensaje = "<p style='color: #2b9348; text-align: center;'>¡Correo enviado! Revisa tu bandeja de entrada para restablecer tu contraseña.</p>";
            } else {
                $mensaje = "<p style='color: #d9534f; text-align: center;'>Hubo un error al enviar el correo.</p>";
            }
        }
    } else {
        $mensaje = "<p style='color: #d9534f; text-align: center;'>El correo electrónico no está registrado.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - Escuela Bíblica</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">
    <div style="width: 100%; max-width: 400px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #0b2545; text-align: center; margin-bottom: 20px;">Recuperar Contraseña</h2>
        <?php echo $mensaje; ?>
        <form action="" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #555;">Correo electrónico:</label>
                <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; background: #0b2545; color: white; padding: 10px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Enviar enlace de recuperación</button>
        </form>
        <p style="text-align: center; margin-top: 20px;"><a href="login.php" style="color: #0b2545; text-decoration: none;">← Volver al Login</a></p>
    </div>
</body>
</html>