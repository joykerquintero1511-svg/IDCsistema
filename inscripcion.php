<?php 
    $niveles = [
    "1A" => "1A-Escuela para Bautismo",
    "1B" => "1B-Iglesia de Jesucristo",
    "1C" => "1C-Autoridad y Santidad",
    "2A" => "2A-Como Evangelizar",
    "2B" => "2B-Formando Líderes con Proposito",
    "2C" => "2C-Formando Carácter",
    "3A" => "3A-Liderazgo",
    "3B" => "3B-Consolidación y Consejería",
    "3C" => "3C-Naturaleza de la Biblia",
    "4A" => "4A-Historia de la Iglesia"
];
?>

<!DOCTYPE html>
<html lang="en" class="no-js" >
<head>

    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EFB</title>

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
                        
                        <form action="registrar_usuarios.php" method="POST" style="text-align: left;">
                            
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
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Cédula</label>
                                <input class="u-fullwidth" type="text" name="cedula" placeholder="V-00000000" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Número de Teléfono</label>
                                <input class="u-fullwidth" type="text" id="telefono" name="telefono" placeholder="Ej. 04121234567" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>

                            <div style="margin-bottom: 4rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Contraseña (Mínimo 6 caracteres)</label>
                                <input class="u-fullwidth" type="password" id="contraseña" name="contraseña" placeholder="••••••••" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>
                            <div style="margin-bottom: 2rem;">
                            
                            <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Nivel Académico</label>
                             <select name="nivel_academico" style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px; width: 100%;">
                             <?php foreach ($niveles as $codigo => $nombre): ?>
                              <option value="<?php echo $codigo; ?>"><?php echo $nombre; ?></option>
                             <?php endforeach; ?>
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
