<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "idcdatabase"; 

// Creamos la conexión usando las variables de arriba
$conexion = mysqli_connect($host, $user, $pass, $db);

// Aplicamos el idioma para las tildes y la ñ
mysqli_set_charset($conexion, "utf8");

// Verificamos si la conexión falló
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

?>