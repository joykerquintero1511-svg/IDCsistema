 <?php
    require_once '../conexion.php';
    session_start();

 // Permitir el acceso unicamente al administrador

  if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
     header("Location: ../login.php");
     exit();

  }

  // Verificar que se recibió el id de la inscripción

if (!isset($_GET['id_inscripcion'])) {
    die("No se recibió la inscripción.");
}

$id_inscripcion = intval($_GET['id_inscripcion']);

 // Buscar los datos de la inscripción, el estudiante y el nivel

    $sql_certificado = "
    SELECT 
    estudiantes.id_estudiante,
    personas.nombre,
    personas.apellido,
    niveles.nivel_academico
    FROM inscripciones
    INNER JOIN estudiantes
    ON inscripciones.id_estudiante = estudiantes.id_estudiante
    INNER JOIN personas
    ON estudiantes.id_persona = personas.id_persona
    INNER JOIN niveles
    ON inscripciones.id_nivel = niveles.id_nivel
    WHERE inscripciones.id_inscripcion = '$id_inscripcion'
    ";

    $resultado_certificado = mysqli_query($conexion, $sql_certificado);

    $datos_certificado = mysqli_fetch_assoc($resultado_certificado);

    // Verificar que la inscripción exista

    if (!$datos_certificado) {
        die("No se encontraron datos para este certificado.");
    }
 
    // Obtener la fecha actual

        $dia = date('d');
        $mes = date('m');
        $anio = date('Y');
    
    $meses = [
        '01' => 'enero',
        '02' => 'febrero',
        '03' => 'marzo',
        '04' => 'abril',
        '05' => 'mayo',
        '06' => 'junio',
        '07' => 'julio',
        '08' => 'agosto',
        '09' => 'septiembre',
        '10' => 'octubre',
        '11' => 'noviembre',
        '12' => 'diciembre'
];

