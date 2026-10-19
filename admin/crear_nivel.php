<?php
session_start();
require_once('../conexion.php'); 

$mensaje = "";
$tipo_alerta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST"){ 
    // Convertimos a mayúsculas y eliminamos cualquier carácter que no sea A-Z o 0-9
    $codigo_nivel = strtoupper(trim($_POST['codigo_nivel']));
    $codigo_nivel = preg_replace('/[^A-Z0-9]/', '', $codigo_nivel);
    $nombre_nivel = trim($_POST['nombre_nivel']);
    
    // Concatenación automática en PHP por seguridad
    $nivel_academico = !empty($_POST['nivel_academico']) 
        ? trim($_POST['nivel_academico']) 
        : $codigo_nivel . ' - ' . $nombre_nivel;
        
    $periodo_academico = date("Y"); 

    if(!empty($nombre_nivel) && !empty($codigo_nivel)){
        $nombre_seguro = mysqli_real_escape_string($conexion, $nombre_nivel);
        $codigo_seguro = mysqli_real_escape_string($conexion, $codigo_nivel);
        $academico_seguro = mysqli_real_escape_string($conexion, $nivel_academico);

        // Guardamos en la base de datos
        $sql = "INSERT INTO niveles (nivel_academico, codigo_nivel, periodo_academico) 
                VALUES ('$academico_seguro', '$codigo_seguro', '$periodo_academico')"; 
        
        if (mysqli_query($conexion, $sql)) { 
            $mensaje = "¡Nivel guardado con éxito!";
            $tipo_alerta = "success";
        } else {
            $mensaje = "Error al guardar: " . mysqli_error($conexion);
            $tipo_alerta = "error";
        }
    } else {
        $mensaje = "Por favor, completa los campos requeridos.";
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

    <!-- Contenedor Principal -->
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
                        <label for="codigo_nivel">Código del Nivel</label>
                        <input class="nivel-input" type="text" id="codigo_nivel" name="codigo_nivel" placeholder="Ej. 1A" required autocomplete="off">
                    </div>

                    <div class="nivel-form-group">
                        <label for="nombre_nivel">Nombre del Nivel</label>
                        <input class="nivel-input" type="text" id="nombre_nivel" name="nombre_nivel" placeholder="Ej. Escuela para Bautismo" required autocomplete="off">
                    </div>

                    <div class="nivel-form-group">
                        <label for="nivel_academico">Nivel Académico (Automático)</label>
                        <input class="nivel-input" type="text" id="nivel_academico" name="nivel_academico" placeholder="Ej. 1A - Escuela para Bautismo" readonly style="background-color: rgba(255, 255, 255, 0.05); color: #94a3b8; cursor: not-allowed;">
                    </div>
<div style="display: flex; justify-content: center; gap: 12px; margin-top: 20px;">
    <button type="submit" class="nivel-btn-submit">Guardar Nivel</button>
    <a href="gestion_niveles.php" class="nivel-btn-cancel">Cancelar</a>
</div>
                    
                </form>

            </div>
        </div>

    </main>

    <?php include '../script-seguridad.php'; ?>

   <!-- JS CON RESTRICCIONES Y CONCATENACIÓN AUTOMÁTICA -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCodigo = document.getElementById('codigo_nivel');
    const inputNombre = document.getElementById('nombre_nivel');
    const inputAcademico = document.getElementById('nivel_academico');

    // 1. RESTRICCIÓN EN TIEMPO REAL PARA EL CÓDIGO DEL NIVEL
    inputCodigo.addEventListener('input', function() {
        // Convierte todo a mayúsculas
        let valor = this.value.toUpperCase();

        // Elimina todo lo que NO sea letra (A-Z) o número (0-9)
        // Se descartan automáticamente: !, ?, -, _, ., espacios y otros símbolos
        this.value = valor.replace(/[^A-Z0-9]/g, '');

        // Actualiza el campo concatenado
        concatenarNivel();
    });

    // 2. EVENTO PARA EL NOMBRE DEL NIVEL
    inputNombre.addEventListener('input', concatenarNivel);

    // 3. FUNCIÓN DE CONCATENACIÓN
    function concatenarNivel() {
        const codigo = inputCodigo.value.trim();
        const nombre = inputNombre.value.trim();

        if (codigo !== '' && nombre !== '') {
            inputAcademico.value = codigo + ' - ' + nombre;
        } else if (codigo !== '') {
            inputAcademico.value = codigo;
        } else if (nombre !== '') {
            inputAcademico.value = nombre;
        } else {
            inputAcademico.value = '';
        }
    }
});
</script>
</body>
</html>