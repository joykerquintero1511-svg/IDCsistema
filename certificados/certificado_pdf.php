    <?php
    // Cargar Dompdf y la conexión del sistema.
    require_once '../vendor/autoload.php';
    require_once '../conexion.php';

    use Dompdf\Dompdf;

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

    // Consultar el nombre y cargo del firmante.
    $sql_firmante = "SELECT nombre_firmante, cargo_firmante FROM configuracion_certificados WHERE id_configuracion = 1";
    $resultado_firmante = mysqli_query($conexion, $sql_firmante);
    $firmante = mysqli_fetch_assoc($resultado_firmante);
    if (!$firmante) {
        die("No se encontró la configuración del firmante.");
    }

    // Fecha de Venezuela.
    date_default_timezone_set('America/Caracas');
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

    // Convertir el logo a una imagen incorporada: Dompdf no depende de rutas del navegador.
    $logo_src = '';
    $logo_archivo = __DIR__ . '/images/marca_agua_logo.png';
    if (is_file($logo_archivo)) {
        $logo_src = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_archivo));
    }
    $marca_src = '';
    $marca_archivo = __DIR__ . '/images/marca_logo3.png';
    if (is_file($marca_archivo)) {
        $marca_src = 'data:image/png;base64,' . base64_encode(file_get_contents($marca_archivo));
    }
    $nombre_completo = mb_strtoupper($datos_certificado['nombre'] . ' ' . $datos_certificado['apellido'], 'UTF-8');
    $clase_nombre = 'nombre';
    if (mb_strlen($nombre_completo, 'UTF-8') > 28) {
        $clase_nombre = 'nombre nombre-largo';
    }

    // Capturar HTML antes de convertirlo en PDF.
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Certificado EFB</title>
        <style>
            /* A4 horizontal: 297 x 210 mm. El certificado absoluto no crea saltos de página. */
            @page {
                size: A4 landscape;
                margin: 0;
            }

            html,
            body {
                margin: 0;
                padding: 0;
            }

            body {
                font-family: "Times New Roman", Georgia, serif;
                color: #2c3e4e;
            }

            .certificado {
                position: absolute;
                top: 8mm;
                left: 8mm;
                width: 281mm;
                height: 194mm;
                border: 7px solid #1c5889;
                box-sizing: border-box;
                background-color: white;
                overflow: hidden;
                text-align: center;
            }

            /* Todo el contenido se posiciona dentro de la misma hoja, sin desbordamientos. */
            .marca {
                position: absolute;
                top: 30mm;
                left: 58mm;
                width: 160mm;
                opacity: .12;
            }

            .encabezado {
                position: absolute;
                top: 5mm;
                left: 12mm;
                right: 12mm;
                height: 51mm;
                border-bottom: 2px solid #1c5889;
            }

            .logo {
                display: block;
                width: 32mm;
                height: 22mm;
                object-fit: contain;
                margin: 0 auto 1mm;

            }

            .encabezado h1 {
                margin: 0;
                font-size: 25px;
                letter-spacing: 2px;
            }

            .encabezado h3 {
                margin: 2mm 0 0;
                font-size: 19px;
                font-weight: normal;
            }

            .subtitulo {
                position: absolute;
                top: 62mm;
                left: 0;
                right: 0;
                font-size: 32px;
                font-weight: bold;
                color: #163f63;
            }


            .certifica {
                position: absolute;
                top: 82mm;
                left: 0;
                right: 0;
                font-size: 25px;
            }

            .nombre {
                position: absolute;
                top: 100mm;
                left: 12%;
                width: 76%;
                adding-bottom: 4mm;
                border-bottom: 1px solid #123b5d;
                font-size: 37px;
                font-weight: bold;
                color: #163f63;
            }

            .nombre-largo {
                font-size: 27px;
            }

            .descripcion {
                position: absolute;
                top: 124mm;
                left: 5%;
                width: 90%;
                font-size: 21px;
            }

            .nivel {
                position: absolute;
                top: 141mm;
                left: 5%;
                width: 90%;
                font-size: 25px;
                color: #163f63;
            }

            .fecha {
                position: absolute;
                left: 12%;
                bottom: 12mm;
                font-size: 16px;
            }

            .firma {
                position: absolute;
                right: 9%;
                bottom: 10mm;
                width: 70mm;
                font-size: 16px;
            }

            .firma-linea {
                border-top: 1px solid #2c3e4e;
                width: 55mm;
                margin: 0 auto 2mm;
            }

            .firma p {
                margin: 1mm 0;
            }

            /* Esquinas: trazos reales, no pseudo-elementos (mayor compatibilidad con Dompdf). */
            .esquina {
                position: absolute;
                width: 21mm;
                height: 21mm;
            }

            .esquina:before {
                content: "";
            }

            .sup-izq {
                top: 4mm;
                left: 4mm;
                border-top: 1px solid #c99b41;
                border-left: 1px solid #c99b41;
            }

            .sup-der {
                top: 4mm;
                right: 4mm;
                border-top: 1px solid #c99b41;
                border-right: 1px solid #c99b41;
            }

            .inf-izq {
                bottom: 4mm;
                left: 4mm;
                border-bottom: 1px solid #c99b41;
                border-left: 1px solid #c99b41;
            }

            .inf-der {
                bottom: 4mm;
                right: 4mm;
                border-bottom: 1px solid #c99b41;
                border-right: 1px solid #c99b41;
            }

            .esquina-interna {
                position: absolute;
                width: 16mm;
                height: 16mm;
            }

            .si {
                top: 6mm;
                left: 6mm;
                border-top: 1px solid #c99b41;
                border-left: 1px solid #c99b41;
            }

            .sd {
                top: 6mm;
                right: 6mm;
                border-top: 1px solid #c99b41;
                border-right: 1px solid #c99b41;
            }

            .ii {
                bottom: 6mm;
                left: 6mm;
                border-bottom: 1px solid #c99b41;
                border-left: 1px solid #c99b41;
            }

            .id {
                bottom: 6mm;
                right: 6mm;
                border-bottom: 1px solid #c99b41;
                border-right: 1px solid #c99b41;
            }
        </style>
    </head>

    <body>
        <div class="certificado">
            <?php if ($marca_src != '') { ?><img class="marca" src="<?php echo $marca_src; ?>" alt=""><?php } ?>
            <div class="esquina sup-izq"></div>
            <div class="esquina sup-der"></div>
            <div class="esquina inf-izq"></div>
            <div class="esquina inf-der"></div>
            <div class="esquina-interna si"></div>
            <div class="esquina-interna sd"></div>
            <div class="esquina-interna ii"></div>
            <div class="esquina-interna id"></div>
            <div class="encabezado">
                <?php if ($logo_src != '') { ?><img class="logo" src="<?php echo $logo_src; ?>" alt="Logo Iglesia Dios en Casa"><?php } ?>
                <h1>IGLESIA DIOS EN CASA</h1>
                <h3>Escuela de Formación Bíblica</h3>
            </div>
            <div class="subtitulo">C E R T I F I C A D O</div>

            <div class="certifica">Se certifica que</div>

            <div class="<?php echo $clase_nombre; ?>"><?php echo htmlspecialchars($nombre_completo); ?></div>

            <div class="descripcion">Ha cursado y aprobado satisfactoriamente el nivel de formación</div>

            <div class="nivel"><strong><?php echo htmlspecialchars($datos_certificado['nivel_academico']); ?></strong></div>

            <div class="fecha">Caracas, <?php echo $dia . ' de ' . $mes . ' de ' . $anio; ?></div>

            <div class="firma">

                <div class="firma-linea"></div>

                <?php if (trim($firmante['nombre_firmante']) != '') { ?>
                    <p><strong><?php echo htmlspecialchars(mb_convert_case($firmante['nombre_firmante'], MB_CASE_TITLE, 'UTF-8')); ?></strong></p>
                <?php } ?>
                <p><?php echo htmlspecialchars($firmante['cargo_firmante']); ?></p>
            </div>
        </div>
    </body>

    </html>
    <?php
    $html = ob_get_clean();
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('certificado.pdf', array('Attachment' => false));
    ?>