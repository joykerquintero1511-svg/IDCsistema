<?php
include("conexion.php");

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

    // 5. INSERCIÓN EN USUARIOS (Con id_nivel)
    $sql = "INSERT INTO usuarios (usuario, email, contraseña, rol, estado) 
            VALUES ('$nombre', '$email', '$password_hash', '$rol', 1)";

    if (mysqli_query($conexion, $sql)) {
        $nuevo_id = mysqli_insert_id($conexion);

// // 6. REGISTRO ESPECÍFICO DEL ESTUDIANTE (¡Aquí sí guardamos el nivel!)
        if ($rol === 'estudiante' || $rol === 'alumno') {
            $sql_est = "INSERT INTO estudiantes (id_persona, id_nivel, fecha_registro) 
                        VALUES ('$nuevo_id', '$id_nivel', NOW())";
            mysqli_query($conexion, $sql_est);
        }
        
        echo "Registro exitoso. <a href='login.php'>Ir al Login</a>";
    } else {
        echo "Error al insertar usuario: " . mysqli_error($conexion);
    }
}
?>