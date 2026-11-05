<?php
require_once 'conexion.php';

// Aseguramos que la respuesta sea siempre en formato JSON
header('Content-Type: application/json');

if (isset($_POST['cedula'])) {
    $cedula = mysqli_real_escape_string($conexion, trim($_POST['cedula']));

    if (empty($cedula)) {
        echo json_encode([
            'encontrado' => false, 
            'mensaje' => 'La cédula es requerida.'
        ]);
        exit();
    }

    // Buscamos a la persona y su nivel académico actual asignado en la escuela
    $sql = "SELECT p.id_persona, p.nombre, p.apellido, p.correo, 
                   e.id_nivel, n.nivel_academico 
            FROM personas p
            LEFT JOIN estudiantes e ON p.id_persona = e.id_persona
            LEFT JOIN niveles n ON e.id_nivel = n.id_nivel
            WHERE p.cedula = '$cedula'";
    
    $resultado = mysqli_query($conexion, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $persona = mysqli_fetch_assoc($resultado);
        
        // Si existe, devolvemos los datos listos para el autocompletado
        echo json_encode([
            'encontrado' => true,
            'mensaje' => '👋 ¡Bienvenido Nuevamente!',
            'id_persona' => $persona['id_persona'],
            'nombre_completo' => $persona['nombre'] . ' ' . $persona['apellido'],
            'correo' => $persona['correo'] ?? '',
            'id_nivel' => $persona['id_nivel'] ?? '',
            'nivel_academico' => $persona['nivel_academico'] ?? 'Sin nivel asignado'
        ]);
    } else {
        // Si la cédula no se encuentra registrada
        echo json_encode([
            'encontrado' => false,
            'mensaje' => '❌ Esta cédula no está registrada en el sistema. Debe inscribirse primero.'
        ]);
    }
    exit();
} else {
    echo json_encode([
        'encontrado' => false,
        'mensaje' => 'No se recibió ninguna cédula.'
    ]);
    exit();
}
?>