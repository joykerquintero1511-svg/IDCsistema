<?php
session_start();
require_once('../conexion.php');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_nivel = intval($_GET['id']);

    // Eliminamos el nivel mediante una consulta preparada
    $sql = "DELETE FROM niveles WHERE id_nivel = ?";
    $stmt = mysqli_prepare($conexion, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_nivel);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: gestion_niveles.php?msg=eliminado");
        } else {
            header("Location: gestion_niveles.php?error=no_se_pudo_eliminar");
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: gestion_niveles.php?error=error_sql");
    }
} else {
    header("Location: gestion_niveles.php");
}
exit();
?>