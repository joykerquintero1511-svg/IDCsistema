
<?php 
   # Aca Conectaremos la BD con (conexion.php)
require_once('conexion.php');

# Consulta para TRAER todos los NIVELES DESDE LA TABLA "NIVELES"

$sql = "SELECT * FROM niveles " ; // el * El comodín que significa "todas las columnas".
$resultado = mysqli_query($conexion , $sql); // Ejecuta la consulta en la base de datos y guarda el resultado en $resultado.
?>

<!DOCTYPE html>
<html lang="en" class="no-js" >
<head>

    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscripción EFB</title>
    <link rel="icon" type="image/png" href="images/EFB.png">


    <script>
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('js');
    </script>

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/styles.css">

    <!-- favicons
    ================================================== -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">

</head>

    <body id="top"><body id="top">

    <main class="s-content">
        <section class="container" style="padding: 10rem 0; text-align: center;">
            <div class="row">
                <div class="column xl-12">

                    <div style="margin-bottom: 2rem;"><img src="images/EFB.png" alt="Logo Escuela de Formación Bíblica" style="max-width: 160px; width: 100%; height: auto; display: inline-block;"></div>
                    
                    <h2 class="text-display-title" style="color: #ffffff; font-size: 4rem; margin-bottom: 1rem;">
                        Inscripción EFB
                    </h2>
                    <h5 style="color: rgba(255, 255, 255, 0.5); font-size: 1.8rem; margin-bottom: 4rem; text-transform: uppercase; letter-spacing: 1px;">
                        ¡Bienvenido! Dios te bendiga
                    </h5>

                    <div style="max-width: 600px; margin: 0 auto; background: rgba(255, 255, 255, 0.02); padding: 4rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08);">
                        
                        <form action="registrar_estudiantes.php" method="POST" style="text-align: left;">
                            
                        <div style="margin-bottom: 2rem;">
                            <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Cédula / Documento de Identidad</label>
    
                        <div style="display: flex; gap: 10px;">
        
                            <select name="nacionalidad" style="width: 80px; background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 0 1.5rem; border-radius: 6px; height: 5.4rem; font-weight: bold; cursor: pointer;">
                            <option value="V" style="background: #142132; color: #ffffff;">V</option>
                            <option value="E" style="background: #142132; color: #ffffff;">E</option>
                            </select>
        
                            <input class="u-fullwidth" type="text" name="cedula" placeholder="00000000" required 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                            style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px; flex: 1; height: 5.4rem; margin-bottom: 0;">
               
                    </div>
                        </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Nombres</label>
                                <input class="u-fullwidth" type="text" id="nombre" name="nombre" placeholder="Ej. Joyker Alejandro" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Apellidos</label>
                                <input class="u-fullwidth" type="text" id="apellidos" name="apellidos" placeholder="Ej. Quintero" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Correo Electrónico</label>
                                <input class="u-fullwidth" type="email" id="email" name="email" placeholder="nombre@correo.com" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div> 

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Dirección </label>
                                <input class="u-fullwidth" type="text" id="direccion" name="direccion" placeholder="Ej. Calle Principal, Ciudad" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div> 



                    

                        <div style="margin-bottom: 2rem;">
                        <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Fecha de Nacimiento</label>
                        <input class="u-fullwidth" type="date" id="fecha_nacimiento" name="fecha_nacimiento" required 
                        style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px; height: 5.4rem;">
                        </div>


                        <div style="margin-bottom: 2rem;">
                            <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Género</label>
    
                            <select name="genero" required style="width: 100%; background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 0 1.5rem; border-radius: 6px; height: 5.4rem; cursor: pointer;">
                            <option value="" disabled selected style="background: #142132; color: rgba(255,255,255,0.4);">Selecciona tu género</option>
                            <option value="F" style="background: #142132; color: #fff;">F (Femenino)</option>
                            <option value="M" style="background: #142132; color: #fff;">M (Masculino)</option>
                            </select>
                        </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Número de Teléfono</label>
                                <input class="u-fullwidth" type="text" id="telefono" name="telefono" placeholder="Ej. 04121234567" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Contacto de Emergencia</label>
                                <input class="u-fullwidth" type="text" id="contacto_emergencia" name="contacto_emergencia" placeholder="Número de contacto" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>

                                <div style="margin-bottom: 2rem;">
                            <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Nivel de Instrucción</label>
    
                            <select name="nivel_instruccion" required style="width: 100%; background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 0 1.5rem; border-radius: 6px; height: 5.4rem; cursor: pointer;">
                            <option value="" disabled selected style="background: #142132; color: rgba(255,255,255,0.4);">Selecciona tu nivel de estudio</option>
                            <option value="Primaria" style="background: #142132; color: #fff;">Educación Primaria</option>
                            <option value="Bachillerato" style="background: #142132; color: #fff;">Educación Media / Bachillerato</option>
                            <option value="Técnico Medio" style="background: #142132; color: #fff;">Técnico Medio</option>
                            <option value="TSU" style="background: #142132; color: #fff;">Técnico Superior Universitario (TSU)</option>
                            <option value="Universitario" style="background: #142132; color: #fff;">Universitario / Licenciatura / Ingeniería</option>
                            <option value="Postgrado" style="background: #142132; color: #fff;">Postgrado / Maestría / Doctorado</option>
                            </select>
                        </div>

                            <div style="margin-bottom: 2rem;">
                            
                            <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Nivel a Cursar</label>
                            <select name="nivel_academico" style="background: rgba(255,255,255,0.05); color: #fff; border-color: #142132; padding: 1.5rem; border-radius: 6px; width: 100%;">
                            
                            <?php # cambio aca para q se vea mas dinamico
                                while($fila = mysqli_fetch_assoc($resultado)): ?>
                                 <option value="<?php echo $fila['nivel_academico'];?>">
                                    <?php echo $fila ['nivel_academico']; ?>
                                 </option>

                                    <?php endwhile;?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn--primary u-fullwidth" style="font-size: 1.6rem; letter-spacing: 2px; text-transform: uppercase; height: 5.5rem; line-height: 5.5rem;">
                                Registrarse
                            </button>

                        </form>

                    </div>

                    <br><br>
                    <a href="index.php" style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 1.5rem; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        ← Volver a la página principal
                    </a>

                </div>
            </div>
        </section>
    </main>

</body>
</html>
