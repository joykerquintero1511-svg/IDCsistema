<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. CAPTURA DE DATOS SEGURA (Evita Inyección SQL)
    $email        = mysqli_real_escape_string($conexion, $_POST['email']);
    $password_plana = $_POST['password'];

    // 3. CONSULTA LIMPIA Y ESTRUCTURADA
    $sql = "SELECT id_usuario, usuario, contraseña, rol, verificado FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        die("<span style='color:red; font-family:sans-serif;'>Error en la base de datos: " . mysqli_error($conexion) . "</span>");
    }

    // 4. VERIFICAR SI EL EMAIL EXISTE EN LA TABLA USUARIOS
    if (mysqli_num_rows($resultado) > 0) {
        $usuario_datos = mysqli_fetch_assoc($resultado);
        
        // 5. COMPROBAR LA CONTRASEÑA ENCRIPTADA (password_verify)
        if (password_verify($password_plana, $usuario_datos['contraseña'])) {
            
            // ==========================================
            // VALIDAR SI EL CORREO ESTÁ VERIFICADO
            // ==========================================
            if ($usuario_datos['verificado'] == 0) {
                session_unset();
                session_destroy();
                ?>
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Cuenta No Verificada - Escuela de Formación Bíblica</title>
                    <link rel="stylesheet" href="css/mystyle.css">
                    <link rel="icon" type="image/png" href="images/EFB.png">
                </head>
                <body style="background: #0b0f19; color: white; font-family: sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0;">
                    <div style="background: #1e293b; padding: 40px; border-radius: 10px; text-align: center; max-width: 500px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                        <img src="images/EFB.png" alt="Logo EFB" style="width: 80px; height: auto; margin-bottom: 20px;">
                        <h2 style="color: #f59e0b; margin-top: 0;">Cuenta No Verificada</h2>
                        <p style="color: #94a3b8; line-height: 1.5;">Por favor, revisa tu correo electrónico y haz clic en el enlace de activación que te enviamos para poder ingresar.</p>
                        <br>
                        <a href="login.php" style="background: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                            Volver al Login
                        </a>
                    </div>
                </body>
                </html>
                <?php
                exit();
            }

            // GUARDAR VARIABLES DE SESIÓN REALES
            $_SESSION['id_usuario'] = $usuario_datos['id_usuario'];
            $_SESSION['usuario']    = $usuario_datos['usuario'];
            $_SESSION['rol']        = $usuario_datos['rol'];
            
            // ==========================================
            // ANEXO: CAPTURAR ID DE ALUMNO Y SU NIVEL (BLINDADO POR CORREO)
            // ==========================================
            if ($_SESSION['rol'] === 'estudiante' || $_SESSION['rol'] === 'alumno') {
                
                $query_alumno = "SELECT e.id_nivel, e.id_estudiante 
                                 FROM estudiantes e 
                                 INNER JOIN personas p ON e.id_persona = p.id_persona 
                                 WHERE e.email = '$email'";
                                 
                $res_alumno = mysqli_query($conexion, $query_alumno);
                
                if ($res_alumno && $row_alumno = mysqli_fetch_assoc($res_alumno)) {
                    $_SESSION['id_estudiante'] = $row_alumno['id_estudiante'];
                    $_SESSION['id_nivel'] = $row_alumno['id_nivel'];
                } else {
                    // ALCABALA ESTRICTA: Cuenta existe pero no está inscrito como estudiante
                    session_unset();
                    session_destroy();
                    ?>
                    <!DOCTYPE html>
                    <html lang="es">
                    <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Acceso Denegado - Escuela de Formación Bíblica</title>
                        <link rel="stylesheet" href="css/mystyle.css">
                        <link rel="icon" type="image/png" href="images/EFB.png">
                    </head>
                    <body style="background: #0b0f19; color: white; font-family: sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0;">
                        <div style="background: #1e293b; padding: 40px; border-radius: 10px; text-align: center; max-width: 500px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                            <img src="images/EFB.png" alt="Logo EFB" style="width: 80px; height: auto; margin-bottom: 20px;">
                            <h2 style="color: #d9534f; margin-top: 0;">Acceso Denegado: Sin Inscripción Formal</h2>
                            <p style="color: #94a3b8; line-height: 1.5;">Tu cuenta de usuario existe, pero este correo no está registrado como estudiante activo en el sistema académico.</p>
                            <p style="color: #94a3b8; line-height: 1.5;">Por favor, completa tu inscripción o contacta al administrador.</p>
                            <br>
                            <a href="login.php" style="background: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                                Volver al Login
                            </a>
                        </div>
                    </body>
                    </html>
                    <?php
                    exit();
                }
            }
            // ==========================================

            // 6. REDIRECCIÓN INTELIGENTE SEGÚN EL ROL
            switch ($_SESSION['rol']) {
                case 'estudiante':
                case 'alumno':
                    header("Location: estudiantes/index.php");
                    exit();

                case 'profesor':
                    header("Location: profesores/index.php");
                    exit();

                case 'admin':
                    header("Location: admin/index.php");
                    exit();

                default:
                    header("Location: index.php");
                    exit();
            }

        } else {
            // Manejo de contraseña incorrecta (Diseño uniforme)
            session_unset();
            session_destroy();
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Contraseña Incorrecta - Escuela de Formación Bíblica</title>
                <link rel="stylesheet" href="css/mystyle.css">
                <link rel="icon" type="image/png" href="images/EFB.png">
            </head>
            <body style="background: #0b0f19; color: white; font-family: sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0;">
                <div style="background: #1e293b; padding: 40px; border-radius: 10px; text-align: center; max-width: 500px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                    <img src="images/EFB.png" alt="Logo EFB" style="width: 80px; height: auto; margin-bottom: 20px;">
                    <h2 style="color: #d9534f; margin-top: 0;">Contraseña Incorrecta</h2>
                    <p style="color: #94a3b8; line-height: 1.5;">La contraseña introducida es incorrecta. Por favor, verifica tus datos e intenta de nuevo.</p>
                    <br>
                    <a href="login.php" style="background: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                        Volver al Login
                    </a>
                </div>
            </body>
            </html>
            <?php
            exit();
        }

    } else {
        // El correo NO existe en la base de datos
        session_unset();
        session_destroy();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Correo No Registrado - Escuela de Formación Bíblica</title>
            <link rel="stylesheet" href="css/mystyle.css">
            <link rel="icon" type="image/png" href="images/EFB.png">
        </head>
        <body style="background: #0b0f19; color: white; font-family: sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0;">
            <div style="background: #1e293b; padding: 40px; border-radius: 10px; text-align: center; max-width: 500px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                <img src="images/EFB.png" alt="Logo EFB" style="width: 80px; height: auto; margin-bottom: 20px;">
                <h2 style="color: #d9534f; margin-top: 0;">Correo No Registrado</h2>
                <p style="color: #94a3b8; line-height: 1.5;">El correo electrónico introducido no está registrado en el sistema de la escuela.</p>
                <br>
                <a href="login.php" style="background: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                    Volver al Login
                </a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}
?>