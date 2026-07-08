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
            // ==========================================
            // ANEXO: CAPTURAR ID DE ALUMNO Y SU NIVEL
            // ==========================================
            if ($_SESSION['rol'] === 'estudiante' || $_SESSION['rol'] === 'alumno') {
            $id_user = $_SESSION['id_usuario'];
            $query_alumno = "SELECT id_estudiante FROM estudiantes WHERE id_persona = '$id_user'";
            $res_alumno = mysqli_query($conexion, $query_alumno);
            
        
          // MODIFICACIÓN EN LÍNEA 41: Validamos que la consulta sea exitosa y tenga datos
            if ($res_alumno && $row_alumno = mysqli_fetch_assoc($res_alumno)) {
            $_SESSION['id_estudiante'] = $row_alumno['id_estudiante'];
            } else {
            // Si es un profesor, la consulta dará falso o vacía, así que asignamos ceros de forma segura
            $_SESSION['id_estudiante'] = 0;
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

} // <-- ESTA ES LA LLAVE QUE FALTA. Cierra el if de la línea 6 ($_SERVER["REQUEST_METHOD"])

mysqli_close($conexion);
?>