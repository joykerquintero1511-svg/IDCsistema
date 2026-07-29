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

        FROM calificaciones

        INNER JOIN estudiantes
        ON calificaciones.id_estudiante = estudiantes.id_estudiante

        INNER JOIN personas
        ON estudiantes.id_persona = personas.id_persona

        WHERE calificaciones.id_nivel = '$id_nivel'
        AND calificaciones.evaluacion = '$evaluacion'

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

        $mostrar_actividad_2 = false;

        while ($fila_encabezado = mysqli_fetch_assoc($resultado)) {

            if (
                $fila_encabezado['descripcion_nota_1'] !== null &&
                $fila_encabezado['descripcion_nota_1'] !== ""
            ) {
                $nombre_actividad_1 = ucwords(
                    strtolower($fila_encabezado['descripcion_nota_1'])
                );
            }

            if (
                $fila_encabezado['descripcion_nota_2'] !== null &&
                $fila_encabezado['descripcion_nota_2'] !== ""
            ) {
                $nombre_actividad_2 = ucwords(
                    strtolower($fila_encabezado['descripcion_nota_2'])
                );

                $mostrar_actividad_2 = true;
            }

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
        <meta charset="UTF-8">

        <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 7px;
            text-align: center;
        }

        th {
            background-color: #eeeeee;
        }

        </style>
        </head>

        <body>

        <h2>Reporte de Calificaciones</h2>

        <p><strong>Nivel:</strong> ' . htmlspecialchars($nombre_nivel) . '</p>

        <p><strong>Evaluación:</strong> ' . htmlspecialchars(ucwords(strtolower($evaluacion))) . '</p>

        <table>

        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>' . htmlspecialchars($nombre_actividad_1) . '</th>';

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
                <td>' . htmlspecialchars($fila['nota_1']) . '</td>
            ';

            if ($mostrar_actividad_2 == true) {

                if ($fila['nota_2'] == null || $fila['nota_2'] == "") {
                    $nota_2 = "No registrada";
                } else {
                    $nota_2 = htmlspecialchars($fila['nota_2']);
                }

                $html .= '<td>' . $nota_2 . '</td>';
            }

            if ($fila['observacion'] == null || $fila['observacion'] == "") {
                $observacion = "Sin observación";
            } else {
                $observacion = htmlspecialchars(ucwords(strtolower($fila['observacion'])));
            }

            $html .= '
                <td>' . htmlspecialchars($fila['nota_final']) . '</td>
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