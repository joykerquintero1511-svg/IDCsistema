    <?php

    require '../vendor/autoload.php';
    require_once '../conexion.php';

    session_start();

    if (
        !isset($_SESSION['rol']) ||
        ($_SESSION['rol'] != 'profesor' && $_SESSION['rol'] != 'admin')
    ) {
        header("Location: ../login.php");
        exit();
    }

    use Dompdf\Dompdf;

    $id_nivel = $_GET['id_nivel'];
    $evaluacion = $_GET['evaluacion'];

    $sql = "
SELECT
personas.nombre,
personas.apellido,
calificaciones.descripcion_nota_1,
calificaciones.descripcion_nota_2,
calificaciones.nota_1,
calificaciones.nota_2,
calificaciones.nota_final,
calificaciones.observacion

FROM inscripciones

INNER JOIN estudiantes
ON inscripciones.id_estudiante = estudiantes.id_estudiante

INNER JOIN personas
ON estudiantes.id_persona = personas.id_persona

LEFT JOIN calificaciones
ON calificaciones.id_estudiante = estudiantes.id_estudiante
AND calificaciones.id_nivel = inscripciones.id_nivel
AND calificaciones.evaluacion = '$evaluacion'

WHERE inscripciones.id_nivel = '$id_nivel'
AND inscripciones.estado = 1

ORDER BY personas.apellido ASC
";

    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado || mysqli_num_rows($resultado) == 0) {
        die("No se encontraron calificaciones.");
    }

    $sql_nivel = "SELECT nivel_academico FROM niveles WHERE id_nivel = '$id_nivel'";
    $resultado_nivel = mysqli_query($conexion, $sql_nivel);
    $fila_nivel = mysqli_fetch_assoc($resultado_nivel);
    $nombre_nivel = $fila_nivel['nivel_academico'];

    // Preparar los nombres de las actividades

    $nombre_actividad_1 = "Nota 1";
    $nombre_actividad_2 = "Nota 2";

    $mostrar_actividad_1 = false;
    $mostrar_actividad_2 = false;

    while ($fila_encabezado = mysqli_fetch_assoc($resultado)) {

        // Obtener el nombre de la Actividad 1, si fue escrito
        if (
            $fila_encabezado['descripcion_nota_1'] !== null &&
            $fila_encabezado['descripcion_nota_1'] !== ""
        ) {
            $nombre_actividad_1 = ucwords(
                strtolower($fila_encabezado['descripcion_nota_1'])
            );
        }

        // Mostrar la columna Nota 1 solamente si algún estudiante tiene Nota 1
        if (
            $fila_encabezado['nota_1'] !== null &&
            $fila_encabezado['nota_1'] !== ""
        ) {
            $mostrar_actividad_1 = true;
        }

        // Obtener el nombre de la Actividad 2, si fue escrito
        if (
            $fila_encabezado['descripcion_nota_2'] !== null &&
            $fila_encabezado['descripcion_nota_2'] !== ""
        ) {
            $nombre_actividad_2 = ucwords(
                strtolower($fila_encabezado['descripcion_nota_2'])
            );
        }

        // Mostrar la columna Nota 2 solamente si algún estudiante tiene Nota 2
        if (
            $fila_encabezado['nota_2'] !== null &&
            $fila_encabezado['nota_2'] !== ""
        ) {
            $mostrar_actividad_2 = true;
        }
    }

    // Regresar el resultado al primer estudiante
    mysqli_data_seek($resultado, 0);

    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <link rel="icon" href="../images/EFB.png" type="image/png">
    <meta charset="UTF-8">

    <style>

    body {
        font-family: "Helvetica", "Arial", sans-serif;
        font-size: 12px;
        color: #333;
    }

    .encabezado {
        text-align: center;
        border-bottom: 2px solid #555;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .encabezado h1 {
        margin: 0;
        font-size: 22px;
        text-transform: uppercase;
        color: #222;
        letter-spacing: 1px;
    }

    .encabezado h2 {
        margin: 5px 0 0 0;
        font-size: 16px;
        font-weight: normal;
        color: #555;
    }

    .info-seccion {
        margin-bottom: 20px;
    }

    .info-seccion p {
        margin: 5px 0;
        font-size: 13px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        border: 1px solid #999;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: #e5e5e5;
        color: #333;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 11px;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    </style>
    </head>

    <body>

    <div class="encabezado">
        <h1>Escuela de Formación Bíblica</h1>
        <h2>Reporte de Calificaciones</h2>
    </div>

    <div class="info-seccion">
        <p><strong>Nivel Académico:</strong> ' . htmlspecialchars($nombre_nivel) . '</p>
        <p><strong>Evaluación:</strong> ' . htmlspecialchars(ucwords(strtolower($evaluacion))) . '</p>
    </div>

    <table>

    <tr>
    <th>Nombre</th>
    <th>Apellido</th>';

    if ($mostrar_actividad_1 == true) {
        $html .= '<th>' . htmlspecialchars($nombre_actividad_1) . '</th>';
    }

    if ($mostrar_actividad_2 == true) {
        $html .= '<th>' . htmlspecialchars($nombre_actividad_2) . '</th>';
    }

    $html .= '
        <th>Nota Final</th>
        <th>Observación</th>
    </tr>
    ';

    while ($fila = mysqli_fetch_assoc($resultado)) {

        $html .= '
    <tr>
    <td>' . htmlspecialchars(ucwords(strtolower($fila['nombre']))) . '</td>
    <td>' . htmlspecialchars(ucwords(strtolower($fila['apellido']))) . '</td>
    ';

        if ($mostrar_actividad_1 == true) {

            if ($fila['nota_1'] == null || $fila['nota_1'] == "") {
                $nota_1 = "-";
            } else {
                $nota_1 = htmlspecialchars($fila['nota_1']);
            }

            $html .= '<td>' . $nota_1 . '</td>';
        }

        if ($mostrar_actividad_2 == true) {

            if ($fila['nota_2'] == null || $fila['nota_2'] == "") {
                $nota_2 = "-";
            } else {
                $nota_2 = htmlspecialchars($fila['nota_2']);
            }

            $html .= '<td>' . $nota_2 . '</td>';
        }

        // Preparar Nota Final
        if ($fila['nota_final'] === null || $fila['nota_final'] === "") {
            $nota_final = "-";
        } else {
            $nota_final = htmlspecialchars($fila['nota_final']);
        }

        // Preparar Observación
        if ($fila['observacion'] === null || $fila['observacion'] === "") {
            $observacion = "Sin observación";
        } else {
            $observacion = htmlspecialchars(
                ucwords(strtolower($fila['observacion']))
            );
        }

        $html .= '
    <td><strong>' . $nota_final . '</strong></td>
    <td>' . $observacion . '</td>
</tr>
';
    }

    $html .= '
    </table>

    </body>
    </html>
    ';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("reporte_calificaciones.pdf", array("Attachment" => false));
