<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. CAPTURA DE DATOS SEGURA (Evita Inyección SQL)
    $email          = mysqli_real_escape_string($conexion, $_POST['email']);
    $password_plana = $_POST['password'];

    // 3. CONSULTA LIMPIA Y ESTRUCTURADA (Añadimos 'verificado' a la selección)
    $sql = "SELECT id_usuario, usuario, contraseña, rol, verificado FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        // Si hay algún error con los nombres de las columnas, MySQL nos lo dirá aquí de frente
        die("<span style='color:red; font-family:sans-serif;'>Error en la base de datos: " . mysqli_error($conexion) . "</span>");
    }

    // 4. VERIFICAR SI EL EMAIL EXISTE
    if (mysqli_num_rows($resultado) > 0) {
        $usuario_datos = mysqli_fetch_assoc($resultado);
        
        // 5. COMPROBAR LA CONTRASEÑA ENCRIPTADA (password_verify)
        if (password_verify($password_plana, $usuario_datos['contraseña'])) {
            
            // ==========================================
            // NUEVO: VALIDAR SI EL CORREO ESTÁ VERIFICADO
            // ==========================================
            if ($usuario_datos['verificado'] == 0) {
                die("<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>
                        <h2 style='color: #d9534f;'>Cuenta no verificada</h2>
                        <p>Por favor, revisa tu correo electrónico y haz clic en el enlace de activación que te enviamos para poder ingresar.</p>
                        <br><a href='login.php' style='background: #0b2545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Volver al Login</a>
                     </div>");
            }

            // GUARDAR VARIABLES DE SESIÓN REALES
            $_SESSION['id_usuario'] = $usuario_datos['id_usuario'];
            $_SESSION['usuario']    = $usuario_datos['usuario'];
            $_SESSION['rol']        = $usuario_datos['rol'];
            
            // ==========================================
            // ANEXO: CAPTURAR ID DE ALUMNO Y SU NIVEL
            // ==========================================
            if ($_SESSION['rol'] === 'estudiante' || $_SESSION['rol'] === 'alumno') {
                $id_user = $_SESSION['id_usuario'];
                $query_alumno = "SELECT id_nivel, id_estudiante FROM estudiantes WHERE id_persona = '$id_user'";
                $res_alumno = mysqli_query($conexion, $query_alumno);
                
                if ($res_alumno && $row_alumno = mysqli_fetch_assoc($res_alumno)) {
                    $_SESSION['id_estudiante'] = $row_alumno['id_estudiante'];
                    $_SESSION['id_nivel'] = $row_alumno['id_nivel'];
                } else {
                    $_SESSION['id_estudiante'] = 0;
                    $_SESSION['id_nivel'] = 0;
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
            // Manejo de contraseña incorrecta
            die("<span style='color:red; font-family:sans-serif;'>Error: La contraseña introducida es incorrecta.</span><br><br><a href='login.php'>Volver</a>");
        }

    } else {
        // Manejo de correo no registrado
        die("<span style='color:red; font-family:sans-serif;'>Error: El correo electrónico no está registrado.</span><br><br><a href='login.php'>Volver</a>");
    }

} // Cierra el if de la línea 6 ($_SERVER["REQUEST_METHOD"])

mysqli_close($conexion);
?>