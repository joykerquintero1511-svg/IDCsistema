<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "idcdatabase"; // Tu base de datos real

// Conexión tradicional / estructurada
$conexion = mysqli_connect($host, $user, $pass, $db);

// Verificamos si la conexión falló
if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

// Configurar los caracteres correctos para acentos y eñes
mysqli_set_charset($conexion, "utf8");
?>