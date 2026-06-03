<?php
include("conexion.php"); // Asegúrate de tener esto al inicio
$query_niveles = "SELECT id_nivel, nivel_academico FROM niveles"; // Ajusta 'nivel_academico' al nombre real de tu columna
$result_niveles = mysqli_query($conexion, $query_niveles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="icon" type="image/png" href="images/EFB.png">

    <style>
        /* Mantenemos tus estilos base exactos */
        body { background-color: #0c0c0c; font-family: sans-serif; }
        .s-content { padding: 5rem 0; }
        .main-card { max-width: 500px; margin: 0 auto; background: #111111; padding: 4rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05); }
        
        /* Forzamos a que tanto inputs como el select y el botón respeten el ancho interno */
        .u-fullwidth { width: 100%; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; font-size: 1.1rem; outline: none; box-sizing: border-box; }
        
        /* El botón con tu color azul original pero contenido */
        .btn-primary { background-color: #245285; border: none; cursor: pointer; font-weight: bold; height: 4.5rem; line-height: 4.5rem; padding: 0; }
        .btn-primary:hover { background-color: #1c426d; }

        .form-group { 
            margin-bottom: 20px; 
            display: flex; 
            flex-direction: column; 
            gap: 8px; 
        }
        
        .form-group label { 
            font-size: 0.85rem; 
            color: #888888; 
            font-weight: 500; 
        }
        
        .form-control { 
            background-color: #161b22; 
            border: 1px solid #30363d; 
            border-radius: 6px; 
            padding: 11px 14px; 
            color: #ffffff; 
            font-size: 0.95rem; 
            transition: border-color 0.2s; 
            width: 100%; 
        }
        
        .form-control:focus { 
            outline: none; 
            border-color: #58a6ff; 
        }
        
        select.form-control { 
            cursor: pointer; 
            color-scheme: dark; 
        }
        
        textarea.form-control { 
            resize: vertical; 
            min-height: 110px; 
        }

    </style>
</head>
<body>

    <main class="s-content">
        <div class="main-card">
            
            <div style="text-align: center; margin-bottom: 3rem;">
                <img src="images/EFB.png" alt="Logo EFB" style="max-width: 100px; height: auto; margin-bottom: 1.5rem;">
                <h2 style="color: #fff; margin: 0; font-size: 2rem;">Registro de Personal</h2>
                <p style="color: #666; margin-top: 0.5rem; font-size: 1.1rem;">Profesores y Administradores</p>
            </div>

            <form action="procesar_registro_usuarios.php" method="POST">
                
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input class="u-fullwidth" type="text" name="nombre" required placeholder="Ej. Juan Pérez">
                </div>

                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input class="u-fullwidth" type="email" name="email" required placeholder="correo@ejemplo.com">
                </div>


                <div class="form-group">
                    <label>Contraseña de Acceso</label>
                    <input class="u-fullwidth" type="password" name="password" required placeholder="••••••••">
                </div>


                <div class="form-group">
                    <label>Tipo de Cuenta</label>
                    <select name="rol" id="selector-rol" class="u-fullwidth" onchange="evaluarRol()" required style="height: 4.5rem;">
                        <option value="estudiante" style="background: #111; color: #fff;">Estudiante (Acceso a clases y notas)</option>
                        <option value="profesor" style="background: #111; color: #fff;">Profesor (Subir notas y evaluar)</option>
                        <option value="admin" style="background: #111; color: #fff;">Administrador (Control Total)</option>
                    </select>
                </div>


                <div id="bloque-codigo" class="form-group" style="display: none; margin-bottom: 3.5rem;">
                    <label style="font-weight: bold;">Clave de Autorización Especial</label>
                    <input class="u-fullwidth" type="password" name="codigo_autorizacion" id="codigo_autorizacion" placeholder="Introduce la clave de la escuela">
                </div>

                <button type="submit" class="u-fullwidth btn-primary">Registrar Cuenta</button>

                <div style="text-align: center; margin-top: 2.5rem;">
                    <a href="index.php" style="color: rgba(255, 255, 255, 0.4); font-size: 1.3rem; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255, 255, 255, 0.4)'">
                        Volver a la Página Principal
                    </a>
                </div>

            </form>
        </div>
    </main>

    <script>
        function evaluarRol() {
            var rolSeleccionado = document.getElementById("selector-rol").value;
            var bloqueCodigo = document.getElementById("bloque-codigo");
            var inputCodigo = document.getElementById("codigo_autorizacion");

            if (rolSeleccionado === "profesor" || rolSeleccionado === "admin") {
                bloqueCodigo.style.display = "block";
                inputCodigo.required = true;
            } else {
                bloqueCodigo.style.display = "none";
                inputCodigo.required = false;
                inputCodigo.value = "";
            }
        }
    </script>
</body>
</html>