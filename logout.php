<?php
session_start();
session_unset(); // Limpia todas las variables de sesión
session_destroy(); // Destruye la sesión por completo

// Te manda de regreso al login de forma limpia
header("Location: login.php");
exit();
?>