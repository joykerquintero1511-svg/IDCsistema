<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Capturamos los datos limpios del formulario
    $cedula   = trim($_POST['cedula']);
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);

    // Validación en el servidor
    if (empty($cedula) || empty($nombre) || empty($apellido)) {
        die("Por favor, rellene todos los campos obligatorios.");
    }

    try {
        // SQL limpio, directo y seguro contra Inyección SQL
        $sql = "INSERT INTO profesores (cedula, nombre, apellido, telefono) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula, $nombre, $apellido, $telefono]);

        // Redirección exitosa al listado principal
        header("Location: index.php?mensaje=registrado");
        exit();

    } catch (PDOException $e) {
        // Control de cédula duplicada
        if ($e->getCode() == 23000) {
            echo "<script>
                    alert('Error: Ya existe un profesor registrado con esta cédula.');
                    window.history.back();
                  </script>";
        } else {
            echo "Error en el sistema: " . $e->getMessage();
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>