<?php
require_once 'conexion.php';

header('Content-Type: application/json');

// VALIDAR CÉDULA
if (isset($_POST['cedula'])) {
    $cedula = mysqli_real_escape_string($conexion, trim($_POST['cedula']));

    if (empty($cedula)) {
        echo json_encode(['status' => 'error', 'mensaje' => 'La cédula es requerida.']);
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

// VALIDAR CORREO
if (isset($_POST['correo'])) {
    $correo = mysqli_real_escape_string($conexion, trim($_POST['correo']));

    if (empty($correo)) {
        echo json_encode(['status' => 'error', 'mensaje' => 'El correo es requerido.']);
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'mensaje' => '❌ Formato de correo inválido.']);
        exit();
    }

    $sql = "SELECT id_persona FROM personas WHERE correo = '$correo'";
    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        echo json_encode(['status' => 'error', 'mensaje' => '❌ Este correo ya está registrado.']);
    } else {
        echo json_encode(['status' => 'success', 'mensaje' => '✓ Correo disponible.']);
    }
    exit();
}
?>