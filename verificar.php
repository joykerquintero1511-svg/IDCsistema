<?php
include("conexion.php");

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conexion, $_GET['token']);
    
    // Buscamos si existe un usuario con ese token
    $query = "SELECT id_usuario, token_expiracion, verificado FROM usuarios WHERE token = '$token'";
    $resultado = mysqli_query($conexion, $query);

    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        
        // Si ya estaba verificado antes
        if ($row['verificado'] == 1) {
            echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>
                    <h2>Tu cuenta ya había sido verificada anteriormente.</h2>
                    <p><a href='login.php' style='background: #0b2545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Login</a></p>
                  </div>";
            exit;
        }

        // Validar si el token expiró (comparando con la fecha actual)
        $fecha_actual = date("Y-m-d H:i:s");
        if ($fecha_actual > $row['token_expiracion']) {
            echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>
                    <h2 style='color: #d9534f;'>El enlace de verificación ha expirado.</h2>
                    <p>Los enlaces duran 24 horas por seguridad. Por favor regístrate de nuevo.</p>
                    <p><a href='registro_usuarios.php' style='background: #0b2545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Registrarse de nuevo</a></p>
                  </div>";
            exit;
        }

        // Si todo está perfecto, activamos la cuenta y borramos el token usado
        $id_usuario = $row['id_usuario'];
        $update = "UPDATE usuarios SET verificado = 1, token = NULL, token_expiracion = NULL WHERE id_usuario = $id_usuario";
        
        if (mysqli_query($conexion, $update)) {
            echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>
                    <h2 style='color: #2b9348;'>¡Correo verificado con éxito!</h2>
                    <p>Tu cuenta ha sido activada correctamente en la Escuela de Formación Bíblica.</p>
                    <p><a href='login.php' style='background: #0b2545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Iniciar Sesión</a></p>
                  </div>";
        } else {
            echo "Error al actualizar la base de datos: " . mysqli_error($conexion);
        }

    } else {
        echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>
                <h2 style='color: #d9534f;'>Token de verificación inválido.</h2>
                <p>Este enlace no es válido o ya fue utilizado.</p>
                <p><a href='login.php' style='background: #0b2545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Login</a></p>
              </div>";
    }
} else {
    echo "No se ha proporcionado ningún token.";
}
?>