<?php
include("conexion.php");
// Incluimos el motor de correos
include("mailer.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. CAPTURA Y SANITIZACIÓN DE DATOS
    // Preparar el nombre para guardarlo en la base de datos (Formato Nombre Propio)
    $nombre = trim($_POST['nombre']);
    $nombre = preg_replace('/\s+/', ' ', $nombre);
    $nombre = mb_convert_case(mb_strtolower($nombre, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    $nombre = mysqli_real_escape_string($conexion, $nombre);

    // Preparar el correo
    $email = strtolower(trim($_POST['email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro_usuarios.php?error=correo_invalido");
        exit();
    }

    $email = mysqli_real_escape_string($conexion, $email);
    
    $password_plana = $_POST['password'];
    $rol = $_POST['rol'];
    $codigo = isset($_POST['codigo_autorizacion']) ? $_POST['codigo_autorizacion'] : '';
    $id_nivel = (isset($_POST['id_nivel']) && !empty($_POST['id_nivel'])) ? intval($_POST['id_nivel']) : null;

    // 2. SEGURIDAD (Solo para Profesores y Admin)
    $clave_secreta_profesor = "TITO_2_7-8";
    $clave_secreta_admin = "1PEDRO_4_10";

    if ($rol == 'profesor' && $codigo !== $clave_secreta_profesor) {
        die("Error: Clave de Autorización de Profesor incorrecta. <a href='registro_usuarios.php'>Volver</a>");
    } elseif ($rol == 'admin' && $codigo !== $clave_secreta_admin) {
        die("Error: Clave de Autorización de Administrador incorrecta. <a href='registro_usuarios.php'>Volver</a>");
    }

    // 3. VERIFICAR DUPLICADOS EN USUARIOS
    $check_email = mysqli_query($conexion, "SELECT id_usuario FROM usuarios WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        die("Error: El correo ya está registrado en el sistema. <a href='registro_usuarios.php'>Volver</a>");
    }

    // VERIFICAR QUE EL ESTUDIANTE SE ENCUENTRE PREVIAMENTE INSCRITO
    if ($rol === 'estudiante' || $rol === 'alumno') {
        $sql_buscar_estudiante = "
            SELECT id_estudiante
            FROM estudiantes
            WHERE email = '$email'
            LIMIT 1
        ";

        $resultado_estudiante = mysqli_query($conexion, $sql_buscar_estudiante);

        if (!$resultado_estudiante || mysqli_num_rows($resultado_estudiante) == 0) {
            die(
                "Error: No existe una inscripción asociada a este correo electrónico. " .
                "Primero debes completar el proceso de inscripción. " .
                "<a href='inscripcion.php'>Ir a inscripciones</a>"
            );
        }
    } // <-- Se cerró correctamente la llave del bloque de validación de estudiante

    // 4. ENCRIPTACIÓN DE CONTRASEÑA
    $password_hash = password_hash($password_plana, PASSWORD_BCRYPT);

    // GENERACIÓN DE TOKEN Y EXPIRACIÓN
    $token = bin2hex(random_bytes(32)); // Token criptográfico único
    $token_expiracion = date("Y-m-d H:i:s", strtotime("+24 hours")); // Expira en 24h

    // 5. INSERCIÓN EN LA TABLA USUARIOS
    $sql = "INSERT INTO usuarios (usuario, email, contraseña, rol, estado, verificado, token, token_expiracion) 
            VALUES ('$nombre', '$email', '$password_hash', '$rol', 'activo', 0, '$token', '$token_expiracion')";

    if (mysqli_query($conexion, $sql)) {
        $nuevo_id = mysqli_insert_id($conexion); // Guardamos el ID insertado

        // 6. REGISTRO ESPECÍFICO SEGÚN EL ROL
        if ($rol === 'profesor') {
            $partes_nombre = explode(' ', trim($nombre), 2);
            $primer_nombre = mysqli_real_escape_string($conexion, $partes_nombre[0]);
            $apellido = isset($partes_nombre[1]) ? mysqli_real_escape_string($conexion, $partes_nombre[1]) : '';

            $val_nivel = $id_nivel ? $id_nivel : "NULL";

            // Si utilizas la nueva tabla pivote 'profesor_nivel', se inserta aquí
            $sql_prof = "
                INSERT INTO profesores (nombre, apellido, id_usuario, id_nivel)
                VALUES ('$primer_nombre', '$apellido', '$nuevo_id', $val_nivel)
            ";

            mysqli_query($conexion, $sql_prof);

            // Si tienes habilitada la tabla pivote profesor_nivel:
            if ($id_nivel) {
                $id_prof_insertado = mysqli_insert_id($conexion);
                @mysqli_query($conexion, "INSERT INTO profesor_nivel (id_profesor, id_nivel) VALUES ($id_prof_insertado, $id_nivel)");
            }
        }

        // 7. DISPARAR EL CORREO DE VERIFICACIÓN
        $enlace = "http://localhost/IDCsistema/verificar.php?token=" . $token;
        
        $asunto = "Verifica tu cuenta - Escuela de Formación Bíblica";
        $cuerpo = "
            <div style='font-family: Arial, sans-serif; color: #333; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 8px;'>
                <h2 style='color: #0b2545; text-align: center;'>¡Bienvenido a la Escuela de Formación Bíblica! 🎓</h2>
                <p>Hola <strong>$nombre</strong>,</p>
                <p>Nos alegra mucho tu registro en nuestro sistema. Para completar tu acceso y activar tu cuenta, por favor haz clic en el siguiente botón:</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='$enlace' style='background-color: #0b2545; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Verificar Mi Correo</a>
                </p>
                <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                <p><a href='$enlace' style='color: #0066cc;'>$enlace</a></p>
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='font-size: 12px; color: #777;'>Este enlace expirará en 24 horas por razones de seguridad. Si no solicitaste esta cuenta, puedes ignorar este correo.</p>
            </div>
        ";

        // Envío mediante la función validada de mailer.php
        $correo_enviado = enviarCorreo($email, $asunto, $cuerpo);

        if ($correo_enviado === true) {
            echo "<div style='font-family: sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; background: #111; color: #fff; text-align: center; border-radius: 8px;'>";
            echo "<h3 style='color: #2ecc71;'>¡Registro Exitoso!</h3>";
            echo "<p>Hemos enviado un correo de verificación a <strong>$email</strong>. Por favor revisa tu bandeja de entrada o spam para activar tu cuenta.</p>";
            echo "<a href='login.php' style='color: #38bdf8;'>Ir al Iniciar Sesión</a>";
            echo "</div>";
        } else {
            echo "<div style='font-family: sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; background: #111; color: #fff; text-align: center; border-radius: 8px;'>";
            echo "<h3 style='color: #f39c12;'>Registro Guardado con Observación</h3>";
            echo "<p>El usuario se creó correctamente, pero no pudimos enviar el correo de activación automáticamente.</p>";
            echo "<a href='login.php' style='color: #38bdf8;'>Ir al Iniciar Sesión</a>";
            echo "</div>";
        }

    } else {
        echo "Error al registrar usuario: " . mysqli_error($conexion);
    }
}
?>