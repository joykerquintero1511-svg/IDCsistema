<?php
include("conexion.php");
// Incluimos nuestro motor de correos que creamos en la fase anterior
include("mailer.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. CAPTURA DE DATOS
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $password_plana = $_POST['password'];
    $rol = $_POST['rol'];
    $codigo = isset($_POST['codigo_autorizacion']) ? $_POST['codigo_autorizacion'] : '';
    $id_nivel = isset($_POST['id_nivel']) ? intval($_POST['id_nivel']) : null;

    // 2. SEGURIDAD (Solo para Profesores y Admin)
    $clave_secreta_profesor = "TITO_2_7-8";
    $clave_secreta_admin = "1PEDRO_4_10";

    if ($rol == 'profesor' && $codigo !== $clave_secreta_profesor) {
        die("Error: Clave de Profesor incorrecta. <a href='registro_usuarios.php'>Volver</a>");
    } elseif ($rol == 'admin' && $codigo !== $clave_secreta_admin) {
        die("Error: Clave de Administrador incorrecta. <a href='registro_usuarios.php'>Volver</a>");
    }

    // 3. VERIFICAR DUPLICADOS
    $check_email = mysqli_query($conexion, "SELECT id_usuario FROM usuarios WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        die("Error: El correo ya está registrado. <a href='registro_usuarios.php'>Volver</a>");
    }

    // 4. ENCRIPTACIÓN
    $password_hash = password_hash($password_plana, PASSWORD_BCRYPT);

    // ==========================================
    // NUEVO: GENERACIÓN DE TOKEN Y EXPIRACIÓN
    // ==========================================
    $token = bin2hex(random_bytes(32)); // Creamos una llave criptográfica única de 64 caracteres
    $token_expiracion = date("Y-m-d H:i:s", strtotime("+24 hours")); // El enlace expira en 24 horas

    // 5. INSERCIÓN EN USUARIOS (Actualizado con verificado = 0, token y token_expiracion)
    // Nota: Guardamos verificado en 0 por defecto hasta que confirme su correo.
    $sql = "INSERT INTO usuarios (usuario, email, contraseña, rol, estado, verificado, token, token_expiracion) 
            VALUES ('$nombre', '$email', '$password_hash', '$rol', 'activo', 0, '$token', '$token_expiracion')";

    if (mysqli_query($conexion, $sql)) {
        $nuevo_id = mysqli_insert_id($conexion);

        // 6. REGISTRO ESPECÍFICO DEL ESTUDIANTE
        if ($rol === 'estudiante' || $rol === 'alumno') {
            $sql_est = "INSERT INTO estudiantes (id_persona, id_nivel, fecha_registro) 
                        VALUES ('$nuevo_id', '$id_nivel', NOW())";
            mysqli_query($conexion, $sql_est);
        }
        
        // ==========================================
        // NUEVO: DISPARAR EL CORREO DE VERIFICACIÓN
        // ==========================================
        // Construimos el enlace dinámico que lo va a verificar
        $enlace = "http://localhost/IDCsistema/verificar.php?token=" . $token;
        
        $asunto = "Verifica tu cuenta - Escuela de Formación Bíblica";
        $cuerpo = "
            <div style='font-family: Arial, sans-serif; color: #333; padding: 20px;'>
                <h2 style='color: #0b2545;'>¡Bienvenido a la Escuela de Formación Bíblica, $nombre!</h2>
                <p>Nos alegra mucho que te hayas registrado. Para completar tu acceso al sistema y activar tu cuenta, por favor haz clic en el siguiente botón:</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='$enlace' style='background-color: #0b2545; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Verificar mi Correo</a>
                </p>
                <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                <p><a href='$enlace'>$enlace</a></p>
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='font-size: 12px; color: #777;'>Este enlace expirará en 24 horas por razones de seguridad. Si no creaste esta cuenta, puedes ignorar este mensaje.</p>
            </div>
        ";

        // Enviamos el correo usando nuestra función de mailer.php
        $correo_enviado = enviarCorreo($email, $asunto, $cuerpo);

        if ($correo_enviado) {
            echo "Registro exitoso. Hemos enviado un enlace de verificación a tu correo electrónico. Por favor revísalo para activar tu cuenta. <a href='login.php'>Ir al Login</a>";
        } else {
            echo "Registro exitoso, pero hubo un error al enviar el correo de verificación. Contacta al soporte. <a href='login.php'>Ir al Login</a>";
        }

    } else {
        echo "Error al insertar usuario: " . mysqli_error($conexion);
    }
}
?>