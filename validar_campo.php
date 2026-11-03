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

    // CORRECCIÓN: Ejecutamos la consulta para que $resultado tenga datos
    $sql = "SELECT id_persona FROM personas WHERE cedula = '$cedula'";
    $resultado = mysqli_query($conexion, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        // Si ya existe, aviso informativo (info) con el mensaje de bienvenida y color azul
        echo json_encode([
            'status' => 'info', 
            'mensaje' => '👋 ¡Bienvenido Nuevamente!'
        ]);
    } else {
        echo json_encode([
            'status' => 'success', 
            'mensaje' => '✓ Cédula disponible.'
        ]);
    }
    exit();
} // <-- IMPORTANTE: Faltaba cerrar esta llave del if de cédula

// 2. VALIDAR CORREO (Acepta 'correo' o 'email' desde AJAX)
if (isset($_POST['correo']) || isset($_POST['email'])) {
    $correo_input = isset($_POST['correo']) ? $_POST['correo'] : $_POST['email'];
    $correo = mysqli_real_escape_string($conexion, trim($correo_input));
    
    $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : 'estudiante';

    if (empty($correo)) {
        echo json_encode(['status' => 'error', 'mensaje' => 'El correo es requerido.']);
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'mensaje' => '❌ Formato de correo inválido.']);
        exit();
    }

    $correo_duplicado = false;

    if ($tipo === 'usuario') {
        $sql_usuarios = "SELECT id_usuario FROM usuarios WHERE email = '$correo'";
        $res_usuarios = mysqli_query($conexion, $sql_usuarios);
        if ($res_usuarios && mysqli_num_rows($res_usuarios) > 0) {
            $correo_duplicado = true;
        }
    } else {
        $sql_estudiantes = "SELECT id_estudiante FROM estudiantes WHERE email = '$correo'";
        $res_estudiantes = mysqli_query($conexion, $sql_estudiantes);
        if ($res_estudiantes && mysqli_num_rows($res_estudiantes) > 0) {
            $correo_duplicado = true;
        }
    }

    // CORRECCIÓN: Usamos la variable correcta con guion bajo ($correo_duplicado)
    if ($correo_duplicado) {
        echo json_encode(['status' => 'info', 'mensaje' => '👋 ¡Bienvenido Nuevamente!']);
    } else {
        echo json_encode(['status' => 'success', 'mensaje' => '✓ Correo válido y disponible.']);
    }
    exit();
}
?>