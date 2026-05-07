<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "idcdatabase";

// Crear conexión
$conexion = mysqli_connect($host, $user, $pass, $db);

// Configurar caracteres (tildes, ñ, etc.)
mysqli_set_charset($conexion, "utf8");

// Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

/*
 * Ejecuta una consulta SQL y maneja errores automáticamente
 * @param mysqli $conexion
 * @param string $sql
 * @param bool $detenerSiError (true = detiene el sistema si falla)
 * @return mysqli_result|false
 */
function ejecutarConsulta($conexion, $sql, $detenerSiError = true) {
    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        $error = mysqli_error($conexion);

        if ($detenerSiError) {
            die("Error en SQL: " . $error . "<br>Consulta: " . $sql);
        } else {
            return false;
        }
    }

    return $resultado;
}
?>