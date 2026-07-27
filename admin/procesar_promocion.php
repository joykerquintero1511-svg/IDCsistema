<?php
session_start();
require_once('../conexion.php'); // Ajusta la ruta a tu conexion.php

// Verificamos que se haya enviado el formulario por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Verificamos si seleccionaron al menos un estudiante y el nivel destino
    if (!isset($_POST['estudiantes']) || empty($_POST['id_nivel_nuevo'])) {
        die("Error: Debes seleccionar al menos un estudiante y el nivel al que serán promovidos.");
    }

    $estudiantes_seleccionados = $_POST['estudiantes']; // Esto es un array de IDs
    $id_nivel_nuevo = mysqli_real_escape_string($conexion, $_POST['id_nivel_nuevo']);

    // Buscamos el texto del nivel nuevo (Ej: "Nivel 2") para guardarlo en inscripciones
    $sql_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel_nuevo'";
    $res_nivel = mysqli_query($conexion, $sql_nivel);
    $fila_nivel = mysqli_fetch_assoc($res_nivel);
    $nombre_nivel_nuevo = $fila_nivel['nivel_academico'];

    $exitos = 0; // Para contar cuántos se promovieron

    // INICIAMOS EL BUCLE: Recorremos cada estudiante seleccionado
    foreach ($estudiantes_seleccionados as $id_estudiante) {
        
        $id_estudiante = mysqli_real_escape_string($conexion, $id_estudiante);

        // 1. Actualizamos su nivel en la tabla 'estudiantes'
        $sql_update = "UPDATE estudiantes SET id_nivel = '$id_nivel_nuevo' WHERE id_estudiante = '$id_estudiante'";
        mysqli_query($conexion, $sql_update);

        // 2. Le creamos una NUEVA inscripción para el nuevo período con un QR nuevo
        $token_qr = md5(uniqid(rand(), true)); // Nuevo token para su nueva entrada
        
        $sql_inscripcion = "INSERT INTO inscripciones (id_estudiante, nivel_academico, fecha_inscripcion, estado, estatus_presencial, token_qr) 
                            VALUES ('$id_estudiante', '$nombre_nivel_nuevo', NOW(), 1, 'pendiente', '$token_qr')";
        
        if (mysqli_query($conexion, $sql_inscripcion)) {
            $exitos++;
        }
    }

    // Al terminar el bucle, le damos un mensaje de éxito
    echo "
    <div style='background:#0b0f19; height:100vh; display:flex; justify-content:center; align-items:center; font-family:sans-serif;'>
        <div style='background:#1e293b; padding:40px; border-radius:10px; text-align:center; color:white;'>
            <h2 style='color:#10b981;'>¡Promoción Exitosa! 🎉</h2>
            <p style='font-size: 1.2rem;'>Se han promovido correctamente <strong>$exitos</strong> estudiantes al <strong>$nombre_nivel_nuevo</strong>.</p>
            <a href='promover_estudiantes.php' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#3b82f6; color:white; text-decoration:none; border-radius:6px; font-weight:bold;'>Volver a la lista</a>
        </div>
    </div>
    ";

} else {
    echo "Acceso denegado.";
}
?>