<?php
session_start();
require_once('../conexion.php'); 

$mensaje = "";
$tipo_alerta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST"){ 
    $nombre_nivel = trim($_POST['nombre_nivel']);
    $codigo_nivel = trim($_POST['codigo_nivel']);
    $nivel_academico = trim($_POST['nivel_academico']);
    $periodo_academico = date("Y"); 

    if(!empty($nombre_nivel) && !empty($codigo_nivel) && !empty($nivel_academico)){
        $nombre_seguro = mysqli_real_escape_string($conexion, $nombre_nivel);
        $codigo_seguro = mysqli_real_escape_string($conexion, $codigo_nivel);
        $academico_seguro = mysqli_real_escape_string($conexion, $nivel_academico);

        $sql = "INSERT INTO niveles (nivel_academico, codigo_nivel, periodo_academico) VALUES ('$academico_seguro', '$codigo_seguro', '$periodo_academico')"; 
        
        if (mysqli_query($conexion, $sql)) { 
            $mensaje = "¡Nivel guardado con éxito!";
            $tipo_alerta = "success";
        } else {
            $mensaje = "Error al guardar: " . mysqli_error($conexion);
            $tipo_alerta = "error";
        }
    } else {
        $mensaje = "Por favor, completa todos los campos.";
        $tipo_alerta = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Niveles - EFB</title>
    <link rel="icon" type="image/png" href="../IDCsistema/images/EFB.png">
    <link rel="stylesheet" href="../css/mystyle.css">
    <link rel="stylesheet" href="/IDCsistema/css/movil.css">
</head>
<body>

    <!-- Menú lateral unificado -->
    <?php include 'sidebaradmin.php'; ?>

    <!-- Contenedor Principal con la clase que respeta el sidebar -->
    <main class="main-content">
        
        <div class="nivel-wrapper">
            <div class="nivel-card">
                
                <div class="nivel-header">
                    <h1>Registro de Niveles</h1>
                    <p>Agrega un nuevo nivel académico al sistema</p>
                </div>

                <?php if(!empty($mensaje)): ?>
                    <div style="padding: 0.8rem; margin-bottom: 1.5rem; border-radius: 4px; font-size: 0.9rem; text-align: center; background: <?php echo ($tipo_alerta == 'success') ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)'; ?>; color: <?php echo ($tipo_alerta == 'success') ? '#4ade80' : '#f87171'; ?>; border: 1px solid <?php echo ($tipo_alerta == 'success') ? 'rgba(34,197,94,0.3)' : 'rgba(239,68,68,0.3)'; ?>;">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <form action="crear_nivel.php" method="POST">
                    
                    <div class="nivel-form-group">
                        <label>Nombre del Nivel</label>
                        <input class="nivel-input" type="text" name="nombre_nivel" placeholder="Ej. Escuela para Bautismo" required>
                    </div>

                    <div class="nivel-form-group">
                        <label>Código del Nivel</label>
                        <input class="nivel-input" type="text" name="codigo_nivel" placeholder="Ej. 1A" required>
                    </div>

                    <div class="nivel-form-group">
                        <label>Nivel Académico</label>
                        <input class="nivel-input" type="text" name="nivel_academico" placeholder="Ej. 1A-Escuela para Bautismo" required>
                    </div>

                    <button type="submit" class="nivel-btn-submit">Guardar Nivel</button>
                    
                </form>

            </div>
        </div>

    </main>

    <?php include '../script-seguridad.php'; ?>
</body>
</html>