<?php
require_once 'conexion.php';

header('Content-Type: application/json');

// 1. VALIDAR CÉDULA (Inscripción / Personas)
if (isset($_POST['cedula'])) {
    $cedula = mysqli_real_escape_string($conexion, trim($_POST['cedula']));

    if (empty($cedula)) {
        echo json_encode(['status' => 'error', 'mensaje' => 'La cédula es requerida.']);
        exit();
    }

    // Comprobamos que la cédula tenga entre 7 y 8 números
    if (!preg_match('/^[0-9]{7,8}$/', $cedula)) {
        echo json_encode([
            'status' => 'error',
            'mensaje' => '❌ La cédula debe tener 7 u 8 números.'
        ]);
        exit();
    }

    $sql = "SELECT id_persona FROM personas WHERE cedula = '$cedula'";
    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        echo json_encode(['status' => 'error', 'mensaje' => '❌ Esta cédula ya está registrada.']);
    } else {
        echo json_encode(['status' => 'success', 'mensaje' => '✓ Cédula disponible.']);
    }
    exit();
}

// 2. VALIDAR CORREO (Acepta 'correo' o 'email' desde AJAX)
if (isset($_POST['correo']) || isset($_POST['email'])) {
    // Captura el valor venga con la clave 'correo' o 'email'
    $correo_input = isset($_POST['correo']) ? $_POST['correo'] : $_POST['email'];
    $correo = mysqli_real_escape_string($conexion, trim($correo_input));

    if (empty($correo)) {
        echo json_encode(['status' => 'error', 'mensaje' => 'El correo es requerido.']);
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'mensaje' => '❌ Formato de correo inválido.']);
        exit();
    }

    // Consulta unificada: verifica si el correo existe en la tabla 'estudiantes' O en la tabla 'usuarios'
    $sql_estudiantes = "SELECT id_estudiante FROM estudiantes WHERE email = '$correo'";
    $res_estudiantes = mysqli_query($conexion, $sql_estudiantes);

    $sql_usuarios = "SELECT id_usuario FROM usuarios WHERE email = '$correo'";
    $res_usuarios = mysqli_query($conexion, $sql_usuarios);

    if (mysqli_num_rows($resultado) > 0) {
        echo json_encode(['status' => 'error', 'mensaje' => '❌ Este correo ya está registrado.']);
    } else {
        echo json_encode(['status' => 'success', 'mensaje' => '✓ Correo válido y no registrado en el sistema.']);
    }
    exit();
}
?>