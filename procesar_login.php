<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. CAPTURA DE DATOS SEGURA (Evita Inyección SQL)
    $email          = mysqli_real_escape_string($conexion, $_POST['email']);
    $password_plana = $_POST['password'];

    // 3. CONSULTA LIMPIA Y ESTRUCTURADA (Usando 'id_usuario' en singular)
    $sql = "SELECT id_usuario, usuario, contraseña, rol FROM usuarios WHERE email = '$email'";
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
            
            // GUARDAR VARIABLES DE SESIÓN REALES
            $_SESSION['id_usuario'] = $usuario_datos['id_usuario'];
            $_SESSION['usuario']    = $usuario_datos['usuario'];
            $_SESSION['rol']        = $usuario_datos['rol'];

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
                    header("Location: admin_panel.php"); 
                    exit();
                
                default:
                    header("Location: index.php");
                    exit();
            }

        } else {
            die("<span style='color:red; font-family:sans-serif;'>Error: La contraseña introducida es incorrecta.</span><br><br><a href='login.php'>Volver a intentar</a>");
        }
    } else {
        die("<span style='color:red; font-family:sans-serif;'>Error: El correo electrónico no está registrado.</span><br><br><a href='login.php'>Volver a intentar</a>");
    }
}

mysqli_close($conexion);
?>