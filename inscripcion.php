<?php 
session_start();
require_once('conexion.php');

$sql = "SELECT * FROM niveles";
$resultado = mysqli_query($conexion, $sql);



// Buscamos el último período académico asegurándonos de traer el ID y el estatus
$q_inscrip = mysqli_query($conexion, "SELECT id_periodo, inscripciones_abiertas FROM periodos_academicos ORDER BY id_periodo DESC LIMIT 1");
$r_inscrip = mysqli_fetch_assoc($q_inscrip);

if (!$r_inscrip || $r_inscrip['inscripciones_abiertas'] == 0) {
    // Si están cerradas, lo pateamos pal inicio
    die("
    <div style='background:#0b0f19; height:100vh; display:flex; justify-content:center; align-items:center; font-family:sans-serif;'>
        <div style='background:#1e293b; padding:40px; border-radius:10px; text-align:center; color:white; max-width:400px;'>
            <h2 style='color:#ef4444;'>Inscripciones Cerradas 🔒</h2>
            <p style='color:#94a3b8;'>El proceso de inscripción no está activo en este momento. Mantente atento a nuestras redes para próximos avisos.</p>
            <a href='index.php' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#3b82f6; color:white; text-decoration:none; border-radius:6px; font-weight:bold;'>Volver al Inicio</a>
        </div>
    </div>
    ");
}

$id_periodo_activo = $r_inscrip['id_periodo'];

?>

<!DOCTYPE html>
<html lang="es" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscripción EFB</title>
    <link rel="icon" type="image/png" href="images/EFB.png">

    <script>
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('js');
    </script>

    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body id="top">

    <main class="s-content">
        <section class="container" style="padding: 10rem 0; text-align: center;">
            <div class="row">
                <div class="column xl-12">

                    <div style="margin-bottom: 2rem;">
                        <img src="images/EFB.png" alt="Logo Escuela de Formación Bíblica" style="max-width: 160px; width: 100%; height: auto; display: inline-block;">
                    </div>

                    <h2 class="text-display-title" style="color: #ffffff; font-size: 4rem; margin-bottom: 1rem;">
                        Inscripción EFB
                    </h2>

                    <h5 style="color: rgba(255, 255, 255, 0.5); font-size: 1.8rem; margin-bottom: 4rem; text-transform: uppercase; letter-spacing: 1px;">
                        ¡Bienvenido! Dios te bendiga
                    </h5>

                    <div style="max-width: 600px; margin: 0 auto; background: rgba(255, 255, 255, 0.02); padding: 4rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08);">
                        
                        <form action="registrar_estudiantes.php" method="POST" style="text-align: left;">
                            
                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">
                                    Cédula / Documento de Identidad
                                </label>

                                <div style="display: flex; gap: 10px;">
                                    <select name="nacionalidad" id="nacionalidad" style="width: 80px; background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 0 1.5rem; border-radius: 6px; height: 5.4rem; font-weight: bold; cursor: pointer;">
                                        <option value="V" style="background: #142132; color: #ffffff;">V</option>
                                        <option value="E" style="background: #142132; color: #ffffff;">E</option>
                                    </select>

                                    <input class="u-fullwidth" type="text" name="cedula" id="cedula" placeholder="00000000" required maxlength="8" pattern="[0-9]{7,8}" title="La cédula debe contener mínimo 7 y máximo 8 números"
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
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Dirección</label>
                                <input class="u-fullwidth" type="text" id="direccion" name="direccion" placeholder="Ej. Calle Principal, Ciudad" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Fecha de Nacimiento</label>
                                <input class="u-fullwidth" type="date" id="fecha_nacimiento" name="fecha_nacimiento" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px; height: 5.4rem;">
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Género</label>

                                <select name="genero"  id="genero" required style="width: 100%; background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 0 1.5rem; border-radius: 6px; height: 5.4rem; cursor: pointer;">
                                    <option value="" disabled selected style="background: #142132; color: rgba(255,255,255,0.4);">Selecciona tu género</option>
                                    <option value="F" style="background: #142132; color: #fff;">F (Femenino)</option>
                                    <option value="M" style="background: #142132; color: #fff;">M (Masculino)</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Número de Teléfono</label>
                                <input class="u-fullwidth" type="text" id="telefono" name="telefono" placeholder="Ej. 04121234567" required maxlength="11" pattern="[0-9]{11}" title="El número de teléfono debe contener exactamente 11 números"
                                oninput="this.value = this.value.replace(/[^0-9]/g,'');"
                                style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Contacto de Emergencia</label>
                                <input class="u-fullwidth" type="text" id="contacto_emergencia" name="contacto_emergencia" placeholder="Número de contacto" required maxlength="11" pattern="[0-9]{11}" title="El número de teléfono debe contener exactamente 11 números"
                                oninput="this.value = this.value.replace(/[^0-9]/g,'');"
                                style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 6px;">
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="color: #ffffff; font-size: 1.4rem; display: block; margin-bottom: 0.8rem;">Nivel de Instrucción</label>

                                <select name="nivel_instruccion" id="nivel_instruccion" required style="width: 100%; background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 0 1.5rem; border-radius: 6px; height: 5.4rem; cursor: pointer;">
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
    
    <select name="id_nivel" required style="background: rgba(255,255,255,0.05); color: #fff; border-color: rgba(255,255,255,0.1); padding: 0 1.5rem; border-radius: 6px; height: 5.4rem; cursor: pointer; width: 100%;">
        
        <!-- Opción por defecto (igual que en tu otro menú) -->
        <option value="" disabled selected style="background: #142132; color: rgba(255,255,255,0.4);">
            Selecciona el nivel a cursar
        </option>
        
        <!-- Opciones generadas desde la Base de Datos -->
        <?php while($fila = mysqli_fetch_assoc($resultado)): ?>
            <option value="<?php echo $fila['id_nivel']; ?>" style="background: #142132; color: #fff;">
                <?php echo $fila['nivel_academico']; ?>
            </option>
        <?php endwhile; ?>
        
    </select>
</div>

                            <button type="submit" class="btn btn--primary u-fullwidth" style="font-size: 1.6rem; letter-spacing: 2px; text-transform: uppercase; height: 5.5rem; line-height: 5.5rem;">
                                Registrarse
                            </button>

                        </form>

                    </div>

                    <br><br>

                    <?php if (isset($_GET['origen']) && $_GET['origen'] === 'admin') { ?>

                    <a href="admin/estudiantes/listar.php"
                    style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 1.5rem; transition: color 0.3s;"onmouseover="this.style.color='#fff'"onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        ← Volver a la lista de estudiantes
                    </a>

                    <?php } else { ?>

                    <a href="index.php"
                    style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 1.5rem; transition: color 0.3s;"onmouseover="this.style.color='#fff'"onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        ← Volver a la página principal
                    </a>

                <?php } ?>

                </div>
            </div>
        </section>
    </main>

    <script>
    document.getElementById('cedula').addEventListener('blur', function() {
        let cedula = this.value;
        
        if (cedula.length >= 6) { // Solo buscar si escribió una cédula válida
            let formData = new FormData();
            formData.append('cedula', cedula);

            fetch('buscar_cedula.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    // Rellenar los campos automáticamente
                    if(data.nacionalidad) document.getElementById('nacionalidad').value = data.nacionalidad;
                    document.getElementById('nombre').value = data.nombre;
                    document.getElementById('apellidos').value = data.apellido;
                    document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento;
                    if(data.genero) document.getElementById('genero').value = data.genero;
                    document.getElementById('telefono').value = data.telefono;
                    document.getElementById('contacto_emergencia').value = data.contacto_emergencia;
                    document.getElementById('direccion').value = data.direccion;
                    
                    if(data.email) document.getElementById('email').value = data.email;
                    if(data.nivel_instruccion) document.getElementById('nivel_instruccion').value = data.nivel_instruccion;

                    // Efecto visual: Ponemos los campos con un borde azul para que note el autorrelleno
                    let campos = ['nombre', 'apellidos', 'fecha_nacimiento', 'telefono', 'contacto_emergencia', 'direccion', 'email'];
                    campos.forEach(id => {
                        document.getElementById(id).style.borderColor = '#3b82f6';
                        document.getElementById(id).style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
                    });
                }
            })
            .catch(error => console.error('Error en la búsqueda:', error));
        }
    });
</script>

</body>
</html>