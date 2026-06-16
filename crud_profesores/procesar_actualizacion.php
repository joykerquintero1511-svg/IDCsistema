<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_profesor = trim($_POST['id_profesor']);
    $cedula      = trim($_POST['cedula']);
    $nombre      = trim($_POST['nombre']);
    $apellido    = trim($_POST['apellido']);
    $telefono    = trim($_POST['telefono']);

    if (empty($id_profesor) || empty($cedula) || empty($nombre) || empty($apellido)) {
        die("Campos requeridos faltantes.");
    }

    try {
        // Sentencia UPDATE con marcadores
        $sql = "UPDATE profesores SET cedula = ?, nombre = ?, apellido = ?, telefono = ? WHERE id_profesor = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula, $nombre, $apellido, $telefono, $id_profesor]);

        // Redirección exitosa
        header("Location: index.php?mensaje=actualizado");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<script>
                    alert('Error: La cédula introducida ya pertenece a otro profesor.');
                    window.history.back();
                  </script>";
        } else {
            echo "Error al actualizar: " . $e->getMessage();
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>