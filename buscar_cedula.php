<?php
require_once 'conexion.php';

if (isset($_POST['cedula'])) {
    $cedula = mysqli_real_escape_string($conexion, $_POST['cedula']);
    
    // 1. Buscamos a la persona
    $sql = "SELECT * FROM personas WHERE cedula = '$cedula' LIMIT 1";
    $resultado = mysqli_query($conexion, $sql);
    
    if (mysqli_num_rows($resultado) > 0) {
        $persona = mysqli_fetch_assoc($resultado);
        
        // 2. Buscamos si ya tiene registro en 'estudiantes' para traer su email y nivel de instrucción
        $id_persona = $persona['id_persona'];
        $sql_est = "SELECT email, nivel_instruccion FROM estudiantes WHERE id_persona = '$id_persona' LIMIT 1";
        $res_est = mysqli_query($conexion, $sql_est);
        
        if (mysqli_num_rows($res_est) > 0) {
            $estudiante = mysqli_fetch_assoc($res_est);
            $persona['email'] = $estudiante['email'];
            $persona['nivel_instruccion'] = $estudiante['nivel_instruccion'];
        } else {
            $persona['email'] = '';
            $persona['nivel_instruccion'] = '';
        }
        
        // Devolvemos los datos en formato JSON
        echo json_encode($persona);
    } else {
        // Si no existe, devolvemos un error controlado
        echo json_encode(['error' => 'no_existe']);
    }
}
?>