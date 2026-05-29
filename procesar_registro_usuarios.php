<?php
// 1. CONEXIÓN Y SESIÓN
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. CAPTURA Y LIMPIEZA
    $nombre    = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email     = mysqli_real_escape_string($conexion, $_POST['email']);
    $password_plana = $_POST['password'];
    $rol       = $_POST['rol'];
    $codigo    = isset($_POST['codigo_autorizacion']) ? $_POST['codigo_autorizacion'] : '';
    $id_nivel  = isset($_POST['id_nivel']) ? $_POST['id_nivel'] : '';

    // 3. CONTROL DE SEGURIDAD (Claves maestras únicas)
    $clave_secreta_profesor    = "TITO_2_7-8";
    $clave_secreta_admin       = "1PEDRO_4_10";
    $clave_secreta_estudiante  = "ROMANOS_8_28"; // Tu clave maestra para estudiantes

    // Validación por ROL (Lógica unificada y simple)
    if ($rol == 'profesor' && $codigo !== $clave_secreta_profesor) {
        die("Error: Clave de Profesor incorrecta. <a href='registro_usuarios.php'>Volver</a>");
    } elseif ($rol == 'admin' && $codigo !== $clave_secreta_admin) {
        die("Error: Clave de Administrador incorrecta. <a href='registro_usuarios.php'>Volver</a>");
    } elseif ($rol == 'estudiante' && $codigo !== $clave_secreta_estudiante) {
        // Aquí validamos que la clave sea la maestra, sin importar el nivel
        die("Error: Clave de Estudiante incorrecta. <a href='registro_usuarios.php'>Volver</a>");
    }

    // 4. VERIFICAR DUPLICADOS
    $check_email = mysqli_query($conexion, "SELECT id_usuario FROM usuarios WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        die("Error: El correo ya está registrado. <a href='registro_usuarios.php'>Volver</a>");
    }

    // 5. ENCRIPTACIÓN
    $password_hash = password_hash($password_plana, PASSWORD_BCRYPT);

    // 6. INSERCIÓN
    // Guardamos el usuario con su nivel asociado
    $sql = "INSERT INTO usuarios (usuario, email, contraseña, rol, id_nivel) 
            VALUES ('$nombre', '$email', '$password_hash', '$rol', " . ($id_nivel ? "'$id_nivel'" : "NULL") . ")";

    if (mysqli_query($conexion, $sql)) {
        $nuevo_id = mysqli_insert_id($conexion);

        // Si es estudiante, vinculamos en la tabla estudiantes para la relación del panel
        if ($rol == 'estudiante') {
            $sql_est = "INSERT INTO estudiantes (id_usuario, id_nivel) VALUES ('$nuevo_id', '$id_nivel')";
            mysqli_query($conexion, $sql_est);
        }

        echo "¡Registro exitoso! <br><a href='login.php'>Ir al Login</a>";
    } else {
        echo "Error al insertar: " . mysqli_error($conexion);
    }
}
?>