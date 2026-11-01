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
    // Capturar y validar el nivel nuevo
    $id_nivel_nuevo = intval($_POST['id_nivel_nuevo']);

    // Buscar el nombre del nivel nuevo para mostrarlo en el mensaje final
    $sql_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel_nuevo'";
    $res_nivel = mysqli_query($conexion, $sql_nivel);
    $fila_nivel = mysqli_fetch_assoc($res_nivel);
    $nombre_nivel_nuevo = $fila_nivel['nivel_academico'];

    $exitos = 0; // Para contar cuántos se promovieron

    // INICIAMOS EL BUCLE: Recorremos cada estudiante seleccionado

    foreach ($estudiantes_seleccionados as $id_estudiante) {

        $id_estudiante = intval($id_estudiante);

        // Consultar el nivel actual del estudiante.
        $sql_actual = "SELECT id_nivel
               FROM estudiantes
               WHERE id_estudiante = '$id_estudiante'";

        $resultado_actual = mysqli_query($conexion, $sql_actual);
        $estudiante_actual = mysqli_fetch_assoc($resultado_actual);

        // Verificar que el estudiante exista.
        if (!$estudiante_actual) {
            continue;
        }

        $id_nivel_actual = intval($estudiante_actual['id_nivel']);

        // Buscar el siguiente nivel permitido.
        $sql_siguiente = "SELECT id_nivel
                  FROM niveles
                  WHERE id_nivel > '$id_nivel_actual'
                  ORDER BY id_nivel ASC
                  LIMIT 1";

        $resultado_siguiente = mysqli_query($conexion, $sql_siguiente);
        $nivel_siguiente = mysqli_fetch_assoc($resultado_siguiente);

        // Impedir promociones a niveles incorrectos.
        if (!$nivel_siguiente || intval($nivel_siguiente['id_nivel']) !== $id_nivel_nuevo) {
            continue;
        }

        // Actualizar únicamente el nivel académico del estudiante.
        // La promoción no crea ni desactiva inscripciones.
        $sql_update = "UPDATE estudiantes
               SET id_nivel = '$id_nivel_nuevo'
               WHERE id_estudiante = '$id_estudiante'";

        // Contar solamente las actualizaciones exitosas.
        if (mysqli_query($conexion, $sql_update)) {
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
