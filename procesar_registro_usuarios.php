<?php
// 1. CONEXIÓN A LA BASE DE DATO
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. CAPTURA Y LIMPIEZA DE DATOS (Evita Inyección SQL)
    // Nota: Usamos 'password' porque es como viaja desde tu HTML
    $nombre         = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email          = mysqli_real_escape_string($conexion, $_POST['email']);
    $password_plana = $_POST['password']; 
    $rol            = $_POST['rol']; 
    $codigo         = isset($_POST['codigo_autorizacion']) ? $_POST['codigo_autorizacion'] : '';

    // 3. CONTROL DE SEGURIDAD (Claves de la escuela)
    $clave_secreta_profesor = "TITO_2_7-8";
    $clave_secreta_admin    = "1PEDRO_4_10";

    if ($rol == 'profesor' && $codigo !== $clave_secreta_profesor) {
        die("<span style='color:red; font-family:sans-serif;'>Error: La clave de autorización para Profesor es incorrecta.</span><br><br><a href='registro_usuarios.php'>Volver a intentar</a>");
    } elseif ($rol == 'admin' && $codigo !== $clave_secreta_admin) {
        die("<span style='color:red; font-family:sans-serif;'>Error: La clave de autorización para Administrador es incorrecta.</span><br><br><a href='registro_usuarios.php'>Volver a intentar</a>");
    }

    // 4. VERIFICACIÓN DE DUPLICADOS (Estructurada con tu llave primaria 'id_usuario')
    $buscar_usuario = "SELECT id_usuario FROM usuarios WHERE email = '$email'";
    $resultado_busqueda = mysqli_query($conexion, $buscar_usuario);
    
    if (!$resultado_busqueda) {
        die("Error técnico en la verificación: " . mysqli_error($conexion));
    }

    if (mysqli_num_rows($resultado_busqueda) > 0) {
        die("<span style='color:red; font-family:sans-serif;'>Error: Este correo electrónico ya está registrado en el sistema.</span><br><br><a href='registro_usuarios.php'>Volver a intentar</a>");
    }

    // 5. ENCRIPTACIÓN SEGURA DE CONTRASEÑA
    $password_encriptada = password_hash($password_plana, PASSWORD_BCRYPT);

    // 6. INSERCIÓN LIMPIA EN LA BASE DE DATOS
    // id_usuario no se coloca aquí porque al ser AUTO_INCREMENT, MySQL le asigna su número solo.
    // Usamos las columnas exactas de tu phpMyAdmin: usuario, email, contraseña, rol.
    $sql = "INSERT INTO usuarios (usuario, email, contraseña, rol) VALUES ('$nombre', '$email', '$password_encriptada', '$rol')";

    if (mysqli_query($conexion, $sql)) {
        echo "<span style='color:green; font-family:sans-serif; font-weight:bold;'>¡Usuario registrado con éxito como " . ucfirst($rol) . "! Tu base de datos está perfectamente indexada. Ya puedes ir al Login.</span>";
        echo "<br><br><a href='login.php' style='color:blue; font-family:sans-serif; text-decoration:none;'>Ir al Login</a>";
    } else {
        echo "Error al insertar el registro: " . mysqli_error($conexion);
    }
}

// 7. CIERRE DE CONEXIÓN
mysqli_close($conexion);
?>