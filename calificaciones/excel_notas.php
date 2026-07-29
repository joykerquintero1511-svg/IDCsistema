        <?php

        require '../vendor/autoload.php';
        require_once '../conexion.php';

        session_start();

        if (
            !isset($_SESSION['rol']) ||
            ($_SESSION['rol'] !== 'profesor' && $_SESSION['rol'] !== 'admin')
        ) {
            header("Location: ../login.php");
            exit();
        }

        use PhpOffice\PhpSpreadsheet\Spreadsheet;
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
        use PhpOffice\PhpSpreadsheet\Style\Alignment;
        use PhpOffice\PhpSpreadsheet\Style\Border;
        use PhpOffice\PhpSpreadsheet\Style\Fill;
        use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
       


        // Recibir y validar los datos

        if (
            !isset($_GET['id_nivel']) ||
            !isset($_GET['evaluacion']) ||
            $_GET['evaluacion'] === ""
        ) {
            die("Faltan datos para generar el archivo Excel.");
        }

        $id_nivel = intval($_GET['id_nivel']);

        $evaluacion = trim($_GET['evaluacion']);

        $evaluacion_segura = mysqli_real_escape_string(
            $conexion,
            $evaluacion
        );


        // Proteger el nivel asignado al profesor

        if ($_SESSION['rol'] === 'profesor') {

            $id_usuario = intval($_SESSION['id_usuario']);

            $sql_profesor = "
                SELECT id_nivel
                FROM profesores
                WHERE id_usuario = '$id_usuario'
                LIMIT 1
            ";

            $resultado_profesor = mysqli_query(
                $conexion,
                $sql_profesor
            );

            if (
                !$resultado_profesor ||
                mysqli_num_rows($resultado_profesor) === 0
            ) {
                die("No se encontró el nivel asignado al profesor.");
            }

            $fila_profesor = mysqli_fetch_assoc(
                $resultado_profesor
            );

            $id_nivel_profesor = intval(
                $fila_profesor['id_nivel']
            );

            // Evitar que un profesor exporte otro nivel modificando la URL

            if ($id_nivel !== $id_nivel_profesor) {

                header("Location: ver_notas.php");
                exit();
            }
        }


        // Consultar las calificaciones

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
            AND calificaciones.evaluacion = '$evaluacion_segura'

            ORDER BY personas.apellido ASC,
                    personas.nombre ASC
        ";

        $resultado = mysqli_query($conexion, $sql);

        if (
            !$resultado ||
            mysqli_num_rows($resultado) === 0
        ) {
            die("No se encontraron calificaciones.");
        }


        // Obtener el nombre del nivel

        $sql_nivel = "
            SELECT nivel_academico
            FROM niveles
            WHERE id_nivel = '$id_nivel'
            LIMIT 1
        ";

        $resultado_nivel = mysqli_query(
            $conexion,
            $sql_nivel
        );

        if (
            !$resultado_nivel ||
            mysqli_num_rows($resultado_nivel) === 0
        ) {
            die("No se encontró el nivel académico.");
        }

        $fila_nivel = mysqli_fetch_assoc(
            $resultado_nivel
        );

        $nombre_nivel = $fila_nivel['nivel_academico'];


        // Obtener los nombres de las actividades

        $fila_encabezado = mysqli_fetch_assoc(
            $resultado
        );

        $nombre_actividad_1 = "Nota 1";
        $nombre_actividad_2 = "Nota 2";

        if (
            $fila_encabezado['descripcion_nota_1'] !== null &&
            $fila_encabezado['descripcion_nota_1'] !== ""
        ) {
            $nombre_actividad_1 = ucwords(
                strtolower(
                    $fila_encabezado['descripcion_nota_1']
                )
            );
        }

        if (
            $fila_encabezado['descripcion_nota_2'] !== null &&
            $fila_encabezado['descripcion_nota_2'] !== ""
        ) {
            $nombre_actividad_2 = ucwords(
                strtolower(
                    $fila_encabezado['descripcion_nota_2']
                )
            );
        }


        // Determinar si debe mostrarse la segunda actividad

        $mostrar_actividad_2 = false;

        if (
            $fila_encabezado['descripcion_nota_2'] !== null &&
            $fila_encabezado['descripcion_nota_2'] !== ""
        ) {
            $mostrar_actividad_2 = true;
        }

        mysqli_data_seek($resultado, 0);


        // Determinar las columnas del Excel

        if ($mostrar_actividad_2 === true) {

            $columna_nota_final = "E";
            $columna_observacion = "F";

        } else {

            $columna_nota_final = "D";
            $columna_observacion = "E";
        }

        $ultima_columna = $columna_observacion;


        // Crear el documento Excel

        $spreadsheet = new Spreadsheet();

        $hoja = $spreadsheet->getActiveSheet();

        $hoja->setTitle("Calificaciones");

        $hoja->setShowGridlines(false);


        // Crear el encabezado principal

        $hoja->mergeCells(
            "B1:" . $ultima_columna . "2"
        );

        $hoja->setCellValue(
            "B1",
            "REPORTE DE CALIFICACIONES\nIglesia Dios en Casa - Sistema Académico"
        );


        // Fondo blanco para el espacio del logo

        $hoja->getStyle("A1:A2")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB("FFFFFFFF");


        // Fondo verde para el título

        $hoja->getStyle(
            "B1:" . $ultima_columna . "2"
        )->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB("FF176B5B");


        // Diseño del título

        $hoja->getStyle(
            "B1:" . $ultima_columna . "2"
        )->getFont()
            ->setBold(true)
            ->setSize(14)
            ->getColor()
            ->setARGB("FFFFFFFF");

        $hoja->getStyle(
            "B1:" . $ultima_columna . "2"
        )->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            )
            ->setWrapText(true);

        $hoja->getRowDimension(1)->setRowHeight(28);
        $hoja->getRowDimension(2)->setRowHeight(22);


        // Insertar el logo dentro del encabezado

        $ruta_logo = "../images/logo_azul.png";

        if (file_exists($ruta_logo)) {

            $logo = new Drawing();

            $logo->setName("Logo EFB");
            $logo->setDescription("Logo de la institución");
            $logo->setPath($ruta_logo);
            $logo->setHeight(55);
            $logo->setCoordinates("A1");
            $logo->setOffsetX(14);
            $logo->setOffsetY(4);
            $logo->setWorksheet($hoja);
        }


        // Información del reporte

        $hoja->setCellValue("A4", "Nivel:");

        $hoja->setCellValue(
            "B4",
            ucwords(strtolower($nombre_nivel))
        );

        $hoja->mergeCells("B4:C4");

        $hoja->setCellValue(
            $columna_nota_final . "4",
            "Fecha:"
        );

        $hoja->setCellValue(
            $columna_observacion . "4",
            date("d/m/Y")
        );

        $hoja->setCellValue("A5", "Evaluación:");

        $hoja->setCellValue(
            "B5",
            ucwords(strtolower($evaluacion))
        );

        $hoja->mergeCells(
            "B5:" . $ultima_columna . "5"
        );


        // Diseño del cuadro informativo

        $rango_informacion =
            "A4:" .
            $ultima_columna .
            "5";

        $hoja->getStyle($rango_informacion)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            )
            ->getColor()
            ->setARGB("FFD1D5DB");

        $hoja->getStyle($rango_informacion)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB("FFF8FAFC");

        $hoja->getStyle($rango_informacion)
            ->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        // Diseño de las etiquetas del cuadro

        $hoja->getStyle("A4:A5")
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setARGB("FF1F2937");

        $hoja->getStyle(
            $columna_nota_final . "4"
        )->getFont()
            ->setBold(true)
            ->getColor()
            ->setARGB("FF1F2937");

        $hoja->getRowDimension(4)->setRowHeight(23);
        $hoja->getRowDimension(5)->setRowHeight(23);
        $hoja->getRowDimension(6)->setRowHeight(10);


        // Encabezados de la tabla

        $fila_encabezados = 7;

        $hoja->setCellValue(
            "A" . $fila_encabezados,
            "Nombre"
        );

        $hoja->setCellValue(
            "B" . $fila_encabezados,
            "Apellido"
        );

        $hoja->setCellValue(
            "C" . $fila_encabezados,
            $nombre_actividad_1
        );

        if ($mostrar_actividad_2 === true) {

            $hoja->setCellValue(
                "D" . $fila_encabezados,
                $nombre_actividad_2
            );
        }

        $hoja->setCellValue(
            $columna_nota_final . $fila_encabezados,
            "Nota Final"
        );

        $hoja->setCellValue(
            $columna_observacion . $fila_encabezados,
            "Observación"
        );


        // Insertar las calificaciones

        $fila_excel = 8;

        while (
            $fila = mysqli_fetch_assoc($resultado)
        ) {

            $nombre = ucwords(
                strtolower($fila['nombre'])
            );

            $apellido = ucwords(
                strtolower($fila['apellido'])
            );

            $hoja->setCellValue(
                "A" . $fila_excel,
                $nombre
            );

            $hoja->setCellValue(
                "B" . $fila_excel,
                $apellido
            );

            $hoja->setCellValue(
                "C" . $fila_excel,
                $fila['nota_1']
            );

            if ($mostrar_actividad_2 === true) {

                if (
                    $fila['nota_2'] === null ||
                    $fila['nota_2'] === ""
                ) {
                    $nota_2 = "No registrada";
                } else {
                    $nota_2 = $fila['nota_2'];
                }

                $hoja->setCellValue(
                    "D" . $fila_excel,
                    $nota_2
                );
            }

            $hoja->setCellValue(
                $columna_nota_final . $fila_excel,
                $fila['nota_final']
            );

            if (
                $fila['observacion'] === null ||
                $fila['observacion'] === ""
            ) {
                $observacion = "Sin observación";
            } else {
                $observacion = ucwords(
                    strtolower(
                        $fila['observacion']
                    )
                );
            }

            $hoja->setCellValue(
                $columna_observacion . $fila_excel,
                $observacion
            );

            $fila_excel++;
        }

        $ultima_fila = $fila_excel - 1;


        // Diseño de los encabezados de la tabla

        $rango_encabezados =
            "A" .
            $fila_encabezados .
            ":" .
            $ultima_columna .
            $fila_encabezados;

        $hoja->getStyle($rango_encabezados)
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setARGB("FFFFFFFF");

        $hoja->getStyle($rango_encabezados)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB("FF237A68");

        $hoja->getStyle($rango_encabezados)
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            )
            ->setWrapText(true);

        $hoja->getRowDimension(
            $fila_encabezados
        )->setRowHeight(28);


        // Bordes de la tabla

        $rango_tabla =
            "A" .
            $fila_encabezados .
            ":" .
            $ultima_columna .
            $ultima_fila;

        $hoja->getStyle($rango_tabla)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            )
            ->getColor()
            ->setARGB("FFCBD5E1");


        // Alineación de los datos

        $hoja->getStyle(
            "A8:" .
            $ultima_columna .
            $ultima_fila
        )->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );

        $hoja->getStyle(
            "C8:" .
            $columna_nota_final .
            $ultima_fila
        )->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $hoja->getStyle(
            $columna_observacion .
            "8:" .
            $columna_observacion .
            $ultima_fila
        )->getAlignment()
            ->setWrapText(true);


        // Aplicar fondo alternado a las filas

        for ($fila_actual = 8; $fila_actual <= $ultima_fila; $fila_actual++) {

            if ($fila_actual % 2 === 0) {

                $hoja->getStyle(
                    "A" .
                    $fila_actual .
                    ":" .
                    $ultima_columna .
                    $fila_actual
                )->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB("FFF4F8F7");
            }

            $hoja->getRowDimension(
                $fila_actual
            )->setRowHeight(22);
        }


        // Ajustar el tamaño de las columnas

        $hoja->getColumnDimension("A")
            ->setWidth(20);

        $hoja->getColumnDimension("B")
            ->setWidth(20);

        $hoja->getColumnDimension("C")
            ->setWidth(18);

        if ($mostrar_actividad_2 === true) {

            $hoja->getColumnDimension("D")
                ->setWidth(18);
        }

        $hoja->getColumnDimension(
            $columna_nota_final
        )->setWidth(15);

        $hoja->getColumnDimension(
            $columna_observacion
        )->setWidth(34);


        // Congelar los encabezados

        $hoja->freezePane("A8");


        // Agregar filtros

        $hoja->setAutoFilter(
            "A" .
            $fila_encabezados .
            ":" .
            $ultima_columna .
            $ultima_fila
        );


        // Configurar la impresión

        $hoja->getPageSetup()
            ->setFitToWidth(1);

        $hoja->getPageSetup()
            ->setFitToHeight(0);

        $hoja->getPageMargins()
            ->setTop(0.5);

        $hoja->getPageMargins()
            ->setBottom(0.5);

        $hoja->getPageMargins()
            ->setLeft(0.4);

        $hoja->getPageMargins()
            ->setRight(0.4);

        $hoja->getPageSetup()
            ->setPrintArea(
                "A1:" .
                $ultima_columna .
                $ultima_fila
            );


        // Preparar el nombre del archivo

        $nombre_nivel_archivo = preg_replace(
            '/[^A-Za-z0-9_-]/',
            '_',
            $nombre_nivel
        );

        $nombre_archivo =
            "calificaciones_" .
            $nombre_nivel_archivo .
            ".xlsx";


        // Descargar el archivo
        // Limpiar cualquier contenido generado antes del archivo Excel

        while (ob_get_level() > 0) {
            ob_end_clean();
        }


        header(
            "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        );

        header(
            'Content-Disposition: attachment; filename="' .
            $nombre_archivo .
            '"'
        );

        header("Cache-Control: max-age=0");

        $writer = new Xlsx($spreadsheet);

        $writer->save("php://output");

        exit();