$mes = $meses[$mes];

 ?>
       
 <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Vista Previa - Certificado EFB</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                
                body {
                    font-family: 'Times New Roman', Georgia, serif;
                    background-color: #e8e8e8;
                    min-height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    padding: 40px;
                }
                
                .certificado {
                    position: relative;
                    background: white;
                    width: 297mm;
                    min-height: 210mm;
                    border: 10px solid #1c5889;
                    box-shadow: inset 0 0 0 4px white, inset 0 0 0 6px #1c5889;
                    padding: 28mm 25mm;
                    text-align: center;
                    overflow: hidden;
                }
                .certificado::before { 
                    content: "";
                    position: absolute;
                    inset: 0;
                    background-image: url('images/marca_logo3.png');
                    background-repeat: no-repeat;
                    background-position: center 35%;
                    background-size: 70%;
                    opacity: 0.09;
                    z-index: 0;
                }
                .certificado > * {
                    position: relative;
                    z-index: 1;
                }
                 @page {
                    size: A4 landscape;
                    margin: 0;
                }

                .logo-certificado {
                    width: 180px;
                    height: auto;
                    margin-bottom: 10px;
                }

                .iglesia {
                    border-bottom: 2px solid #1c5889;
                    padding-bottom: 15px;
                    margin-bottom: 25px;
                }      
                
                .iglesia h1 {
                    font-size: 28px;
                    color: #2c3e4e;
                    letter-spacing: 2px;
                }
                
                .iglesia h3 {
                    font-size: 16px;
                    color: #5d6d7e;
                    font-weight: normal;
                    margin-top: 5px;
                }

                .subtitulo {
                    font-size: 32px;
                    letter-spacing: 6px;
                    color: #2c3e4e;
                    margin: 25px 0 20px;
                    font-weight: bold;
                }
                
                .certifica {
                    font-size: 28px;
                    margin: 30px 0 10px;
                }
                .descripcion {
                    font-size: 20px;
                    margin: 30px 0 10px;
                }

                .nombre {
                    font-size: 40px;
                    font-weight: bold;
                    text-transform: uppercase;
                    margin: 20px auto;
                    color: #2c3e4e;
                    border-bottom: 1px solid #123B5D;
                    padding-bottom: 12px;
                    display: block;
                    width: 85%;
                }

                .nombre-largo {
                    font-size: 30px;
                }
                
                .nivel {
                    padding: 12px 20px;
                    font-size: 22px;
                    margin: 20px auto;
                    color: #2c3e4e;
                    letter-spacing: 1px;
                }
                
                .pie-certificado {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                    width: 75%;
                    margin: 30px auto 0;
                }

                .fecha {
                    margin: 0;
                    font-size: 17px;
                    color: #2c3e4e;
                    text-align: left;
                }

                .firma {
                    margin: 0;
                    font-size: 16px;
                    color: #2c3e4e;
                    text-align: center;
                }

                .espacio-firma {
                    height: 55px;
                }

                .firma-linea {
                    width: 220px;
                    border-top: 1px solid #2c3e4e;
                    margin: 0 auto 8px auto;
                }
                
                .btn-imprimir {
                    text-align: center;
                    margin-top: 30px;
                }
                
                .btn-imprimir button {
                    background: #2c3e4e;
                    color: white;
                    border: none;
                    padding: 10px 25px;
                    font-size: 14px;
                    border-radius: 4px;
                    cursor: pointer;
                }
                
                .btn-imprimir button:hover {
                    background: #c9a03d;
                }
                
                /* Estilos que se aplican al imprimir o guardar el certificado como PDF */
                
                @media print { 

                body {
                    background: white;
                    padding: 0;
                    margin: 0;
                    min-height: auto;
                    display: block;
                }

                .btn-imprimir {
                    display: none;
                }

                .certificado {
                    width: 297mm;
                    height: 210mm;
                    min-height: 0;
                    padding: 15mm 25mm 25mm 25mm;
                    border: 10px solid #1c5889;
                    box-shadow: inset 0 0 0 4px white, inset 0 0 0 6px #1c5889;
                    page-break-inside: avoid;
                }
            }
                
             </style>
        </head>
        <body>
            <div>
                <div class="certificado">
                    <div class="iglesia">
                 <img src="images/marca_agua_logo.png" alt="Logo Iglesia Dios en Casa" class="logo-certificado">
                        <h1>IGLESIA DIOS EN CASA</h1>
                        <h3>Escuela de Formación Bíblica</h3>
                    </div>
                    
                    <div class="subtitulo"> 
                       C E R T I F I C A D O
                    </div>
                    
                    <div class="certifica">
                        <p>Se certifica que</p>
                    </div>
                    
                    <?php
                    $nombre_completo = mb_strtoupper($datos_certificado['nombre'] . ' ' . $datos_certificado['apellido'],'UTF-8');

                    $clase_nombre = 'nombre';

                    if (mb_strlen($nombre_completo, 'UTF-8') > 28) {
                        $clase_nombre = 'nombre nombre-largo';
                    }
                    ?>

                    <div class="<?php echo $clase_nombre; ?>">
                        <?php echo htmlspecialchars($nombre_completo); ?>
                    </div>
                                    
                    <div class="descripcion">
                        <p>Ha cursado y aprobado satisfactoriamente el nivel de formación</p>
                    </div>
                    
                 <div class="nivel">
                        <strong>
                        <?php echo htmlspecialchars($datos_certificado['nivel_academico']); ?>
                        </strong>
                </div>
                    
                    <div class="pie-certificado">

                <div class="fecha">
                    Caracas, <?php echo $dia . ' de ' . $mes . ' de ' . $anio; ?>
                </div>

                    <div class="firma">
                        <div class="espacio-firma"></div>
                        <div class="firma-linea"></div>
                        <p>Coordinación Académica</p>
                    </div>
                </div>
                     <div class="btn-imprimir">
            <button onclick="window.print();">🖨️ IMPRIMIR / GUARDAR COMO PDF</button>
         <a href="../admin/estudiantes/certificados_estudiante.php?id_estudiante=<?php echo $datos_certificado['id_estudiante']; ?>">
        ← Volver
    </a>
     </div>
 </div>
 </body>
  </html>