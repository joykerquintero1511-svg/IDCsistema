
<?php
session_start();
// Si no existe la sesión "usuario", redirige al login de una vez
if (!isset($_SESSION['usuario'])) {
    echo '<script>window.location.replace("../login.php");</script>';
    exit();
}

?>