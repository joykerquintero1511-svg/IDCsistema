 <?php
    require_once '../conexion.php';
    session_start();

    // Permitir el acceso unicamente al administrador

    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
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
         * {
             margin: 0;
             padding: 0;
             box-sizing: border-box;
         }

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
             opacity: 0.18;
             z-index: 0;
         }

         .certificado>* {
             position: relative;
             z-index: 1;
         }



         @page {
             size: A4 landscape;
             margin: 0;
         }

         /* Esquinas doradas decorativas */
         .certificado>.esquina-dorada {
             position: absolute;
             width: 125px;
             height: 125px;
             z-index: 0;
             opacity: 0.65;
         }

         /* Esquina superior derecha */
         .esquina-superior {
             top: 22px;
             right: 22px;
             border-top: 1px solid #c99b41;
             border-right: 1px solid #c99b41;
         }

         /* Esquina inferior izquierda */
         .esquina-inferior {
             bottom: 22px;
             left: 22px;
             border-bottom: 1px solid #c99b41;
             border-left: 1px solid #c99b41;
         }

         /* Líneas internas de las esquinas doradas */
         .esquina-dorada::before,
         .esquina-dorada::after {
             content: "";
             position: absolute;
             border-color: #c99b41;
         }

         .esquina-superior::before {
             width: 95px;
             height: 95px;
             top: 8px;
             right: 8px;
             border-top: 1px solid #c99b41;
             border-right: 1px solid #c99b41;
         }

         .esquina-superior::after {
             width: 65px;
             height: 65px;
             top: 16px;
             right: 16px;
             border-top: 1px solid #c99b41;
             border-right: 1px solid #c99b41;
         }

         .esquina-inferior::before {
             width: 95px;
             height: 95px;
             bottom: 8px;
             left: 8px;
             border-bottom: 1px solid #c99b41;
             border-left: 1px solid #c99b41;
         }

         .esquina-inferior::after {
             width: 65px;
             height: 65px;
             bottom: 16px;
             left: 16px;
             border-bottom: 1px solid #c99b41;
             border-left: 1px solid #c99b41;
         }

         /* Esquina superior izquierda */
         .esquina-superior-izquierda {
             top: 22px;
             left: 22px;
             border-top: 1px solid #c99b41;
             border-left: 1px solid #c99b41;
         }

         .esquina-superior-izquierda::before {
             width: 95px;
             height: 95px;
             top: 8px;
             left: 8px;
             border-top: 1px solid #c99b41;
             border-left: 1px solid #c99b41;
         }

         .esquina-superior-izquierda::after {
             width: 65px;
             height: 65px;
             top: 16px;
             left: 16px;
             border-top: 1px solid #c99b41;
             border-left: 1px solid #c99b41;
         }

         /* Esquina inferior derecha */
         .esquina-inferior-derecha {
             bottom: 22px;
             right: 22px;
             border-bottom: 1px solid #c99b41;
             border-right: 1px solid #c99b41;
         }

         .esquina-inferior-derecha::before {
             width: 95px;
             height: 95px;
             bottom: 8px;
             right: 8px;
             border-bottom: 1px solid #c99b41;
             border-right: 1px solid #c99b41;
         }

         .esquina-inferior-derecha::after {
             width: 65px;
             height: 65px;
             bottom: 16px;
             right: 16px;
             border-bottom: 1px solid #c99b41;
             border-right: 1px solid #c99b41;
         }


         .logo-certificado {
             width: 180px;
             height: auto;
             margin-bottom: 10px;
         }

         .iglesia {
             border-bottom: 2px solid #1c5889;
             padding-bottom: 15px;
             margin-bottom: 18px;
         }

         .iglesia h1 {
             font-size: 28px;
             color: #2c3e4e;
             letter-spacing: 2px;
         }

         .iglesia h3 {
             font-size: 22px;
             color: #2c3e4e;
             font-weight: normal;
             margin-top: 9px;
         }

         .subtitulo {
             font-size: 34px;
             letter-spacing: 5px;
             margin: 20px 0 15px;
             font-weight: bold;
             color: #163f63;
         }

         .certifica {
             font-size: 30px;
             margin: 20px 0 8px;
         }

         .descripcion {
             font-size: 24px;
             margin: 25px 0 8px;
         }

         .nombre {
             font-size: 44px;
             font-weight: bold;
             text-transform: uppercase;
             margin: 15px auto;
             color: #163f63;
             border-bottom: 1px solid #123B5D;
             padding-bottom: 12px;
             display: block;
             width: 85%;
         }

         .nombre-largo {
             font-size: 30px;
         }

         .nivel {
             padding: 8px 20px;
             font-size: 28px;
             margin: 22px auto 32px;
             color: #163f63;
             letter-spacing: 1px;

         }

         .pie-certificado {
             display: flex;
             justify-content: space-between;
             align-items: flex-end;
             width: 78%;
             margin: 32px auto 0;
         }

         .fecha {
             margin: 0;
             font-size: 18px;
             color: #2c3e4e;
             text-align: left;
             position: relative;
             top: 4px;
         }

         .firma {
             margin: 0;
             font-size: 18px;
             color: #2c3e4e;
             text-align: center;
             position: relative;
             top: 7px;
         }


         .espacio-firma {
             height: 40px;
         }

         .firma-linea {

             width: 200px;
             border-top: 1px solid #2c3e4e;
             margin: 0 auto 7px auto;
         }

         .btn-imprimir {
             margin-top: 45px;
             display: flex;
             justify-content: center;
             align-items: center;
             gap: 12px;
         }

         .btn-imprimir button {
             background: #1c5889;
             color: white;
             border: none;
             padding: 11px 22px;
             font-size: 13px;
             font-family: Arial, sans-serif;
             border-radius: 5px;
             cursor: pointer;
             box-shadow: 0 2px 5px rgba(0, 0, 0, 0.18);
         }

         .btn-imprimir button:hover {
             background: #c9a03d;
         }

         /* Botón Volver */
         .btn-imprimir a {
             background: white;
             color: #1c5889;
             border: 1px solid #1c5889;
             padding: 10px 18px;
             font-size: 13px;
             font-family: Arial, sans-serif;
             text-decoration: none;
             border-radius: 5px;
         }

         /* Efecto al pasar el mouse por Volver */
         .btn-imprimir a:hover {
             background: #1c5889;
             color: white;
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

             <!-- Esquinas doradas decorativas -->
             <div class="esquina-dorada esquina-superior"></div>
             <div class="esquina-dorada esquina-superior-izquierda"></div>

             <div class="esquina-dorada esquina-inferior"></div>
             <div class="esquina-dorada esquina-inferior-derecha"></div>

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
                $nombre_completo = mb_strtoupper($datos_certificado['nombre'] . ' ' . $datos_certificado['apellido'], 'UTF-8');

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