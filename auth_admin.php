<?php 
 /*este archivo aun no funciona todavia queda acomodar ya q no cumple 
 la funcion de al cerrar sesion al darle a la flecha atras sigue volviendo a la sesion */

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}