<?php
include("Niveles.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inscripción EFB</title>
        <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        
        <div class="background-titulo">
            <div class="header-container">
            
            <img src="imagenes/EFB.png" alt="Logo Escuela de Formación Bíblica" class="header-logo">
    <h1 class="header-title">Inscripción EFB</h1>
        </div>
</div>
        <h5 class="subtitulos">¡Bienvenido! Dios te bendiga</h5>

        <div class="form-container">
            <form  action="registrar_usuarios.php" method="POST">
                
                <input class="formulario-input" type="text" id="nombre" name="nombre" placeholder="Nombres" required> <br>

                <input class="formulario-input" type="text" id="apellidos" name="apellidos" placeholder="Apellidos" required> <br>

                
                <input class="formulario-input" type="email" id="email" name="email" placeholder="Correo Electrónico" required><br>

                
                <input class="formulario-input" type="text" id="telefono" name="telefono" placeholder="Número de Teléfono" required><br>
                <input class="formulario-input" type="password" name="contraseña" placeholder="Contraseña (mínimo 6 caracteres)" required>
                    <small style="color: gray;">La contraseña debe tener al menos 6 caracteres</small><br><br>
                
                <select class="inscripciones-input" name="nivel_academico">
                <option value="">Seleccione el nivel</option>
                <?php foreach ($niveles as $codigo => $nombre): ?>
                <option value="<?php echo $codigo; ?>"><?php echo $nombre; ?></option>
                <?php endforeach; ?>

            </select>

                <button class="boton-input" type="submit">Enviar Inscripción</button>

                <a href="EFB.php" class="boton-volver">Volver</a>
            </form>
        </div>

    </body>
</html>